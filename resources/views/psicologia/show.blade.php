@extends('layouts.app')
@section('title', 'Psicología · '.$paciente->nombre_completo)

@section('content')
    <div class="page-head">
        <div>
            <h1><i class="fa-solid fa-brain" style="color:#8b5cf6"></i> Seguimiento psicológico</h1>
            <p>{{ $paciente->nombre_completo }} · {{ $paciente->edad !== null ? $paciente->edad.' años' : 'Edad N/D' }}</p>
        </div>
        <div class="flex gap">
            <a href="{{ route('pacientes.show',$paciente) }}" class="btn btn-ghost"><i class="fa-solid fa-user"></i> Ficha</a>
            <a href="{{ route('psicologia.index') }}" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Volver</a>
        </div>
    </div>

    @if(session('ok'))<div class="alert ok mb">{{ session('ok') }}</div>@endif

    @if($ultima)
    <div class="grid g-4 mb">
        <div class="kpi k1"><div class="kpi-top"><span class="kpi-cap">Sesiones</span><span class="kpi-ic"><i class="fa-solid fa-list-ol"></i></span></div>
            <div><div class="kpi-val">{{ $sesiones->count() }}</div><div class="kpi-foot">Realizadas</div></div></div>
        <div class="kpi k2"><div class="kpi-top"><span class="kpi-cap">Estado de ánimo</span><span class="kpi-ic"><i class="fa-solid fa-face-smile"></i></span></div>
            <div><div class="kpi-val">{{ $ultima->estado_animo ?? '—' }}<span style="font-size:14px">/10</span></div><div class="kpi-foot">Última sesión</div></div></div>
        <div class="kpi k3"><div class="kpi-top"><span class="kpi-cap">Progreso</span><span class="kpi-ic"><i class="fa-solid fa-chart-line"></i></span></div>
            <div><div class="kpi-val">{{ $ultima->progreso !== null ? $ultima->progreso.'%' : '—' }}</div><div class="kpi-foot">Terapéutico</div></div></div>
        <div class="kpi k4"><div class="kpi-top"><span class="kpi-cap">Próxima cita</span><span class="kpi-ic"><i class="fa-solid fa-calendar"></i></span></div>
            <div><div class="kpi-val" style="font-size:18px">{{ $ultima->proxima_cita ? $ultima->proxima_cita->locale('es')->isoFormat('D MMM') : '—' }}</div><div class="kpi-foot">Agendada</div></div></div>
    </div>
    @endif

    <div class="grid g-2e">
        <div class="card mb">
            <h3 class="mb"><i class="fa-solid fa-plus" style="color:var(--info)"></i> Nueva sesión (n.º {{ $nextNum }})</h3>
            <form method="POST" action="{{ route('psicologia.store',$paciente) }}">
                @csrf
                <div class="form-grid">
                    <div class="field"><label>Fecha *</label><input type="date" name="fecha" value="{{ now()->toDateString() }}" required></div>
                    <div class="field"><label>N.º sesión</label><input type="number" name="numero" value="{{ $nextNum }}" min="1"></div>
                    <div class="field"><label>Enfoque</label>
                        <select name="enfoque"><option value="">—</option>
                            @foreach(['TCC','Psicoanálisis','Sistémico','Humanista','Gestalt','EMDR','Mindfulness'] as $en)<option>{{ $en }}</option>@endforeach
                        </select></div>
                    <div class="field"><label>Estado de ánimo (1-10)</label><input type="number" name="estado_animo" min="1" max="10"></div>
                    <div class="field"><label>Progreso (%)</label><input type="number" name="progreso" min="0" max="100"></div>
                    <div class="field"><label>Próxima cita</label><input type="date" name="proxima_cita"></div>
                </div>
                <div class="field mb"><label>Motivo / tema</label><input name="motivo" placeholder="Ansiedad, duelo, pareja…"></div>
                <div class="field mb"><label>Desarrollo de la sesión</label><textarea name="desarrollo" style="min-height:90px"></textarea></div>
                <div class="field mb"><label>Tareas para casa</label><textarea name="tareas"></textarea></div>
                <button class="btn btn-primary"><i class="fa-solid fa-check"></i> Registrar sesión</button>
            </form>
        </div>

        <div class="card mb">
            <h3 class="mb"><i class="fa-solid fa-chart-line" style="color:var(--violet)"></i> Evolución</h3>
            <div class="chart-hold" style="height:240px"><canvas id="chPsico"></canvas></div>
            <p class="muted" style="font-size:12px">Estado de ánimo (1-10) y progreso terapéutico (%) por sesión.</p>
        </div>
    </div>

    <div class="card">
        <h3 class="mb">Historial de sesiones</h3>
        @forelse($sesiones as $s)
            <div style="border-left:3px solid #8b5cf6;padding:8px 0 8px 14px;margin-bottom:12px">
                <div class="flex between" style="flex-wrap:wrap;gap:8px">
                    <b>Sesión {{ $s->numero ? '#'.$s->numero : '' }} · {{ $s->fecha->locale('es')->isoFormat('D MMM YYYY') }}</b>
                    <div class="flex gap" style="align-items:center">
                        @if($s->enfoque)<span class="pill violet">{{ $s->enfoque }}</span>@endif
                        @if($s->progreso!==null)<span class="pill blue">{{ $s->progreso }}%</span>@endif
                        <form method="POST" action="{{ route('psicologia.destroy',$s) }}" onsubmit="return confirm('¿Eliminar esta sesión?')">
                            @csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fa-solid fa-xmark"></i></button>
                        </form>
                    </div>
                </div>
                @if($s->motivo)<p class="muted" style="margin:4px 0"><b>Tema:</b> {{ $s->motivo }}</p>@endif
                @if($s->desarrollo)<p class="muted" style="margin:4px 0">{{ $s->desarrollo }}</p>@endif
                @if($s->tareas)<p class="muted" style="margin:4px 0"><b>Tareas:</b> {{ $s->tareas }}</p>@endif
            </div>
        @empty
            <div class="empty"><i class="fa-solid fa-brain"></i><p>Aún no hay sesiones registradas.</p></div>
        @endforelse
    </div>

    @push('scripts')
    <script>
    window.addEventListener('load', function(){
        if(!window.Chart) return;
        @php
            $datosGrafico = $sesiones->sortBy('fecha')->values()->map(fn ($x) => ['n' => $x->numero ?? '', 'a' => $x->estado_animo, 'p' => $x->progreso]);
        @endphp
        const s=@json($datosGrafico);
        const el=document.getElementById('chPsico'); if(!el||!s.length) return;
        new Chart(el,{ type:'line',
            data:{ labels:s.map((x,i)=>'S'+(x.n||i+1)), datasets:[
                {label:'Ánimo (1-10)', data:s.map(x=>x.a), borderColor:'#8b5cf6', backgroundColor:'#8b5cf6', yAxisID:'y', tension:.3, borderWidth:3, pointRadius:4},
                {label:'Progreso (%)', data:s.map(x=>x.p), borderColor:'#22c55e', backgroundColor:'#22c55e', yAxisID:'y1', tension:.3, borderWidth:3, pointRadius:4}
            ]},
            options:{ responsive:true, maintainAspectRatio:false, plugins:{legend:{position:'bottom',labels:{boxWidth:12,font:{size:11}}}},
                scales:{ y:{min:0,max:10,position:'left',title:{display:true,text:'Ánimo'}}, y1:{min:0,max:100,position:'right',grid:{drawOnChartArea:false},title:{display:true,text:'%'}} } } });
    });
    </script>
    @endpush
@endsection
