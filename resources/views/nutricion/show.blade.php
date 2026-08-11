@extends('layouts.app')
@section('title', 'Nutrición · '.$paciente->nombre_completo)

@section('content')
    <div class="page-head">
        <div>
            <h1><i class="fa-solid fa-apple-whole" style="color:#22c55e"></i> Control nutricional</h1>
            <p>{{ $paciente->nombre_completo }} · {{ $paciente->edad !== null ? $paciente->edad.' años' : 'Edad N/D' }}</p>
        </div>
        <div class="flex gap">
            <a href="{{ route('pacientes.show',$paciente) }}" class="btn btn-ghost"><i class="fa-solid fa-user"></i> Ficha</a>
            <a href="{{ route('nutricion.index') }}" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Volver</a>
        </div>
    </div>

    @if(session('ok'))<div class="alert ok mb">{{ session('ok') }}</div>@endif

    @if($ultima)
    @php $col = ['Normal'=>'#22c55e','Bajo peso'=>'#3b82f6','Sobrepeso'=>'#f59e0b'][$ultima->clasificacion_imc] ?? (str_contains($ultima->clasificacion_imc,'Obesidad')?'#ef4444':'#94a3b8'); @endphp
    <div class="grid g-4 mb">
        <div class="kpi k1"><div class="kpi-top"><span class="kpi-cap">Peso actual</span><span class="kpi-ic"><i class="fa-solid fa-weight-scale"></i></span></div>
            <div><div class="kpi-val">{{ $ultima->peso ?? '—' }}<span style="font-size:14px"> kg</span></div><div class="kpi-foot">Objetivo: {{ $ultima->peso_objetivo ?? '—' }} kg</div></div></div>
        <div class="kpi k2" style="border-left:4px solid {{ $col }}"><div class="kpi-top"><span class="kpi-cap">IMC</span><span class="kpi-ic"><i class="fa-solid fa-calculator"></i></span></div>
            <div><div class="kpi-val" style="color:{{ $col }}">{{ $ultima->imc ?? '—' }}</div><div class="kpi-foot">{{ $ultima->clasificacion_imc }}</div></div></div>
        <div class="kpi k3"><div class="kpi-top"><span class="kpi-cap">% Grasa</span><span class="kpi-ic"><i class="fa-solid fa-percent"></i></span></div>
            <div><div class="kpi-val">{{ $ultima->grasa ?? '—' }}</div><div class="kpi-foot">Músculo {{ $ultima->musculo ?? '—' }}%</div></div></div>
        <div class="kpi k4"><div class="kpi-top"><span class="kpi-cap">Índice cintura-cadera</span><span class="kpi-ic"><i class="fa-solid fa-ruler"></i></span></div>
            <div><div class="kpi-val">{{ $ultima->icc ?? '—' }}</div><div class="kpi-foot">Meta: {{ $ultima->objetivo_kcal ?? '—' }} kcal</div></div></div>
    </div>
    @endif

    <div class="grid g-2e">
        <div class="card mb">
            <h3 class="mb"><i class="fa-solid fa-plus" style="color:var(--info)"></i> Nueva evaluación</h3>
            <form method="POST" action="{{ route('nutricion.store',$paciente) }}">
                @csrf
                <div class="form-grid">
                    <div class="field"><label>Fecha *</label><input type="date" name="fecha" value="{{ now()->toDateString() }}" required></div>
                    <div class="field"><label>Peso (kg)</label><input type="number" step="0.01" name="peso"></div>
                    <div class="field"><label>Talla (cm)</label><input type="number" step="0.1" name="talla" value="{{ $paciente->consultas()->latest('fecha')->first()->talla ?? '' }}"></div>
                    <div class="field"><label>% Grasa</label><input type="number" step="0.1" name="grasa"></div>
                    <div class="field"><label>% Músculo</label><input type="number" step="0.1" name="musculo"></div>
                    <div class="field"><label>Cintura (cm)</label><input type="number" step="0.1" name="cintura"></div>
                    <div class="field"><label>Cadera (cm)</label><input type="number" step="0.1" name="cadera"></div>
                    <div class="field"><label>Peso objetivo (kg)</label><input type="number" step="0.01" name="peso_objetivo"></div>
                    <div class="field"><label>Objetivo (kcal/día)</label><input type="number" name="objetivo_kcal"></div>
                </div>
                <div class="field mb"><label>Plan alimentario</label><textarea name="plan" style="min-height:80px" placeholder="Distribución de comidas, restricciones, suplementos…"></textarea></div>
                <div class="field mb"><label>Observaciones</label><textarea name="observaciones"></textarea></div>
                <button class="btn btn-primary"><i class="fa-solid fa-check"></i> Guardar y calcular IMC</button>
            </form>
        </div>

        <div class="card mb">
            <h3 class="mb"><i class="fa-solid fa-chart-line" style="color:var(--violet)"></i> Evolución de peso e IMC</h3>
            <div class="chart-hold" style="height:240px"><canvas id="chNutri"></canvas></div>
        </div>
    </div>

    <div class="card" style="padding:0">
        <div style="padding:18px 20px 6px"><h3 style="margin:0">Historial</h3></div>
        <div style="overflow-x:auto">
            <div class="table-wrap" style="box-shadow:none;border-radius:0;min-width:720px">
                <table>
                    <thead><tr><th>Fecha</th><th>Peso</th><th>IMC</th><th>Clasificación</th><th>% Grasa</th><th>ICC</th><th></th></tr></thead>
                    <tbody>
                    @forelse($evaluaciones as $e)
                        <tr>
                            <td>{{ $e->fecha->locale('es')->isoFormat('D MMM YYYY') }}</td>
                            <td>{{ $e->peso ?? '—' }}</td>
                            <td>{{ $e->imc ?? '—' }}</td>
                            <td>{{ $e->clasificacion_imc }}</td>
                            <td>{{ $e->grasa ?? '—' }}</td>
                            <td>{{ $e->icc ?? '—' }}</td>
                            <td style="text-align:right">
                                <form method="POST" action="{{ route('nutricion.destroy',$e) }}" onsubmit="return confirm('¿Eliminar esta evaluación?')">
                                    @csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fa-solid fa-xmark"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><div class="empty"><i class="fa-solid fa-apple-whole"></i><p>Aún no hay evaluaciones registradas.</p></div></td></tr>
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
            $datosGrafico = $evaluaciones->sortBy('fecha')->values()->map(fn ($e) => ['f' => $e->fecha->format('d/m/y'), 'peso' => $e->peso, 'imc' => $e->imc]);
        @endphp
        const s=@json($datosGrafico);
        const el=document.getElementById('chNutri'); if(!el||!s.length) return;
        new Chart(el,{ type:'line',
            data:{ labels:s.map(x=>x.f), datasets:[
                {label:'Peso (kg)', data:s.map(x=>x.peso), borderColor:'#22c55e', backgroundColor:'#22c55e', yAxisID:'y', tension:.3, borderWidth:3, pointRadius:4},
                {label:'IMC', data:s.map(x=>x.imc), borderColor:'#a855f7', backgroundColor:'#a855f7', yAxisID:'y1', tension:.3, borderWidth:3, pointRadius:4}
            ]},
            options:{ responsive:true, maintainAspectRatio:false, plugins:{legend:{position:'bottom',labels:{boxWidth:12,font:{size:11}}}},
                scales:{ y:{position:'left',title:{display:true,text:'kg'}}, y1:{position:'right',grid:{drawOnChartArea:false},title:{display:true,text:'IMC'}} } } });
    });
    </script>
    @endpush
@endsection
