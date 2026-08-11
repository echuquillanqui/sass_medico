<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php $prefs = auth()->user()->preferencias ?? []; @endphp
    <script>
    (function(){
        try {
            var pref = @json($prefs['tema'] ?? 'auto');
            var ls = localStorage.getItem('tema');
            var dark = ls ? (ls === 'dark') : (pref === 'oscuro');
            if (dark) document.documentElement.setAttribute('data-theme','dark');
            var dens = @json($prefs['densidad'] ?? 'comodo');
            if (dens === 'compacto') document.documentElement.setAttribute('data-densidad','compacto');
        } catch(e){}
    })();
    </script>
    <title>@yield('title', 'Panel') · Suite Salud Modular</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js" defer></script>
    @php $brand = optional(auth()->user()->empresa)->color_primario; @endphp
    @if($brand)
    <style>
        :root{
            --violet: {{ $brand }};
            --violet-2: {{ $brand }};
            --grad: linear-gradient(135deg, {{ $brand }} 0%, #ec4899 100%);
        }
    </style>
    @endif
</head>
<body>
<div class="app">
    @include('layouts.sidebar')

    <div class="main">
        <header class="topbar">
            <form class="search" method="GET" action="{{ route('buscador.index') }}">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar paciente, cita o módulo...">
            </form>
            <div class="icons">
                <button class="icon-btn" onclick="toggleTema()" title="Cambiar tema" type="button"><i class="fa-solid fa-moon" id="temaIcon"></i></button>
                @php
                    $noLeidas = auth()->user()->empresa_id
                        ? \App\Models\Notificacion::where('empresa_id', auth()->user()->empresa_id)->where('leido', false)->count()
                        : 0;
                @endphp
                <a href="{{ route('notificaciones.index') }}" class="icon-btn" title="Notificaciones">
                    <i class="fa-regular fa-bell"></i>
                    @if($noLeidas)
                        <span class="badge" style="width:auto;height:auto;min-width:16px;padding:1px 5px;border-radius:9px;top:6px;right:5px;font-size:10px;color:#fff;display:grid;place-items:center;font-weight:700">{{ $noLeidas > 9 ? '9+' : $noLeidas }}</span>
                    @endif
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="icon-btn" title="Cerrar sesión"><i class="fa-solid fa-right-from-bracket"></i></button>
                </form>
            </div>
        </header>

        <main class="content">
            @if(session('ok'))
                <div class="alert ok"><i class="fa-solid fa-circle-check"></i> {{ session('ok') }}</div>
            @endif
            @if($errors->any() && !request()->is('login'))
                <div class="alert error"><i class="fa-solid fa-triangle-exclamation"></i> Revisa los datos del formulario.</div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
<script>
function toggleTema(){
    const dark = document.documentElement.getAttribute('data-theme') === 'dark';
    if (dark) { document.documentElement.removeAttribute('data-theme'); try{localStorage.setItem('tema','light');}catch(e){} }
    else { document.documentElement.setAttribute('data-theme','dark'); try{localStorage.setItem('tema','dark');}catch(e){} }
    syncTemaIcon();
}
function syncTemaIcon(){
    const i = document.getElementById('temaIcon'); if(!i) return;
    const dark = document.documentElement.getAttribute('data-theme') === 'dark';
    i.className = dark ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
}
syncTemaIcon();
</script>
</body>
</html>
