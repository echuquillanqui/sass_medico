@extends('layouts.app')
@section('title', 'Estados de cuenta')

@section('content')
    @php $mon = auth()->user()->empresa->moneda ?? 'S/'; @endphp
    <div class="page-head">
        <div><h1>Estados de cuenta</h1><p>Pacientes con saldo pendiente.</p></div>
        <a href="{{ route('pagos.index') }}" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Pagos</a>
    </div>

    <div class="grid g-3 mb">
        <div class="card"><div class="cap" style="font-size:12px;color:var(--ink-soft);text-transform:uppercase">Deudores</div><div style="font-size:26px;font-weight:700;margin-top:6px">{{ $deudores->count() }}</div></div>
        <div class="card pink" style="grid-column:span 2"><div class="cap" style="font-size:12px;color:var(--ink-soft);text-transform:uppercase">Deuda total</div><div style="font-size:26px;font-weight:700;margin-top:6px;color:#b45309">@money($totalDeuda, null, 2)</div></div>
    </div>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Paciente</th><th>Pagos pend.</th><th>Desde</th><th>Saldo</th><th></th></tr></thead>
            <tbody>
            @forelse($deudores as $d)
                @php
                    $tel = preg_replace('/[^0-9]/', '', (string) optional($d->paciente)->telefono);
                    $msg = rawurlencode('Hola '.optional($d->paciente)->nombres.', te recordamos que tienes un saldo pendiente de '.$mon.' '.number_format($d->deuda,2).' en '.(auth()->user()->empresa->nombre ?? 'la clínica').'. ¡Gracias!');
                @endphp
                <tr>
                    <td><span class="avatar-sm">{{ mb_substr(optional($d->paciente)->nombres,0,1) }}{{ mb_substr(optional($d->paciente)->apellidos,0,1) }}</span>{{ optional($d->paciente)->nombre_completo ?? '—' }}</td>
                    <td>{{ $d->items }}</td>
                    <td>{{ \Illuminate\Support\Carbon::parse($d->desde)->format('d/m/Y') }}</td>
                    <td><b style="color:#b45309">@money($d->deuda, null, 2)</b></td>
                    <td style="text-align:right;white-space:nowrap">
                        <a href="{{ route('pacientes.show', $d->paciente_id) }}" class="btn btn-light btn-sm"><i class="fa-solid fa-eye"></i></a>
                        @if($tel)<a href="https://wa.me/{{ $tel }}?text={{ $msg }}" target="_blank" class="btn btn-light btn-sm" style="color:#25d366"><i class="fa-brands fa-whatsapp"></i> Recordar</a>@endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="empty"><i class="fa-solid fa-circle-check"></i><p>No hay saldos pendientes. ¡Todo al día!</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
