@extends('layouts.app')
@section('title', 'Configuración de la empresa')

@section('content')
    <div class="page-head">
        <div class="flex gap">
            <div style="width:56px;height:56px;border-radius:16px;background:{{ $empresa->color_primario ?? '#7c3aed' }};color:#fff;display:grid;place-items:center;font-size:24px"><i class="fa-solid fa-gear"></i></div>
            <div><h1>Configuración de la empresa</h1><p>Datos, logo, moneda y formato de {{ $empresa->nombre }}.</p></div>
        </div>
    </div>

    @if(session('ok'))<div class="alert ok mb">{{ session('ok') }}</div>@endif
    @if(session('error'))<div class="alert" style="background:#fef2f2;border-left:4px solid #ef4444;color:#991b1b">{{ session('error') }}</div>@endif

    <form method="POST" action="{{ route('admin.empresa.update') }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="grid g-2e">
            {{-- Datos de la empresa --}}
            <div class="card mb">
                <h3 class="mb"><i class="fa-solid fa-building" style="color:var(--violet)"></i> Datos de la empresa</h3>
                <div class="form-grid">
                    <div class="field full"><label>Nombre / Razón social *</label><input name="nombre" value="{{ old('nombre',$empresa->nombre) }}" required></div>
                    <div class="field"><label>RUC / Identificación</label><input name="ruc" value="{{ old('ruc',$empresa->ruc) }}"></div>
                    <div class="field"><label>Teléfono</label><input name="telefono" value="{{ old('telefono',$empresa->telefono) }}"></div>
                    <div class="field"><label>Email</label><input type="email" name="email" value="{{ old('email',$empresa->email) }}"></div>
                    <div class="field"><label>Sitio web</label><input name="sitio_web" value="{{ old('sitio_web',$empresa->sitio_web) }}"></div>
                    <div class="field full"><label>Dirección</label><input name="direccion" value="{{ old('direccion',$empresa->direccion) }}"></div>
                </div>
            </div>

            {{-- Logo --}}
            <div class="card mb">
                <h3 class="mb"><i class="fa-solid fa-image" style="color:var(--pink)"></i> Logo de la empresa</h3>
                <div class="flex gap" style="align-items:center">
                    <div style="width:96px;height:96px;border-radius:14px;border:1.5px dashed var(--line);display:grid;place-items:center;overflow:hidden;background:#faf9ff;flex:0 0 96px">
                        @if($empresa->logo)
                            <img id="logoImg" src="{{ asset('storage/'.$empresa->logo) }}" style="max-width:100%;max-height:100%">
                        @else
                            <i id="logoIcon" class="fa-solid fa-hospital" style="font-size:30px;color:#c4b5fd"></i><img id="logoImg" style="display:none;max-width:100%;max-height:100%">
                        @endif
                    </div>
                    <div style="flex:1">
                        <label>Subir logo</label>
                        <input type="file" name="logo" accept="image/*" onchange="previewLogo(this)">
                        <p class="muted" style="font-size:12px;margin-top:6px">PNG o JPG, máx. 2 MB. Se muestra en el menú y en los documentos PDF.</p>
                    </div>
                </div>
                <div class="field" style="margin-top:12px"><label>Color primario (branding)</label>
                    <input type="color" name="color_primario" value="{{ old('color_primario',$empresa->color_primario ?? '#7c3aed') }}" style="height:44px;width:100%"></div>
            </div>
        </div>

        {{-- Moneda y formato numérico --}}
        <div class="card mb">
            <h3 class="mb"><i class="fa-solid fa-coins" style="color:#f59e0b"></i> Moneda y formato de números</h3>
            <div class="form-grid">
                <div class="field"><label>Símbolo de moneda</label>
                    <input name="moneda" id="f_mon" value="{{ old('moneda',$empresa->moneda ?? 'S/') }}" oninput="previewMoney()" placeholder="S/"></div>
                <div class="field"><label>Posición del símbolo</label>
                    <select name="moneda_posicion" id="f_pos" onchange="previewMoney()">
                        <option value="antes" @selected(($empresa->moneda_posicion ?? 'antes')==='antes')>Antes (S/ 1500)</option>
                        <option value="despues" @selected(($empresa->moneda_posicion ?? '')==='despues')>Después (1500 S/)</option>
                    </select></div>
                <div class="field"><label>Separador de miles</label>
                    <select name="separador_miles" id="f_mil" onchange="previewMoney()">
                        <option value="," @selected(($empresa->separador_miles ?? ',')===',')>Coma (1,500)</option>
                        <option value="." @selected(($empresa->separador_miles ?? '')==='.')>Punto (1.500)</option>
                        <option value=" " @selected(($empresa->separador_miles ?? '')===' ')>Espacio (1 500)</option>
                    </select></div>
                <div class="field"><label>Separador de decimales</label>
                    <select name="separador_decimal" id="f_dec" onchange="previewMoney()">
                        <option value="." @selected(($empresa->separador_decimal ?? '.')==='.')>Punto (0.50)</option>
                        <option value="," @selected(($empresa->separador_decimal ?? '')===',')>Coma (0,50)</option>
                    </select></div>
                <div class="field"><label>N.º de decimales</label>
                    <select name="decimales" id="f_num" onchange="previewMoney()">
                        @for($i=0;$i<=4;$i++)<option value="{{ $i }}" @selected((int)($empresa->decimales ?? 2)===$i)>{{ $i }}</option>@endfor
                    </select></div>
            </div>
            <div style="margin-top:12px;background:linear-gradient(135deg,#faf5ff,#fdf2f8);border:1px solid #e9d5ff;border-radius:10px;padding:14px 16px">
                <span class="muted" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px">Vista previa</span>
                <div style="font-size:22px;font-weight:700;color:#6d28d9;margin-top:4px" id="moneyPreview">S/ 1,234.50</div>
                <div class="muted" style="font-size:12px">Así se verán los montos en recibos, reportes y dashboard.</div>
            </div>
        </div>

        {{-- Horarios de atención --}}
        <div class="card mb">
            <h3 class="mb"><i class="fa-solid fa-clock" style="color:var(--info)"></i> Horarios de atención</h3>
            <div class="form-grid">
                <div class="field"><label>Horario inicio</label><input type="time" name="horario_inicio" value="{{ old('horario_inicio',$empresa->horario_inicio) }}"></div>
                <div class="field"><label>Horario fin</label><input type="time" name="horario_fin" value="{{ old('horario_fin',$empresa->horario_fin) }}"></div>
                <div class="field full"><label>Días de atención</label><input name="dias_atencion" value="{{ old('dias_atencion',$empresa->dias_atencion) }}" placeholder="Lun a Sáb"></div>
            </div>
        </div>

        <button class="btn btn-primary" style="padding:12px 28px"><i class="fa-solid fa-floppy-disk"></i> Guardar configuración</button>
    </form>

    @push('scripts')
    <script>
    function previewLogo(input){
        if(!input.files || !input.files[0]) return;
        const r=new FileReader();
        r.onload=function(e){ var img=document.getElementById('logoImg'); img.src=e.target.result; img.style.display='block'; var ic=document.getElementById('logoIcon'); if(ic) ic.style.display='none'; };
        r.readAsDataURL(input.files[0]);
    }
    function agrupar(entero, sep){
        var out='', c=0;
        for(var i=entero.length-1;i>=0;i--){ out=entero[i]+out; c++; if(c%3===0 && i>0){ out=sep+out; } }
        return out;
    }
    function previewMoney(){
        var mon=document.getElementById('f_mon').value||'S/';
        var pos=document.getElementById('f_pos').value;
        var mil=document.getElementById('f_mil').value;
        var dec=document.getElementById('f_dec').value;
        var nd=parseInt(document.getElementById('f_num').value);
        var n=(1234.5).toFixed(nd);
        var parts=n.split('.');
        var ent=agrupar(parts[0], mil);
        var out=ent+(nd>0?(dec+parts[1]):'');
        document.getElementById('moneyPreview').textContent = pos==='despues' ? (out+' '+mon) : (mon+' '+out);
    }
    document.addEventListener('DOMContentLoaded', previewMoney);
    </script>
    @endpush
@endsection
