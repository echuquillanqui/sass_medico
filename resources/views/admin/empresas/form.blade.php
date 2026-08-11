@extends('layouts.app')
@section('title', $empresa->exists ? 'Editar empresa' : 'Nueva empresa')

@section('content')
    <div class="page-head">
        <div><h1>{{ $empresa->exists ? 'Editar empresa' : 'Nueva empresa' }}</h1>
            <p>Datos del cliente y especialidades que podrá ver en su panel.</p></div>
        <div class="flex gap">
            @if($empresa->exists)
                <a href="{{ route('admin.suscripcion.show',$empresa) }}" class="btn btn-primary"><i class="fa-solid fa-receipt"></i> Gestionar suscripción</a>
            @endif
            <a href="{{ route('admin.empresas.index') }}" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Volver</a>
        </div>
    </div>

    @if($empresa->exists)
        <div class="card mb" style="background:linear-gradient(135deg,#faf5ff,#fdf2f8);border:1px solid #e9d5ff">
            <div class="flex between" style="flex-wrap:wrap;gap:10px;align-items:center">
                <div>
                    <span class="muted" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px">Suscripción</span>
                    <div style="font-size:16px;font-weight:700;color:#6d28d9">
                        {{ $empresa->planRef->nombre ?? 'Sin plan asignado' }}
                        @if($empresa->planRef) · {{ $empresa->moneda ?: 'S/' }} {{ number_format($empresa->planRef->precio,2) }}/{{ $empresa->planRef->ciclo }}@endif
                    </div>
                    <div class="muted" style="font-size:12.5px">
                        @if($empresa->vence_suscripcion)
                            Vence el <b>{{ $empresa->vence_suscripcion->format('d/m/Y') }}</b>
                            @if($empresa->estado_suscripcion==='vencida')<span class="pill red">Vencida</span>
                            @elseif($empresa->estado_suscripcion==='por_vencer')<span class="pill amber">Por vencer ({{ $empresa->dias_restantes }} días)</span>
                            @else<span class="pill green">Vigente ({{ $empresa->dias_restantes }} días)</span>@endif
                        @else
                            Sin vigencia · genera la primera suscripción.
                        @endif
                    </div>
                </div>
                <a href="{{ route('admin.suscripcion.show',$empresa) }}" class="btn btn-primary"><i class="fa-solid fa-gear"></i> Gestionar suscripción</a>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ $empresa->exists ? route('admin.empresas.update',$empresa) : route('admin.empresas.store') }}" class="card">
        @csrf
        @if($empresa->exists) @method('PUT') @endif
        <div class="form-grid">
            <div class="field full"><label>Nombre de la empresa *</label><input name="nombre" value="{{ old('nombre',$empresa->nombre) }}" required>@error('nombre')<span class="err">{{ $message }}</span>@enderror</div>
            <div class="field"><label>RUC</label><input name="ruc" value="{{ old('ruc',$empresa->ruc) }}"></div>
            @if($empresa->exists)
                <div class="field"><label>Plan actual</label>
                    <input value="{{ $empresa->planRef->nombre ?? 'Sin plan' }}" readonly style="background:var(--bg-pink)"></div>
            @else
                <div class="field"><label>Plan inicial</label>
                    <select name="plan_id">
                        <option value="">— Sin plan (asignar luego) —</option>
                        @foreach($planes as $p)
                            <option value="{{ $p->id }}" @selected(old('plan_id')==$p->id)>{{ $p->nombre }} — S/ {{ number_format($p->precio,2) }} / {{ $p->ciclo }}</option>
                        @endforeach
                    </select></div>
            @endif
            <div class="field"><label>Email</label><input type="email" name="email" value="{{ old('email',$empresa->email) }}"></div>
            <div class="field"><label>Teléfono</label><input name="telefono" value="{{ old('telefono',$empresa->telefono) }}"></div>
            <div class="field full"><label>Dirección</label><input name="direccion" value="{{ old('direccion',$empresa->direccion) }}"></div>
            <div class="field"><label>Estado</label>
                <select name="activo">
                    <option value="1" @selected(old('activo',$empresa->activo ?? 1)==1)>Activa</option>
                    <option value="0" @selected(old('activo',$empresa->activo)==='0' || old('activo',$empresa->activo)===0 || old('activo',$empresa->activo)===false)>Inactiva</option>
                </select></div>
        </div>

        <h3 style="margin:24px 0 12px">Especialidades habilitadas</h3>
        <p class="muted mb">Marca las especialidades que esta empresa verá en su menú.</p>
        <div class="grid g-3" style="gap:12px">
            @foreach($especialidades as $e)
                <label class="card pink" style="display:flex;align-items:center;gap:12px;cursor:pointer;padding:14px">
                    <input type="checkbox" name="especialidades[]" value="{{ $e->id }}" @checked(in_array($e->id,$asignadas))>
                    <span style="width:38px;height:38px;border-radius:11px;background:{{ $e->color }};color:#fff;display:grid;place-items:center"><i class="fa-solid {{ $e->icono }}"></i></span>
                    <span><b style="font-size:13.5px">{{ $e->nombre }}</b><br><small class="muted">{{ $e->descripcion }}</small></span>
                </label>
            @endforeach
        </div>

        @unless($empresa->exists)
            <h3 style="margin:24px 0 12px"><i class="fa-solid fa-user-shield" style="color:var(--violet)"></i> Usuario administrador</h3>
            <p class="muted mb">Se crea la cuenta con la que el cliente ingresará a gestionar su clínica.</p>
            <div class="form-grid">
                <div class="field"><label>Nombre del administrador *</label><input name="admin_nombre" value="{{ old('admin_nombre') }}" required>@error('admin_nombre')<span class="err">{{ $message }}</span>@enderror</div>
                <div class="field"><label>Correo de acceso *</label><input type="email" name="admin_email" value="{{ old('admin_email') }}" required>@error('admin_email')<span class="err">{{ $message }}</span>@enderror</div>
                <div class="field"><label>Contraseña *</label><input type="password" name="admin_password" required>@error('admin_password')<span class="err">{{ $message }}</span>@enderror</div>
            </div>
        @endunless

        <div class="mt"><button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar empresa</button></div>
    </form>
@endsection
