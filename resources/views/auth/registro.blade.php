<!DOCTYPE html>
<html lang="es"><head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registra tu clínica · Suite Salud</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head><body style="background:var(--bg)">
    <div style="max-width:760px;margin:0 auto;padding:30px 20px">
        <div style="text-align:center;margin-bottom:22px">
            <div class="logo" style="width:56px;height:56px;border-radius:16px;background:var(--grad);display:inline-grid;place-items:center;font-size:24px;color:#fff"><i class="fa-solid fa-heart-pulse"></i></div>
            <h1 style="margin:12px 0 2px">Registra tu clínica</h1>
            <p class="muted">Crea tu cuenta en la Suite Salud Modular y empieza a gestionar tus especialidades.</p>
        </div>

        @if($errors->any())<div class="alert error"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}</div>@endif

        <form method="POST" action="{{ route('registro.store') }}" class="card">
            @csrf
            <h3 class="mb"><i class="fa-solid fa-hospital" style="color:var(--violet)"></i> Datos de la clínica</h3>
            <div class="form-grid mb">
                <div class="field full"><label>Nombre de la clínica *</label><input name="empresa" value="{{ old('empresa') }}" required></div>
                <div class="field"><label>RUC</label><input name="ruc" value="{{ old('ruc') }}"></div>
                <div class="field"><label>Teléfono</label><input name="telefono" value="{{ old('telefono') }}"></div>
                <div class="field full"><label>Plan *</label>
                    <select name="plan">
                        <option value="basico" @selected(old('plan')=='basico')>Básico</option>
                        <option value="profesional" @selected(old('plan','profesional')=='profesional')>Profesional</option>
                        <option value="enterprise" @selected(old('plan')=='enterprise')>Enterprise</option>
                    </select></div>
            </div>

            <h3 class="mb"><i class="fa-solid fa-layer-group" style="color:var(--pink)"></i> Especialidades a activar *</h3>
            <div class="grid g-3 mb" style="gap:10px">
                @foreach($especialidades as $e)
                    <label class="card pink" style="display:flex;align-items:center;gap:10px;cursor:pointer;padding:12px">
                        <input type="checkbox" name="especialidades[]" value="{{ $e->id }}" @checked(collect(old('especialidades'))->contains($e->id))>
                        <span style="width:34px;height:34px;border-radius:10px;background:{{ $e->color }};color:#fff;display:grid;place-items:center"><i class="fa-solid {{ $e->icono }}"></i></span>
                        <b style="font-size:13px">{{ $e->nombre }}</b>
                    </label>
                @endforeach
            </div>

            <h3 class="mb"><i class="fa-solid fa-user-shield" style="color:var(--info)"></i> Tu cuenta de administrador</h3>
            <div class="form-grid mb">
                <div class="field"><label>Tu nombre *</label><input name="admin_nombre" value="{{ old('admin_nombre') }}" required></div>
                <div class="field"><label>Correo *</label><input type="email" name="email" value="{{ old('email') }}" required></div>
                <div class="field"><label>Contraseña *</label><input type="password" name="password" required></div>
                <div class="field"><label>Confirmar contraseña *</label><input type="password" name="password_confirmation" required></div>
            </div>

            <button class="btn btn-primary" style="width:100%;justify-content:center"><i class="fa-solid fa-circle-check"></i> Crear mi clínica</button>
            <div style="text-align:center;margin-top:16px;font-size:13px">¿Ya tienes cuenta? <a href="{{ route('login') }}" style="color:var(--violet);font-weight:600">Inicia sesión</a></div>
        </form>
    </div>
</body></html>
