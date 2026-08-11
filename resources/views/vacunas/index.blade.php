@extends('layouts.app')
@section('title', 'Vacunas · '.$paciente->nombre_completo)

@section('content')
    <div class="page-head">
        <div><h1>Esquema de vacunación</h1><p>{{ $paciente->nombre_completo }} · {{ $paciente->edad !== null ? $paciente->edad.' años' : 'Edad N/D' }} · {{ $aplicadas }}/{{ $vacunas->count() }} aplicadas</p></div>
        <a href="{{ route('pacientes.show', $paciente) }}" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>

    <div class="grid g-2">
        <div class="table-wrap">
            <table>
                <thead><tr><th>Vacuna</th><th>Edad/Dosis</th><th>Estado</th><th></th></tr></thead>
                <tbody>
                @forelse($vacunas as $v)
                    <tr>
                        <td><b>{{ $v->nombre }}</b></td>
                        <td>{{ $v->dosis ?? '—' }}</td>
                        <td>
                            @if($v->estado === 'aplicada')
                                <span class="pill green"><i class="fa-solid fa-check"></i> {{ optional($v->fecha_aplicada)->format('d/m/Y') }}</span>
                            @else
                                <span class="pill amber">Pendiente</span>
                            @endif
                        </td>
                        <td style="text-align:right;white-space:nowrap">
                            @if($v->estado !== 'aplicada')
                                <form method="POST" action="{{ route('vacunas.aplicar', $v) }}" style="display:inline">@csrf<button class="btn btn-primary btn-sm"><i class="fa-solid fa-syringe"></i> Aplicar</button></form>
                            @endif
                            <form method="POST" action="{{ route('vacunas.destroy', $v) }}" style="display:inline" onsubmit="return confirm('¿Eliminar?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4"><div class="empty"><i class="fa-solid fa-syringe"></i><p>Sin vacunas registradas.</p>
                        <form method="POST" action="{{ route('vacunas.esquema', $paciente) }}">@csrf<button class="btn btn-primary btn-sm">Generar esquema estándar</button></form></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div>
            @if($vacunas->isNotEmpty())
                <form method="POST" action="{{ route('vacunas.esquema', $paciente) }}" class="card mb">@csrf
                    <p class="muted" style="margin:0 0 8px">Completa el esquema estándar con las vacunas faltantes.</p>
                    <button class="btn btn-light btn-sm" style="width:100%;justify-content:center"><i class="fa-solid fa-list-check"></i> Completar esquema</button>
                </form>
            @endif
            <div class="card">
                <h3 class="mb">Agregar vacuna</h3>
                <form method="POST" action="{{ route('vacunas.store') }}">
                    @csrf
                    <input type="hidden" name="paciente_id" value="{{ $paciente->id }}">
                    <div class="field mb"><label>Nombre</label><input name="nombre" required></div>
                    <div class="field mb"><label>Edad / Dosis</label><input name="dosis" placeholder="2 meses, refuerzo..."></div>
                    <div class="field mb"><label>Fecha programada</label><input type="date" name="fecha_programada"></div>
                    <button class="btn btn-primary"><i class="fa-solid fa-plus"></i> Agregar</button>
                </form>
            </div>
        </div>
    </div>
@endsection
