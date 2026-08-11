@extends('layouts.app')
@section('title', 'Evaluación cardiovascular · '.$paciente->nombre_completo)

@section('content')
    <div class="page-head">
        <div>
            <h1><i class="fa-solid fa-heart-pulse" style="color:#ef4444"></i> Evaluación cardiovascular</h1>
            <p>{{ $paciente->nombre_completo }} · {{ $paciente->edad !== null ? $paciente->edad.' años' : 'Edad N/D' }} · {{ ['M'=>'Masculino','F'=>'Femenino'][$paciente->sexo] ?? '—' }}</p>
        </div>
        <div class="flex gap">
            <a href="{{ route('pacientes.show',$paciente) }}" class="btn btn-ghost"><i class="fa-solid fa-user"></i> Ficha</a>
            <a href="{{ route('cardio.index') }}" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Volver</a>
        </div>
    </div>

    @if(session('ok'))<div class="alert ok mb">{{ session('ok') }}</div>@endif

    @if($ultima)
    @php $col = ['bajo'=>'#22c55e','moderado'=>'#f59e0b','alto'=>'#ef4444','muy alto'=>'#b91c1c'][$ultima->riesgo_nivel] ?? '#94a3b8'; @endphp
    <div class="grid g-4 mb">
        <div class="kpi k1"><div class="kpi-top"><span class="kpi-cap">Presión arterial</span><span class="kpi-ic"><i class="fa-solid fa-gauge-high"></i></span></div>
            <div><div class="kpi-val">{{ $ultima->pa_sistolica ?? '—' }}/{{ $ultima->pa_diastolica ?? '—' }}</div><div class="kpi-foot">mmHg · FC {{ $ultima->fc ?? '—' }}</div></div></div>
        <div class="kpi k2"><div class="kpi-top"><span class="kpi-cap">Colesterol total</span><span class="kpi-ic"><i class="fa-solid fa-droplet"></i></span></div>
            <div><div class="kpi-val">{{ $ultima->colesterol_total ?? '—' }}</div><div class="kpi-foot">HDL {{ $ultima->hdl ?? '—' }} · LDL {{ $ultima->ldl ?? '—' }}</div></div></div>
        <div class="kpi k3"><div class="kpi-top"><span class="kpi-cap">ECG</span><span class="kpi-ic"><i class="fa-solid fa-wave-square"></i></span></div>
            <div><div class="kpi-val" style="font-size:18px">{{ $ultima->ecg_ritmo ?? '—' }}</div><div class="kpi-foot">Ritmo</div></div></div>
        <div class="kpi k4" style="border-left:4px solid {{ $col }}"><div class="kpi-top"><span class="kpi-cap">Riesgo CV (10 años)</span><span class="kpi-ic"><i class="fa-solid fa-heart-crack"></i></span></div>
            <div><div class="kpi-val" style="color:{{ $col }}">{{ $ultima->riesgo_pct }}%</div><div class="kpi-foot">{{ ucfirst($ultima->riesgo_nivel) }}</div></div></div>
    </div>
    @endif

    <div class="grid g-2e">
        {{-- Nueva evaluación --}}
        <div class="card mb">
            <h3 class="mb"><i class="fa-solid fa-plus" style="color:var(--info)"></i> Nueva evaluación</h3>
            <form method="POST" action="{{ route('cardio.store',$paciente) }}">
                @csrf
                <div class="form-grid">
                    <div class="field"><label>Fecha *</label><input type="date" name="fecha" value="{{ now()->toDateString() }}" required></div>
                    <div class="field"><label>PA sistólica</label><input type="number" name="pa_sistolica" placeholder="120"></div>
                    <div class="field"><label>PA diastólica</label><input type="number" name="pa_diastolica" placeholder="80"></div>
                    <div class="field"><label>Frec. cardíaca</label><input type="number" name="fc" placeholder="72"></div>
                    <div class="field"><label>Colesterol total</label><input type="number" name="colesterol_total"></div>
                    <div class="field"><label>HDL</label><input type="number" name="hdl"></div>
                    <div class="field"><label>LDL</label><input type="number" name="ldl"></div>
                    <div class="field"><label>Triglicéridos</label><input type="number" name="trigliceridos"></div>
                    <div class="field"><label>Glucosa</label><input type="number" name="glucosa"></div>
                    <div class="field"><label>Ritmo ECG</label><input name="ecg_ritmo" placeholder="Sinusal"></div>
                </div>
                <div class="flex gap" style="margin:10px 0;flex-wrap:wrap">
                    <label class="flex gap" style="align-items:center;font-size:13px;cursor:pointer"><input type="hidden" name="fumador" value="0"><input type="checkbox" name="fumador" value="1" style="width:auto"> Fumador</label>
                    <label class="flex gap" style="align-items:center;font-size:13px;cursor:pointer"><input type="hidden" name="diabetes" value="0"><input type="checkbox" name="diabetes" value="1" style="width:auto"> Diabetes</label>
                </div>
                <div class="field mb"><label>Hallazgos ECG</label><textarea name="ecg_hallazgos"></textarea></div>
                <div class="field mb"><label>Observaciones</label><textarea name="observaciones"></textarea></div>
                <button class="btn btn-primary"><i class="fa-solid fa-check"></i> Guardar y calcular riesgo</button>
                <p class="muted" style="font-size:12px;margin-top:8px">El riesgo cardiovascular a 10 años es una <b>estimación orientativa</b> basada en edad, sexo, presión, lípidos, tabaquismo y diabetes; no reemplaza el juicio clínico.</p>
            </form>
        </div>

        {{-- Tendencia de PA --}}
        <div class="card mb">
            <h3 class="mb"><i class="fa-solid fa-chart-line" style="color:var(--violet)"></i> Tendencia de presión arterial</h3>
            <div class="chart-hold" style="height:240px"><canvas id="chPA"></canvas></div>
        </div>
    </div>

    {{-- Historial --}}
    <div class="card" style="padding:0">
        <div style="padding:18px 20px 6px"><h3 style="margin:0">Historial de evaluaciones</h3></div>
        <div style="overflow-x:auto">
            <div class="table-wrap" style="box-shadow:none;border-radius:0;min-width:760px">
                <table>
                    <thead><tr><th>Fecha</th><th>PA</th><th>FC</th><th>Col.</th><th>HDL/LDL</th><th>Glucosa</th><th>Riesgo</th><th></th></tr></thead>
                    <tbody>
                    @forelse($evaluaciones as $e)
                        @php $c=['bajo'=>'green','moderado'=>'orange','alto'=>'red','muy alto'=>'red'][$e->riesgo_nivel]??'gray'; @endphp
                        <tr>
                            <td>{{ $e->fecha->locale('es')->isoFormat('D MMM YYYY') }}</td>
                            <td>{{ $e->pa_sistolica ?? '—' }}/{{ $e->pa_diastolica ?? '—' }}</td>
                            <td>{{ $e->fc ?? '—' }}</td>
                            <td>{{ $e->colesterol_total ?? '—' }}</td>
                            <td>{{ $e->hdl ?? '—' }}/{{ $e->ldl ?? '—' }}</td>
                            <td>{{ $e->glucosa ?? '—' }}</td>
                            <td><span class="pill {{ $c }}">{{ $e->riesgo_pct }}%</span></td>
                            <td style="text-align:right">
                                <form method="POST" action="{{ route('cardio.destroy',$e) }}" onsubmit="return confirm('¿Eliminar esta evaluación?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm"><i class="fa-solid fa-xmark"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8"><div class="empty"><i class="fa-solid fa-heart-pulse"></i><p>Aún no hay evaluaciones registradas.</p></div></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    window.addEventListener('load', function(){
        if(!window.Chart) return;
        @php
            $datosGrafico = $evaluaciones->sortBy('fecha')->values()->map(fn ($e) => ['f' => $e->fecha->format('d/m/y'), 's' => $e->pa_sistolica, 'd' => $e->pa_diastolica]);
        @endphp
        const serie=@json($datosGrafico);
        const el=document.getElementById('chPA'); if(!el||!serie.length) return;
        new Chart(el,{ type:'line',
            data:{ labels:serie.map(x=>x.f), datasets:[
                {label:'Sistólica', data:serie.map(x=>x.s), borderColor:'#ef4444', backgroundColor:'#ef4444', tension:.3, borderWidth:3, pointRadius:4},
                {label:'Diastólica', data:serie.map(x=>x.d), borderColor:'#3b82f6', backgroundColor:'#3b82f6', tension:.3, borderWidth:3, pointRadius:4}
            ]},
            options:{ responsive:true, maintainAspectRatio:false, plugins:{legend:{position:'bottom',labels:{boxWidth:12,font:{size:11}}}},
                scales:{ y:{beginAtZero:false, title:{display:true,text:'mmHg'}} } } });
    });
    </script>
    @endpush
@endsection
