@extends('layouts.app')
@section('title', 'Inventario')

@section('content')
    @php $mon = auth()->user()->empresa->moneda ?? 'S/'; @endphp
    <div class="page-head">
        <div><h1>Inventario de insumos</h1><p>Stock de medicamentos e insumos de la clínica.</p></div>
        <a href="{{ route('insumos.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Nuevo insumo</a>
    </div>

    <div class="grid g-3 mb">
        <div class="card"><div class="cap" style="font-size:12px;color:var(--ink-soft);text-transform:uppercase">Insumos</div><div style="font-size:26px;font-weight:700;margin-top:6px">{{ $insumos->total() }}</div></div>
        <div class="card {{ $bajoStock ? '' : '' }}" style="{{ $bajoStock ? 'background:#fef2f2' : '' }}">
            <div class="cap" style="font-size:12px;color:var(--ink-soft);text-transform:uppercase">Bajo stock</div>
            <div style="font-size:26px;font-weight:700;margin-top:6px;color:{{ $bajoStock ? '#dc2626' : 'inherit' }}">{{ $bajoStock }}</div>
        </div>
        <div class="card pink"><div class="cap" style="font-size:12px;color:var(--ink-soft);text-transform:uppercase">Valor del stock</div><div style="font-size:26px;font-weight:700;margin-top:6px">@money($valorTotal, null, 2)</div></div>
    </div>

    <form method="GET" class="card mb" style="padding:14px">
        <div class="flex gap">
            <div class="search" style="max-width:none;flex:1"><i class="fa-solid fa-magnifying-glass"></i><input name="q" value="{{ $q }}" placeholder="Buscar insumo..."></div>
            <button class="btn btn-primary">Buscar</button>
        </div>
    </form>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Insumo</th><th>Categoría</th><th>Stock</th><th>Mínimo</th><th>Precio</th><th>Estado</th><th></th></tr></thead>
            <tbody>
            @forelse($insumos as $i)
                <tr>
                    <td><b>{{ $i->nombre }}</b>@if($i->codigo)<br><small class="muted">{{ $i->codigo }}</small>@endif</td>
                    <td>{{ $i->categoria ?? '—' }}</td>
                    <td><b>{{ rtrim(rtrim(number_format($i->stock,2),'0'),'.') }}</b> {{ $i->unidad }}</td>
                    <td>{{ rtrim(rtrim(number_format($i->stock_minimo,2),'0'),'.') }}</td>
                    <td>@money($i->precio, null, 2)</td>
                    <td>@if($i->bajo_stock)<span class="pill red"><i class="fa-solid fa-triangle-exclamation"></i> Bajo</span>@else<span class="pill green">OK</span>@endif</td>
                    <td style="text-align:right;white-space:nowrap">
                        <a href="{{ route('insumos.movimientos',$i) }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-right-left"></i> Movimientos</a>
                        <a href="{{ route('insumos.edit',$i) }}" class="btn btn-light btn-sm"><i class="fa-solid fa-pen"></i></a>
                        <form method="POST" action="{{ route('insumos.destroy',$i) }}" style="display:inline" onsubmit="return confirm('¿Eliminar insumo?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7"><div class="empty"><i class="fa-solid fa-boxes-stacked"></i><p>No hay insumos registrados.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $insumos->links() }}
@endsection
