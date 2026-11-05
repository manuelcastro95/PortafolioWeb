<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Carga los proyectos propios y las colaboraciones del portafolio.
 *
 * Es idempotente: se puede volver a ejecutar sin duplicar filas ni volver a
 * subir imágenes. Los proyectos se identifican por su título.
 *
 * Las imágenes viven en database/seeders/assets/projects y se suben al mismo
 * disco que usa el panel de administración (S3 cuando hay credenciales AWS).
 */
class PortfolioContentSeeder extends Seeder
{
    /**
     * Carpeta dentro del disco donde quedan las imágenes de este seeder.
     * Es una ruta fija (no aleatoria como en el panel) para que reejecutar
     * el seeder no genere copias huérfanas en el bucket.
     */
    private const REMOTE_DIR = 'projects/seed';

    public function run(): void
    {
        foreach ($this->projects() as $data) {
            $project = Project::updateOrCreate(
                ['title' => $data['title']],
                [
                    'type' => $data['type'],
                    'description' => $data['description'],
                    'link' => $data['link'],
                    'github' => $data['github'],
                    'order' => $data['order'],
                    'is_active' => true,
                ]
            );

            $project->skills()->sync($this->skillIds($data['skills']));
            $this->syncImages($project, $data['images']);

            $this->command->info("✓ {$data['type']}: {$data['title']}");
        }
    }

    /**
     * Devuelve los ids de las skills, creando las que falten.
     *
     * Las skills nuevas se crean con is_active = false a propósito: sirven
     * como etiqueta en las tarjetas de proyecto (la relación no filtra por
     * ese campo), pero no aparecen en la sección "Skills" de la portada,
     * que sí filtra por is_active y además espera un icono. Al cargarles un
     * icono desde el panel se pueden activar una por una.
     */
    private function skillIds(array $names): array
    {
        return collect($names)
            ->map(fn (string $name) => Skill::firstOrCreate(
                ['name' => $name],
                ['is_active' => false, 'order' => 99]
            )->id)
            ->all();
    }

    /**
     * Sube las imágenes que falten y las asocia al proyecto.
     */
    private function syncImages(Project $project, array $files): void
    {
        $disk = $this->diskName();

        foreach (array_values($files) as $order => $file) {
            $localPath = database_path("seeders/assets/projects/{$file}");

            if (! is_file($localPath)) {
                $this->command->warn("  · falta el archivo {$file}, se omite");
                continue;
            }

            $remotePath = self::REMOTE_DIR . '/' . $file;

            if (! Storage::disk($disk)->exists($remotePath)) {
                Storage::disk($disk)->put($remotePath, file_get_contents($localPath));
            }

            if ($disk === 's3') {
                Storage::disk($disk)->setVisibility($remotePath, 'public');
            }

            $project->images()->updateOrCreate(
                ['image_path' => $remotePath],
                ['order' => $order]
            );
        }

        // Quita imágenes de ejecuciones anteriores que ya no están en la lista.
        $project->images()
            ->where('image_path', 'like', self::REMOTE_DIR . '/%')
            ->whereNotIn('image_path', array_map(
                fn (string $f) => self::REMOTE_DIR . '/' . $f,
                $files
            ))
            ->delete();
    }

    /**
     * Mismo criterio que ProjectController y los modelos: S3 si hay
     * credenciales, si no el disco por defecto.
     */
    private function diskName(): string
    {
        return env('AWS_ACCESS_KEY_ID') && env('AWS_SECRET_ACCESS_KEY') && env('AWS_BUCKET')
            ? 's3'
            : config('filesystems.default');
    }

