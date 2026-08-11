@extends('layouts.app')
@section('title', 'Usuarios')

@section('content')
    <div class="page-head">
        <div><h1>Usuarios</h1><p>Equipo de tu clínica: médicos, recepción y administradores.</p></div>
        <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary"><i class="fa-solid fa-user-plus"></i> Nuevo usuario</a>
    </div>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Usuario</th><th>Correo</th><th>Rol</th><th>Especialidad</th><th>Estado</th><th></th></tr></thead>
            <tbody>
            @forelse($usuarios as $u)
                <tr>
                    <td><span class="avatar-sm">{{ $u->initials() }}</span>{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td><span class="pill violet">{{ ucfirst($u->role) }}</span></td>
                    <td>{{ $u->especialidad->nombre ?? '—' }}</td>
                    <td>@if($u->activo)<span class="pill green">Activo</span>@else<span class="pill red">Inactivo</span>@endif</td>
                    <td style="text-align:right;white-space:nowrap">
                        <a href="{{ route('admin.usuarios.edit',$u) }}" class="btn btn-light btn-sm"><i class="fa-solid fa-pen"></i></a>
                        <form method="POST" action="{{ route('admin.usuarios.destroy',$u) }}" style="display:inline" onsubmit="return confirm('¿Eliminar usuario?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="empty"><i class="fa-solid fa-users"></i><p>No hay usuarios registrados.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $usuarios->links() }}
@endsection
