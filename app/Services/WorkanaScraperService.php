<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Scraping "best effort" de un proyecto de Workana.
 *
 * Workana suele responder 403 a peticiones desde servidor (aunque en el
 * navegador cargue bien). Por eso este servicio NUNCA lanza excepción ni
 * bloquea: devuelve siempre un arreglo con 'success' y, si pudo, los campos
 * extraídos. Si falla, el formulario permite pegar los datos a mano.
 */
class WorkanaScraperService
{
    public function fetch(string $url): array
    {
        $result = [
            'success' => false,
            'reason' => null,
            'data' => [
                'project_title' => null,
                'project_description' => null,
                'skills' => [],
                'published_at' => null,
                'proposals_count' => null,
                'client_name' => null,
                'client_published_projects' => null,
                'client_paid_projects' => null,
                'client_member_since' => null,
            ],
        ];

        if (!filter_var($url, FILTER_VALIDATE_URL) || !str_contains($url, 'workana.com')) {
            $result['reason'] = 'url_invalida';
            return $result;
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 '
                    . '(KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'es-ES,es;q=0.9,en;q=0.8',
            ])->timeout(12)->get($url);

            if (!$response->ok()) {
                $result['reason'] = 'http_' . $response->status();
                return $result;
            }

            $html = $response->body();
            $result['data'] = array_merge($result['data'], $this->parse($html));
            $result['success'] = true;

            return $result;
        } catch (\Throwable $e) {
            Log::info('WorkanaScraperService: no se pudo leer el proyecto', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            $result['reason'] = 'excepcion';
            return $result;
        }
    }

    private function parse(string $html): array
    {
        $data = [];

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
        $xpath = new \DOMXPath($dom);

        // Título: og:title o <h1>
        $data['project_title'] = $this->meta($xpath, 'og:title')
            ?? $this->firstText($xpath, '//h1');

        // Descripción: og:description o meta description
        $data['project_description'] = $this->meta($xpath, 'og:description')
            ?? $this->metaName($xpath, 'description');

        // Habilidades: enlaces de skills suelen apuntar a /jobs?skills= o clase skill
        $skills = [];
        foreach ($xpath->query('//a[contains(@href, "skills") or contains(@class, "skill")]') as $node) {
            $text = trim($node->textContent);
            if ($text !== '' && mb_strlen($text) < 40) {
                $skills[] = $text;
            }
        }
        $data['skills'] = array_values(array_unique($skills));

        // Nº de propuestas: busca patrones tipo "43 propuestas" / "Bids: 43"
        if (preg_match('/(\d+)\s*(propuesta|bid|oferta)/iu', $html, $m)) {
            $data['proposals_count'] = (int) $m[1];
        }

        // Cliente: proyectos publicados / pagos
        if (preg_match('/(\d+)\s*proyecto[s]?\s*publicad/iu', $html, $m)) {
            $data['client_published_projects'] = (int) $m[1];
        }
        if (preg_match('/(\d+)\s*proyecto[s]?\s*(pagad|pago)/iu', $html, $m)) {
            $data['client_paid_projects'] = (int) $m[1];
        }
        if (preg_match('/miembro desde\s*:?\s*([A-Za-zÁÉÍÓÚáéíóú0-9 ,\/]{4,30})/iu', $html, $m)) {
            $data['client_member_since'] = trim($m[1]);
        }

        libxml_clear_errors();

        return array_filter($data, fn ($v) => $v !== null && $v !== []);
    }

    private function meta(\DOMXPath $xpath, string $property): ?string
    {
        $node = $xpath->query('//meta[@property="' . $property . '"]/@content')->item(0);
        return $node ? trim($node->nodeValue) : null;
    }

    private function metaName(\DOMXPath $xpath, string $name): ?string
    {
        $node = $xpath->query('//meta[@name="' . $name . '"]/@content')->item(0);
        return $node ? trim($node->nodeValue) : null;
    }

    private function firstText(\DOMXPath $xpath, string $query): ?string
    {
        $node = $xpath->query($query)->item(0);
        return $node ? trim($node->textContent) : null;
    }
}
