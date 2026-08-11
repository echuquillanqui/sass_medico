@extends('layouts.app')
@section('title', 'Consulta · '.$paciente->nombre_completo)

@section('content')
    @php $recetaJson = ($consulta->exists ? $consulta->recetaItems : collect())->map(fn($r)=>['medicamento'=>$r->medicamento,'presentacion'=>$r->presentacion,'dosis'=>$r->dosis,'frecuencia'=>$r->frecuencia,'duracion'=>$r->duracion,'indicaciones'=>$r->indicaciones])->values(); @endphp
    @php $d = $consulta->datos_especialidad ?? []; $slug = optional($especialidad)->slug; @endphp
    <div class="page-head">
        <div>
            <h1>{{ $consulta->exists ? 'Editar consulta' : 'Nueva consulta' }}</h1>
            <p>{{ $paciente->nombre_completo }} · {{ $paciente->edad !== null ? $paciente->edad.' años' : 'Edad N/D' }}
               @if($especialidad) · <span class="pill pink"><i class="fa-solid {{ $especialidad->icono }}"></i> {{ $especialidad->nombre }}</span>@endif</p>
        </div>
        <a href="{{ route('pacientes.show', $paciente) }}" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>

    <form method="POST" action="{{ $consulta->exists ? route('consultas.update',$consulta) : route('consultas.store') }}">
        @csrf
        @if($consulta->exists) @method('PUT') @endif
        <input type="hidden" name="paciente_id" value="{{ $paciente->id }}">
        @if($cita)<input type="hidden" name="cita_id" value="{{ $cita->id }}">@endif

        <div class="grid g-2">
            <div>
                {{-- Signos vitales --}}
                <div class="card mb">
                    <h3 class="mb"><i class="fa-solid fa-heart-pulse" style="color:var(--pink)"></i> Signos vitales</h3>
                    <div class="form-grid">
                        <div class="field"><label>Fecha *</label><input type="date" name="fecha" value="{{ old('fecha', optional($consulta->fecha)->format('Y-m-d') ?? now()->toDateString()) }}" required></div>
                        <div class="field"><label>Peso (kg)</label><input type="number" step="0.01" id="peso" name="peso" value="{{ old('peso',$consulta->peso) }}" oninput="calcImc()"></div>
                        <div class="field"><label>Talla (cm)</label><input type="number" step="0.1" id="talla" name="talla" value="{{ old('talla',$consulta->talla) }}" oninput="calcImc()"></div>
                        <div class="field"><label>IMC</label><input id="imc" readonly value="{{ $consulta->imc }}" style="background:var(--bg-pink)"></div>
                        <div class="field"><label>Presión arterial</label><input name="presion_arterial" value="{{ old('presion_arterial',$consulta->presion_arterial) }}" placeholder="120/80"></div>
                        <div class="field"><label>Frec. cardíaca (lpm)</label><input type="number" name="frecuencia_cardiaca" value="{{ old('frecuencia_cardiaca',$consulta->frecuencia_cardiaca) }}"></div>
                        <div class="field"><label>Temperatura (°C)</label><input type="number" step="0.1" name="temperatura" value="{{ old('temperatura',$consulta->temperatura) }}"></div>
                    </div>
                </div>

                {{-- Evaluación clínica --}}
                <div class="card mb">
                    <h3 class="mb"><i class="fa-solid fa-notes-medical" style="color:var(--violet)"></i> Evaluación clínica</h3>
                    <div class="field mb"><label>Motivo de consulta</label><textarea name="motivo">{{ old('motivo',$consulta->motivo) }}</textarea></div>
                    <div class="field mb"><label>Diagnóstico</label><textarea name="diagnostico">{{ old('diagnostico',$consulta->diagnostico) }}</textarea></div>
                    <div class="field mb"><label>Tratamiento / Receta</label><textarea name="tratamiento" style="min-height:110px">{{ old('tratamiento',$consulta->tratamiento) }}</textarea></div>
                    <div class="field"><label>Observaciones</label><textarea name="observaciones">{{ old('observaciones',$consulta->observaciones) }}</textarea></div>
                </div>

                {{-- Receta: medicamentos --}}
                <div class="card mb">
                    <div class="flex between mb">
                        <h3 style="margin:0"><i class="fa-solid fa-prescription" style="color:var(--pink)"></i> Receta médica</h3>
                        <button type="button" class="btn btn-light btn-sm" onclick="addMed()"><i class="fa-solid fa-plus"></i> Agregar</button>
                    </div>
                    <div id="meds"></div>
                    <p class="muted" style="font-size:12px">Cada fila se imprime en la receta en PDF.</p>
                </div>
            </div>

            {{-- Panel derecho: módulo por especialidad --}}
            <div>
                @includeIf('consultas.especialidad.'.$slug, ['d' => $d])
                @if(!view()->exists('consultas.especialidad.'.$slug))
                    <div class="card pink mb">
                        <h3 class="mb">Ficha de especialidad</h3>
                        <p class="muted">Esta especialidad usa la ficha clínica general.</p>
                    </div>
                @endif

                <div class="card">
                    <button class="btn btn-primary" style="width:100%;justify-content:center"><i class="fa-solid fa-floppy-disk"></i> Guardar consulta</button>
                    @if($consulta->exists)
                        <a href="{{ route('consultas.receta',$consulta) }}" target="_blank" class="btn btn-light" style="width:100%;justify-content:center;margin-top:10px"><i class="fa-solid fa-file-prescription"></i> Imprimir receta (PDF)</a>
                    @endif
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
    function calcImc(){
        const p=parseFloat(document.getElementById('peso').value), t=parseFloat(document.getElementById('talla').value)/100;
        document.getElementById('imc').value = (p>0&&t>0) ? (p/(t*t)).toFixed(1) : '';
    }
    </script>
    @endpush

    @push('scripts')
    <script>
    const MEDS = @json($recetaJson);
    function medRow(m={}){
        const i = document.querySelectorAll('#meds .med').length;
        const div = document.createElement('div');
        div.className='med';
        div.style.cssText='border:1px dashed var(--line);border-radius:12px;padding:12px;margin-bottom:10px';
        div.innerHTML =
            '<div class="flex between" style="margin-bottom:8px"><b style="font-size:12px;color:var(--ink-soft)">Medicamento '+(i+1)+'</b>'+
            '<button type="button" class="btn btn-danger btn-sm" onclick="this.closest(&quot;.med&quot;).remove()"><i class="fa-solid fa-xmark"></i></button></div>'+
            '<div class="form-grid">'+
            fld(i,'medicamento','Medicamento',m.medicamento)+
            fld(i,'presentacion','Presentación',m.presentacion)+
            fld(i,'dosis','Dosis',m.dosis)+
            fld(i,'frecuencia','Frecuencia',m.frecuencia)+
            fld(i,'duracion','Duración',m.duracion)+
            fld(i,'indicaciones','Indicaciones',m.indicaciones)+
            '</div>';
        document.getElementById('meds').appendChild(div);
    }
    function fld(i,name,label,val){
        return '<div class="field"><label>'+label+'</label><input name="receta['+i+']['+name+']" value="'+(val?String(val).replace(/"/g,'&quot;'):'')+'"></div>';
    }
    function addMed(){ medRow(); }
    document.addEventListener('DOMContentLoaded', function(){
        if (MEDS.length) MEDS.forEach(m=>medRow(m)); else medRow();
    });
    </script>
    @endpush

@endsection
