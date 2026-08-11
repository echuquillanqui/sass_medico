@extends('layouts.app')
@section('title', 'Reportes')

@section('content')
    @php $mon = $empresa->moneda ?? 'S/'; @endphp
    <div class="page-head">
        <div><h1>Reportes</h1><p>Del {{ $desde->format('d/m/Y') }} al {{ $hasta->format('d/m/Y') }}</p></div>
        <div class="flex gap">
            <a href="{{ route('reportes.pdf', request()->only('desde','hasta')) }}" target="_blank" class="btn btn-ghost"><i class="fa-solid fa-file-pdf"></i> PDF</a>
            <a href="{{ route('reportes.clinico') }}" class="btn btn-light"><i class="fa-solid fa-heart-pulse"></i> Clínico</a>
            <a href="{{ route('reportes.financiero') }}" class="btn btn-light"><i class="fa-solid fa-coins"></i> Financiero</a>
            <a href="{{ route('reportes.excel', request()->only('desde','hasta')) }}" class="btn btn-primary"><i class="fa-solid fa-file-excel"></i> Excel</a>
        </div>
    </div>

    <form method="GET" class="card mb" style="padding:14px">
        <div class="flex gap" style="flex-wrap:wrap;align-items:flex-end">
            <div class="field"><label>Desde</label><input type="date" name="desde" value="{{ $desde->toDateString() }}"></div>
            <div class="field"><label>Hasta</label><input type="date" name="hasta" value="{{ $hasta->toDateString() }}"></div>
            <button class="btn btn-primary">Aplicar</button>
        </div>
    </form>

    <div class="grid g-4 mb">
        <div class="card"><div class="cap" style="color:var(--ink-soft);text-transform:uppercase;font-size:12px">Citas</div><div style="font-size:28px;font-weight:700;margin-top:6px">{{ $totalCitas }}</div></div>
        <div class="card"><div class="cap" style="color:var(--ink-soft);text-transform:uppercase;font-size:12px">Pacientes</div><div style="font-size:28px;font-weight:700;margin-top:6px">{{ $totalPacientes }}</div></div>
        <div class="card"><div class="cap" style="color:var(--ink-soft);text-transform:uppercase;font-size:12px">Nuevos pacientes</div><div style="font-size:28px;font-weight:700;margin-top:6px">{{ $nuevosPacientes }}</div></div>
        <div class="card pink"><div class="cap" style="color:var(--ink-soft);text-transform:uppercase;font-size:12px">Ingresos</div><div style="font-size:28px;font-weight:700;margin-top:6px;background:var(--grad);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent">@money($ingresos, null, 2)</div></div>
    </div>

    <div class="grid g-2">
        <div class="card"><h3 class="mb">Citas por estado</h3><div class="chart-box"><canvas id="chEstado"></canvas></div></div>
        <div class="card"><h3 class="mb">Citas por especialidad</h3><div class="chart-box"><canvas id="chEsp"></canvas></div></div>
    </div>

    @push('scripts')
    <script>
    window.addEventListener('load', function(){
        if(!window.Chart) return;
        new Chart(document.getElementById('chEstado'), {
            type:'doughnut',
            data:{ labels:@json($porEstado->keys()), datasets:[{ data:@json($porEstado->values()),
                backgroundColor:['#f59e0b','#3b82f6','#22c55e','#ef4444','#94a3b8','#a855f7'] }] },
            options:{ responsive:true, maintainAspectRatio:false, plugins:{legend:{position:'bottom'}} }
        });
        new Chart(document.getElementById('chEsp'), {
            type:'bar',
            data:{ labels:@json($porEspecialidad->keys()), datasets:[{ data:@json($porEspecialidad->values()),
                backgroundColor:'#ec4899', borderRadius:10 }] },
            options:{ responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}},
                scales:{y:{beginAtZero:true,ticks:{precision:0}}} }
        });
    });
    </script>
    @endpush
@endsection
