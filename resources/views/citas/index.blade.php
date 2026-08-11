@extends('layouts.app')
@section('title', 'Citas')

@section('content')
    <div class="page-head">
        <div><h1>Citas</h1><p>Agenda de citas de la clínica.</p></div>
        <a href="{{ route('citas.create') }}" class="btn btn-primary"><i class="fa-solid fa-calendar-plus"></i> Nueva cita</a>
    </div>

    <div class="flex gap mb" style="flex-wrap:wrap">
        <a href="{{ route('citas.index') }}" class="btn {{ !$estado ? 'btn-primary' : 'btn-ghost' }} btn-sm">Todas</a>
        @foreach(['pendiente'=>'Pendientes','confirmada'=>'Confirmadas','atendida'=>'Atendidas','cancelada'=>'Canceladas'] as $k=>$v)
            <a href="{{ route('citas.index',['estado'=>$k]) }}" class="btn {{ $estado==$k ? 'btn-primary' : 'btn-ghost' }} btn-sm">{{ $v }}</a>
        @endforeach
    </div>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Paciente</th><th>Especialidad</th><th>Médico</th><th>Fecha</th><th>Hora</th><th>Estado</th><th></th></tr></thead>
            <tbody>
            @forelse($citas as $c)
                <tr>
                    <td><span class="avatar-sm">{{ mb_substr($c->paciente->nombres,0,1) }}{{ mb_substr($c->paciente->apellidos,0,1) }}</span>{{ $c->paciente->nombre_completo }}</td>
                    <td>{{ $c->especialidad->nombre ?? '—' }}</td>
                    <td>{{ $c->medico->name ?? '—' }}</td>
                    <td>{{ $c->fecha->locale('es')->isoFormat('D MMM YYYY') }}</td>
                    <td>{{ \Illuminate\Support\Str::of($c->hora)->substr(0,5) }}</td>
                    <td>@include('citas.estado', ['estado' => $c->estado])</td>
                    <td style="text-align:right;white-space:nowrap">
                        @if(in_array($c->estado, ['pendiente','confirmada']))
                            <a href="{{ route('consultas.create', ['paciente_id' => $c->paciente_id, 'cita_id' => $c->id]) }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-stethoscope"></i> Atender</a>
                        @endif
                        @if($c->es_teleconsulta)
                            <a href="{{ $c->sala_video_url }}" target="_blank" class="btn btn-light btn-sm" title="Videollamada"><i class="fa-solid fa-video"></i></a>
                        @endif
                        @if($c->whatsapp_url)
                            <a href="{{ $c->whatsapp_url }}" target="_blank" class="btn btn-light btn-sm" title="Recordar por WhatsApp" style="color:#25d366"><i class="fa-brands fa-whatsapp"></i></a>
                        @endif
                        <a href="{{ route('citas.edit',$c) }}" class="btn btn-light btn-sm"><i class="fa-solid fa-pen"></i></a>
                        <form method="POST" action="{{ route('citas.destroy',$c) }}" style="display:inline" onsubmit="return confirm('¿Eliminar cita?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7"><div class="empty"><i class="fa-regular fa-calendar"></i><p>No hay citas registradas.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $citas->links() }}
@endsection
