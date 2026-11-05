<?php

namespace Database\Seeders;

use App\Models\Proposal;
use App\Models\ProposalTextBlock;
use Illuminate\Database\Seeder;

class ProposalExampleSeeder extends Seeder
{
    public function run(): void
    {
        $title = 'Desarrollo de Sistema de Venta de Diseños con Laravel y Módulos de Administración y Cliente';

        // Bloques de texto tomados de la plantilla base (snapshot por propuesta).
        $textBlocks = ProposalTextBlock::where('is_active', true)
            ->orderBy('order')
            ->get()
            ->mapWithKeys(fn ($b) => [$b->key => ['title' => $b->title, 'content' => $b->content]])
            ->all();

        $phases = [
            [
                'name' => 'Núcleo del sistema',
                'amount_cop' => 2800000,
                'items' => [
                    ['description' => 'Autenticación y roles (admin / cliente)', 'hours' => 4],
                    ['description' => 'CRUD de diseños y catálogo', 'hours' => 12],
                    ['description' => 'Panel de administración', 'hours' => 10],
                    ['description' => 'Módulo de cliente (exploración y compra de diseños)', 'hours' => 14],
                    ['description' => 'Carga y gestión de archivos/imágenes', 'hours' => 8],
                    ['description' => 'Diseño responsive / maquetación', 'hours' => 12],
                ],
            ],
            [
                'name' => 'Cobro y pagos',
                'amount_cop' => 1200000,
                'items' => [
                    ['description' => 'Integración con pasarela de pago', 'hours' => 15],
                    ['description' => 'Notificaciones por correo (compra / entrega)', 'hours' => 6],
                    ['description' => 'Pruebas del flujo de pago', 'hours' => 4],
                ],
            ],
            [
                'name' => 'Reportes',
                'amount_cop' => 950000,
                'items' => [
                    ['description' => 'Reportes y exportación (PDF/Excel)', 'hours' => 8],
                    ['description' => 'Dashboard de ventas', 'hours' => 7],
                    ['description' => 'Pruebas y ajustes finales', 'hours' => 5],
                ],
            ],
        ];

        // Solo las tareas que SÍ van en la propuesta. Las demás (Hosting,
        // Dominio, Chat, Subir el contenido, Integración con pasarela de pago,
        // Kit para redes sociales, API Integration, SEO structure) se omiten:
        // en Workana se borran con la ×.
        $labels = collect(config('proposals.workana_tasks'))->keyBy('key');
        $tareas = [
            ['archivo_fuente', 'incluido', null],
            ['ssl', 'incluido', null],
            ['whatsapp_redes', 'incluido', null],
            ['diseno_adaptativo', 'incluido', null],
            ['speed_optimized', 'incluido', null],
            ['revision_extra', 'extra', 200000],
        ];
        $workanaTasks = collect($tareas)->map(fn ($t) => [
            'key' => $t[0],
            'label' => $labels[$t[0]]['label'] ?? $t[0],
            'status' => $t[1],
            'valor' => $t[2],
        ])->all();

        $details = $this->buildDetails($textBlocks, $phases, $workanaTasks);

        Proposal::updateOrCreate(
            ['project_title' => $title],
            [
                'workana_url' => null,
                'project_description' => 'Se requiere un sistema web para vender diseños digitales, con un '
                    . 'módulo de administración (gestión de diseños, precios y pedidos) y un módulo de '
                    . 'cliente (exploración, compra y descarga). Debe incluir pasarela de pago y reportes '
                    . 'de ventas.',
                'skills' => ['Laravel', 'PHP', 'MySQL', 'Vue.js', 'Pasarelas de pago'],
                'published_at' => now()->subDays(3)->toDateString(),
                'proposals_count' => 43,
                'client_name' => 'Luis A.',
                'client_published_projects' => 8,
                'client_paid_projects' => 5,
                'client_member_since' => '2021',
                'hourly_rate_usd' => 18,
                'workana_commission' => 10,
                'usd_to_cop_rate' => 3245,
                'phases' => $phases,
                'workana_tasks' => $workanaTasks,
                'text_blocks' => $textBlocks,
                'details_text' => $details,
                'delivery_time' => '4 semanas',
                'status' => 'enviada',
                'sent_at' => now()->subDays(2)->toDateString(),
            ]
        );
    }

    private function buildDetails(array $blocks, array $phases, array $tasks = []): string
    {
        $parts = [];

        if (isset($blocks['presentacion'])) {
            $parts[] = $blocks['presentacion']['content'];
        }

        $fases = "Propuesta de trabajo por fases:\n";
        foreach ($phases as $phase) {
            $hours = collect($phase['items'])->sum('hours');
            $fases .= "\n• {$phase['name']} ({$hours} h)\n";
            foreach ($phase['items'] as $item) {
                $fases .= "   - {$item['description']}\n";
            }
        }
        $parts[] = trim($fases);

        // Qué entra en el precio y qué es adicional (no hay notas en Workana,
        // así que esto se comunica en el texto de Detalles).
        $incluidas = collect($tasks)->where('status', 'incluido')->pluck('label');
        $extras = collect($tasks)->where('status', 'extra');
        if ($incluidas->isNotEmpty() || $extras->isNotEmpty()) {
            $qe = "Qué entra en el precio y qué no:\n";
            if ($incluidas->isNotEmpty()) {
                $qe .= "\nIncluido: " . $incluidas->implode(', ') . '.';
            }
            if ($extras->isNotEmpty()) {
                $qe .= "\nCon costo adicional: " . $extras->map(
                    fn ($t) => $t['label'] . ' (CO$ ' . number_format((float) $t['valor'], 0, ',', '.') . ')'
                )->implode(', ') . '.';
            }
            $parts[] = trim($qe);
        }

        if (isset($blocks['como_trabaja'])) {
            $parts[] = $blocks['como_trabaja']['content'];
        }
        if (isset($blocks['stack'])) {
            $parts[] = 'Stack: ' . $blocks['stack']['content'];
        }
        if (isset($blocks['enlaces'])) {
            $parts[] = $blocks['enlaces']['content'];
        }
        if (isset($blocks['cierre'])) {
            $parts[] = $blocks['cierre']['content'];
        }

        return implode("\n\n", $parts);
    }
}
