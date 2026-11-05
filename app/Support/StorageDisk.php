<?php

namespace App\Support;

/**
 * Único punto donde se decide en qué disco vive un archivo subido.
 *
 * Antes cada controlador y cada modelo resolvía el disco por su cuenta con
 * env(). Eso rompe en producción: cuando la configuración está cacheada,
 * env() devuelve null fuera de los archivos de config, así que cada sitio
 * caía en un valor por defecto distinto. El panel guardaba el CV en el disco
 * local mientras el modelo firmaba una URL de S3, y el enlace respondía
 * NoSuchKey.
 *
 * config() sí lee de la caché, de modo que da el mismo resultado con y sin
 * config:cache.
 */
class StorageDisk
{
    /**
     * Nombre del disco a usar: s3 cuando hay credenciales completas,
     * si no el disco por defecto de la aplicación.
     */
    public static function name(): string
    {
        $s3 = config('filesystems.disks.s3', []);

        $tieneCredenciales = ! empty($s3['key'])
            && ! empty($s3['secret'])
            && ! empty($s3['bucket']);

        return $tieneCredenciales ? 's3' : config('filesystems.default');
    }

    /**
     * Si el disco resuelto es S3. Útil para los sitios que solo entonces
     * necesitan fijar la visibilidad pública del objeto.
     */
    public static function isS3(): bool
    {
        return self::name() === 's3';
    }
}
