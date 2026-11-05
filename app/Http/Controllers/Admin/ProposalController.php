<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use App\Models\ProposalSetting;
use App\Models\ProposalTaskCatalog;
use App\Models\ProposalTextBlock;
use App\Services\CurrencyService;
use App\Services\WorkanaScraperService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProposalController extends Controller
{
    public function index()
    {
        $proposals = Proposal::orderByDesc('created_at')->get();

        return Inertia::render('Admin/Proposals/Index', [
            'proposals' => $proposals,
            'metrics' => $this->metrics(),
            'settings' => ProposalSetting::current(),
            'statuses' => config('proposals.statuses'),
        ]);
    }

    public function create(CurrencyService $currency)
    {
        return Inertia::render('Admin/Proposals/Create', $this->formData($currency));
    }

    public function store(Request $request)
    {
        $data = $this->validateProposal($request);
        $proposal = Proposal::create($data);

        return redirect()->route('admin.proposals.show', $proposal)
            ->with('success', 'Propuesta creada. Ya puedes copiar los campos a Workana.');
    }

    public function edit(Proposal $proposal, CurrencyService $currency)
    {
        return Inertia::render('Admin/Proposals/Edit', array_merge(
            $this->formData($currency),
            ['proposal' => $proposal]
        ));
    }

    public function update(Request $request, Proposal $proposal)
    {
        $data = $this->validateProposal($request);
        $proposal->update($data);

        return redirect()->route('admin.proposals.show', $proposal)
            ->with('success', 'Propuesta actualizada.');
    }

    public function show(Proposal $proposal)
    {
        return Inertia::render('Admin/Proposals/Show', [
            'proposal' => $proposal,
            'statuses' => config('proposals.statuses'),
            'taskStatuses' => config('proposals.task_statuses'),
        ]);
    }

    public function destroy(Proposal $proposal)
    {
        $proposal->delete();

        return redirect()->route('admin.proposals.index')
            ->with('success', 'Propuesta eliminada.');
    }

    public function updateStatus(Request $request, Proposal $proposal)
    {
        $validated = $request->validate([
            'status' => 'required|in:borrador,enviada,en_conversacion,ganada,perdida',
            'sent_at' => 'nullable|date',
        ]);

        $proposal->update($validated);

        return back()->with('success', 'Estado actualizado.');
    }

    public function pdf(Proposal $proposal)
    {
        $pdf = Pdf::loadView('pdf.proposal', [
            'proposal' => $proposal,
            'taskStatuses' => config('proposals.task_statuses'),
        ])->setPaper('letter');

        $slug = \Illuminate\Support\Str::slug($proposal->project_title) ?: 'propuesta';

        return $pdf->download("propuesta-{$slug}.pdf");
    }

    /**
     * Scraping best-effort de un proyecto de Workana (AJAX).
     */
    public function scrape(Request $request, WorkanaScraperService $scraper)
    {
        $request->validate(['url' => 'required|string']);

        return response()->json($scraper->fetch($request->input('url')));
    }

    /**
     * Parsea el contenido que el usuario copió de la página (AJAX).
     */
    public function parsePaste(Request $request, WorkanaScraperService $scraper)
    {
        $request->validate(['content' => 'required|string']);

        return response()->json($scraper->parsePasted($request->input('content')));
    }

    /**
     * Devuelve la tasa USD->COP actual (AJAX). force=1 refresca el caché.
     */
    public function rate(Request $request, CurrencyService $currency)
    {
        return response()->json($currency->usdToCop($request->boolean('force')));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'default_hourly_rate_usd' => 'required|numeric|min:1',
            'default_workana_commission' => 'required|numeric|min:0|max:100',
            'fallback_cop_rate' => 'required|numeric|min:1',
        ]);

        ProposalSetting::current()->update($validated);

        return back()->with('success', 'Configuración actualizada.');
    }

    /**
     * Datos compartidos por los formularios create / edit.
     */
    private function formData(CurrencyService $currency): array
    {
        $settings = ProposalSetting::current();

        return [
            'catalog' => ProposalTaskCatalog::where('is_active', true)->orderBy('order')->get(),
            'textTemplates' => ProposalTextBlock::where('is_active', true)->orderBy('order')->get(),
            'workanaTasks' => config('proposals.workana_tasks'),
            'taskStatuses' => config('proposals.task_statuses'),
            'statuses' => config('proposals.statuses'),
            'settings' => $settings,
            'currentRate' => $currency->usdToCop(),
        ];
    }

    private function validateProposal(Request $request): array
    {
        $validated = $request->validate([
            'workana_url' => 'nullable|string|max:500',
            'project_title' => 'required|string|max:255',
            'project_description' => 'nullable|string',
            'skills' => 'nullable|array',
            'skills.*' => 'string|max:60',
            'published_at' => 'nullable|date',
            'proposals_count' => 'nullable|integer|min:0',
            'client_name' => 'nullable|string|max:255',
            'client_published_projects' => 'nullable|integer|min:0',
            'client_paid_projects' => 'nullable|integer|min:0',
            'client_member_since' => 'nullable|string|max:60',
            'hourly_rate_usd' => 'required|numeric|min:1',
            'workana_commission' => 'required|numeric|min:0|max:100',
            'usd_to_cop_rate' => 'required|numeric|min:1',
            'phases' => 'nullable|array',
            'phases.*.name' => 'required|string|max:255',
            'phases.*.amount_cop' => 'nullable|numeric|min:0',
            'phases.*.items' => 'nullable|array',
            'phases.*.items.*.description' => 'required|string|max:500',
            'phases.*.items.*.hours' => 'required|numeric|min:0',
            'workana_tasks' => 'nullable|array',
            'workana_tasks.*.key' => 'required|string',
            'workana_tasks.*.label' => 'required|string',
            'workana_tasks.*.status' => 'required|in:incluido,no_incluido,costo_adicional',
            'workana_tasks.*.note' => 'nullable|string|max:255',
            'text_blocks' => 'nullable|array',
            'details_text' => 'nullable|string',
            'delivery_time' => 'nullable|string|max:120',
            'status' => 'required|in:borrador,enviada,en_conversacion,ganada,perdida',
            'sent_at' => 'nullable|date',
        ]);

        return $validated;
    }

    /**
     * Métricas de seguimiento.
     */
    private function metrics(): array
    {
        $all = Proposal::all();

        $sent = $all->whereNotIn('status', ['borrador']);
        $won = $all->where('status', 'ganada');
        $decided = $all->whereIn('status', ['ganada', 'perdida']);

        $conversion = $decided->count() > 0
            ? round($won->count() / $decided->count() * 100, 1)
            : 0;

        $avgValue = $sent->count() > 0
            ? round($sent->sum(fn ($p) => $p->total_cop) / $sent->count())
            : 0;

        return [
            'total' => $all->count(),
            'sent' => $sent->count(),
            'won' => $won->count(),
            'lost' => $all->where('status', 'perdida')->count(),
            'in_conversation' => $all->where('status', 'en_conversacion')->count(),
            'draft' => $all->where('status', 'borrador')->count(),
            'conversion_rate' => $conversion,
            'avg_value_cop' => $avgValue,
            'hours_quoted' => round($sent->sum(fn ($p) => $p->total_hours)),
            'hours_won' => round($won->sum(fn ($p) => $p->total_hours)),
        ];
    }
}
