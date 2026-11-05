<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 32px 40px; }
        * { font-family: DejaVu Sans, sans-serif; }
        body { color: #2d2d2d; font-size: 12px; line-height: 1.5; }
        h1 { font-size: 20px; margin: 0 0 4px; color: #1a1a1a; }
        h2 { font-size: 14px; margin: 22px 0 8px; color: #6d28d9; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; }
        .muted { color: #6b7280; }
        .header { border-bottom: 2px solid #6d28d9; padding-bottom: 12px; margin-bottom: 8px; }
        .meta { font-size: 11px; color: #6b7280; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th, td { text-align: left; padding: 6px 8px; font-size: 11px; }
        thead th { background: #f3f4f6; color: #374151; }
        tbody tr { border-bottom: 1px solid #eee; }
        .right { text-align: right; }
        .total-row td { font-weight: bold; background: #faf5ff; }
        .phase-name { font-weight: bold; }
        .items { color: #6b7280; font-size: 10px; margin: 2px 0 0; padding-left: 14px; }
        .summary { margin-top: 8px; }
        .summary td { padding: 4px 8px; }
        .badge { font-size: 10px; padding: 1px 6px; border-radius: 8px; }
        .b-inc { background: #dcfce7; color: #166534; }
        .b-add { background: #fef3c7; color: #92400e; }
        .b-no { background: #f3f4f6; color: #6b7280; }
        .contact { margin-top: 24px; font-size: 11px; color: #4b5563; border-top: 1px solid #e5e7eb; padding-top: 10px; }
        .pre { white-space: pre-wrap; font-size: 11px; }
    </style>
</head>
<body>
    @php
        $cop = fn ($n) => '$' . number_format((float) $n, 0, ',', '.') . ' COP';
    @endphp

    <div class="header">
        <h1>{{ $proposal->project_title }}</h1>
        <div class="meta">
            Propuesta de Juan Manuel Castro · Desarrollador Full Stack ·
            {{ now()->format('d/m/Y') }}
            @if($proposal->client_name) · Cliente: {{ $proposal->client_name }} @endif
        </div>
    </div>

    @if($proposal->project_description)
        <h2>Resumen del proyecto</h2>
        <p>{{ $proposal->project_description }}</p>
    @endif

    <h2>Fases y presupuesto</h2>
    <table>
        <thead>
            <tr>
                <th>Fase</th>
                <th class="right">Horas</th>
                <th class="right">Valor</th>
            </tr>
        </thead>
        <tbody>
            @foreach($proposal->phase_breakdown as $phase)
                <tr>
                    <td>
                        <span class="phase-name">{{ $phase['name'] }}</span>
                        @if(!empty($phase['items']))
                            <ul class="items">
                                @foreach($phase['items'] as $item)
                                    <li>{{ $item['description'] }} ({{ $item['hours'] }} h)</li>
                                @endforeach
                            </ul>
                        @endif
                    </td>
                    <td class="right">{{ $phase['hours'] }} h</td>
                    <td class="right">{{ $cop($phase['cop']) }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td>Total</td>
                <td class="right">{{ $proposal->total_hours }} h</td>
                <td class="right">{{ $cop($proposal->total_cop) }}</td>
            </tr>
        </tbody>
    </table>

    <table class="summary">
        <tr>
            <td class="muted">Precio por hora efectivo</td>
            <td class="right">US$ {{ number_format((float) $proposal->effective_hourly_usd, 2) }} ({{ $cop($proposal->effective_hourly_cop) }}/h)</td>
        </tr>
        <tr>
            <td class="muted">Comisión Workana ({{ $proposal->workana_commission }}%)</td>
            <td class="right">- {{ $cop($proposal->commission_cop) }}</td>
        </tr>
        <tr>
            <td class="muted">Neto estimado</td>
            <td class="right">{{ $cop($proposal->net_cop) }}</td>
        </tr>
        @if($proposal->delivery_time)
            <tr>
                <td class="muted">Tiempo de entrega</td>
                <td class="right">{{ $proposal->delivery_time }}</td>
            </tr>
        @endif
    </table>

    @php
        $relevantTasks = collect($proposal->workana_tasks ?? [])->filter(fn ($t) => ($t['status'] ?? 'no_incluido') !== 'no_incluido');
    @endphp
    @if($relevantTasks->isNotEmpty())
        <h2>Qué incluye</h2>
        <table>
            <tbody>
                @foreach($relevantTasks as $task)
                    <tr>
                        <td style="width: 32%">{{ $task['label'] }}</td>
                        <td style="width: 18%">
                            <span class="badge {{ $task['status'] === 'incluido' ? 'b-inc' : 'b-add' }}">
                                {{ $taskStatuses[$task['status']] ?? $task['status'] }}
                            </span>
                        </td>
                        <td>{{ $task['note'] ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($proposal->details_text)
        <h2>Detalles de la propuesta</h2>
        <div class="pre">{{ $proposal->details_text }}</div>
    @endif

    <div class="contact">
        <strong>Juan Manuel Castro</strong> — Desarrollador Full Stack (Laravel · Vue · React)<br>
        Portafolio: https://manuel-castro.site/ ·
        LinkedIn: linkedin.com/in/manuel-castro-minotta ·
        GitHub: github.com/manuelcastro95
    </div>
</body>
</html>
