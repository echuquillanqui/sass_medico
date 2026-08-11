@extends('layouts.app')
@section('title', 'Laboratorio')

@section('content')
    <div class="page-head">
        <div><h1>Laboratorio clínico</h1><p>Órdenes de examen y resultados.</p></div>
        <a href="{{ route('laboratorio.create') }}" class="btn btn-primary"><i class="fa-solid fa-vial-circle-check"></i> Nueva orden</a>
    </div>

    <div class="flex gap mb" style="flex-wrap:wrap">
        <a href="{{ route('laboratorio.index') }}" class="btn {{ !$estado ? 'btn-primary' : 'btn-ghost' }} btn-sm">Todas</a>
        @foreach(['solicitada'=>'Solicitadas','en_proceso'=>'En proceso','completada'=>'Completadas','entregada'=>'Entregadas'] as $k=>$v)
            <a href="{{ route('laboratorio.index',['estado'=>$k]) }}" class="btn {{ $estado==$k ? 'btn-primary' : 'btn-ghost' }} btn-sm">{{ $v }}</a>
        @endforeach
    </div>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Orden</th><th>Paciente</th><th>Médico</th><th>Fecha</th><th>Exámenes</th><th>Estado</th><th></th></tr></thead>
            <tbody>
            @forelse($ordenes as $o)
                <tr>
                    <td><b>#{{ str_pad($o->id,5,'0',STR_PAD_LEFT) }}</b></td>
                    <td>{{ $o->paciente->nombre_completo ?? '—' }}</td>
                    <td>{{ $o->medico->name ?? '—' }}</td>
                    <td>{{ $o->fecha->format('d/m/Y') }}</td>
                    <td>{{ $o->items->count() }}</td>
                    <td>
                        @php $c=['solicitada'=>'amber','en_proceso'=>'blue','completada'=>'green','entregada'=>'violet'][$o->estado]??'gray'; @endphp
                        <span class="pill {{ $c }}">{{ $o->estado_label }}</span>
                    </td>
                    <td style="text-align:right;white-space:nowrap">
                        <a href="{{ route('laboratorio.show',$o) }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-flask"></i> Resultados</a>
                        <a href="{{ route('laboratorio.pdf',$o) }}" target="_blank" class="btn btn-light btn-sm"><i class="fa-solid fa-file-pdf"></i></a>
                        <form method="POST" action="{{ route('laboratorio.destroy',$o) }}" style="display:inline" onsubmit="return confirm('¿Eliminar orden?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7"><div class="empty"><i class="fa-solid fa-vials"></i><p>No hay órdenes de laboratorio.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $ordenes->links() }}
@endsection
