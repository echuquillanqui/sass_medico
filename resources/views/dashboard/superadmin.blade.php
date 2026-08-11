@extends('layouts.app')
@section('title', 'Panel de Plataforma')

@section('content')
    <div class="page-head">
        <div>
            <h1>Panel de Plataforma</h1>
            <p>Vista global del SaaS · gestiona empresas cliente y sus módulos.</p>
        </div>
        <a href="{{ route('admin.empresas.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Nueva empresa</a>
    </div>

    <div class="grid g-4 mb">
        @php
            $kpis = [
                ['Empresas', $totalEmpresas, 'fa-building', 'violet'],
                ['Activas', $empresasActivas, 'fa-circle-check', 'green'],
                ['Usuarios', $totalUsuarios, 'fa-users', 'pink'],
                ['Pacientes', $totalPacientes, 'fa-user-injured', 'blue'],
            ];
        @endphp
        @foreach($kpis as $k)
            <div class="card">
                <div class="flex between">
                    <div>
                        <div class="cap" style="color:var(--ink-soft);font-size:12px;text-transform:uppercase;letter-spacing:.5px">{{ $k[0] }}</div>
                        <div style="font-size:30px;font-weight:700;margin-top:6px">{{ $k[1] }}</div>
                    </div>
                    <div style="width:52px;height:52px;border-radius:16px;background:var(--grad);color:#fff;display:grid;place-items:center;font-size:20px">
                        <i class="fa-solid {{ $k[2] }}"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card" style="padding:0">
        <div class="flex between" style="padding:20px 22px 12px">
            <h3 style="margin:0">Empresas cliente</h3>
            <a href="{{ route('admin.empresas.index') }}" class="btn btn-light btn-sm">Administrar</a>
        </div>
        <div class="table-wrap" style="box-shadow:none;border-radius:0">
            <table>
                <thead><tr><th>Empresa</th><th>Plan</th><th>Especialidades</th><th>Usuarios</th><th>Pacientes</th><th>Estado</th></tr></thead>
                <tbody>
                @forelse($empresas as $e)
                    <tr>
                        <td><span class="avatar-sm">{{ mb_substr($e->nombre,0,2) }}</span>{{ $e->nombre }}</td>
                        <td><span class="pill violet">{{ ucfirst($e->plan) }}</span></td>
                        <td>
                            @foreach($e->especialidadesActivas as $esp)
                                <span class="pill pink" style="margin:2px"><i class="fa-solid {{ $esp->icono }}"></i> {{ $esp->nombre }}</span>
                            @endforeach
                        </td>
                        <td>{{ $e->usuarios_count }}</td>
                        <td>{{ $e->pacientes_count }}</td>
                        <td>@if($e->activo)<span class="pill green">Activa</span>@else<span class="pill red">Inactiva</span>@endif</td>
                    </tr>
                @empty
                    <tr><td colspan="6"><div class="empty"><i class="fa-solid fa-building"></i><p>Aún no hay empresas registradas.</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
