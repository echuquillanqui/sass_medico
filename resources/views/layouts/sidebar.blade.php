@php
    $u = auth()->user();
    $empresa = $u->empresa;
    $modulos = $u->isSuperAdmin()
        ? collect()
        : ($empresa?->especialidadesActivas()->get() ?? collect());
@endphp
<aside class="sidebar">
    <button class="menu-toggle" type="button" aria-label="Menú" onclick="this.closest('.sidebar').classList.toggle('open')"><i class="fa-solid fa-bars"></i></button>
    <div class="brand">
        <div class="logo"><i class="fa-solid fa-heart-pulse"></i></div>
        <div>
            <b>Suite Salud</b>
            <small>{{ $empresa?->nombre ?? 'Plataforma SaaS' }}</small>
        </div>
    </div>

    <div class="profile">
        <div class="avatar">{{ $u->initials() }}</div>
        <div class="name">{{ $u->name }}</div>
        <div class="role">{{ $u->role === 'medico' && $u->especialidad ? 'Médico · '.$u->especialidad->nombre : $u->role }}</div>
    </div>

    <nav class="nav">
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge-high"></i> Dashboard
        </a>

        @unless($u->isSuperAdmin())
            <div class="label">Gestión clínica</div>
            <a href="{{ route('pacientes.index') }}" class="{{ request()->routeIs('pacientes.*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-injured"></i> Pacientes
            </a>
            <a href="{{ route('citas.index') }}" class="{{ request()->routeIs('citas.*') ? 'active' : '' }}">
                <i class="fa-regular fa-calendar-check"></i> Citas
            </a>
            <a href="{{ route('agenda.index') }}" class="{{ request()->routeIs('agenda.*') ? 'active' : '' }}">
                <i class="fa-regular fa-calendar-days"></i> Agenda
            </a>
            <a href="{{ route('cola.index') }}" class="{{ request()->routeIs('cola.*') ? 'active' : '' }}">
                <i class="fa-solid fa-chair"></i> Sala de espera
            </a>
            <a href="{{ route('triaje.index') }}" class="{{ request()->routeIs('triaje.*') ? 'active' : '' }}">
                <i class="fa-solid fa-truck-medical"></i> Emergencias
            </a>
            <a href="{{ route('pagos.index') }}" class="{{ request()->routeIs('pagos.*') ? 'active' : '' }}">
                <i class="fa-solid fa-money-bill-wave"></i> Pagos
            </a>
            <a href="{{ route('comprobantes.index') }}" class="{{ request()->routeIs('comprobantes.*') ? 'active' : '' }}">
                <i class="fa-solid fa-file-invoice"></i> Comprobantes
            </a>
            <a href="{{ route('resumenes.index') }}" class="{{ request()->routeIs('resumenes.*') ? 'active' : '' }}">
                <i class="fa-solid fa-layer-group"></i> Resumen diario
            </a>
            <a href="{{ route('insumos.index') }}" class="{{ request()->routeIs('insumos.*') ? 'active' : '' }}">
                <i class="fa-solid fa-boxes-stacked"></i> Inventario
            </a>
            <a href="{{ route('farmacia.index') }}" class="{{ request()->routeIs('farmacia.*') ? 'active' : '' }}">
                <i class="fa-solid fa-pills"></i> Farmacia
            </a>
            <a href="{{ route('bancosangre.index') }}" class="{{ request()->routeIs('bancosangre.*') ? 'active' : '' }}">
                <i class="fa-solid fa-droplet"></i> Banco de sangre
            </a>
            <a href="{{ route('laboratorio.index') }}" class="{{ request()->routeIs('laboratorio.*') ? 'active' : '' }}">
                <i class="fa-solid fa-flask-vial"></i> Laboratorio
            </a>
            <a href="{{ route('hospitalizacion.index') }}" class="{{ request()->routeIs('hospitalizacion.*') ? 'active' : '' }}">
                <i class="fa-solid fa-bed-pulse"></i> Hospitalización
            </a>
            <a href="{{ route('imagenes.index') }}" class="{{ request()->routeIs('imagenes.*') ? 'active' : '' }}">
                <i class="fa-solid fa-x-ray"></i> Imágenes
            </a>

            @if($modulos->isNotEmpty())
                <div class="label">Especialidades</div>
                @foreach($modulos as $m)
                    <a href="{{ route('modulo.show', $m->slug) }}"
                       class="{{ request()->is('modulo/'.$m->slug) ? 'active' : '' }}">
                        <i class="fa-solid {{ $m->icono }}"></i> {{ $m->nombre }}
                    </a>
                @endforeach
            @endif

            <div class="label">Análisis</div>
            <a href="{{ route('reportes.index') }}" class="{{ request()->routeIs('reportes.*') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line"></i> Reportes
            </a>
        @endunless

        @if($u->isAdmin())
            <div class="label">Administración</div>
            <a href="{{ route('admin.usuarios.index') }}" class="{{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
                <i class="fa-solid fa-users-gear"></i> Usuarios
            </a>
            <a href="{{ route('admin.horarios.index') }}" class="{{ request()->routeIs('admin.horarios.*') ? 'active' : '' }}">
                <i class="fa-solid fa-business-time"></i> Horarios
            </a>
            <a href="{{ route('admin.servicios.index') }}" class="{{ request()->routeIs('admin.servicios.*') ? 'active' : '' }}">
                <i class="fa-solid fa-tags"></i> Servicios
            </a>
            <a href="{{ route('admin.lab-examenes.index') }}" class="{{ request()->routeIs('admin.lab-examenes.*') ? 'active' : '' }}">
                <i class="fa-solid fa-vials"></i> Catálogo Lab
            </a>
            <a href="{{ route('admin.camas.index') }}" class="{{ request()->routeIs('admin.camas.*') ? 'active' : '' }}">
                <i class="fa-solid fa-bed"></i> Camas
            </a>
            <a href="{{ route('admin.empresa.edit') }}" class="{{ request()->routeIs('admin.empresa.*') ? 'active' : '' }}">
                <i class="fa-solid fa-gear"></i> Configuración
            </a>
            <a href="{{ route('admin.auditoria.index') }}" class="{{ request()->routeIs('admin.auditoria.*') ? 'active' : '' }}">
                <i class="fa-solid fa-clock-rotate-left"></i> Bitácora
            </a>
            <a href="{{ route('admin.facturacion.configuracion') }}" class="{{ request()->routeIs('admin.facturacion.*') ? 'active' : '' }}">
                <i class="fa-solid fa-file-invoice"></i> Facturación electrónica
            </a>
            <a href="{{ route('admin.mantenimiento.index') }}" class="{{ request()->routeIs('admin.mantenimiento.*') ? 'active' : '' }}">
                <i class="fa-solid fa-database"></i> Copia y mantenimiento
            </a>
        @endif

        @if($u->isSuperAdmin())
            <div class="label">Plataforma</div>
            <a href="{{ route('admin.empresas.index') }}" class="{{ request()->routeIs('admin.empresas.*') ? 'active' : '' }}">
                <i class="fa-solid fa-building"></i> Empresas / Clientes
            </a>
            <a href="{{ route('admin.metricas.index') }}" class="{{ request()->routeIs('admin.metricas.*') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i> Métricas SaaS
            </a>
            <a href="{{ route('admin.planes.index') }}" class="{{ request()->routeIs('admin.planes.*') ? 'active' : '' }}">
                <i class="fa-solid fa-gem"></i> Planes
            </a>
            <a href="{{ route('admin.especialidades.index') }}" class="{{ request()->routeIs('admin.especialidades.*') ? 'active' : '' }}">
                <i class="fa-solid fa-notes-medical"></i> Especialidades
            </a>
        @endif

        <div class="label">Cuenta</div>
        <a href="{{ route('notificaciones.index') }}" class="{{ request()->routeIs('notificaciones.*') ? 'active' : '' }}">
            <i class="fa-regular fa-bell"></i> Notificaciones
        </a>
        <a href="{{ route('perfil.edit') }}" class="{{ request()->routeIs('perfil.*') ? 'active' : '' }}">
            <i class="fa-solid fa-user"></i> Mi perfil
        </a>
        <a href="{{ route('ajustes.edit') }}" class="{{ request()->routeIs('ajustes.*') ? 'active' : '' }}">
            <i class="fa-solid fa-sliders"></i> Ajustes
        </a>
    </nav>
</aside>
