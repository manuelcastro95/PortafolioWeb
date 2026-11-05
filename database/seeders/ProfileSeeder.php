<?php

namespace Database\Seeders;

use App\Models\Experience;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Perfil y experiencia profesional, tomados de la hoja de vida.
 *
 * Es idempotente. La experiencia se identifica por empresa normalizada, no
 * por título exacto, porque el mismo empleo está escrito distinto en cada
 * entorno ("Partes equipos y suministros" en el servidor, "Partes Equipos y
 * Suministros" en local). Buscar por nombre exacto crearía un duplicado.
 *
 * No toca el nombre, el correo, la foto ni el CV: eso se gestiona desde el
 * panel de administración.
 */
class ProfileSeeder extends Seeder
{
    public function run(): void
    {
        $this->actualizarPerfil();
        $this->cargarExperiencia();
    }

    private function actualizarPerfil(): void
    {
        $user = User::orderBy('id')->first();

        if (! $user) {
            $this->command->warn('No hay usuario que actualizar.');

            return;
        }

        $user->update([
            'bio' => 'Desarrollador FullStack con más de 5 años de experiencia, enfocado en backend '
                . 'con PHP y Laravel, diseño de APIs RESTful y modelado de bases de datos relacionales '
                . 'en MySQL. En frontend trabajo con Vue.js y con React y TypeScript, construyendo '
                . 'interfaces responsivas y orientadas a la experiencia de usuario. He creado y '
                . 'modernizado módulos empresariales y plataformas web aplicando arquitectura limpia '
                . 'y buenas prácticas, incluyendo flujos clínicos completos con funcionalidades '
                . 'asistidas por IA e integraciones con WhatsApp y generación de documentos PDF.',
            'phone' => '+57 317 250 1811',
            'whatsapp' => '+57 317 250 1811',
            'linkedin_url' => 'https://www.linkedin.com/in/manuel-castro-minotta/',
            'github_url' => 'https://github.com/manuelcastro95',
        ]);

        $this->command->info('✓ perfil actualizado');
    }

    private function cargarExperiencia(): void
    {
        $existentes = Experience::orderBy('id')->get();

        foreach ($this->experiencias() as $data) {
            $ya = $existentes->first(
                fn (Experience $e) => $this->normalizar($e->company) === $this->normalizar($data['company'])
            );

            if ($ya) {
                $ya->update($data);
                $this->command->info("✓ actualizada: {$data['company']}");

                continue;
            }

            $nueva = Experience::create($data);
            $existentes->push($nueva);
            $this->command->info("✓ creada: {$data['company']}");
        }
    }

    private function normalizar(string $valor): string
    {
        return preg_replace('/[^a-z0-9]/', '', mb_strtolower($valor));
    }

    private function experiencias(): array
    {
        return [
            [
                'position' => 'Desarrollador FullStack',
                'company' => 'SICAP SAS',
                'duration' => 'Enero 2026 – Octubre 2026',
                'location' => 'Cali, Colombia',
                'order' => 0,
                'is_active' => true,
                'responsibilities' => [
                    'Analicé, desarrollé y entregué funcionalidades de software de extremo a extremo, gestionando el proceso desde la planificación hasta la implementación.',
                    'Diseñé y estructuré bases de datos en MySQL, definiendo tablas y relaciones, y optimicé consultas con SQL y Eloquent.',
                    'Implementé soluciones completas integrando frontend y backend en aplicaciones web escalables y robustas.',
                    'Desarrollé interfaces centradas en el usuario, priorizando la claridad y la rapidez de los procesos de uso diario.',
                ],
            ],
            [
                'position' => 'Desarrollador FullStack — Plataforma Nutrabiotics',
                'company' => 'JOINUP',
                'duration' => 'Febrero 2026 – Mayo 2026 · proyecto paralelo',
                'location' => null,
                'order' => 1,
                'is_active' => true,
                'responsibilities' => [
                    'Desarrollé el flujo completo de la cita médica: agenda, estados, sala de espera, preconsulta asistida por IA y consulta.',
                    'Construí con React y TypeScript las interfaces de IA del recorrido del paciente: anamnesis guiada, detección de incoherencias, matriz de salud, matching médico y sugerencias durante la consulta.',
                    'Implementé backend y frontend de la verificación por WhatsApp para la confirmación de citas.',
                    'Desarrollé la generación de documentos PDF y el flujo de consentimientos informados.',
                ],
            ],
            [
                'position' => 'Desarrollador de Software',
                'company' => 'Partes, Equipos y Suministros',
                'duration' => 'Agosto 2021 – Diciembre 2025',
                'location' => 'Cali, Colombia',
                'order' => 2,
                'is_active' => true,
                'responsibilities' => [
                    'Desarrollé y mantuve durante más de cuatro años los módulos del sistema de gestión de la empresa, cubriendo backend y frontend.',
                    'Implementé APIs RESTful para la comunicación entre módulos y la integración con servicios externos.',
                    'Modelé y mantuve la base de datos MySQL, definiendo tablas y relaciones y optimizando consultas con SQL y Eloquent.',
                    'Modernicé interfaces de usuario existentes, mejorando la usabilidad de los procesos operativos del día a día.',
                    'Acompañé el ciclo completo de cada funcionalidad: análisis, desarrollo, entrega e implementación.',
                ],
            ],
            [
                'position' => 'Técnico en Programación',
                'company' => 'Fergon Outsourcing SAS',
                'duration' => 'Febrero 2021 – Agosto 2021',
                'location' => 'Cali, Colombia',
                'order' => 3,
                'is_active' => true,
                'responsibilities' => [
                    'Desarrollé y mantuve aplicaciones web abarcando frontend y backend con buenas prácticas de desarrollo.',
                    'Diseñé y gestioné bases de datos en MySQL.',
                    'Actualicé e implementé interfaces de usuario.',
                    'Corregí errores y apliqué mejoras de usabilidad sobre aplicaciones en producción.',
                ],
            ],
        ];
    }
}
