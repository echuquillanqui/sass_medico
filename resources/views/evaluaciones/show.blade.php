@extends('layouts.app')
@section('title', $cfg['titulo'].' · '.$paciente->nombre_completo)

@section('content')
    @php
        $labels = collect($cfg['campos'])->pluck('label', 'name');
        $kpis = $cfg['kpis'] ?? [];
        $graf = $cfg['grafico'] ?? null;
    @endphp
    <div class="page-head">
        <div>
            <h1><i class="fa-solid {{ $cfg['icono'] }}" style="color:{{ $cfg['color'] }}"></i> {{ $cfg['titulo'] }}</h1>
            <p>{{ $paciente->nombre_completo }} · {{ $paciente->edad !== null ? $paciente->edad.' años' : 'Edad N/D' }} · Doc. {{ $paciente->documento ?? '—' }}</p>
        </div>
        <div class="flex gap">
            <a href="{{ route('pacientes.show',$paciente) }}" class="btn btn-ghost"><i class="fa-solid fa-user"></i> Ficha</a>
            <a href="{{ route('evaluacion.index', $slug) }}" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Volver</a>
        </div>
    </div>

    @if(session('ok'))<div class="alert ok mb">{{ session('ok') }}</div>@endif

    @if($ultima)
    <div class="grid g-4 mb">
        @foreach($kpis as $k)
            <div class="kpi k{{ $loop->iteration }}">
                <div class="kpi-top"><span class="kpi-cap">{{ $labels[$k] ?? ucfirst($k) }}</span><span class="kpi-ic"><i class="fa-solid {{ $cfg['icono'] }}"></i></span></div>
                <div><div class="kpi-val" style="font-size:{{ mb_strlen((string)$ultima->dato($k,'—')) > 6 ? '17px' : '26px' }}">{{ $ultima->dato($k, '—') }}</div><div class="kpi-foot">{{ $ultima->fecha->locale('es')->isoFormat('D MMM YYYY') }}</div></div>
            </div>
        @endforeach
    </div>
    @endif

    <div class="grid g-2e">
        {{-- Formulario dinámico --}}
        <div class="card mb">
            <h3 class="mb"><i class="fa-solid fa-plus" style="color:var(--info)"></i> Nueva evaluación</h3>
            <form method="POST" action="{{ route('evaluacion.store', [$slug, $paciente]) }}">
                @csrf
                <div class="form-grid">
                    <div class="field"><label>Fecha *</label><input type="date" name="fecha" value="{{ now()->toDateString() }}" required></div>
                    @foreach($cfg['campos'] as $c)
                        @php $full = ($c['col'] ?? 1) == 2 || in_array($c['tipo'], ['textarea']); @endphp
                        <div class="field" @if($full) style="grid-column:1/-1" @endif>
                            <label>{{ $c['label'] }}</label>
                            @switch($c['tipo'])
                                @case('textarea')
                                    <textarea name="datos[{{ $c['name'] }}]"></textarea>
                                    @break
                                @case('select')
                                    <select name="datos[{{ $c['name'] }}]">
                                        <option value="">—</option>
                                        @foreach($c['opciones'] as $op)<option value="{{ $op }}">{{ $op }}</option>@endforeach
                                    </select>
                                    @break
                                @case('number')
                                    <input type="number" step="{{ $c['step'] ?? 'any' }}" name="datos[{{ $c['name'] }}]">
                                    @break
                                @default
                                    <input name="datos[{{ $c['name'] }}]">
                            @endswitch
                        </div>
                    @endforeach
                </div>
                <div class="field mb" style="margin-top:10px"><label>Notas</label><textarea name="notas"></textarea></div>
                <button class="btn btn-primary"><i class="fa-solid fa-check"></i> Guardar evaluación</button>
            </form>
        </div>

        {{-- Tendencia --}}
        @if($graf)
        <div class="card mb">
            <h3 class="mb"><i class="fa-solid fa-chart-line" style="color:var(--violet)"></i> Tendencia: {{ $graf['label'] }}</h3>
            <div class="chart-hold" style="height:240px"><canvas id="chEval"></canvas></div>
        </div>
        @endif
    </div>

    {{-- Historial --}}
    <div class="card">
        <h3 class="mb">Historial de evaluaciones</h3>
        @forelse($evaluaciones as $e)
            <div style="border-left:3px solid {{ $cfg['color'] }};padding:8px 0 10px 14px;margin-bottom:12px">
                <div class="flex between" style="flex-wrap:wrap;gap:8px">
                    <b>{{ $e->fecha->locale('es')->isoFormat('D MMM YYYY') }}</b>
                    <form method="POST" action="{{ route('evaluacion.destroy',$e) }}" onsubmit="return confirm('¿Eliminar esta evaluación?')">
                        @csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fa-solid fa-xmark"></i></button>
                    </form>
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:6px">
                    @foreach($cfg['campos'] as $c)
                        @php $v = $e->dato($c['name']); @endphp
                        @if($v !== null && $v !== '' && $c['tipo'] !== 'textarea')
                            <span class="pill gray" style="font-weight:400"><b>{{ $c['label'] }}:</b>&nbsp;{{ $v }}</span>
                        @endif
                    @endforeach
                </div>
                @foreach($cfg['campos'] as $c)
                    @php $v = $e->dato($c['name']); @endphp
                    @if($v !== null && $v !== '' && $c['tipo'] === 'textarea')
                        <p class="muted" style="margin:6px 0 0"><b>{{ $c['label'] }}:</b> {{ $v }}</p>
                    @endif
                @endforeach
                @if($e->notas)<p class="muted" style="margin:6px 0 0"><b>Notas:</b> {{ $e->notas }}</p>@endif
            </div>
        @empty
            <div class="empty"><i class="fa-solid {{ $cfg['icono'] }}"></i><p>Aún no hay evaluaciones registradas.</p></div>
        @endforelse
    </div>

    @if($graf)
    @push('scripts')
    <script>
    window.addEventListener('load', function(){
        if(!window.Chart) return;
        const s=@json($evaluaciones->sortBy('fecha')->values()->map(fn($e)=>['f'=>$e->fecha->format('d/m/y'),'v'=>is_numeric($e->dato($graf['campo'])) ? (float)$e->dato($graf['campo']) : null]));
        const pts=s.filter(x=>x.v!==null);
        const el=document.getElementById('chEval'); if(!el||!pts.length) return;
        new Chart(el,{ type:'line',
            data:{ labels:pts.map(x=>x.f), datasets:[{label:@json($graf['label']), data:pts.map(x=>x.v), borderColor:@json($cfg['color']), backgroundColor:@json($cfg['color']), tension:.3, borderWidth:3, pointRadius:4}]},
            options:{ responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:false}} } });
    });
    </script>
    @endpush
    @endif
@endsection
