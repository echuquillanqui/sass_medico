# 🏥 Suite Web SaaS Médico Modular

Plataforma **SaaS multi-especialidad** para clínicas y consultorios, construida con **Laravel 11 + MySQL**.
Cada empresa cliente ve únicamente las especialidades que le asignes (Pediatría, Ginecología, Odontología, Cardiología, Psicología, etc.).

---

## ✨ Características

- **Login profesional** con roles y sesión segura.
- **Dashboard** con métricas, gráfico semanal de citas y accesos a módulos.
- **Menú vertical** dinámico según el rol y las especialidades habilitadas.
- **Multi-tenant por empresa**: el SuperAdmin decide qué especialidades ve cada cliente.
- **Roles**: `SuperAdmin` (dueño del SaaS), `Admin` (empresa), `Médico`, `Recepción`.
- **Módulos clínicos**: Pacientes, Citas e Historia clínica.
- **Historia clínica / consultas**: signos vitales, IMC automático, diagnóstico, tratamiento y **receta en PDF**.
- **Fichas ricas por especialidad**: odontograma interactivo, control de crecimiento pediátrico, control prenatal, evaluación cardiovascular y sesiones de psicología.
- **Agenda visual** (FullCalendar) con vista mes/semana/día y **arrastrar-soltar** para reprogramar citas.
- **Facturación**: pagos por consulta/tratamiento, estado de cuenta y **recibo en PDF**.
- **Reportes**: métricas, gráficos y exportación a **PDF y Excel/CSV**.
- **Configuración de empresa**: branding (logo, color, moneda), horarios y datos.
- **Perfil de usuario** con cambio de contraseña.
- **Recordatorios de citas por correo**: confirmación al agendar y recordatorio automático diario.
- **Portal del paciente**: los pacientes ingresan a ver sus citas, historia y pagos.
- **Percentiles OMS**: curvas de peso/talla por edad (método LMS) en la ficha pediátrica.
- **Inventario de insumos**: stock, entradas/salidas y alertas de stock bajo.
- **Dashboards por rol** (Admin, Médico, Recepción) y **buscador global** de pacientes/citas.
- **Branding por empresa**: el color configurado se aplica al tema de la interfaz.
- **Recetas detalladas**: varios medicamentos (dosis, frecuencia, duración) con firma y CMP del médico.
- **Certificados y constancias** médicas en PDF.
- **Reserva online**: el paciente agenda su cita desde el portal según disponibilidad.
- **Notificaciones internas** (campana) por nuevas citas, pagos y stock bajo.
- **Bitácora / auditoría**: registro automático de altas, cambios y bajas.
- **Panel SaaS del SuperAdmin**: MRR/ARR, planes, altas de empresas e ingresos por cliente.
- **Reportes financieros**: por método de pago, comparativa mensual y top de pacientes.
- **Teleconsulta**: sala de videollamada (Jitsi) por cita para médico y paciente.
- **Recordatorio por WhatsApp**: enlace directo `wa.me` con el mensaje de la cita.
- **Adjuntos**: subir exámenes/imágenes/PDF a la historia del paciente.
- **Reserva y autogestión desde el portal**: el paciente agenda, reprograma o cancela su cita.
- **Horarios por médico**: disponibilidad semanal que la reserva online respeta.
- **Catálogo de servicios y precios** que autocompleta el cobro.
- **Exportar historia clínica** completa del paciente en PDF.
- **Firma digital** del médico (dibujada) en recetas, certificados y constancias.
- **Modo oscuro** con preferencia guardada.
- **Indicadores clínicos**: diagnósticos frecuentes, pacientes por especialidad, edad/sexo y satisfacción.
- **Estados de cuenta**: deudores y recordatorio de saldo por WhatsApp.
- **Sala de espera**: cola del día (por llegar, en espera, en atención, atendidos).
- **Encuesta de satisfacción** y **confirmación de cita** desde el portal del paciente.
- **Registro público de clínicas** (signup) y **recuperación de contraseña**.
- **Gestión de especialidades** desde el SuperAdmin y **alta de admin** al crear una empresa.
- **Esquema de vacunación** pediátrico y **exportación de pacientes** a Excel/CSV.
- **Laboratorio clínico**: catálogo de exámenes, órdenes, captura de resultados (con marca de valores fuera de rango) e informe en PDF.
- **Hospitalización**: gestión de camas, ingresos/altas, mapa de ocupación y evolución diaria por paciente.
- **Diagnóstico por imágenes**: órdenes de estudio (radiografía, ecografía, TAC, RM…), informe radiológico, archivo adjunto e informe en PDF.
- **Emergencias / Triaje**: clasificación por prioridad (Manchester, 5 niveles con colores), cola ordenada por urgencia y flujo de atención.
- **Farmacia / Dispensación**: entrega de medicamentos e insumos con descuento automático de stock del inventario y comprobante en PDF.
- **Banco de sangre**: donantes, stock por grupo sanguíneo, registro de unidades, solicitudes de transfusión y despacho que descuenta unidades.
- **Ajustes por usuario** (tema, densidad, filas por página, notificaciones).
- **Diseño** morado/rosa moderno, con **modo oscuro** y **100% responsive** (menú adaptable en móvil).

