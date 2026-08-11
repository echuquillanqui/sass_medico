<!DOCTYPE html>
<html lang="es"><head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recuperar contraseña · Suite Salud</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head><body>
<div class="login">
    <div class="promo"><div class="z">
        <div class="logo"><i class="fa-solid fa-key"></i></div>
        <h2>¿Olvidaste tu<br>contraseña?</h2>
        <p>No te preocupes. Ingresa tu correo y te enviaremos un enlace para crear una nueva.</p>
    </div></div>
    <div class="panel"><div class="box">
        <h1>Recuperar acceso</h1>
        <p class="sub">Te enviaremos un enlace de restablecimiento.</p>
        @if(session('ok'))<div class="alert ok"><i class="fa-solid fa-circle-check"></i> {{ session('ok') }}</div>@endif
        @if($errors->any())<div class="alert error"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="field"><label>Correo electrónico</label><input type="email" name="email" value="{{ old('email') }}" required autofocus></div>
            <button class="btn btn-primary" style="width:100%;justify-content:center;margin-top:8px"><i class="fa-solid fa-paper-plane"></i> Enviar enlace</button>
        </form>
        <div style="text-align:center;margin-top:16px;font-size:13px"><a href="{{ route('login') }}" style="color:var(--violet);font-weight:600">← Volver al inicio de sesión</a></div>
    </div></div>
</div>
</body></html>
