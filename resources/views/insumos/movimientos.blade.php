@extends('layouts.app')
@section('title', 'Movimientos · '.$insumo->nombre)

@section('content')
    <div class="page-head">
        <div><h1>{{ $insumo->nombre }}</h1><p>Stock actual: <b>{{ rtrim(rtrim(number_format($insumo->stock,2),'0'),'.') }} {{ $insumo->unidad }}</b> @if($insumo->bajo_stock)<span class="pill red">Bajo stock</span>@endif</p></div>
        <a href="{{ route('insumos.index') }}" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>

    <div class="grid g-2">
        <div class="card" style="height:fit-content">
            <h3 class="mb">Registrar movimiento</h3>
            <form method="POST" action="{{ route('insumos.movimiento.store',$insumo) }}">
                @csrf
                <div class="field mb"><label>Tipo</label>
                    <select name="tipo">
                        <option value="entrada">Entrada (+)</option>
                        <option value="salida">Salida (−)</option>
                    </select></div>
                <div class="field mb"><label>Cantidad</label><input type="number" step="0.01" name="cantidad" required>@error('cantidad')<span class="err">{{ $message }}</span>@enderror</div>
                <div class="field mb"><label>Motivo</label><input name="motivo" placeholder="Compra, uso en consulta..."></div>
                <div class="field mb"><label>Fecha</label><input type="date" name="fecha" value="{{ now()->toDateString() }}" required></div>
                <button class="btn btn-primary"><i class="fa-solid fa-right-left"></i> Registrar</button>
            </form>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Fecha</th><th>Tipo</th><th>Cantidad</th><th>Motivo</th><th>Usuario</th></tr></thead>
                <tbody>
                @forelse($movs as $m)
                    <tr>
                        <td>{{ $m->fecha->format('d/m/Y') }}</td>
                        <td>@if($m->tipo==='entrada')<span class="pill green">Entrada</span>@else<span class="pill amber">Salida</span>@endif</td>
                        <td><b>{{ $m->tipo==='entrada'?'+':'−' }}{{ rtrim(rtrim(number_format($m->cantidad,2),'0'),'.') }}</b></td>
                        <td>{{ $m->motivo ?? '—' }}</td>
                        <td>{{ $m->user->name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5"><div class="empty"><i class="fa-solid fa-right-left"></i><p>Sin movimientos.</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt">{{ $movs->links() }}</div>
@endsection
