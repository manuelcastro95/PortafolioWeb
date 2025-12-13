<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        
        // Agregar las URLs completas a los datos del usuario
        $userData = $user->toArray();
        $userData['profile_image_url'] = $user->profile_image_url;
        $userData['cv_file_url'] = $user->cv_file_url;
        
        return Inertia::render('Admin/Profile/Edit', [
            'user' => $userData,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // Debug: ver qué está llegando - más detallado
        Log::info('Datos recibidos en update:', [
            'all' => $request->all(),
            'input_name' => $request->input('name'),
            'input_email' => $request->input('email'),
            'request_method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'has_profile_image' => $request->hasFile('profile_image'),
            'has_cv_file' => $request->hasFile('cv_file'),
            'files' => $request->allFiles(),
            'json' => $request->json()->all() ?? 'no json',
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'bio' => 'nullable|string',
            'phone' => 'nullable|string|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'github_url' => 'nullable|url|max:255',
            'whatsapp' => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|max:2048',
            'cv_file' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        // Preparar datos para actualizar (sin incluir archivos aún)
        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'bio' => $validated['bio'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'linkedin_url' => $validated['linkedin_url'] ?? null,
            'github_url' => $validated['github_url'] ?? null,
            'whatsapp' => $validated['whatsapp'] ?? null,
        ];

        // Obtener el disco configurado (s3 si está configurado, sino local)
        $diskName = env('FILESYSTEM_DISK', 'local');
        
        // Si S3 está configurado, usarlo; sino usar local
        if (env('AWS_ACCESS_KEY_ID') && env('AWS_SECRET_ACCESS_KEY') && env('AWS_BUCKET')) {
            $diskName = 's3';
        }
        
        $disk = Storage::disk($diskName);

        // Manejar imagen de perfil
        if ($request->hasFile('profile_image')) {
            try {
                // Eliminar imagen anterior si existe
                if ($user->profile_image) {
                    try {
                        if ($disk->exists($user->profile_image)) {
                            $disk->delete($user->profile_image);
                        }
                    } catch (\Exception $e) {
                        // Continuar aunque falle la eliminación
                    }
                }
                
                // Subir nueva imagen
                $path = $request->file('profile_image')->store('profile', $diskName);
                
                // Solo establecer visibilidad si es S3
                if ($diskName === 's3') {
                    $disk->setVisibility($path, 'public');
                }
                
                $updateData['profile_image'] = $path;
            } catch (\Exception $e) {
                Log::error('Error al subir imagen de perfil: ' . $e->getMessage());
                return redirect()->route('admin.profile.edit')
                    ->with('error', 'Error al subir la imagen: ' . $e->getMessage());
            }
        }

        // Manejar archivo CV
        if ($request->hasFile('cv_file')) {
            try {
                // Eliminar CV anterior si existe
                if ($user->cv_file) {
                    try {
                        if ($disk->exists($user->cv_file)) {
                            $disk->delete($user->cv_file);
                        }
                    } catch (\Exception $e) {
                        // Continuar aunque falle la eliminación
                    }
                }
                
                // Subir nuevo CV
                $path = $request->file('cv_file')->store('cv', $diskName);
                
                // Solo establecer visibilidad si es S3
                if ($diskName === 's3') {
                    $disk->setVisibility($path, 'public');
                }
                
                $updateData['cv_file'] = $path;
            } catch (\Exception $e) {
                Log::error('Error al subir CV: ' . $e->getMessage());
                return redirect()->route('admin.profile.edit')
                    ->with('error', 'Error al subir el CV: ' . $e->getMessage());
            }
        }

        // Actualizar usuario
        try {
            $user->update($updateData);
            
            Log::info('Perfil actualizado exitosamente para usuario: ' . $user->id);
            
            return redirect()->route('admin.profile.edit')
                ->with('success', 'Perfil actualizado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al actualizar perfil: ' . $e->getMessage());
            
            return redirect()->route('admin.profile.edit')
                ->with('error', 'Error al actualizar el perfil: ' . $e->getMessage());
        }
    }
}

