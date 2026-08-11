@extends('layouts.app')
@section('title', 'Bitácora')

@section('content')
    <div class="page-head"><div><h1>Bitácora de actividad</h1><p>Registro de acciones del equipo.</p></div></div>

    <form method="GET" class="card mb" style="padding:14px">
        <div class="flex gap" style="flex-wrap:wrap">
            <div class="field"><label>Módulo</label>
                <select name="modelo" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    @foreach($modelos as $m)<option value="{{ $m }}" @selected(request('modelo')==$m)>{{ $m }}</option>@endforeach
                </select></div>
            <div class="field"><label>Acción</label>
                <select name="accion" onchange="this.form.submit()">
                    <option value="">Todas</option>
                    @foreach(['creó','actualizó','eliminó'] as $a)<option value="{{ $a }}" @selected(request('accion')==$a)>{{ ucfirst($a) }}</option>@endforeach
                </select></div>
        </div>
    </form>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Fecha</th><th>Usuario</th><th>Acción</th><th>Registro</th><th>IP</th></tr></thead>
            <tbody>
            @forelse($registros as $r)
                <tr>
                    <td>{{ $r->created_at->format('d/m/Y H:i') }}</td>
                    <td><span class="avatar-sm">{{ mb_substr($r->user_nombre,0,2) }}</span>{{ $r->user_nombre }}</td>
                    <td>
                        @php $c=['creó'=>'green','actualizó'=>'amber','eliminó'=>'red'][$r->accion]??'gray'; @endphp
                        <span class="pill {{ $c }}">{{ ucfirst($r->accion) }}</span>
                    </td>
                    <td>{{ $r->descripcion }}</td>
                    <td class="muted">{{ $r->ip ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="empty"><i class="fa-solid fa-clock-rotate-left"></i><p>Sin actividad registrada.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $registros->links() }}
@endsection
