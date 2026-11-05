<?php

namespace Database\Seeders;

use App\Models\ProposalSetting;
use App\Models\ProposalTaskCatalog;
use App\Models\ProposalTextBlock;
use Illuminate\Database\Seeder;

class ProposalCatalogSeeder extends Seeder
{
    public function run(): void
    {
        // Configuración por defecto (fila única).
        ProposalSetting::current();

        // Catálogo reutilizable de tareas con horas por defecto.
        $tasks = [
            ['Autenticación y roles', 4, 'Backend'],
            ['CRUD de administración', 12, 'Backend'],
            ['Panel de administración (dashboard)', 10, 'Backend'],
            ['Módulo de cliente / usuario', 10, 'Backend'],
            ['Pasarela de pago', 15, 'Integraciones'],
            ['Reportes y exportación (PDF/Excel)', 8, 'Backend'],
            ['API REST', 10, 'Backend'],
            ['Notificaciones (correo / WhatsApp)', 6, 'Integraciones'],
            ['Carga y gestión de archivos/imágenes', 5, 'Backend'],
            ['Búsqueda y filtros', 4, 'Frontend'],
            ['Diseño responsive / maquetación', 8, 'Frontend'],
            ['Despliegue y configuración de servidor', 5, 'Infraestructura'],
            ['Pruebas y ajustes finales', 6, 'General'],
            ['Integración con API externa', 8, 'Integraciones'],
        ];

        foreach ($tasks as $i => [$name, $hours, $category]) {
            ProposalTaskCatalog::updateOrCreate(
                ['name' => $name],
                [
                    'default_hours' => $hours,
                    'category' => $category,
                    'order' => $i,
                    'is_active' => true,
                ]
            );
        }

        // Bloques de texto reutilizables (plantilla base).
        $blocks = [
            [
                'key' => 'presentacion',
                'title' => 'Presentación',
                'content' => "Hola, soy Juan Manuel Castro, desarrollador Full Stack con más de 3 años de "
                    . "experiencia construyendo sistemas web a la medida con Laravel, Vue y React. "
                    . "Leí con atención tu proyecto y puedo ayudarte a sacarlo adelante con una solución "
                    . "sólida, mantenible y lista para crecer.",
            ],
            [
                'key' => 'como_trabaja',
                'title' => 'Cómo trabajo',
                'content' => "Trabajo por fases con entregas revisables: acordamos el alcance, divido el "
                    . "proyecto en etapas con objetivos claros y te muestro avances funcionales para que "
                    . "siempre sepas en qué vamos. Mantengo comunicación constante y entrego código limpio "
                    . "y documentado.",
            ],
            [
                'key' => 'stack',
                'title' => 'Stack técnico',
                'content' => "Backend: Laravel (PHP), MySQL. Frontend: Vue.js / React, Inertia, Tailwind CSS. "
                    . "Integraciones: pasarelas de pago, APIs REST, notificaciones. Despliegue en servidores "
                    . "Linux (nginx) y AWS.",
            ],
            [
                'key' => 'enlaces',
                'title' => 'Enlaces',
                'content' => "Portafolio: https://manuel-castro.site/\n"
                    . "LinkedIn: https://www.linkedin.com/in/manuel-castro-minotta/\n"
                    . "GitHub: https://github.com/manuelcastro95",
            ],
            [
                'key' => 'cierre',
                'title' => 'Cierre',
                'content' => "Quedo atento para conversar los detalles y ajustar el alcance a lo que "
                    . "necesitas. Gracias por considerar mi propuesta. ¡Saludos!",
            ],
        ];

        foreach ($blocks as $i => $block) {
            ProposalTextBlock::updateOrCreate(
                ['key' => $block['key']],
                [
                    'title' => $block['title'],
                    'content' => $block['content'],
                    'order' => $i,
                    'is_active' => true,
                ]
            );
        }
    }
}
