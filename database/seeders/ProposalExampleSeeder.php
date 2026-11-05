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
                'amount_cop' => 3500000,
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
                'amount_cop' => 1500000,
                'items' => [
                    ['description' => 'Integración con pasarela de pago', 'hours' => 15],
                    ['description' => 'Notificaciones por correo (compra / entrega)', 'hours' => 6],
                    ['description' => 'Pruebas del flujo de pago', 'hours' => 4],
                ],
            ],
            [
                'name' => 'Reportes',
                'amount_cop' => 1200000,
                'items' => [
                    ['description' => 'Reportes y exportación (PDF/Excel)', 'hours' => 8],
                    ['description' => 'Dashboard de ventas', 'hours' => 7],
                    ['description' => 'Pruebas y ajustes finales', 'hours' => 5],
                ],
            ],
        ];

        $taskStatuses = [
            'archivo_fuente' => ['incluido', 'Entrego el código fuente completo.'],
            'hosting' => ['costo_adicional', 'Puedo configurarlo; el costo del servicio corre por tu cuenta.'],
            'dominio' => ['costo_adicional', 'Lo configuro; la compra del dominio corre por tu cuenta.'],
            'ssl' => ['incluido', 'Certificado Let\'s Encrypt incluido.'],
            'chat' => ['no_incluido', ''],
            'subir_contenido' => ['no_incluido', 'La carga del catálogo inicial la hace tu equipo.'],
            'pasarela_pago' => ['incluido', 'Integración con una pasarela (Stripe / MercadoPago / Wompi).'],
            'kit_redes' => ['no_incluido', ''],
            'whatsapp_redes' => ['no_incluido', ''],
            'revision_extra' => ['costo_adicional', 'Dos rondas de ajustes incluidas; adicionales por hora.'],
            'diseno_adaptativo' => ['incluido', 'Interfaz responsive para escritorio y móvil.'],
            'api_integration' => ['no_incluido', ''],
            'speed_optimized' => ['incluido', 'Buenas prácticas de rendimiento desde el inicio.'],
            'seo_structure' => ['no_incluido', ''],
        ];

        $workanaTasks = collect(config('proposals.workana_tasks'))->map(function ($task) use ($taskStatuses) {
            [$status, $note] = $taskStatuses[$task['key']] ?? ['no_incluido', ''];
            return [
                'key' => $task['key'],
                'label' => $task['label'],
                'status' => $status,
                'note' => $note,
            ];
        })->all();

        $details = $this->buildDetails($textBlocks, $phases);

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

    private function buildDetails(array $blocks, array $phases): string
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
