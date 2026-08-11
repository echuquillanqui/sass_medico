@extends('layouts.app')
@section('title', 'Pagos')

@section('content')
    @php $mon = auth()->user()->empresa->moneda ?? 'S/'; @endphp
    <div class="page-head">
        <div><h1>Pagos y facturación</h1><p>Del {{ \Illuminate\Support\Carbon::parse($desde)->format('d/m/Y') }} al {{ \Illuminate\Support\Carbon::parse($hasta)->format('d/m/Y') }}</p></div>
        <div class="flex gap"><a href="{{ route('pagos.estados') }}" class="btn btn-light"><i class="fa-solid fa-file-invoice-dollar"></i> Estados de cuenta</a>
            <a href="{{ route('pagos.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Registrar pago</a></div>
    </div>

    <div class="grid g-3 mb">
        <div class="card pink"><div class="cap" style="color:var(--ink-soft);text-transform:uppercase;font-size:12px">Cobrado</div><div style="font-size:26px;font-weight:700;margin-top:6px;color:#15803d">@money($total, null, 2)</div></div>
        <div class="card"><div class="cap" style="color:var(--ink-soft);text-transform:uppercase;font-size:12px">Pendiente</div><div style="font-size:26px;font-weight:700;margin-top:6px;color:#b45309">@money($pendiente, null, 2)</div></div>
        <form method="GET" class="card" style="display:flex;gap:8px;align-items:flex-end;flex-wrap:wrap">
            <div class="field"><label>Desde</label><input type="date" name="desde" value="{{ $desde }}"></div>
            <div class="field"><label>Hasta</label><input type="date" name="hasta" value="{{ $hasta }}"></div>
            <button class="btn btn-primary btn-sm">Filtrar</button>
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Fecha</th><th>Paciente</th><th>Concepto</th><th>Método</th><th>Monto</th><th>Estado</th><th></th></tr></thead>
            <tbody>
            @forelse($pagos as $p)
                <tr>
                    <td>{{ $p->fecha->format('d/m/Y') }}</td>
                    <td>{{ $p->paciente->nombre_completo ?? '—' }}</td>
                    <td>{{ $p->concepto }}</td>
                    <td>{{ $p->metodo_label }}</td>
                    <td><b>@money($p->monto, null, 2)</b></td>
                    <td>
                        @php $mp=['pagado'=>'green','pendiente'=>'amber','anulado'=>'red'][$p->estado]??'gray'; @endphp
                        <span class="pill {{ $mp }}">{{ ucfirst($p->estado) }}</span>
                    </td>
                    <td style="text-align:right;white-space:nowrap">
                        <a href="{{ route('pagos.recibo',$p) }}" target="_blank" class="btn btn-light btn-sm"><i class="fa-solid fa-receipt"></i></a>
                        <a href="{{ route('pagos.edit',$p) }}" class="btn btn-light btn-sm"><i class="fa-solid fa-pen"></i></a>
                        <form method="POST" action="{{ route('pagos.destroy',$p) }}" style="display:inline" onsubmit="return confirm('¿Eliminar pago?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7"><div class="empty"><i class="fa-solid fa-receipt"></i><p>No hay pagos en este periodo.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $pagos->links() }}
@endsection
