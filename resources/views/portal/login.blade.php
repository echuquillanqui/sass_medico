<!DOCTYPE html>
<html lang="es"><head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal del Paciente · Iniciar sesión</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head><body>
<div class="login">
    <div class="promo"><div class="z">
        <div class="logo"><i class="fa-solid fa-user-shield"></i></div>
        <h2>Portal del<br>Paciente</h2>
        <p>Consulta tus próximas citas, tu historia clínica y tus recibos de pago en un solo lugar.</p>
        <ul>
            <li><i class="fa-regular fa-calendar-check"></i> Tus próximas citas</li>
            <li><i class="fa-solid fa-notes-medical"></i> Tu historia clínica</li>
            <li><i class="fa-solid fa-receipt"></i> Tus pagos y recibos</li>
        </ul>
    </div></div>
    <div class="panel"><div class="box">
        <h1>Hola de nuevo 👋</h1>
        <p class="sub">Ingresa con el correo registrado en tu clínica.</p>
        @if(session('aviso'))<div class="alert ok"><i class="fa-solid fa-circle-info"></i> {{ session('aviso') }}</div>@endif
        @if($errors->any())<div class="alert error"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('portal.login.attempt') }}">
            @csrf
            <div class="field"><label>Correo</label><input type="email" name="email" value="{{ old('email') }}" required autofocus></div>
            <div class="field"><label>Contraseña</label><input type="password" name="password" required></div>
            <div class="remember"><label><input type="checkbox" name="remember"> Recordarme</label></div>
            <button class="btn btn-primary"><i class="fa-solid fa-right-to-bracket"></i> Ingresar</button>
        </form>
        <div class="demo"><b><i class="fa-solid fa-flask"></i> Demo</b>
            Correo: valentina@paciente.test · Contraseña: <b>password</b><br>
            <a href="{{ route('login') }}" style="color:var(--violet)">¿Eres personal de la clínica? Ingresa aquí</a>
        </div>
    </div></div>
</div>
</body></html>
