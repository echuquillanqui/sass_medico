@extends('portal.layout')
@section('title','Mis pagos')
@section('content')
    @php $mon = $empresa->moneda ?? 'S/'; @endphp
    <h1 style="margin:0 0 16px">Mis pagos</h1>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Fecha</th><th>Concepto</th><th>Método</th><th>Monto</th><th>Estado</th></tr></thead>
            <tbody>
            @forelse($pagos as $p)
                <tr>
                    <td>{{ $p->fecha->format('d/m/Y') }}</td>
                    <td>{{ $p->concepto }}</td>
                    <td>{{ $p->metodo_label }}</td>
                    <td><b>@money($p->monto, null, 2)</b></td>
                    <td>@php $mp=['pagado'=>'green','pendiente'=>'amber','anulado'=>'red'][$p->estado]??'gray'; @endphp<span class="pill {{ $mp }}">{{ ucfirst($p->estado) }}</span></td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="empty"><i class="fa-solid fa-receipt"></i><p>No tienes pagos registrados.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
