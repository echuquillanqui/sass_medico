<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión · Suite Salud Modular</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="login">
    <div class="promo">
        <div class="z">
            <div class="logo"><i class="fa-solid fa-heart-pulse"></i></div>
            <h2>Suite Salud<br>Modular SaaS</h2>
            <p>Una sola plataforma para todas las especialidades de tu clínica. Activa solo los módulos que cada cliente necesita.</p>
            <ul>
                <li><i class="fa-solid fa-baby"></i> Pediatría, Ginecología y Odontología</li>
                <li><i class="fa-solid fa-heart-pulse"></i> Cardiología y Psicología</li>
                <li><i class="fa-solid fa-shield-halved"></i> Multi-empresa con roles y permisos</li>
                <li><i class="fa-solid fa-calendar-check"></i> Pacientes, citas e historia clínica</li>
            </ul>
        </div>
    </div>

    <div class="panel">
        <div class="box">
            <h1>Bienvenido 👋</h1>
            <p class="sub">Ingresa tus credenciales para acceder al panel.</p>

            @if(session('ok'))<div class="alert ok"><i class="fa-solid fa-circle-check"></i> {{ session('ok') }}</div>@endif
            @if($errors->any())
                <div class="alert error"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="field">
                    <label>Correo electrónico</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="tucorreo@clinica.com" required autofocus>
                </div>
                <div class="field">
                    <label>Contraseña</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                <div class="remember">
                    <label><input type="checkbox" name="remember"> Recordarme</label>
                    <a href="{{ route('password.request') }}" style="color:var(--violet);font-weight:600">¿Olvidaste tu contraseña?</a>
                </div>
                <button class="btn btn-primary"><i class="fa-solid fa-right-to-bracket"></i> Iniciar sesión</button>
            </form>

            <div class="demo">
                <b><i class="fa-solid fa-flask"></i> Credenciales de prueba (personal)</b>
                SuperAdmin: superadmin@suitesalud.test<br>
                Admin: admin@clinicavida.test<br>
                Médico: medico@clinicavida.test<br>
                Recepción: recepcion@clinicavida.test<br>
                Contraseña para todos: <b>password</b>
            </div>
            <div style="text-align:center;margin-top:16px;font-size:13px">
                ¿Nuevo aquí? <a href="{{ route('registro') }}" style="color:var(--violet);font-weight:600">Registra tu clínica</a>
                <br style="margin-top:4px">
                ¿Eres paciente?
                <a href="{{ route('portal.login') }}" style="color:var(--violet);font-weight:600">Ingresa al Portal del Paciente →</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
