@extends('layouts.app')
@section('title', 'Indicadores clínicos')

@section('content')
    <div class="page-head">
        <div><h1>Indicadores clínicos</h1><p>Panorama de la actividad clínica de la clínica.</p></div>
        <a href="{{ route('reportes.index') }}" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Reportes</a>
    </div>

    <div class="grid g-4 mb">
        <div class="card"><div class="cap" style="font-size:12px;color:var(--ink-soft);text-transform:uppercase">Consultas</div><div style="font-size:26px;font-weight:700;margin-top:6px">{{ $totalConsultas }}</div></div>
        <div class="card"><div class="cap" style="font-size:12px;color:var(--ink-soft);text-transform:uppercase">Pacientes</div><div style="font-size:26px;font-weight:700;margin-top:6px">{{ $totalPacientes }}</div></div>
        <div class="card pink"><div class="cap" style="font-size:12px;color:var(--ink-soft);text-transform:uppercase">Satisfacción</div><div style="font-size:26px;font-weight:700;margin-top:6px">{{ $satisfaccion }} <span style="color:#f59e0b">★</span></div></div>
        <div class="card"><div class="cap" style="font-size:12px;color:var(--ink-soft);text-transform:uppercase">Encuestas</div><div style="font-size:26px;font-weight:700;margin-top:6px">{{ $totalEncuestas }}</div></div>
    </div>

    <div class="grid g-2 mb">
        <div class="card"><h3 class="mb">Consultas por mes</h3><div class="chart-box"><canvas id="chMes"></canvas></div></div>
        <div class="card"><h3 class="mb">Pacientes por especialidad</h3><div class="chart-box"><canvas id="chEsp"></canvas></div></div>
    </div>

    <div class="grid g-2 mb">
        <div class="card"><h3 class="mb">Distribución por edad</h3><div class="chart-box"><canvas id="chEdad"></canvas></div></div>
        <div class="card"><h3 class="mb">Distribución por sexo</h3><div class="chart-box"><canvas id="chSexo"></canvas></div></div>
    </div>

    <div class="card" style="padding:0">
        <div style="padding:18px 22px 8px"><h3 style="margin:0">Diagnósticos más frecuentes</h3></div>
        <div class="table-wrap" style="box-shadow:none;border-radius:0">
            <table>
                <thead><tr><th>Diagnóstico</th><th>Casos</th></tr></thead>
                <tbody>
                @forelse($diagnosticos as $dx => $c)
                    <tr><td>{{ \Illuminate\Support\Str::limit($dx, 80) }}</td><td><b>{{ $c }}</b></td></tr>
                @empty
                    <tr><td colspan="2"><div class="empty"><i class="fa-solid fa-notes-medical"></i><p>Sin diagnósticos registrados.</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script>
    window.addEventListener('load', function(){
        if(!window.Chart) return;
        const P=['#a855f7','#ec4899','#7c3aed','#06b6d4','#f59e0b','#22c55e','#ef4444','#3b82f6'];
        new Chart(document.getElementById('chMes'), { type:'line',
            data:{ labels:@json($labels), datasets:[{ data:@json($serie), borderColor:'#a855f7', backgroundColor:'rgba(168,85,247,.15)', fill:true, tension:.3, pointRadius:4 }] },
            options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{precision:0}}}} });
        new Chart(document.getElementById('chEsp'), { type:'bar',
            data:{ labels:@json($porEspecialidad->keys()), datasets:[{ data:@json($porEspecialidad->values()), backgroundColor:'#ec4899', borderRadius:8 }] },
            options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{precision:0}}}} });
        new Chart(document.getElementById('chEdad'), { type:'bar',
            data:{ labels:@json(array_keys($edades)), datasets:[{ data:@json(array_values($edades)), backgroundColor:'#7c3aed', borderRadius:8 }] },
            options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{precision:0}}}} });
        new Chart(document.getElementById('chSexo'), { type:'doughnut',
            data:{ labels:@json(array_keys($sexo)), datasets:[{ data:@json(array_values($sexo)), backgroundColor:P }] },
            options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom'}}} });
    });
    </script>
    @endpush
@endsection
