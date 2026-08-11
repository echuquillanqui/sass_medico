@extends('layouts.app')
@section('title', 'Consulta')

@section('content')
    @php $d = $consulta->datos_especialidad ?? []; @endphp
    <div class="page-head">
        <div>
            <h1>Consulta · {{ $consulta->fecha->locale('es')->isoFormat('D MMM YYYY') }}</h1>
            <p>{{ $consulta->paciente->nombre_completo }} · {{ $consulta->especialidad->nombre ?? 'General' }} · Dr(a). {{ $consulta->medico->name ?? '—' }}</p>
        </div>
        <div class="flex gap">
            <a href="{{ route('consultas.receta',$consulta) }}" target="_blank" class="btn btn-primary"><i class="fa-solid fa-file-prescription"></i> Receta PDF</a>
            <a href="{{ route('documentos.certificado',$consulta) }}" target="_blank" class="btn btn-light"><i class="fa-solid fa-file-lines"></i> Certificado</a>
            <a href="{{ route('consultas.edit',$consulta) }}" class="btn btn-ghost"><i class="fa-solid fa-pen"></i> Editar</a>
        </div>
    </div>

    <div class="card mb">
        <div class="flex between" style="flex-wrap:wrap;gap:10px">
            <h3 style="margin:0"><i class="fa-solid fa-share-nodes" style="color:var(--violet)"></i> Órdenes y acciones para este paciente</h3>
            <div class="flex gap" style="flex-wrap:wrap">
                <a href="{{ route('laboratorio.create', ['paciente_id' => $consulta->paciente_id]) }}" class="btn btn-light btn-sm"><i class="fa-solid fa-flask-vial"></i> Pedir laboratorio</a>
                <a href="{{ route('imagenes.create', ['paciente_id' => $consulta->paciente_id]) }}" class="btn btn-light btn-sm"><i class="fa-solid fa-x-ray"></i> Pedir imágenes</a>
                <a href="{{ route('farmacia.create', ['paciente_id' => $consulta->paciente_id]) }}" class="btn btn-light btn-sm"><i class="fa-solid fa-pills"></i> Dispensar</a>
                @if(($consulta->especialidad->slug ?? '') === 'odontologia')
                    <a href="{{ route('odontograma.edit', $consulta->paciente_id) }}" class="btn btn-light btn-sm"><i class="fa-solid fa-tooth"></i> Odontograma</a>
                @endif
                <a href="{{ route('pacientes.show', $consulta->paciente_id) }}" class="btn btn-light btn-sm"><i class="fa-solid fa-user"></i> Ficha del paciente</a>
            </div>
        </div>
    </div>

    <div class="grid g-2">
        <div class="card">
            <h3 class="mb">Evaluación</h3>
            <p class="muted"><b>Motivo:</b> {{ $consulta->motivo ?? '—' }}</p>
            <p class="muted"><b>Diagnóstico:</b> {{ $consulta->diagnostico ?? '—' }}</p>
            <p class="muted"><b>Tratamiento:</b> {{ $consulta->tratamiento ?? '—' }}</p>
            <p class="muted"><b>Observaciones:</b> {{ $consulta->observaciones ?? '—' }}</p>
        </div>
        <div>
            <div class="card mb">
                <h3 class="mb">Signos vitales</h3>
                <div class="grid" style="grid-template-columns:1fr 1fr;gap:10px">
                    <div class="metric"><div class="big">{{ $consulta->peso ?? '—' }}</div><div class="cap">Peso kg</div></div>
                    <div class="metric"><div class="big">{{ $consulta->talla ?? '—' }}</div><div class="cap">Talla cm</div></div>
                    <div class="metric"><div class="big">{{ $consulta->imc ?? '—' }}</div><div class="cap">IMC</div></div>
                    <div class="metric"><div class="big">{{ $consulta->presion_arterial ?? '—' }}</div><div class="cap">P. Arterial</div></div>
                </div>
            </div>
            @if(!empty($d))
                <div class="card pink">
                    <h3 class="mb">Ficha de especialidad</h3>
                    @foreach($d as $k => $v)
                        @if($k !== 'odontograma' && $v !== '' && $v !== null)
                            <p class="muted"><b>{{ ucfirst(str_replace('_',' ',$k)) }}:</b> {{ is_array($v) ? implode(', ',$v) : $v }}</p>
                        @endif
                    @endforeach
                    @if(!empty($d['odontograma']) && $d['odontograma'] !== '{}')
                        <p class="muted"><b>Odontograma:</b> registrado ✓</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endsection
