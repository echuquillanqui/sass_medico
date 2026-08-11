@extends('layouts.app')
@section('title', 'Crecimiento · '.$paciente->nombre_completo)

@section('content')
    <div class="page-head">
        <div>
            <h1>Curvas de crecimiento OMS</h1>
            <p>{{ $paciente->nombre_completo }} · {{ $paciente->sexo === 'F' ? 'Niña' : 'Niño' }} · {{ $paciente->edad_meses }} meses</p>
        </div>
        <a href="{{ route('pacientes.show', $paciente) }}" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>

    @php
        $ultimoPeso = end($puntos['peso']) ?: null;
        $ultimaTalla = end($puntos['talla']) ?: null;
    @endphp
    <div class="grid g-2 mb">
        <div class="card pink">
            <h3 class="mb"><i class="fa-solid fa-weight-scale" style="color:#ec4899"></i> Peso para la edad</h3>
            @if($ultimoPeso)<p class="muted">Último registro: <b>{{ $ultimoPeso['y'] }} kg</b> · percentil <b>P{{ $ultimoPeso['p'] }}</b></p>@else<p class="muted">Sin registros de peso.</p>@endif
            <div class="chart-box" style="height:280px"><canvas id="chPeso"></canvas></div>
        </div>
        <div class="card pink">
            <h3 class="mb"><i class="fa-solid fa-ruler-vertical" style="color:#a855f7"></i> Talla para la edad</h3>
            @if($ultimaTalla)<p class="muted">Último registro: <b>{{ $ultimaTalla['y'] }} cm</b> · percentil <b>P{{ $ultimaTalla['p'] }}</b></p>@else<p class="muted">Sin registros de talla.</p>@endif
            <div class="chart-box" style="height:280px"><canvas id="chTalla"></canvas></div>
        </div>
    </div>

    <div class="card">
        <p class="muted" style="font-size:12px"><i class="fa-solid fa-circle-info"></i> Referencia: Patrones de Crecimiento Infantil de la OMS (método LMS). Las líneas muestran los percentiles P3, P50 y P97; los puntos son las mediciones del paciente.</p>
    </div>

    @push('scripts')
    <script>
    window.addEventListener('load', function () {
        if (!window.Chart) return;
        function mk(canvas, curvas, puntos, unidad) {
            const xy = (serie) => curvas.labels.map((m, i) => ({ x: m, y: serie[i] }));
            new Chart(document.getElementById(canvas), {
                type:'line',
                data:{ datasets:[
                    { label:'P97', data:xy(curvas.p97), borderColor:'#fca5a5', borderWidth:1, pointRadius:0, fill:false, tension:.3 },
                    { label:'P50', data:xy(curvas.p50), borderColor:'#a855f7', borderWidth:1.5, pointRadius:0, borderDash:[5,4], fill:false, tension:.3 },
                    { label:'P3', data:xy(curvas.p3), borderColor:'#fca5a5', borderWidth:1, pointRadius:0, fill:false, tension:.3 },
                    { label:'Paciente', data:puntos, borderColor:'#ec4899', backgroundColor:'#ec4899', borderWidth:2, pointRadius:5, showLine:true, tension:.2 }
                ]},
                options:{ responsive:true, maintainAspectRatio:false, parsing:false,
                    interaction:{intersect:false},
                    plugins:{ legend:{position:'bottom', labels:{boxWidth:12, font:{size:10}}} },
                    scales:{
                        x:{ type:'linear', title:{display:true,text:'Edad (meses)'}, min:0, max:60, ticks:{stepSize:6} },
                        y:{ title:{display:true,text:unidad} }
                    }
                }
            });
        }
        mk('chPeso', @json($curvasPeso), @json($puntos['peso']), 'Peso (kg)');
        mk('chTalla', @json($curvasTalla), @json($puntos['talla']), 'Talla (cm)');
    });
    </script>
    @endpush
@endsection
