<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Analytics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AnalyticsController extends Controller
{
    /**
     * Guardar un evento de analytics
     */
    public function track(Request $request)
    {
        try {
            // Validación más flexible
            $data = $request->validate([
                'event_type' => 'required|string|in:page_view,click,download,link_click',
                'event_name' => 'required|string',
                'section' => 'nullable|string|max:255',
                'target_url' => 'nullable|string|max:2048',
                'metadata' => 'nullable|array',
            ]);

            // Obtener información del usuario
            $ipAddress = $request->ip();
            $userAgent = $request->userAgent() ?? 'Unknown';
            $referrer = $request->header('referer');

            // Detectar dispositivo y navegador
            $deviceInfo = $this->detectDevice($userAgent);

            // Validar y limpiar target_url
            $targetUrl = $data['target_url'] ?? null;
            if ($targetUrl && !filter_var($targetUrl, FILTER_VALIDATE_URL) && !str_starts_with($targetUrl, '/')) {
                // Si no es una URL válida ni una ruta relativa, guardarla como está (puede ser mailto:, tel:, etc.)
                $targetUrl = $targetUrl;
            }

            // Crear el registro de analytics
            Analytics::create([
                'event_type' => $data['event_type'],
                'event_name' => $data['event_name'],
                'section' => $data['section'] ?? null,
                'target_url' => $targetUrl,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'referrer' => $referrer,
                'device_type' => $deviceInfo['device'],
                'browser' => $deviceInfo['browser'],
                'os' => $deviceInfo['os'],
                'metadata' => $data['metadata'] ?? null,
            ]);

            return response()->json(['success' => true], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation error tracking analytics: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error tracking analytics: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error tracking event: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Detectar información del dispositivo
     */
    private function detectDevice($userAgent)
    {
        $device = 'desktop';
        $browser = 'unknown';
        $os = 'unknown';

        // Detectar dispositivo
        if (preg_match('/mobile|android|iphone|ipad/i', $userAgent)) {
            if (preg_match('/tablet|ipad/i', $userAgent)) {
                $device = 'tablet';
            } else {
                $device = 'mobile';
            }
        }

        // Detectar navegador
        if (preg_match('/chrome/i', $userAgent) && !preg_match('/edg/i', $userAgent)) {
            $browser = 'chrome';
        } elseif (preg_match('/firefox/i', $userAgent)) {
            $browser = 'firefox';
        } elseif (preg_match('/safari/i', $userAgent) && !preg_match('/chrome/i', $userAgent)) {
            $browser = 'safari';
        } elseif (preg_match('/edg/i', $userAgent)) {
            $browser = 'edge';
        }

        // Detectar OS
        if (preg_match('/windows/i', $userAgent)) {
            $os = 'windows';
        } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
            $os = 'macos';
        } elseif (preg_match('/linux/i', $userAgent)) {
            $os = 'linux';
        } elseif (preg_match('/android/i', $userAgent)) {
            $os = 'android';
        } elseif (preg_match('/iphone|ipad|ipod/i', $userAgent)) {
            $os = 'ios';
        }

        return [
            'device' => $device,
            'browser' => $browser,
            'os' => $os,
        ];
    }
}

