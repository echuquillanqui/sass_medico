<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','Portal') · Mi Salud</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body style="background:var(--bg)">
    @php $p = auth('paciente')->user(); @endphp
    <header style="background:var(--navy);color:#fff;padding:16px 30px;display:flex;align-items:center;gap:16px">
        <div class="logo" style="width:40px;height:40px;border-radius:12px;background:var(--grad);display:grid;place-items:center"><i class="fa-solid fa-heart-pulse"></i></div>
        <div><b>Portal del Paciente</b><br><small style="color:#9c95d6">{{ $p->empresa->nombre ?? '' }}</small></div>
        <nav style="margin-left:30px;display:flex;gap:6px">
            <a href="{{ route('portal.dashboard') }}" class="btn btn-sm {{ request()->routeIs('portal.dashboard') ? 'btn-primary' : '' }}" style="color:{{ request()->routeIs('portal.dashboard') ? '#fff' : '#cfc9ef' }}"><i class="fa-solid fa-house"></i> Inicio</a>
            <a href="{{ route('portal.historia') }}" class="btn btn-sm {{ request()->routeIs('portal.historia') ? 'btn-primary' : '' }}" style="color:{{ request()->routeIs('portal.historia') ? '#fff' : '#cfc9ef' }}"><i class="fa-solid fa-notes-medical"></i> Mi historia</a>
            <a href="{{ route('portal.pagos') }}" class="btn btn-sm {{ request()->routeIs('portal.pagos') ? 'btn-primary' : '' }}" style="color:{{ request()->routeIs('portal.pagos') ? '#fff' : '#cfc9ef' }}"><i class="fa-solid fa-receipt"></i> Mis pagos</a>
        </nav>
        <div style="margin-left:auto;display:flex;align-items:center;gap:12px">
            <span style="font-size:13px">{{ $p->nombre_completo ?? '' }}</span>
            <form method="POST" action="{{ route('portal.logout') }}">@csrf<button class="icon-btn" title="Salir"><i class="fa-solid fa-right-from-bracket"></i></button></form>
        </div>
    </header>
    <main style="max-width:1000px;margin:0 auto;padding:30px">
        @if(session('ok'))<div class="alert ok"><i class="fa-solid fa-circle-check"></i> {{ session('ok') }}</div>@endif
        @yield('content')
    </main>
</body>
</html>
