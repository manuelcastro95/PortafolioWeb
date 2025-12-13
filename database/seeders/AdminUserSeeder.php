<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si ya existe un usuario admin
        $adminExists = User::where('email', 'admin@portafolio.com')->exists();

        if (!$adminExists) {
            User::create([
                'name' => 'Administrador',
                'email' => 'admin@portafolio.com',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
                'bio' => 'Desarrollador FullStack con más de 3 años de experiencia especializado en el diseño e implementación de soluciones web empresariales.',
                'phone' => '+57 317 250 1811',
                'linkedin_url' => 'https://www.linkedin.com/in/manuel-castro-minotta/',
                'github_url' => 'https://github.com/manuelcastro95',
                'whatsapp' => '+57 317 250 1811',
            ]);

            $this->command->info('Usuario administrador creado exitosamente!');
            $this->command->info('Email: admin@portafolio.com');
            $this->command->info('Password: admin123');
            $this->command->warn('IMPORTANTE: Cambia la contraseña después del primer inicio de sesión!');
        } else {
            $this->command->warn('El usuario administrador ya existe.');
        }
    }
}

