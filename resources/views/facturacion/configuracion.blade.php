@extends('layouts.app')
@section('title', 'Facturación Electrónica')

@section('content')
    <div class="page-head"><div><h1>Facturación Electrónica</h1><p>Emisión de comprobantes ante SUNAT (Perú).</p></div></div>

    @if(session('ok'))<div class="alert ok mb">{{ session('ok') }}</div>@endif

    <form method="POST" action="{{ route('admin.facturacion.guardar') }}" id="facForm">
        @csrf

        {{-- Banner de estado --}}
        <div class="fac-banner mb">
            <div class="flex gap" style="align-items:flex-start">
                <div class="fac-ic"><i class="fa-solid fa-file-invoice"></i></div>
                <div style="flex:1">
                    <div style="font-size:19px;font-weight:800">Facturación Electrónica <span style="font-size:14px">🇵🇪 Perú</span></div>
                    <p style="margin:4px 0 0;opacity:.92;font-size:12.5px;max-width:560px">Emisión de comprobantes electrónicos ante <b>SUNAT</b> · UBL 2.1 · Boletas, facturas y notas de crédito.</p>
                </div>
                <div style="text-align:right">
                    <span class="fac-sunat">SUNAT</span>
                    <div style="font-size:10.5px;opacity:.85;margin-top:4px">Comprobantes de Pago Electrónicos</div>
                </div>
            </div>
            <div class="flex between" style="align-items:center;margin-top:14px;flex-wrap:wrap;gap:10px">
                <div class="flex gap" style="flex-wrap:wrap">
                    <span class="fac-chip {{ $estado['habilitada'] ? 'on' : 'off' }}"><i class="fa-solid fa-circle" style="font-size:7px"></i> {{ $estado['habilitada'] ? 'Habilitada' : 'Deshabilitada' }}</span>
                    <span class="fac-chip dark">Driver: {{ $estado['driver'] }}</span>
                    <span class="fac-chip dark">Modo: {{ $estado['entorno'] }}</span>
                    <span class="fac-chip {{ $estado['certificado'] ? 'on' : 'bad' }}"><i class="fa-solid fa-{{ $estado['certificado'] ? 'check' : 'xmark' }}"></i> {{ $estado['certificado'] ? 'Certificado encontrado' : 'Certificado no encontrado' }}</span>
                </div>
                <button formaction="{{ route('admin.facturacion.probar') }}" class="fac-btn-test"><i class="fa-solid fa-bolt"></i> Probar conexión con SUNAT</button>
            </div>
        </div>

        {{-- Resultado de la prueba --}}
        @if($prueba)
            <div class="card mb" style="border-left:4px solid {{ $prueba['listo'] ? '#10b981' : '#f59e0b' }}">
                <h3 class="mb"><i class="fa-solid fa-list-check" style="color:var(--violet)"></i> Resultado de la verificación</h3>
                <p class="muted" style="margin-bottom:10px">{{ $prueba['mensaje'] }}</p>
                @foreach($prueba['checks'] as $chk)
                    <div class="flex gap" style="align-items:flex-start;padding:5px 0;border-bottom:1px solid var(--line)">
                        <span style="color:{{ $chk['ok'] ? '#16a34a' : '#dc2626' }};font-size:15px"><i class="fa-solid fa-{{ $chk['ok'] ? 'circle-check' : 'circle-xmark' }}"></i></span>
                        <div><b style="font-size:13px">{{ $chk['titulo'] }}</b><div class="muted" style="font-size:12px">{{ $chk['detalle'] }}</div></div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Estado y modo --}}
        <div class="card mb">
            <h3 class="mb"><i class="fa-solid fa-bolt" style="color:#f59e0b"></i> Estado y modo</h3>
            <label class="fac-check">
                <input type="hidden" name="habilitada" value="0"><input type="checkbox" name="habilitada" value="1" @checked($config->habilitada)>
                <div><b>Habilitar facturación electrónica</b><div class="muted" style="font-size:12px">Si está desactivada, las ventas no generan comprobante ante SUNAT.</div></div>
            </label>
            <label class="fac-check">
                <input type="hidden" name="emitir_automatico" value="0"><input type="checkbox" name="emitir_automatico" value="1" @checked($config->emitir_automatico)>
                <div><b>Emitir automáticamente al cobrar</b><div class="muted" style="font-size:12px">Cada boleta o factura se emite apenas se registra el pago.</div></div>
            </label>
            <div class="form-grid" style="margin-top:12px">
                <div class="field"><label>Driver de emisión</label>
                    <select name="driver">
                        <option value="ninguno" @selected($config->driver==='ninguno')>Ninguno (no emite, deja pendiente)</option>
                        <option value="greenter" @selected($config->driver==='greenter')>Greenter (emisión real a SUNAT)</option>
                    </select></div>
                <div class="field"><label>Entorno SUNAT</label>
                    <select name="entorno">
                        <option value="beta" @selected($config->entorno==='beta')>Beta (homologación / pruebas)</option>
                        <option value="produccion" @selected($config->entorno==='produccion')>Producción</option>
                    </select></div>
            </div>
        </div>

        {{-- Datos del emisor --}}
        <div class="card mb">
            <h3 class="mb"><i class="fa-solid fa-building" style="color:var(--violet)"></i> Datos del emisor <span class="muted" style="font-weight:400;font-size:12px">· aparecen en el comprobante</span></h3>
            <div class="form-grid">
                <div class="field"><label>RUC *</label><input name="ruc" value="{{ old('ruc',$config->ruc) }}" maxlength="11" placeholder="20000000001"></div>
                <div class="field"><label>Razón social *</label><input name="razon_social" value="{{ old('razon_social',$config->razon_social) }}"></div>
                <div class="field"><label>Nombre comercial</label><input name="nombre_comercial" value="{{ old('nombre_comercial',$config->nombre_comercial) }}"></div>
                <div class="field"><label>Dirección fiscal</label><input name="direccion_fiscal" value="{{ old('direccion_fiscal',$config->direccion_fiscal) }}"></div>
                <div class="field"><label>Ubigeo</label><input name="ubigeo" value="{{ old('ubigeo',$config->ubigeo) }}" maxlength="6" placeholder="150101"></div>
                <div class="field"><label>Departamento</label><input name="departamento" value="{{ old('departamento',$config->departamento) }}" placeholder="LIMA"></div>
                <div class="field"><label>Provincia</label><input name="provincia" value="{{ old('provincia',$config->provincia) }}" placeholder="LIMA"></div>
                <div class="field"><label>Distrito</label><input name="distrito" value="{{ old('distrito',$config->distrito) }}" placeholder="LIMA"></div>
            </div>
        </div>

        {{-- Credenciales SUNAT --}}
        <div class="card mb">
            <h3 class="mb"><i class="fa-solid fa-key" style="color:#f59e0b"></i> Credenciales SUNAT <span class="muted" style="font-weight:400;font-size:12px">· Clave SOL y certificado digital</span></h3>
            <div class="note" style="background:#eff6ff;border-left:4px solid #3b82f6;color:#1e3a8a;padding:9px 12px;border-radius:0 8px 8px 0;font-size:12.5px">
                <i class="fa-solid fa-circle-info"></i> En <b>beta</b> puedes usar RUC <b>20000000001</b> con usuario y clave <b>MODDATOS</b>.
            </div>
            <div class="form-grid" style="margin-top:6px">
                <div class="field"><label>Usuario Clave SOL</label><input name="sol_usuario" value="{{ old('sol_usuario',$config->sol_usuario) }}" placeholder="MODDATOS"></div>
                <div class="field"><label>Clave SOL</label><input type="password" name="sol_clave" placeholder="{{ $config->sol_clave ? '•••••• (guardada)' : 'MODDATOS' }}" autocomplete="new-password"></div>
                <div class="field full"><label>Ruta del certificado (.pem)</label>
                    <input name="certificado_ruta" value="{{ old('certificado_ruta',$config->certificado_ruta) }}" placeholder="C:\ruta\a\certificado.pem">
                    @unless($config->certificadoExiste())
                        @if($config->certificado_ruta)<div style="color:#dc2626;font-size:12px;margin-top:4px"><i class="fa-solid fa-circle-exclamation"></i> No se encontró el certificado en la ruta indicada.</div>@endif
                    @else
                        <div style="color:#16a34a;font-size:12px;margin-top:4px"><i class="fa-solid fa-circle-check"></i> Certificado encontrado.</div>
                    @endunless
                </div>
            </div>
        </div>

        {{-- Series y correlativos --}}
        <div class="card mb">
            <h3 class="mb"><i class="fa-solid fa-hashtag" style="color:var(--pink)"></i> Series e impuestos</h3>
            <div class="form-grid">
                <div class="field"><label>Serie de boleta</label><input name="serie_boleta" value="{{ old('serie_boleta',$config->serie_boleta ?? 'B001') }}" maxlength="4"></div>
                <div class="field"><label>Serie de factura</label><input name="serie_factura" value="{{ old('serie_factura',$config->serie_factura ?? 'F001') }}" maxlength="4"></div>
                <div class="field"><label>Serie nota de factura</label><input name="serie_nota" value="{{ old('serie_nota',$config->serie_nota ?? 'FC01') }}" maxlength="4" placeholder="FC01"></div>
                <div class="field"><label>Serie nota de boleta</label><input name="serie_nota_boleta" value="{{ old('serie_nota_boleta',$config->serie_nota_boleta ?? 'BC01') }}" maxlength="4" placeholder="BC01"></div>
                <div class="field"><label>IGV (%)</label><input type="number" step="0.01" name="igv_porcentaje" value="{{ old('igv_porcentaje',$config->igv_porcentaje ?? 18) }}"></div>
                <div class="field"><label>Afectación del IGV por defecto</label>
                    <select name="afectacion_igv">
                        @foreach(\App\Models\Comprobante::AFECTACIONES as $k => $v)
                            <option value="{{ $k }}" @selected(old('afectacion_igv',$config->afectacion_igv ?? '10')===$k)>{{ $k }} · {{ $v }}</option>
                        @endforeach
                    </select>
                    <div class="muted" style="font-size:11.5px;margin-top:4px">Muchos servicios de salud están <b>exonerados</b> del IGV. Se aplica a los comprobantes nuevos.</div>
                </div>
                <div class="field"><label>Correlativos actuales</label><input value="Boleta {{ $config->correlativo_boleta ?? 0 }} · Factura {{ $config->correlativo_factura ?? 0 }}" readonly style="background:var(--bg-pink)"></div>
            </div>
        </div>

        <div class="flex between" style="flex-wrap:wrap;gap:10px">
            <a href="{{ route('dashboard') }}" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Volver</a>
            <button class="btn btn-primary" style="padding:11px 24px"><i class="fa-solid fa-check"></i> Guardar configuración</button>
        </div>
    </form>

    <style>
    .fac-banner{background:linear-gradient(135deg,#4c1d95,#7c3aed 55%,#ec4899);color:#fff;border-radius:16px;padding:20px 22px;box-shadow:0 8px 24px rgba(124,58,237,.22)}
    .fac-ic{width:52px;height:52px;border-radius:14px;background:rgba(255,255,255,.18);display:grid;place-items:center;font-size:22px;flex:0 0 52px}
    .fac-sunat{display:inline-block;background:rgba(255,255,255,.9);color:#6d28d9;font-weight:800;font-size:12px;letter-spacing:1px;padding:5px 12px;border-radius:8px}
    .fac-chip{display:inline-flex;align-items:center;gap:5px;background:rgba(255,255,255,.16);border-radius:20px;padding:4px 11px;font-size:11.5px;font-weight:600}
    .fac-chip.on{background:rgba(34,197,94,.9);color:#052e16}
    .fac-chip.off{background:rgba(255,255,255,.9);color:#6d28d9}
    .fac-chip.bad{background:rgba(239,68,68,.92)}
    .fac-chip.dark{background:rgba(0,0,0,.22)}
    .fac-btn-test{background:#fff;color:#6d28d9;border:none;font-weight:700;font-size:12.5px;padding:9px 15px;border-radius:11px;cursor:pointer;display:inline-flex;align-items:center;gap:7px}
    .fac-btn-test:hover{filter:brightness(.97)}
    .fac-check{display:flex;gap:12px;align-items:flex-start;border:1px solid var(--line);border-radius:12px;padding:12px 14px;margin-bottom:10px;cursor:pointer}
    .fac-check input[type=checkbox]{width:auto;margin-top:2px;transform:scale(1.15)}
    </style>
@endsection
