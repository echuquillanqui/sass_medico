@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    @php $mon = $empresa->moneda ?? 'S/'; @endphp
    <div class="page-head">
        <div>
            <h1>Hola, {{ explode(' ', auth()->user()->name)[0] }} 👋</h1>
            <p>Resumen de {{ $empresa->nombre ?? 'tu clínica' }} · {{ now()->locale('es')->isoFormat('dddd, D [de] MMMM YYYY') }}</p>
        </div>
        <a href="{{ route('citas.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Nueva cita</a>
    </div>

    <div class="grid g-4 mb">
        <div class="kpi k1">
            <div class="kpi-top"><span class="kpi-cap">Citas hoy</span><span class="kpi-ic"><i class="fa-solid fa-calendar-day"></i></span></div>
            <div><div class="kpi-val">{{ $citasHoy }}</div><div class="kpi-foot"><i class="fa-solid fa-calendar-week"></i> {{ $citasMes }} este mes</div></div>
        </div>
        <div class="kpi k2">
            <div class="kpi-top"><span class="kpi-cap">Pacientes</span><span class="kpi-ic"><i class="fa-solid fa-user-group"></i></span></div>
            <div><div class="kpi-val">{{ $totalPacientes }}</div><div class="kpi-foot"><i class="fa-solid fa-users"></i> Registrados</div></div>
        </div>
        <div class="kpi k3">
            <div class="kpi-top"><span class="kpi-cap">Ingresos (mes)</span><span class="kpi-ic"><i class="fa-solid fa-sack-dollar"></i></span></div>
            <div><div class="kpi-val" style="font-size:24px">@money($ingresosMes, null, 0)</div><div class="kpi-foot"><i class="fa-solid fa-arrow-trend-up"></i> Cobrado este mes</div></div>
        </div>
        <div class="kpi k4">
            <div class="kpi-top"><span class="kpi-cap">Atención</span><span class="kpi-ic"><i class="fa-solid fa-heart-pulse"></i></span></div>
            <div><div class="kpi-val">{{ $porcentajeAtencion }}%</div><div class="kpi-foot"><i class="fa-solid fa-check"></i> Citas atendidas</div></div>
        </div>
    </div>

    <div class="grid g-2e mb">
        <div class="chart-card">
            <div class="chart-head"><h3><i class="fa-solid fa-chart-line" style="color:var(--violet)"></i> Citas por mes</h3><span class="pill violet">últimos 6 meses</span></div>
            <div class="chart-hold"><canvas id="chCitas"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="chart-head"><h3><i class="fa-solid fa-sack-dollar" style="color:var(--info)"></i> Ingresos por mes</h3><span class="pill blue">{{ $mon }}</span></div>
            <div class="chart-hold"><canvas id="chIngresos"></canvas></div>
        </div>
    </div>

    <div class="grid g-2e mb">
        <div class="chart-card">
            <div class="chart-head"><h3><i class="fa-solid fa-chart-pie" style="color:var(--pink)"></i> Citas por estado</h3></div>
            <div class="chart-hold"><canvas id="chEstado"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="chart-head"><h3><i class="fa-solid fa-layer-group" style="color:var(--violet-2)"></i> Pacientes por especialidad</h3></div>
            <div class="chart-hold"><canvas id="chEsp"></canvas></div>
        </div>
    </div>

    @if($especialidades->isNotEmpty())
        <div class="card mb">
            <h3 class="mb">Módulos activos</h3>
            <div class="grid g-4">
                @foreach($especialidades as $e)
                    <a href="{{ route('modulo.show', $e->slug) }}" class="metric" style="text-decoration:none;display:flex;flex-direction:column;align-items:center;gap:6px">
                        <div style="width:44px;height:44px;border-radius:13px;background:{{ $e->color }}1a;color:{{ $e->color }};display:grid;place-items:center;font-size:19px"><i class="fa-solid {{ $e->icono }}"></i></div>
                        <div class="cap">{{ $e->nombre }}</div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <div class="card" style="padding:0">
        <div class="flex between" style="padding:20px 22px 12px">
            <h3 style="margin:0">Próximas citas</h3>
            <a href="{{ route('citas.index') }}" class="btn btn-light btn-sm">Ver todas</a>
        </div>
        <div style="overflow-x:auto">
            <div class="table-wrap" style="box-shadow:none;border-radius:0;min-width:640px">
                <table>
                    <thead><tr><th>Paciente</th><th>Especialidad</th><th>Médico</th><th>Fecha</th><th>Hora</th><th>Estado</th></tr></thead>
                    <tbody>
                    @forelse($proximasCitas as $cita)
                        <tr>
                            <td><span class="avatar-sm">{{ mb_substr($cita->paciente->nombres,0,1) }}{{ mb_substr($cita->paciente->apellidos,0,1) }}</span>{{ $cita->paciente->nombre_completo }}</td>
                            <td>{{ $cita->especialidad->nombre ?? '—' }}</td>
                            <td>{{ $cita->medico->name ?? '—' }}</td>
                            <td>{{ $cita->fecha->locale('es')->isoFormat('D MMM') }}</td>
                            <td>{{ \Illuminate\Support\Str::of($cita->hora)->substr(0,5) }}</td>
                            <td>@include('citas.estado', ['estado' => $cita->estado])</td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="empty"><i class="fa-regular fa-calendar"></i><p>No hay citas próximas.</p></div></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    window.addEventListener('load', function () {
        if (!window.Chart) return;
        const soft = (getComputedStyle(document.documentElement).getPropertyValue('--ink-soft') || '#9ca3af').trim();
        Chart.defaults.font.family = 'Poppins, sans-serif';
        Chart.defaults.color = soft;
        const grid = 'rgba(148,148,180,.15)';

        const c1 = document.getElementById('chCitas').getContext('2d');
        const g1 = c1.createLinearGradient(0,0,0,260); g1.addColorStop(0,'rgba(168,85,247,.35)'); g1.addColorStop(1,'rgba(168,85,247,0)');
        new Chart(c1, { type:'line',
            data:{ labels:@json($mesLabels), datasets:[{ label:'Citas', data:@json($citasMesSerie), borderColor:'#a855f7', backgroundColor:g1, fill:true, tension:.4, pointRadius:4, pointBackgroundColor:'#a855f7', borderWidth:3 }] },
            options:{ responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}},
                scales:{ y:{beginAtZero:true, ticks:{precision:0}, grid:{color:grid}}, x:{grid:{display:false}} } } });

        const c2 = document.getElementById('chIngresos').getContext('2d');
        const g2 = c2.createLinearGradient(0,0,0,260); g2.addColorStop(0,'#22d3ee'); g2.addColorStop(1,'#0891b2');
        new Chart(c2, { type:'bar',
            data:{ labels:@json($mesLabels), datasets:[{ data:@json($ingresosMesSerie), backgroundColor:g2, borderRadius:10, maxBarThickness:34 }] },
            options:{ responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}},
                scales:{ y:{beginAtZero:true, grid:{color:grid}}, x:{grid:{display:false}} } } });

        new Chart(document.getElementById('chEstado'), { type:'doughnut',
            data:{ labels:@json(array_keys($porEstado)), datasets:[{ data:@json(array_values($porEstado)),
                backgroundColor:['#f59e0b','#3b82f6','#22c55e','#ef4444','#94a3b8'], borderWidth:0, hoverOffset:8 }] },
            options:{ responsive:true, maintainAspectRatio:false, cutout:'62%', plugins:{legend:{position:'bottom', labels:{boxWidth:12, padding:14, font:{size:11}}}} } });

        new Chart(document.getElementById('chEsp'), { type:'bar',
            data:{ labels:@json($porEspecialidad->keys()), datasets:[{ data:@json($porEspecialidad->values()),
                backgroundColor:['#a855f7','#ec4899','#06b6d4','#f59e0b','#22c55e','#7c3aed'], borderRadius:8, maxBarThickness:26 }] },
            options:{ indexAxis:'y', responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}},
                scales:{ x:{beginAtZero:true, ticks:{precision:0}, grid:{color:grid}}, y:{grid:{display:false}} } } });
    });
    </script>
    @endpush
@endsection
