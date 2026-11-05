<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Lectura "best effort" de un proyecto de Workana.
 *
 * Dos caminos:
 *  - fetch(url): intenta leer desde el servidor. Workana suele responder 403,
 *    así que puede fallar.
 *  - parsePasted(contenido): recibe el texto (o HTML) que el usuario copió desde
 *    su navegador ya logueado y extrae los campos. Este sí funciona siempre,
 *    porque no depende de que el servidor pueda leer Workana.
 *
 * Ninguno lanza excepción: devuelven un arreglo con 'success' y los datos.
 */
class WorkanaScraperService
{
    private const EMPTY = [
        'project_title' => null,
        'project_description' => null,
        'skills' => [],
        'published_at' => null,
        'proposals_count' => null,
        'client_name' => null,
        'client_published_projects' => null,
        'client_paid_projects' => null,
        'client_member_since' => null,
    ];

    private const MESES = [
        'enero' => '01', 'febrero' => '02', 'marzo' => '03', 'abril' => '04',
        'mayo' => '05', 'junio' => '06', 'julio' => '07', 'agosto' => '08',
        'septiembre' => '09', 'setiembre' => '09', 'octubre' => '10',
        'noviembre' => '11', 'diciembre' => '12',
    ];

    /**
     * Intenta leer el proyecto desde el servidor (puede dar 403).
     */
    public function fetch(string $url): array
    {
        $result = ['success' => false, 'reason' => null, 'data' => self::EMPTY];

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

            $result['data'] = $this->parse($response->body());
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

    /**
     * Parsea el contenido (texto plano o HTML) que el usuario copió de la página.
     */
    public function parsePasted(string $content): array
    {
        $content = trim($content);

        if (mb_strlen($content) < 20) {
            return ['success' => false, 'reason' => 'contenido_vacio', 'data' => self::EMPTY];
        }

        // Si parece HTML, aprovechamos og:title/description antes de aplanar.
        $data = self::EMPTY;
        if (str_contains($content, '<') && str_contains($content, '>')) {
            $data = array_merge($data, $this->parse($content));
            $content = $this->htmlToText($content);
        }

        $fromText = $this->extractFromText($content);
        // Lo extraído del texto completa lo que falte (y pisa valores vacíos).
        foreach ($fromText as $key => $value) {
            if ($value !== null && $value !== []) {
                $data[$key] = $value;
            }
        }

        $found = collect($data)->filter(fn ($v) => $v !== null && $v !== [])->isNotEmpty();

        return [
            'success' => $found,
            'reason' => $found ? null : 'sin_datos',
            'data' => $data,
        ];
    }

    /**
     * Parseo desde HTML (usado por fetch): og tags + texto aplanado.
     */
    private function parse(string $html): array
    {
        $data = self::EMPTY;

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
        $xpath = new \DOMXPath($dom);

        $data['project_title'] = $this->meta($xpath, 'og:title') ?: null;
        $data['project_description'] = $this->meta($xpath, 'og:description')
            ?: $this->metaName($xpath, 'description') ?: null;

        libxml_clear_errors();

        // Completa con lo que se pueda sacar del texto plano del HTML.
        $fromText = $this->extractFromText($this->htmlToText($html));
        foreach ($fromText as $key => $value) {
            if (empty($data[$key]) && $value !== null && $value !== []) {
                $data[$key] = $value;
            }
        }

        return $data;
    }

    /**
     * Extrae los campos a partir de texto plano (el copiado de la página).
     */
    private function extractFromText(string $text): array
    {
        $data = self::EMPTY;

        // Normaliza saltos de línea y espacios.
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = preg_replace('/[ \t]+/', ' ', $text);

        // Título: la línea justo antes de "Publicado el".
        if (preg_match('/\n?([^\n]{10,180})\n[ ]*Publicado el/u', $text, $m)) {
            $data['project_title'] = trim($m[1]);
        }

        // Fecha de publicación: "Publicado el 04 Noviembre, 2026".
        if (preg_match('/Publicado el\s+(\d{1,2})\s+([A-Za-zÁÉÍÓÚáéíóú]+),?\s+(\d{4})/u', $text, $m)) {
            $mes = self::MESES[mb_strtolower($m[2])] ?? null;
            if ($mes) {
                $data['published_at'] = sprintf('%04d-%s-%02d', (int) $m[3], $mes, (int) $m[1]);
            }
        }

        // Descripción: entre "Sobre este proyecto" y "Categoría".
        if (preg_match('/Sobre este proyecto\s*(?:Abierto)?\s*(.+?)\s*Categoría/su', $text, $m)) {
            $desc = trim($m[1]);
            if (mb_strlen($desc) > 20) {
                $data['project_description'] = $desc;
            }
        }

        // Habilidades: después de "Habilidades necesarias".
        if (preg_match('/Habilidades necesarias\s*\n?([^\n]+)/u', $text, $m)) {
            $raw = trim($m[1]);
            $parts = preg_split('/\s{2,}|\n|,/', $raw) ?: [];
            if (count($parts) <= 1) {
                $parts = preg_split('/\s+/', $raw) ?: [];
            }
            $skills = collect($parts)
                ->map(fn ($s) => trim($s))
                ->filter(fn ($s) => $s !== '' && mb_strlen($s) < 30)
                ->take(15)
                ->values()
                ->all();
            if ($skills) {
                $data['skills'] = $skills;
            }
        }

        // Nº de propuestas: "43 ... Propuestas".
        if (preg_match('/(\d+)\s*\n?\s*Propuestas/u', $text, $m)) {
            $data['proposals_count'] = (int) $m[1];
        }

        // Cliente: proyectos publicados / pagos.
        if (preg_match('/(\d+)\s*\n?\s*Proyectos? publicad/iu', $text, $m)) {
            $data['client_published_projects'] = (int) $m[1];
        }
        if (preg_match('/(\d+)\s*\n?\s*Proyectos? pag/iu', $text, $m)) {
            $data['client_paid_projects'] = (int) $m[1];
        }

        // Miembro desde: "Miembro desde: Abril, 2020".
        if (preg_match('/Miembro desde:?\s*([A-Za-zÁÉÍÓÚáéíóú]+,?\s*\d{4})/u', $text, $m)) {
            $data['client_member_since'] = trim($m[1]);
        }

        // Nombre del cliente: línea antes del nº de "Proyectos publicados".
        if (preg_match('/\n([A-ZÁÉÍÓÚ][\p{L}.\-\' ]{1,40}?)\s*\n+\s*\d+\s*\n+\s*Proyectos? publicad/u', $text, $m)) {
            $data['client_name'] = trim($m[1]);
        }

        return $data;
    }

    private function htmlToText(string $html): string
    {
        $html = preg_replace('#<(script|style)[^>]*>.*?</\1>#si', ' ', $html);
        $html = preg_replace('#<br\s*/?>#i', "\n", $html);
        $html = preg_replace('#</(p|div|li|h[1-6]|tr|section|article)>#i', "\n", $html);
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return trim($text);
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
}
