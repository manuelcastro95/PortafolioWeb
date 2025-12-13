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

        // Eventos por día (últimos 7 días)
        $eventsByDay = Analytics::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

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
            ],
            'topEvents' => $topEvents,
            'eventsBySection' => $eventsBySection,
            'eventsByDay' => $eventsByDay,
            'devices' => $devices,
            'browsers' => $browsers,
            'days' => $days,
        ]);
    }
}