    private function projects(): array
    {
        return [
            // ---------------------------------------------------------------
            // Proyectos propios
            // ---------------------------------------------------------------
            [
                'title' => 'Control de Asistencias QR',
                'type' => 'proyecto',
                'order' => 1,
                'link' => null,
                'github' => 'https://github.com/manuelcastro95/control_asistencias',
                'description' => 'Sistema de control de asistencia estudiantil mediante códigos QR, '
                    . 'construido en Laravel. Gestiona instituciones, sedes, grados y alumnos; registra '
                    . 'la asistencia por escáner QR o manualmente; permite carga masiva de alumnos desde '
                    . 'Excel y expone una API REST que consume la app móvil. Incluye dashboard con '
                    . 'estadísticas, autenticación separada para alumnos y administradores, y paginación '
                    . 'del lado del servidor.',
                'skills' => ['Laravel', 'PHP', 'Blade', 'MySQL', 'API REST', 'Bootstrap'],
                'images' => [
                    'control-asistencias-dashboard.jpg',
                    'control-asistencias-alumnos.jpg',
                    'control-asistencias-detalle.jpg',
                    'control-asistencias-login.jpg',
                ],
            ],
            [
                'title' => 'Control de Asistencias · App móvil',
                'type' => 'proyecto',
                'order' => 2,
                'link' => null,
                'github' => 'https://github.com/manuelcastro95/control_asistencias_app',
                'description' => 'App móvil en Flutter que consume la API REST del sistema de control de '
                    . 'asistencias. El estudiante consulta su código QR personal, su historial de '
                    . 'asistencias paginado y sus estadísticas del mes y la semana. Login diferenciado '
                    . 'para estudiante y administrador, sesión persistente en el dispositivo y un único '
                    . 'código base para Android e iOS.',
                'skills' => ['Flutter', 'Dart', 'API REST'],
                'images' => ['control-asistencias-app.jpg'],
            ],
            [
                'title' => 'Saborear Express',
                'type' => 'proyecto',
                'order' => 3,
                'link' => 'https://software-restaurante.vercel.app',
                'github' => 'https://github.com/manuelcastro95/restaurante',
                'description' => 'Software de operación para restaurantes con tres roles: el mesero toma '
                    . 'y cobra pedidos por mesa, la cocina ve la cola de preparación y el administrador '
                    . 'gestiona productos, categorías, mesas, usuarios y roles. Frontend en React con '
                    . 'rutas protegidas por contexto de autenticación y backend propio en Node.js, '
                    . 'Express y MongoDB.',
                'skills' => ['React', 'JavaScript', 'Node.js', 'Express', 'MongoDB', 'Tailwind CSS'],
                'images' => ['saborear-express.jpg'],
            ],
            [
                'title' => 'Codelandia',
                'type' => 'proyecto',
                'order' => 4,
                'link' => 'https://blog-codelandia-mu.vercel.app',
                'github' => 'https://github.com/manuelcastro95/blog-codelandia',
                'description' => 'Blog sobre enseñar programación a niños de 5 a 12 años, con diez '
                    . 'artículos propios publicados. Construido en React 19 con Vite y Tailwind, '
                    . 'enrutado con React Router, componente de SEO por página, layout de artículo '
                    . 'reutilizable y páginas de términos y privacidad. Desplegado en Vercel.',
                'skills' => ['React', 'JavaScript', 'Vite', 'Tailwind CSS'],
                'images' => ['codelandia.jpg'],
            ],
            [
                'title' => 'Portafolio Web',
                'type' => 'proyecto',
                'order' => 5,
                'link' => 'https://manuel-castro.site',
                'github' => 'https://github.com/manuelcastro95/PortafolioWeb',
                'description' => 'Este mismo sitio. Laravel 10 con Inertia y Vue 3, estilado con '
                    . 'Tailwind. Trae un panel de administración para editar perfil, proyectos, skills '
                    . 'y experiencia sin tocar código, almacenamiento de imágenes y hoja de vida en AWS '
                    . 'S3, y analítica propia de visitas y clics. Desplegado en EC2 detrás de nginx.',
                'skills' => ['Laravel', 'PHP', 'Vue.js', 'Inertia.js', 'Tailwind CSS', 'AWS S3'],
                'images' => ['portafolio-web.jpg'],
            ],

            // ---------------------------------------------------------------
            // Colaboraciones
            // ---------------------------------------------------------------
            [
                'title' => 'KaiDoc',
                'type' => 'colaboracion',
                'order' => 1,
                'link' => 'https://kaidoc.co/',
                'github' => null,
                'description' => 'Plataforma colombiana de gestión documental y talento humano, hoy '
                    . 'activa en más de setenta empresas. Un SGDEA alineado a la Ley 594 y a los '
                    . 'lineamientos del Archivo General de la Nación, con versionado y trazabilidad de '
                    . 'documentos, flujos de aprobación, SGI ISO 9001/14001/45001 y workflows no-code. '
                    . 'Desarrollo sobre Laravel y Vue.',
                'skills' => ['Laravel', 'PHP', 'Vue.js', 'MySQL'],
                'images' => ['kaidoc.jpg'],
            ],
            [
                'title' => 'KaiLiving',
                'type' => 'colaboracion',
                'order' => 2,
                'link' => 'https://kailiving.app/',
                'github' => null,
                'description' => 'Ecosistema digital para propiedad horizontal en Colombia: contabilidad '
                    . 'con PUC ESAL, cuotas de administración, portería con autorización de visitantes, '
                    . 'PQRS con trazabilidad, reservas de zonas comunes y mantenimientos, más una app '
                    . 'para residentes y un agente de WhatsApp. Módulos integrados de forma nativa sobre '
                    . 'Laravel y Vue, con infraestructura en AWS.',
                'skills' => ['Laravel', 'PHP', 'Vue.js', 'MySQL', 'AWS S3'],
                'images' => ['kailiving.jpg'],
            ],
            [
                'title' => 'Directorio KaiLiving',
                'type' => 'colaboracion',
                'order' => 3,
                'link' => 'https://directorio.kailiving.app/',
                'github' => null,
                'description' => 'Directorio que conecta conjuntos residenciales y copropiedades con '
                    . 'proveedores en Colombia. Las copropiedades publican su convocatoria y comparan '
                    . 'documentos, reputación y sellos de verificación; los proveedores reciben las '
                    . 'oportunidades por WhatsApp. Buscador por servicio, categoría y ciudad, con '
                    . 'bolsa de empleo. Laravel 13 con Inertia y Vue 3.',
                'skills' => ['Laravel', 'PHP', 'Vue.js', 'Inertia.js', 'Tailwind CSS', 'MySQL'],
                'images' => ['directorio-kailiving.jpg'],
            ],
            [
                'title' => 'Crisal·IA — Nutrabiotics',
                'type' => 'colaboracion',
                'order' => 4,
                'link' => 'https://nutrabiotics.mozartai.com.co/login',
                'github' => null,
                'description' => 'Plataforma de atención médica asistida por IA desarrollada para '
                    . 'Nutrabiotics durante mi paso por JOINUP. Construí el flujo completo de la cita: '
                    . 'agenda, estados, sala de espera, preconsulta y consulta; y las interfaces de IA '
                    . 'del recorrido del paciente en React con TypeScript: anamnesis guiada, detección '
                    . 'de incoherencias, matriz de salud, matching médico y sugerencias durante la '
                    . 'consulta. También la verificación de citas por WhatsApp y la generación de PDF '
                    . 'de historia clínica y consentimientos informados.',
                'skills' => ['React', 'TypeScript', 'Node.js', 'Express', 'API REST'],
                'images' => ['crisalia-nutrabiotics.jpg'],
            ],
        ];
    }
}
