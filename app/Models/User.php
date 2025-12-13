<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'bio',
        'profile_image',
        'cv_file',
        'phone',
        'linkedin_url',
        'github_url',
        'whatsapp',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the URL for the profile image.
     */
    public function getProfileImageUrlAttribute()
    {
        if (!$this->profile_image) {
            return null;
        }

        // Si ya es una URL completa, retornarla
        if (str_starts_with($this->profile_image, 'http')) {
            return $this->profile_image;
        }

        // Determinar qué disco usar
        $diskName = config('filesystems.default');
        if (env('AWS_ACCESS_KEY_ID') && env('AWS_SECRET_ACCESS_KEY') && env('AWS_BUCKET')) {
            $diskName = 's3';
        }

        // Obtener la URL del archivo (permanente si es público en S3)
        return Storage::disk($diskName)->temporaryUrl($this->profile_image, now()->addMinutes(45));
    }

    /**
     * Get the URL for the CV file.
     */
    public function getCvFileUrlAttribute()
    {
        if (!$this->cv_file) {
            return null;
        }

        // Si ya es una URL completa, retornarla
        if (str_starts_with($this->cv_file, 'http')) {
            return $this->cv_file;
        }

        // Determinar qué disco usar
        $diskName = config('filesystems.default');
        if (env('AWS_ACCESS_KEY_ID') && env('AWS_SECRET_ACCESS_KEY') && env('AWS_BUCKET')) {
            $diskName = 's3';
        }

        // Obtener la URL del archivo (permanente si es público en S3)
        return Storage::disk($diskName)->temporaryUrl($this->cv_file, now()->addMinutes(45));
    }
}
