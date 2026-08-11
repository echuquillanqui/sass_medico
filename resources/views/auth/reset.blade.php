<!DOCTYPE html>
<html lang="es"><head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nueva contraseña · Suite Salud</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head><body>
<div class="login">
    <div class="promo"><div class="z">
        <div class="logo"><i class="fa-solid fa-lock"></i></div>
        <h2>Crea tu nueva<br>contraseña</h2>
        <p>Elige una contraseña segura para tu cuenta.</p>
    </div></div>
    <div class="panel"><div class="box">
        <h1>Nueva contraseña</h1>
        @if($errors->any())<div class="alert error"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="field"><label>Correo</label><input type="email" name="email" value="{{ $email ?? old('email') }}" required></div>
            <div class="field"><label>Nueva contraseña</label><input type="password" name="password" required></div>
            <div class="field"><label>Confirmar contraseña</label><input type="password" name="password_confirmation" required></div>
            <button class="btn btn-primary" style="width:100%;justify-content:center;margin-top:8px"><i class="fa-solid fa-check"></i> Actualizar contraseña</button>
        </form>
    </div></div>
</div>
</body></html>
