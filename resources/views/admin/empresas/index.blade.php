@extends('layouts.app')
@section('title', 'Empresas')

@section('content')
    <div class="page-head">
        <div><h1>Empresas / Clientes</h1><p>Gestiona las empresas cliente y las especialidades que cada una puede ver.</p></div>
        <a href="{{ route('admin.empresas.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Nueva empresa</a>
    </div>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Empresa</th><th>RUC</th><th>Plan</th><th>Especialidades habilitadas</th><th>Usuarios</th><th>Estado</th><th></th></tr></thead>
            <tbody>
            @forelse($empresas as $e)
                <tr>
                    <td><span class="avatar-sm">{{ mb_substr($e->nombre,0,2) }}</span>{{ $e->nombre }}</td>
                    <td>{{ $e->ruc ?? '—' }}</td>
                    <td>
                        @if($e->planRef)<span class="pill violet">{{ $e->planRef->nombre }}</span>@else<span class="pill gray">Sin plan</span>@endif
                        @if($e->vence_suscripcion)
                            <div class="muted" style="font-size:11px;margin-top:3px">Vence {{ $e->vence_suscripcion->format('d/m/Y') }}
                                @if($e->estado_suscripcion==='vencida')<span class="pill red">Vencida</span>@elseif($e->estado_suscripcion==='por_vencer')<span class="pill amber">Por vencer</span>@endif
                            </div>
                        @endif
                    </td>
                    <td>
                        @forelse($e->especialidadesActivas as $esp)
                            <span class="pill pink" style="margin:2px"><i class="fa-solid {{ $esp->icono }}"></i> {{ $esp->nombre }}</span>
                        @empty <span class="muted">Ninguna</span> @endforelse
                    </td>
                    <td>{{ $e->usuarios_count }}</td>
                    <td>@if($e->activo)<span class="pill green">Activa</span>@else<span class="pill red">Inactiva</span>@endif</td>
                    <td style="text-align:right;white-space:nowrap">
                        <a href="{{ route('admin.suscripcion.show',$e) }}" class="btn btn-light btn-sm" title="Suscripción"><i class="fa-solid fa-receipt"></i></a>
                        <a href="{{ route('admin.empresas.edit',$e) }}" class="btn btn-light btn-sm"><i class="fa-solid fa-pen"></i></a>
                        <form method="POST" action="{{ route('admin.empresas.destroy',$e) }}" style="display:inline" onsubmit="return confirm('¿Eliminar empresa y todos sus datos?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7"><div class="empty"><i class="fa-solid fa-building"></i><p>No hay empresas registradas.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $empresas->links() }}
@endsection
