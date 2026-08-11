@extends('layouts.app')
@section('title', 'Suscripción · '.$empresa->nombre)

@section('content')
    @php $mon = $empresa->moneda ?: 'S/'; $venceAct = $empresa->vence_suscripcion; @endphp
    <div class="page-head">
        <div>
            <h1>Gestionar suscripción</h1>
            <p>Empresa <b>{{ $empresa->nombre }}</b> · Vence actual:
                <b>{{ $venceAct ? $venceAct->format('d/m/Y') : 'sin suscripción' }}</b>
                @if($empresa->estado_suscripcion==='vencida')<span class="pill red">Vencida</span>
                @elseif($empresa->estado_suscripcion==='por_vencer')<span class="pill amber">Por vencer</span>
                @elseif($empresa->estado_suscripcion==='vigente')<span class="pill green">Vigente</span>@endif
            </p>
        </div>
        <a href="{{ route('admin.empresas.edit',$empresa) }}" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Volver a la empresa</a>
    </div>

    @if(session('ok'))<div class="alert ok mb">{{ session('ok') }}</div>@endif
    @if(session('error'))<div class="alert mb" style="background:#fef2f2;border-left:4px solid #ef4444;color:#991b1b">{{ session('error') }}</div>@endif

    <div class="grid g-2e">
        {{-- Nueva suscripción --}}
        <div class="card mb">
            <h3 class="mb"><i class="fa-solid fa-receipt" style="color:var(--violet)"></i> Nueva suscripción</h3>
            @if($planes->isEmpty())
                <p class="muted">No hay planes activos. <a href="{{ route('admin.planes.index') }}">Crea un plan primero</a>.</p>
            @else
            <form method="POST" action="{{ route('admin.suscripcion.store',$empresa) }}">
                @csrf
                <div class="field mb"><label>Plan *</label>
                    <select name="plan_id" id="s_plan" onchange="calc()">
                        @foreach($planes as $p)
                            <option value="{{ $p->id }}" data-precio="{{ $p->precio }}" data-ciclo="{{ $p->ciclo }}" @selected($empresa->plan_id==$p->id)>
                                {{ $p->nombre }} — {{ $mon }} {{ number_format($p->precio,2) }} / {{ $p->ciclo }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-grid mb">
                    <div class="field"><label>Duración *</label><input type="number" name="duracion" id="s_dur" value="1" min="1" oninput="calc()"></div>
                    <div class="field"><label>Unidad *</label>
                        <select name="unidad" id="s_uni" onchange="calc()"><option value="meses">Meses</option><option value="anios">Años</option></select></div>
                    <div class="field"><label>Descuento</label><input type="number" step="0.01" name="descuento" id="s_desc" value="0" min="0" oninput="calc()"></div>
                    <div class="field"><label>Tipo de descuento</label>
                        <select name="tipo_descuento" id="s_tipo" onchange="calc()"><option value="monto">Monto ({{ $mon }})</option><option value="porcentaje">Porcentaje (%)</option></select></div>
                </div>

                <div style="background:#faf5ff;border:1px solid #e9d5ff;border-radius:12px;padding:14px 16px;margin-bottom:14px">
                    <div class="flex between" style="padding:4px 0"><span class="muted">Subtotal</span><b id="r_sub">{{ $mon }} 0.00</b></div>
                    <div class="flex between" style="padding:4px 0;border-bottom:1px dashed #e9d5ff"><span class="muted">Descuento</span><b style="color:#b45309" id="r_desc">- {{ $mon }} 0.00</b></div>
                    <div class="flex between" style="padding:8px 0"><span style="font-weight:600">Total a pagar</span><b style="font-size:18px;color:#6d28d9" id="r_total">{{ $mon }} 0.00</b></div>
                    <div class="flex between" style="padding:4px 0"><span class="muted">Vence el</span><b style="color:#be185d" id="r_vence">—</b></div>
                </div>

                <div class="form-grid mb">
                    <div class="field"><label>Método (referencial)</label>
                        <select name="metodo"><option>Ticket / manual</option><option>Efectivo</option><option>Transferencia</option><option>Tarjeta</option><option>Yape / Plin</option></select></div>
                    <div class="field"><label>Nota</label><input name="nota" placeholder="Referencia…"></div>
                </div>
                <p class="muted" style="font-size:12px;margin-bottom:12px">El monto y la fecha de vencimiento se calculan automáticamente según el plan y la duración. Se registra la suscripción y se genera un <b>ticket</b>.</p>
                <button class="btn btn-primary" style="padding:11px 22px"><i class="fa-solid fa-receipt"></i> Generar suscripción y ticket</button>
            </form>
            @endif
        </div>

        {{-- Info del plan actual --}}
        <div>
            <div class="card mb" style="border-top:3px solid var(--violet)">
                <h3 class="mb"><i class="fa-solid fa-crown" style="color:#f59e0b"></i> Plan actual</h3>
                @if($empresa->planRef)
                    @php $pl = $empresa->planRef; @endphp
                    <div style="font-size:22px;font-weight:800;color:#6d28d9">{{ $pl->nombre }}</div>
                    <div class="muted mb">{{ $mon }} {{ number_format($pl->precio,2) }} / {{ $pl->ciclo }}</div>
                    <p class="muted" style="font-size:12.5px">{{ $pl->descripcion }}</p>
                    <div style="margin-top:10px;font-size:12.5px">
                        <div>Especialidades: <b>{{ $pl->limite_especialidades ?? 'Ilimitadas' }}</b></div>
                        <div>Usuarios: <b>{{ $pl->limite_usuarios ?? 'Ilimitados' }}</b></div>
                    </div>
                @else
                    <p class="muted">Esta empresa aún no tiene un plan asignado. Genera su primera suscripción.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Historial --}}
    <div class="card" style="padding:0">
        <div style="padding:18px 20px 6px"><h3 style="margin:0"><i class="fa-solid fa-clock-rotate-left" style="color:var(--violet)"></i> Historial de suscripciones</h3></div>
        <div style="overflow-x:auto">
            <div class="table-wrap" style="box-shadow:none;border-radius:0;min-width:760px">
                <table>
                    <thead><tr><th>Ticket</th><th>Plan</th><th>Compra</th><th>Vigencia</th><th>Vence</th><th>Descuento</th><th>Total</th><th></th></tr></thead>
                    <tbody>
                    @forelse($historial as $s)
                        <tr>
                            <td><b>{{ $s->ticket ?? '—' }}</b></td>
                            <td>{{ $s->plan_nombre }}</td>
                            <td>{{ $s->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $s->fecha_inicio->format('d/m/Y') }} → {{ $s->fecha_fin->format('d/m/Y') }}</td>
                            <td>{{ $s->fecha_fin->format('d/m/Y') }}</td>
                            <td>{{ $s->descuento>0 ? ($s->tipo_descuento==='porcentaje' ? $s->descuento.'%' : $mon.' '.number_format($s->descuento,2)) : '—' }}</td>
                            <td><b>{{ $mon }} {{ number_format($s->total,2) }}</b></td>
                            <td style="text-align:right"><a href="{{ route('admin.suscripcion.ticket',$s) }}" target="_blank" class="btn btn-light btn-sm"><i class="fa-solid fa-receipt"></i> Ticket</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8"><div class="empty"><i class="fa-solid fa-receipt"></i><p>Aún no hay suscripciones registradas.</p></div></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    (function(){ window.__mon = @json($mon); window.__vence = @json(optional($venceAct)->toDateString()); })();
    function fmt(n){ return window.__mon+' '+(Math.round(n*100)/100).toFixed(2); }
    function calc(){
        var opt=document.getElementById('s_plan'); if(!opt) return;
        var o=opt.options[opt.selectedIndex];
        var precio=parseFloat(o.getAttribute('data-precio'))||0;
        var ciclo=o.getAttribute('data-ciclo');
        var dur=parseInt(document.getElementById('s_dur').value)||0;
        var uni=document.getElementById('s_uni').value;
        var meses=uni==='anios'?dur*12:dur;
        var mensual=ciclo==='anual'?precio/12:precio;
        var subtotal=mensual*meses;
        var desc=parseFloat(document.getElementById('s_desc').value)||0;
        var tipo=document.getElementById('s_tipo').value;
        var descMonto=tipo==='porcentaje'?subtotal*desc/100:desc;
        var total=Math.max(0,subtotal-descMonto);
        document.getElementById('r_sub').textContent=fmt(subtotal);
        document.getElementById('r_desc').textContent='- '+fmt(descMonto);
        document.getElementById('r_total').textContent=fmt(total);
        // Vence
        var hoy=new Date(); hoy.setHours(0,0,0,0);
        var base=window.__vence?new Date(window.__vence+'T00:00:00'):hoy;
        if(base<hoy) base=hoy;
        var fin=new Date(base); fin.setMonth(fin.getMonth()+meses);
        document.getElementById('r_vence').textContent=('0'+fin.getDate()).slice(-2)+'/'+('0'+(fin.getMonth()+1)).slice(-2)+'/'+fin.getFullYear();
    }
    document.addEventListener('DOMContentLoaded', calc);
    </script>
    @endpush
@endsection
