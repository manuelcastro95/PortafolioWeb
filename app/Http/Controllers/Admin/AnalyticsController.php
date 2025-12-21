<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Analytics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AnalyticsController extends Controller
{
    /**
     * Mostrar dashboard de analytics
     */
    public function index(Request $request)
    {
        $days = $request->get('days', 30); // Por defecto últimos 30 días
        $startDate = now()->subDays($days);
        $endDate = now();

        // Estadísticas generales
        $totalPageViews = Analytics::where('event_type', 'page_view')
            ->dateRange($startDate, $endDate)
            ->count();

        $totalClicks = Analytics::where('event_type', 'click')
            ->dateRange($startDate, $endDate)
            ->count();

        $totalDownloads = Analytics::where('event_type', 'download')
            ->dateRange($startDate, $endDate)
            ->count();

        $totalLinkClicks = Analytics::where('event_type', 'link_click')
            ->dateRange($startDate, $endDate)
            ->count();

        // Eventos más populares
        $topEvents = Analytics::select('event_name', DB::raw('count(*) as count'))
            ->dateRange($startDate, $endDate)
            ->groupBy('event_name')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Eventos por sección
        $eventsBySection = Analytics::select('section', DB::raw('count(*) as count'))
            ->whereNotNull('section')
            ->dateRange($startDate, $endDate)
            ->groupBy('section')
            ->orderBy('count', 'desc')
            ->get();

        // Eventos por día - ajustar según el período seleccionado
        $chartDays = min($days, 30); // Máximo 30 días para el gráfico
        $eventsByDay = Analytics::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', now()->subDays($chartDays))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Completar días faltantes con 0
        $eventsByDayMap = $eventsByDay->pluck('count', 'date')->toArray();
        $completeEventsByDay = [];
        for ($i = $chartDays - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $completeEventsByDay[] = [
                'date' => $date,
                'count' => $eventsByDayMap[$date] ?? 0,
            ];
        }

        // Dispositivos
        $devices = Analytics::select('device_type', DB::raw('count(*) as count'))
            ->whereNotNull('device_type')
            ->dateRange($startDate, $endDate)
            ->groupBy('device_type')
            ->orderBy('count', 'desc')
            ->get();

        // Navegadores
        $browsers = Analytics::select('browser', DB::raw('count(*) as count'))
            ->whereNotNull('browser')
            ->dateRange($startDate, $endDate)
            ->groupBy('browser')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        // Sistemas operativos
        $operatingSystems = Analytics::select('os', DB::raw('count(*) as count'))
            ->whereNotNull('os')
            ->dateRange($startDate, $endDate)
            ->groupBy('os')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        // Fuentes de tráfico (Referrers)
        $referrers = Analytics::select('referrer', DB::raw('count(*) as count'))
            ->whereNotNull('referrer')
            ->where('referrer', '!=', '')
            ->dateRange($startDate, $endDate)
            ->groupBy('referrer')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                // Limpiar y formatear el referrer
                $referrer = $item->referrer;
                if (filter_var($referrer, FILTER_VALIDATE_URL)) {
                    $parsed = parse_url($referrer);
                    $domain = $parsed['host'] ?? $referrer;
                    // Remover www.
                    $domain = preg_replace('/^www\./', '', $domain);
                    return [
                        'referrer' => $domain,
                        'full_url' => $referrer,
                        'count' => $item->count,
                    ];
                }
                return [
                    'referrer' => $referrer,
                    'full_url' => $referrer,
                    'count' => $item->count,
                ];
            });

        // Tráfico directo (sin referrer)
        $directTraffic = Analytics::where(function($query) {
                $query->whereNull('referrer')
                    ->orWhere('referrer', '');
            })
            ->dateRange($startDate, $endDate)
            ->count();

        // Eventos de CV
        $cvViews = Analytics::where('event_name', 'cv_view')
            ->dateRange($startDate, $endDate)
            ->count();

        $cvDownloads = Analytics::where('event_name', 'cv_download')
            ->dateRange($startDate, $endDate)
            ->count();

        // Clics en proyectos
        $projectClicks = Analytics::where('section', 'projects')
            ->dateRange($startDate, $endDate)
            ->count();

        // Clics en contacto
        $contactClicks = Analytics::where('section', 'contact')
            ->dateRange($startDate, $endDate)
            ->count();

        // Clics en menú
        $menuClicks = Analytics::where('section', 'navbar')
            ->dateRange($startDate, $endDate)
            ->count();

        return Inertia::render('Admin/Analytics/Index', [
            'stats' => [
                'total_page_views' => $totalPageViews,
                'total_clicks' => $totalClicks,
                'total_downloads' => $totalDownloads,
                'total_link_clicks' => $totalLinkClicks,
                'cv_views' => $cvViews,
                'cv_downloads' => $cvDownloads,
                'project_clicks' => $projectClicks,
                'contact_clicks' => $contactClicks,
                'menu_clicks' => $menuClicks,
                'direct_traffic' => $directTraffic,
            ],
            'topEvents' => $topEvents,
            'eventsBySection' => $eventsBySection,
            'eventsByDay' => $completeEventsByDay,
            'devices' => $devices,
            'browsers' => $browsers,
            'operatingSystems' => $operatingSystems,
            'referrers' => $referrers,
            'days' => $days,
        ]);
    }
}

