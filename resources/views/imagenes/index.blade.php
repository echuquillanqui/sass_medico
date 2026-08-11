@extends('layouts.app')
@section('title', 'Imágenes')

@section('content')
    <div class="page-head">
        <div><h1>Diagnóstico por imágenes</h1><p>Órdenes de estudio, informes y archivos.</p></div>
        <a href="{{ route('imagenes.create') }}" class="btn btn-primary"><i class="fa-solid fa-x-ray"></i> Nuevo estudio</a>
    </div>

    <div class="flex gap mb" style="flex-wrap:wrap">
        <a href="{{ route('imagenes.index') }}" class="btn {{ !$estado ? 'btn-primary' : 'btn-ghost' }} btn-sm">Todos</a>
        @foreach(['solicitado'=>'Solicitados','realizado'=>'Realizados','informado'=>'Informados'] as $k=>$v)
            <a href="{{ route('imagenes.index',['estado'=>$k]) }}" class="btn {{ $estado==$k ? 'btn-primary' : 'btn-ghost' }} btn-sm">{{ $v }}</a>
        @endforeach
    </div>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Estudio</th><th>Paciente</th><th>Modalidad</th><th>Fecha</th><th>Estado</th><th></th></tr></thead>
            <tbody>
            @forelse($estudios as $e)
                <tr>
                    <td><b>#{{ str_pad($e->id,5,'0',STR_PAD_LEFT) }}</b></td>
                    <td>{{ $e->paciente->nombre_completo ?? '—' }}</td>
                    <td>{{ $e->modalidad }}@if($e->region)<br><small class="muted">{{ $e->region }}</small>@endif</td>
                    <td>{{ $e->fecha->format('d/m/Y') }}</td>
                    <td>@php $c=['solicitado'=>'amber','realizado'=>'blue','informado'=>'green'][$e->estado]??'gray'; @endphp<span class="pill {{ $c }}">{{ $e->estado_label }}</span></td>
                    <td style="text-align:right;white-space:nowrap">
                        <a href="{{ route('imagenes.show',$e) }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-file-medical"></i> Ver</a>
                        <a href="{{ route('imagenes.pdf',$e) }}" target="_blank" class="btn btn-light btn-sm"><i class="fa-solid fa-file-pdf"></i></a>
                        <form method="POST" action="{{ route('imagenes.destroy',$e) }}" style="display:inline" onsubmit="return confirm('¿Eliminar estudio?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="empty"><i class="fa-solid fa-x-ray"></i><p>No hay estudios de imágenes.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $estudios->links() }}
@endsection
