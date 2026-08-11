@extends('layouts.app')
@section('title', 'Mi perfil')

@section('content')
    <div class="page-head"><div><h1>Mi perfil</h1><p>Actualiza tus datos y contraseña.</p></div></div>

    <div class="grid g-2">
        <form method="POST" action="{{ route('perfil.update') }}" class="card">
            @csrf @method('PUT')
            <h3 class="mb">Datos personales</h3>
            <div class="field mb"><label>Nombre</label><input name="name" value="{{ old('name',$usuario->name) }}" required></div>
            <div class="field mb"><label>Correo</label><input type="email" name="email" value="{{ old('email',$usuario->email) }}" required></div>
            <div class="field mb"><label>Teléfono</label><input name="telefono" value="{{ old('telefono',$usuario->telefono) }}"></div>
            <div class="field mb"><label>Título profesional</label><input name="titulo_profesional" value="{{ old('titulo_profesional',$usuario->titulo_profesional) }}" placeholder="Dr., Dra., Lic."></div>
            <div class="field mb"><label>CMP / Colegiatura</label><input name="cmp" value="{{ old('cmp',$usuario->cmp) }}"></div>
            <div class="field mb"><label>Rol</label><input value="{{ ucfirst($usuario->role) }}" disabled style="background:var(--bg-pink)"></div>
            @if($usuario->isMedico())
            <div class="field mb">
                <label>Firma digital</label>
                <div style="border:1.5px solid var(--line);border-radius:12px;padding:8px;background:#fff">
                    <canvas id="firmaCanvas" width="380" height="130" style="width:100%;height:130px;touch-action:none;cursor:crosshair;border-radius:8px;background:#fbfbfe"></canvas>
                    <div class="flex gap mt" style="justify-content:space-between">
                        <button type="button" class="btn btn-light btn-sm" onclick="limpiarFirma()"><i class="fa-solid fa-eraser"></i> Limpiar</button>
                        <small class="muted">Dibuja tu firma; se usará en recetas y certificados.</small>
                    </div>
                </div>
                <input type="hidden" name="firma" id="firmaInput" value="{{ $usuario->firma }}">
                @if($usuario->firma)<img src="{{ $usuario->firma }}" alt="firma actual" style="max-height:60px;margin-top:8px">@endif
            </div>
            @endif
            <button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar</button>
        </form>

        <form method="POST" action="{{ route('perfil.password') }}" class="card">
            @csrf @method('PUT')
            <h3 class="mb">Cambiar contraseña</h3>
            <div class="field mb"><label>Contraseña actual</label><input type="password" name="actual" required>@error('actual')<span class="err">{{ $message }}</span>@enderror</div>
            <div class="field mb"><label>Nueva contraseña</label><input type="password" name="password" required></div>
            <div class="field mb"><label>Confirmar nueva contraseña</label><input type="password" name="password_confirmation" required></div>
            <button class="btn btn-primary"><i class="fa-solid fa-key"></i> Actualizar contraseña</button>
        </form>
    </div>

    @push('scripts')
    <script>
    (function(){
        const c=document.getElementById('firmaCanvas'); if(!c) return;
        const ctx=c.getContext('2d'); let draw=false;
        ctx.lineWidth=2; ctx.lineCap='round'; ctx.strokeStyle='#1e1b4b';
        const input=document.getElementById('firmaInput');
        // precargar firma existente
        if(input.value){ const img=new Image(); img.onload=()=>ctx.drawImage(img,0,0,c.width,c.height); img.src=input.value; }
        function pos(e){ const r=c.getBoundingClientRect(); const t=e.touches?e.touches[0]:e;
            return {x:(t.clientX-r.left)*(c.width/r.width), y:(t.clientY-r.top)*(c.height/r.height)}; }
        function start(e){ draw=true; const p=pos(e); ctx.beginPath(); ctx.moveTo(p.x,p.y); e.preventDefault(); }
        function move(e){ if(!draw) return; const p=pos(e); ctx.lineTo(p.x,p.y); ctx.stroke(); e.preventDefault(); }
        function end(){ if(!draw) return; draw=false; input.value=c.toDataURL('image/png'); }
        c.addEventListener('mousedown',start); c.addEventListener('mousemove',move);
        window.addEventListener('mouseup',end);
        c.addEventListener('touchstart',start); c.addEventListener('touchmove',move); c.addEventListener('touchend',end);
        window.limpiarFirma=function(){ ctx.clearRect(0,0,c.width,c.height); input.value=''; };
    })();
    </script>
    @endpush

@endsection
