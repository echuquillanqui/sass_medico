@extends('layouts.app')
@section('title', 'Mapa de lesiones óseas')

@section('content')
    <div class="page-head">
        <div class="flex gap">
            <div style="width:56px;height:56px;border-radius:16px;background:#f97316;color:#fff;display:grid;place-items:center;font-size:24px">
                <i class="fa-solid fa-bone"></i>
            </div>
            <div>
                <h1>Mapa de lesiones óseas</h1>
                <p>Ubicación de fracturas, esguinces y lesiones musculoesqueléticas.</p>
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
                @php $t = $p->traumatograma; $n = $t && is_array($t->lesiones) ? count($t->lesiones) : 0; @endphp
                <tr>
                    <td><span class="avatar-sm">{{ mb_substr($p->nombres,0,1) }}{{ mb_substr($p->apellidos,0,1) }}</span>{{ $p->nombre_completo }}</td>
                    <td>{{ $p->documento ?? '—' }}</td>
                    <td>{{ $p->edad !== null ? $p->edad.' años' : '—' }}</td>
                    <td>@if($t && $t->exists)<span class="pill orange">{{ $n }} lesión(es)</span>@else<span class="pill gray">Sin registrar</span>@endif</td>
                    <td>{{ $t && $t->exists ? $t->updated_at->locale('es')->isoFormat('D MMM YYYY') : '—' }}</td>
                    <td style="text-align:right">
                        <a href="{{ route('traumatograma.edit',$p) }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-bone"></i> Abrir</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="empty"><i class="fa-solid fa-bone"></i><p>No hay pacientes traumatológicos todavía.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $pacientes->links() }}
@endsection
