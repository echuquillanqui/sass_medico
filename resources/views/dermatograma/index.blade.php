@extends('layouts.app')
@section('title', 'Mapa de lesiones')

@section('content')
    <div class="page-head">
        <div class="flex gap">
            <div style="width:56px;height:56px;border-radius:16px;background:#14b8a6;color:#fff;display:grid;place-items:center;font-size:24px">
                <i class="fa-solid fa-hand-dots"></i>
            </div>
            <div>
                <h1>Mapa de lesiones</h1>
                <p>Ubicación corporal de lesiones dermatológicas y su evolución.</p>
            </div>
        </div>
        <a href="{{ route('pacientes.create') }}" class="btn btn-primary"><i class="fa-solid fa-user-plus"></i> Nuevo paciente</a>
    </div>

    <div class="card mb">
        <form method="GET" class="flex gap" style="flex-wrap:wrap">
            <input name="q" value="{{ $buscar }}" placeholder="Buscar paciente por nombre o documento…" style="flex:1;min-width:220px">
            <button class="btn btn-light"><i class="fa-solid fa-magnifying-glass"></i> Buscar</button>
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Paciente</th><th>Documento</th><th>Edad</th><th>Lesiones</th><th>Actualizado</th><th></th></tr></thead>
            <tbody>
            @forelse($pacientes as $p)
                @php $d = $p->dermatograma; $n = $d && is_array($d->lesiones) ? count($d->lesiones) : 0; @endphp
                <tr>
                    <td><span class="avatar-sm">{{ mb_substr($p->nombres,0,1) }}{{ mb_substr($p->apellidos,0,1) }}</span>{{ $p->nombre_completo }}</td>
                    <td>{{ $p->documento ?? '—' }}</td>
                    <td>{{ $p->edad !== null ? $p->edad.' años' : '—' }}</td>
                    <td>@if($d && $d->exists)<span class="pill teal">{{ $n }} lesión(es)</span>@else<span class="pill gray">Sin registrar</span>@endif</td>
                    <td>{{ $d && $d->exists ? $d->updated_at->locale('es')->isoFormat('D MMM YYYY') : '—' }}</td>
                    <td style="text-align:right">
                        <a href="{{ route('dermatograma.edit',$p) }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-hand-dots"></i> Abrir</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="empty"><i class="fa-solid fa-hand-dots"></i><p>No hay pacientes dermatológicos todavía.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $pacientes->links() }}
@endsection
