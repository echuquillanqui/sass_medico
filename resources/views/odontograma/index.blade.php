@extends('layouts.app')
@section('title', 'Odontograma')

@section('content')
    <div class="page-head">
        <div class="flex gap">
            <div style="width:56px;height:56px;border-radius:16px;background:#06b6d4;color:#fff;display:grid;place-items:center;font-size:24px">
                <i class="fa-solid fa-tooth"></i>
            </div>
            <div>
                <h1>Odontograma</h1>
                <p>Tablero dental y plan de tratamiento por paciente.</p>
            </div>
        </div>
        <a href="{{ route('pacientes.create') }}" class="btn btn-primary"><i class="fa-solid fa-user-plus"></i> Nuevo paciente</a>
    </div>

    @if(session('ok'))<div class="alert ok mb">{{ session('ok') }}</div>@endif

    <div class="card mb">
        <form method="GET" class="flex gap" style="flex-wrap:wrap">
            <input name="q" value="{{ $buscar }}" placeholder="Buscar por nombre o documento…" style="flex:1;min-width:220px">
            <button class="btn btn-light"><i class="fa-solid fa-magnifying-glass"></i> Buscar</button>
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Paciente</th><th>Documento</th><th>Edad</th>
                    <th>Odontograma</th><th>Última actualización</th><th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($pacientes as $p)
                @php
                    $odo = $p->odontograma;
                    $piezas = $odo && is_array($odo->dientes) ? collect($odo->dientes) : collect();
                    $planPend = $odo && is_array($odo->plan) ? collect($odo->plan)->where('estado','pendiente')->count() : 0;
                @endphp
                <tr>
                    <td><span class="avatar-sm">{{ mb_substr($p->nombres,0,1) }}{{ mb_substr($p->apellidos,0,1) }}</span>{{ $p->nombre_completo }}</td>
                    <td>{{ $p->documento ?? '—' }}</td>
                    <td>{{ $p->edad !== null ? $p->edad.' años' : '—' }}</td>
                    <td>
                        @if($odo && $odo->exists)
                            <span class="pill blue">{{ $piezas->count() }} pieza(s) marcadas</span>
                            @if($planPend)<span class="pill orange">{{ $planPend }} pendiente(s)</span>@endif
                        @else
                            <span class="pill gray">Sin registrar</span>
                        @endif
                    </td>
                    <td>{{ $odo && $odo->exists ? $odo->updated_at->locale('es')->isoFormat('D MMM YYYY') : '—' }}</td>
                    <td style="text-align:right">
                        <a href="{{ route('odontograma.edit',$p) }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-tooth"></i> Abrir</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="empty"><i class="fa-solid fa-tooth"></i><p>No hay pacientes de odontología todavía.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $pacientes->links() }}
@endsection
