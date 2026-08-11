@extends('layouts.app')
@section('title', $paciente->nombre_completo)

@section('content')
    <div class="page-head">
        <div><h1>{{ $paciente->nombre_completo }}</h1>
            <p>{{ $paciente->tipo_documento }} {{ $paciente->documento }} · {{ $paciente->edad !== null ? $paciente->edad.' años' : 'Edad no registrada' }}
               @if($paciente->especialidad) · <span class="pill pink"><i class="fa-solid {{ $paciente->especialidad->icono }}"></i> {{ $paciente->especialidad->nombre }}</span>@endif</p></div>
        <div class="flex gap">
            @if(optional($paciente->especialidad)->slug === 'pediatria' && $paciente->fecha_nacimiento && in_array($paciente->sexo, ['M','F']))
                <a href="{{ route('pacientes.crecimiento', $paciente) }}" class="btn btn-light"><i class="fa-solid fa-chart-line"></i> Crecimiento OMS</a>
            @endif
            @if(optional($paciente->especialidad)->slug === 'pediatria')
                <a href="{{ route('vacunas.index', $paciente) }}" class="btn btn-light"><i class="fa-solid fa-syringe"></i> Vacunas</a>
            @endif
            <a href="{{ route('historia.pdf', $paciente) }}" target="_blank" class="btn btn-light"><i class="fa-solid fa-file-pdf"></i> Historia PDF</a>
            <a href="{{ route('consultas.create', ['paciente_id' => $paciente->id]) }}" class="btn btn-primary"><i class="fa-solid fa-notes-medical"></i> Nueva consulta</a>
            <a href="{{ route('documentos.constancia', $paciente) }}" target="_blank" class="btn btn-light"><i class="fa-solid fa-file-medical"></i> Constancia</a>
            <a href="{{ route('pacientes.edit',$paciente) }}" class="btn btn-ghost"><i class="fa-solid fa-pen"></i> Editar</a>
        </div>
    </div>

    <div class="grid g-2 mb">
        <div class="card">
            <div class="flex between mb"><h3 style="margin:0">Historia clínica</h3><span class="pill violet">{{ $paciente->consultas->count() }} consultas</span></div>
            @forelse($paciente->consultas->sortByDesc('fecha') as $c)
                <div style="border-left:3px solid var(--violet-2);padding:6px 0 14px 16px;position:relative;margin-bottom:6px">
                    <div class="flex between">
                        <b>{{ $c->fecha->locale('es')->isoFormat('D MMM YYYY') }}</b>
                        <div class="flex gap">
                            <span class="pill violet">{{ $c->especialidad->nombre ?? 'General' }}</span>
                            <a href="{{ route('consultas.show',$c) }}" class="btn btn-light btn-sm"><i class="fa-solid fa-eye"></i></a>
                            <a href="{{ route('consultas.receta',$c) }}" target="_blank" class="btn btn-light btn-sm"><i class="fa-solid fa-file-prescription"></i></a>
                        </div>
                    </div>
                    <div class="muted" style="margin:4px 0"><b>Motivo:</b> {{ $c->motivo ?? '—' }}</div>
                    <div class="muted"><b>Diagnóstico:</b> {{ $c->diagnostico ?? '—' }}</div>
                    @if($c->imc)<div class="muted"><b>IMC:</b> {{ $c->imc }}</div>@endif
                </div>
            @empty
                <div class="empty"><i class="fa-solid fa-notes-medical"></i><p>Sin consultas registradas.</p><a href="{{ route('consultas.create', ['paciente_id' => $paciente->id]) }}" class="btn btn-primary btn-sm">Registrar primera consulta</a></div>
            @endforelse
        </div>
        <div>
            <div class="card mb">
                <h3 class="mb">Datos</h3>
                <p class="muted"><b>Teléfono:</b> {{ $paciente->telefono ?? '—' }}</p>
                <p class="muted"><b>Email:</b> {{ $paciente->email ?? '—' }}</p>
                <p class="muted"><b>Dirección:</b> {{ $paciente->direccion ?? '—' }}</p>
                <p class="muted"><b>Grupo sanguíneo:</b> {{ $paciente->grupo_sanguineo ?? '—' }}</p>
            </div>
            <div class="card pink mb">
                <h3 class="mb">Alergias y antecedentes</h3>
                <p class="muted"><b>Alergias:</b> {{ $paciente->alergias ?? 'Ninguna registrada' }}</p>
                <p class="muted"><b>Antecedentes:</b> {{ $paciente->antecedentes ?? 'Ninguno registrado' }}</p>
            </div>
            <div class="card mb">
                <div class="flex between mb"><h3 style="margin:0">Pagos</h3><a href="{{ route('pagos.create', ['paciente_id' => $paciente->id]) }}" class="btn btn-light btn-sm"><i class="fa-solid fa-plus"></i></a></div>
                @forelse($paciente->pagos->sortByDesc('fecha')->take(5) as $pago)
                    <div class="flex between" style="padding:6px 0;border-bottom:1px solid var(--line)">
                        <span class="muted">{{ $pago->fecha->format('d/m/Y') }} · {{ $pago->concepto }}</span>
                        <b>{{ $paciente->empresa->moneda ?? 'S/' }} {{ number_format($pago->monto,2) }}</b>
                    </div>
                @empty
                    <p class="muted">Sin pagos registrados.</p>
                @endforelse
            </div>

            <div class="card">
                <h3 class="mb"><i class="fa-solid fa-paperclip" style="color:var(--violet)"></i> Archivos</h3>
                <form method="POST" action="{{ route('adjuntos.store') }}" enctype="multipart/form-data" class="mb">
                    @csrf
                    <input type="hidden" name="paciente_id" value="{{ $paciente->id }}">
                    <div class="flex gap" style="flex-wrap:wrap;align-items:center">
                        <select name="categoria" style="flex:1;min-width:110px;border:1.5px solid var(--line);border-radius:12px;padding:9px">
                            <option value="examen">Examen</option>
                            <option value="imagen">Imagen</option>
                            <option value="receta">Receta</option>
                            <option value="otro">Otro</option>
                        </select>
                        <input type="file" name="archivo" required style="flex:2;min-width:150px">
                        <button class="btn btn-primary btn-sm"><i class="fa-solid fa-upload"></i> Subir</button>
                    </div>
                    @error('archivo')<span class="err">{{ $message }}</span>@enderror
                </form>
                @forelse($paciente->adjuntos->sortByDesc('created_at') as $a)
                    <div class="flex between" style="padding:8px 0;border-bottom:1px solid var(--line)">
                        <span class="flex gap"><i class="fa-solid {{ $a->es_imagen ? 'fa-image' : 'fa-file' }}" style="color:var(--violet-2)"></i>
                            <span><b style="font-size:13px">{{ $a->nombre }}</b><br><small class="muted">{{ ucfirst($a->categoria) }} · {{ $a->tamano_legible }}</small></span></span>
                        <span class="flex gap">
                            <a href="{{ route('adjuntos.download',$a) }}" class="btn btn-light btn-sm"><i class="fa-solid fa-download"></i></a>
                            <form method="POST" action="{{ route('adjuntos.destroy',$a) }}" onsubmit="return confirm('¿Eliminar archivo?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button></form>
                        </span>
                    </div>
                @empty
                    <p class="muted">Sin archivos adjuntos.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
