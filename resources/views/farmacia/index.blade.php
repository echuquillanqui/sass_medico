@extends('layouts.app')
@section('title', 'Farmacia')

@section('content')
    @php $mon = auth()->user()->empresa->moneda ?? 'S/'; @endphp
    <div class="page-head">
        <div><h1>Farmacia / Dispensación</h1><p>Entrega de medicamentos con descuento de inventario.</p></div>
        <a href="{{ route('farmacia.create') }}" class="btn btn-primary"><i class="fa-solid fa-pills"></i> Nueva dispensación</a>
    </div>

    <div class="grid g-3 mb">
        <div class="card pink"><div class="cap" style="font-size:12px;color:var(--ink-soft);text-transform:uppercase">Total dispensado</div><div style="font-size:26px;font-weight:700;margin-top:6px">@money($total, null, 2)</div></div>
        <form method="GET" class="card" style="grid-column:span 2;display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap">
            <div class="field"><label>Desde</label><input type="date" name="desde" value="{{ $desde }}"></div>
            <div class="field"><label>Hasta</label><input type="date" name="hasta" value="{{ $hasta }}"></div>
            <button class="btn btn-primary btn-sm">Filtrar</button>
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead><tr><th>N°</th><th>Fecha</th><th>Paciente</th><th>Ítems</th><th>Total</th><th></th></tr></thead>
            <tbody>
            @forelse($dispensaciones as $d)
                <tr>
                    <td><b>#{{ str_pad($d->id,5,'0',STR_PAD_LEFT) }}</b></td>
                    <td>{{ $d->fecha->format('d/m/Y') }}</td>
                    <td>{{ $d->paciente->nombre_completo ?? 'Venta libre' }}</td>
                    <td>{{ $d->items->count() }}</td>
                    <td><b>@money($d->total, null, 2)</b></td>
                    <td style="text-align:right;white-space:nowrap">
                        <a href="{{ route('farmacia.comprobante',$d) }}" target="_blank" class="btn btn-light btn-sm"><i class="fa-solid fa-receipt"></i> Comprobante</a>
                        <form method="POST" action="{{ route('farmacia.destroy',$d) }}" style="display:inline" onsubmit="return confirm('¿Anular y reponer stock?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fa-solid fa-rotate-left"></i></button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="empty"><i class="fa-solid fa-pills"></i><p>No hay dispensaciones en el periodo.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $dispensaciones->links() }}
@endsection