---

## 🧩 Requisitos

- PHP **8.2+**
- Composer 2
- MySQL 5.7+ / MariaDB 10.4+
- Recomendado: **Laragon** o **XAMPP** en Windows

---

## 🚀 Instalación paso a paso

### 1. Crear la base de datos
Abre **phpMyAdmin** e importa `database/suite_saas_medico_modular.sql`,
o ejecuta en consola:

```bash
mysql -u root < database/suite_saas_medico_modular.sql
```

Esto solo crea la base `suite_saas_medico_modular` (vacía). Las tablas las crean las migraciones.

### 2. Instalar dependencias
Desde la carpeta `medico-saas`:

```bash
composer install
```

### 3. Configurar el entorno
El archivo `.env` ya viene configurado para `localhost`, `root`, sin contraseña.
Si tu MySQL tiene contraseña, edítala en `.env` (`DB_PASSWORD=`).

Genera la clave de la aplicación:

```bash
php artisan key:generate
```

### 4. Crear tablas y datos demo

```bash
php artisan migrate --seed
```

### 5. Enlazar el almacenamiento (para logos de empresa)

```bash
php artisan storage:link
```

### 6. Levantar el servidor

```bash
php artisan serve
```

Abre 👉 **http://localhost:8000**

---

## 🔄 ¿Ya tenías una versión anterior instalada?

Si actualizaste el proyecto con las nuevas fases (consultas, agenda, pagos, reportes, configuración):

```bash
composer install            # instala dompdf (recibos y reportes PDF)
php artisan migrate          # aplica migraciones nuevas (pagos, config empresa)
php artisan db:seed          # datos demo de consultas y pagos
php artisan storage:link     # para subir logos
```

> Si prefieres empezar limpio: `php artisan migrate:fresh --seed`
>
> Tras actualizar vistas, limpia la caché de Blade: `php artisan view:clear`

---

## 🔔 Recordatorios por correo

Los correos usan `MAIL_MAILER=log` por defecto (se escriben en `storage/logs/laravel.log`).
Para envío real, configura SMTP en `.env`.

- Probar el recordatorio manualmente: `php artisan citas:recordatorios`
- Para que se envíe solo cada día, agrega el cron de Laravel:

```
* * * * * cd /ruta/al/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

## 👤 Portal del paciente

Los pacientes con acceso habilitado entran en **http://localhost:8000/portal/login**.

| Correo                    | Contraseña |
|---------------------------|------------|
| valentina@paciente.test   | password   |

El acceso se habilita marcando *acceso al portal* y asignando contraseña al paciente.

---

## 🔑 Credenciales de prueba

| Rol         | Correo                        | Contraseña |
|-------------|-------------------------------|------------|
| SuperAdmin  | superadmin@suitesalud.test    | password   |
| Admin       | admin@clinicavida.test        | password   |
| Médico      | medico@clinicavida.test       | password   |
| Recepción   | recepcion@clinicavida.test    | password   |

La empresa demo **Clínica Vida Sana** tiene habilitadas: Pediatría, Ginecología y Odontología.

---

## 🧠 Cómo funciona el modelo modular

1. Entra como **SuperAdmin** → *Empresas / Clientes* → crea una empresa y **marca las especialidades** que verá.
2. Esas especialidades aparecen automáticamente en el menú vertical de los usuarios de esa empresa.
3. El middleware `module` bloquea el acceso a un módulo que la empresa no tenga habilitado.
4. El **Admin** de la empresa crea sus usuarios (médicos, recepción) desde *Usuarios*.

---

## 📂 Estructura relevante

```
app/
 ├─ Http/Controllers/        Dashboard, Paciente, Cita, Modulo, Admin/*
 ├─ Http/Middleware/         EnsureUserRole, EnsureModuleEnabled
 └─ Models/                  User, Empresa, Especialidad, Paciente, Cita, Consulta
database/
 ├─ migrations/              Esquema completo
 ├─ seeders/                 Datos demo
 └─ suite_saas_medico_modular.sql
resources/views/             Blade (login, dashboard, módulos, CRUDs)
public/css/app.css           Sistema de diseño (sin build, listo para usar)
routes/web.php               Rutas y protección por rol/módulo
```

---

## ➕ Agregar una nueva especialidad

1. Añádela en el seeder o desde la base (`especialidades`): nombre, slug, icono (FontAwesome), color.
2. Asígnala a la empresa desde el panel SuperAdmin.
3. (Opcional) Personaliza sus tarjetas en `resources/views/modulos/show.blade.php`.

---

Hecho con Laravel 11. El diseño no requiere compilación de assets (CSS estático en `public/css`).
