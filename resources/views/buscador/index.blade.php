@extends('layouts.app')
@section('title', 'Búsqueda')
@section('content')
    <div class="page-head"><div><h1>Resultados de búsqueda</h1><p>Para: <b>{{ $q }}</b></p></div></div>

    <h3 class="mb">Pacientes ({{ $pacientes->count() }})</h3>
    <div class="table-wrap mb">
        <table>
            <thead><tr><th>Paciente</th><th>Documento</th><th>Teléfono</th><th></th></tr></thead>
            <tbody>
            @forelse($pacientes as $p)
                <tr>
                    <td><span class="avatar-sm">{{ mb_substr($p->nombres,0,1) }}{{ mb_substr($p->apellidos,0,1) }}</span>{{ $p->nombre_completo }}</td>
                    <td>{{ $p->documento ?? '—' }}</td>
                    <td>{{ $p->telefono ?? '—' }}</td>
                    <td style="text-align:right"><a href="{{ route('pacientes.show',$p) }}" class="btn btn-light btn-sm"><i class="fa-solid fa-eye"></i> Ver</a></td>
                </tr>
            @empty
                <tr><td colspan="4"><div class="empty"><i class="fa-solid fa-user-slash"></i><p>Sin pacientes que coincidan.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <h3 class="mb">Citas ({{ $citas->count() }})</h3>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Paciente</th><th>Especialidad</th><th>Fecha</th><th>Estado</th><th></th></tr></thead>
            <tbody>
            @forelse($citas as $c)
                <tr>
                    <td>{{ $c->paciente->nombre_completo }}</td>
                    <td>{{ $c->especialidad->nombre ?? '—' }}</td>
                    <td>{{ $c->fecha->format('d/m/Y') }} {{ \Illuminate\Support\Str::of($c->hora)->substr(0,5) }}</td>
                    <td>@include('citas.estado', ['estado' => $c->estado])</td>
                    <td style="text-align:right"><a href="{{ route('citas.edit',$c) }}" class="btn btn-light btn-sm"><i class="fa-solid fa-pen"></i></a></td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="empty"><i class="fa-regular fa-calendar-xmark"></i><p>Sin citas que coincidan.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
