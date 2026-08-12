<?php

use App\Http\Controllers\Admin\AuditoriaController;
use App\Http\Controllers\Admin\CamaController;
use App\Http\Controllers\Admin\EmpresaConfigController;
use App\Http\Controllers\Admin\EmpresaController;
use App\Http\Controllers\Admin\EspecialidadController;
use App\Http\Controllers\Admin\FacturacionController;
use App\Http\Controllers\Admin\HorarioController;
use App\Http\Controllers\Admin\MantenimientoController;
use App\Http\Controllers\Admin\LabExamenController;
use App\Http\Controllers\Admin\MetricasController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\SuscripcionController;
use App\Http\Controllers\Admin\ServicioController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\AdjuntoController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\AjustesController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegistroController;
use App\Http\Controllers\BuscadorController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\ColaController;
use App\Http\Controllers\ComprobanteController;
use App\Http\Controllers\ConsultaController;
use App\Http\Controllers\ResumenController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\EstadoCuentaController;
use App\Http\Controllers\BancoSangreController;
use App\Http\Controllers\FarmaciaController;
use App\Http\Controllers\InsumoController;
use App\Http\Controllers\HospitalizacionController;
use App\Http\Controllers\ImagenController;
use App\Http\Controllers\TriajeController;
use App\Http\Controllers\LaboratorioController;
use App\Http\Controllers\ModuloController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\OdontogramaController;
use App\Http\Controllers\CrecimientoController;
use App\Http\Controllers\PrenatalController;
use App\Http\Controllers\CardioController;
use App\Http\Controllers\DermatogramaController;
use App\Http\Controllers\PsicologiaController;
use App\Http\Controllers\OftalmoController;
use App\Http\Controllers\NutricionController;
use App\Http\Controllers\TraumatogramaController;
use App\Http\Controllers\EvaluacionEspecialidadController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\Portal\PortalAuthController;
use App\Http\Controllers\Portal\PortalController;
use App\Http\Controllers\Portal\ReservaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\VacunaController;
use Illuminate\Support\Facades\Route;

// ---------- Autenticacion (personal) ----------
Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Registro publico de clinicas (signup)
    Route::get('/registro', [RegistroController::class, 'show'])->name('registro');
    Route::post('/registro', [RegistroController::class, 'store'])->name('registro.store');

    // Recuperar contrasena
    Route::get('/olvide-contrasena', [PasswordResetController::class, 'showForgot'])->name('password.request');
    Route::post('/olvide-contrasena', [PasswordResetController::class, 'sendLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showReset'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// ---------- Portal del paciente ----------
Route::prefix('portal')->group(function () {
    Route::get('/login', [PortalAuthController::class, 'show'])->name('portal.login');
    Route::post('/login', [PortalAuthController::class, 'login'])->name('portal.login.attempt');

    Route::middleware('auth:paciente')->group(function () {
        Route::post('/logout', [PortalAuthController::class, 'logout'])->name('portal.logout');
        Route::get('/', [PortalController::class, 'dashboard'])->name('portal.dashboard');
        Route::get('/historia', [PortalController::class, 'historia'])->name('portal.historia');
        Route::get('/pagos', [PortalController::class, 'pagos'])->name('portal.pagos');
        Route::get('/reservar', [ReservaController::class, 'create'])->name('portal.reservar');
        Route::post('/reservar', [ReservaController::class, 'store'])->name('portal.reservar.store');
        Route::get('/citas/{cita}/editar', [ReservaController::class, 'editar'])->name('portal.cita.editar');
        Route::put('/citas/{cita}', [ReservaController::class, 'actualizar'])->name('portal.cita.actualizar');
        Route::post('/citas/{cita}/cancelar', [ReservaController::class, 'cancelar'])->name('portal.cita.cancelar');
        Route::post('/citas/{cita}/confirmar', [ReservaController::class, 'confirmar'])->name('portal.cita.confirmar');
        Route::get('/citas/{cita}/encuesta', [ReservaController::class, 'encuestar'])->name('portal.cita.encuesta');
        Route::post('/citas/{cita}/encuesta', [ReservaController::class, 'guardarEncuesta'])->name('portal.cita.encuesta.guardar');
    });
});

// ---------- Area privada (personal) ----------
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil y ajustes del usuario
    Route::get('/perfil', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
    Route::put('/perfil/password', [PerfilController::class, 'password'])->name('perfil.password');
    Route::get('/ajustes', [AjustesController::class, 'edit'])->name('ajustes.edit');
    Route::put('/ajustes', [AjustesController::class, 'update'])->name('ajustes.update');

    // Gestion clinica (empresa)
    Route::middleware('role:admin,medico,recepcion')->group(function () {
        Route::get('/buscar', [BuscadorController::class, 'index'])->name('buscador.index');

        // Notificaciones
        Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
        Route::post('/notificaciones/leer', [NotificacionController::class, 'marcarTodas'])->name('notificaciones.leer');

        // Pacientes
        Route::get('/pacientes/exportar', [PacienteController::class, 'exportar'])->name('pacientes.exportar');
        Route::get('/pacientes/{paciente}/crecimiento', [PacienteController::class, 'crecimiento'])->name('pacientes.crecimiento');
        Route::get('/pacientes/{paciente}/constancia', [DocumentoController::class, 'constancia'])->name('documentos.constancia');
        Route::get('/pacientes/{paciente}/historia-pdf', [DocumentoController::class, 'historia'])->name('historia.pdf');
        Route::resource('pacientes', PacienteController::class);
        Route::resource('citas', CitaController::class)->except(['show']);

        // Odontograma (proceso propio de odontologia)
        Route::get('/odontograma', [OdontogramaController::class, 'index'])->name('odontograma.index');
        Route::get('/odontograma/{paciente}', [OdontogramaController::class, 'edit'])->name('odontograma.edit');
        Route::put('/odontograma/{paciente}', [OdontogramaController::class, 'update'])->name('odontograma.update');

        // Crecimiento (pediatria) — reutiliza la curva OMS existente
        Route::get('/crecimiento', [CrecimientoController::class, 'index'])->name('crecimiento.index');

        // Control prenatal (gineco / obstetricia)
        Route::get('/prenatal', [PrenatalController::class, 'index'])->name('prenatal.index');
        Route::get('/prenatal/{paciente}', [PrenatalController::class, 'show'])->name('prenatal.show');
        Route::post('/prenatal/{paciente}/embarazo', [PrenatalController::class, 'guardarEmbarazo'])->name('prenatal.embarazo');
        Route::post('/prenatal/{paciente}/control', [PrenatalController::class, 'guardarControl'])->name('prenatal.control');
        Route::delete('/prenatal/control/{control}', [PrenatalController::class, 'eliminarControl'])->name('prenatal.control.destroy');

        // Evaluacion cardiovascular (cardiologia)
        Route::get('/cardio', [CardioController::class, 'index'])->name('cardio.index');
        Route::get('/cardio/{paciente}', [CardioController::class, 'show'])->name('cardio.show');
        Route::post('/cardio/{paciente}', [CardioController::class, 'store'])->name('cardio.store');
        Route::delete('/cardio/eval/{evaluacion}', [CardioController::class, 'destroy'])->name('cardio.destroy');

        // Mapa de lesiones (dermatologia)
        Route::get('/dermatograma', [DermatogramaController::class, 'index'])->name('dermatograma.index');
        Route::get('/dermatograma/{paciente}', [DermatogramaController::class, 'edit'])->name('dermatograma.edit');
        Route::put('/dermatograma/{paciente}', [DermatogramaController::class, 'update'])->name('dermatograma.update');

        // Psicologia — sesiones y seguimiento
        Route::get('/psicologia', [PsicologiaController::class, 'index'])->name('psicologia.index');
        Route::get('/psicologia/{paciente}', [PsicologiaController::class, 'show'])->name('psicologia.show');
        Route::post('/psicologia/{paciente}', [PsicologiaController::class, 'store'])->name('psicologia.store');
        Route::delete('/psicologia/sesion/{sesion}', [PsicologiaController::class, 'destroy'])->name('psicologia.destroy');

        // Oftalmologia — agudeza visual y refraccion
        Route::get('/oftalmologia', [OftalmoController::class, 'index'])->name('oftalmo.index');
        Route::get('/oftalmologia/{paciente}', [OftalmoController::class, 'show'])->name('oftalmo.show');
        Route::post('/oftalmologia/{paciente}', [OftalmoController::class, 'store'])->name('oftalmo.store');
        Route::delete('/oftalmologia/eval/{evaluacion}', [OftalmoController::class, 'destroy'])->name('oftalmo.destroy');

        // Nutricion — antropometria y plan
        Route::get('/nutricion', [NutricionController::class, 'index'])->name('nutricion.index');
        Route::get('/nutricion/{paciente}', [NutricionController::class, 'show'])->name('nutricion.show');
        Route::post('/nutricion/{paciente}', [NutricionController::class, 'store'])->name('nutricion.store');
        Route::delete('/nutricion/eval/{evaluacion}', [NutricionController::class, 'destroy'])->name('nutricion.destroy');

        // Traumatologia — mapa de lesiones oseas
        Route::get('/traumatograma', [TraumatogramaController::class, 'index'])->name('traumatograma.index');
        Route::get('/traumatograma/{paciente}', [TraumatogramaController::class, 'edit'])->name('traumatograma.edit');
        Route::put('/traumatograma/{paciente}', [TraumatogramaController::class, 'update'])->name('traumatograma.update');

        // Motor generico de evaluacion por especialidad (endocrino, neumo, neuro, uro, etc.)
        Route::get('/evaluacion/{slug}', [EvaluacionEspecialidadController::class, 'index'])->name('evaluacion.index')->where('slug', '[a-z0-9\-]+');
        Route::get('/evaluacion/{slug}/{paciente}', [EvaluacionEspecialidadController::class, 'show'])->name('evaluacion.show')->where('slug', '[a-z0-9\-]+');
        Route::post('/evaluacion/{slug}/{paciente}', [EvaluacionEspecialidadController::class, 'store'])->name('evaluacion.store')->where('slug', '[a-z0-9\-]+');
        Route::delete('/evaluacion-registro/{evaluacion}', [EvaluacionEspecialidadController::class, 'destroy'])->name('evaluacion.destroy');

        // Vacunas (pediatria)
        Route::get('/pacientes/{paciente}/vacunas', [VacunaController::class, 'index'])->name('vacunas.index');
        Route::post('/pacientes/{paciente}/vacunas/esquema', [VacunaController::class, 'generarEsquema'])->name('vacunas.esquema');
        Route::post('/vacunas', [VacunaController::class, 'store'])->name('vacunas.store');
        Route::post('/vacunas/{vacuna}/aplicar', [VacunaController::class, 'aplicar'])->name('vacunas.aplicar');
        Route::delete('/vacunas/{vacuna}', [VacunaController::class, 'destroy'])->name('vacunas.destroy');

        // Sala de espera
        Route::get('/cola', [ColaController::class, 'index'])->name('cola.index');
        Route::post('/cola/{cita}/llegada', [ColaController::class, 'llegada'])->name('cola.llegada');
        Route::post('/cola/{cita}/iniciar', [ColaController::class, 'iniciar'])->name('cola.iniciar');
        Route::post('/cola/{cita}/finalizar', [ColaController::class, 'finalizar'])->name('cola.finalizar');

        // Adjuntos
        Route::post('/adjuntos', [AdjuntoController::class, 'store'])->name('adjuntos.store');
        Route::get('/adjuntos/{adjunto}/download', [AdjuntoController::class, 'download'])->name('adjuntos.download');
        Route::delete('/adjuntos/{adjunto}', [AdjuntoController::class, 'destroy'])->name('adjuntos.destroy');

        // Historia clinica / consultas
        Route::get('/consultas/create', [ConsultaController::class, 'create'])->name('consultas.create');
        Route::post('/consultas', [ConsultaController::class, 'store'])->name('consultas.store');
        Route::get('/consultas/{consulta}', [ConsultaController::class, 'show'])->name('consultas.show');
        Route::get('/consultas/{consulta}/edit', [ConsultaController::class, 'edit'])->name('consultas.edit');
        Route::put('/consultas/{consulta}', [ConsultaController::class, 'update'])->name('consultas.update');
        Route::get('/consultas/{consulta}/receta', [ConsultaController::class, 'receta'])->name('consultas.receta');
        Route::get('/consultas/{consulta}/certificado', [DocumentoController::class, 'certificado'])->name('documentos.certificado');

        // Agenda / calendario
        Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');
        Route::get('/agenda/eventos', [AgendaController::class, 'eventos'])->name('agenda.eventos');
        Route::put('/agenda/citas/{cita}/mover', [AgendaController::class, 'mover'])->name('agenda.mover');

        // Facturacion / pagos
        Route::resource('pagos', PagoController::class)->except(['show']);
        Route::get('/pagos-estados', [EstadoCuentaController::class, 'index'])->name('pagos.estados');
        Route::get('/pagos/{pago}/recibo', [PagoController::class, 'recibo'])->name('pagos.recibo');

        // Comprobantes electrónicos (SUNAT)
        Route::get('/comprobantes', [ComprobanteController::class, 'index'])->name('comprobantes.index');
        Route::get('/comprobantes/{comprobante}/pdf', [ComprobanteController::class, 'pdf'])->name('comprobantes.pdf');
        Route::get('/comprobantes/{comprobante}/xml', [ComprobanteController::class, 'xml'])->name('comprobantes.xml');
        Route::get('/comprobantes/{comprobante}/cdr', [ComprobanteController::class, 'cdr'])->name('comprobantes.cdr');
        Route::post('/comprobantes/{comprobante}/emitir', [ComprobanteController::class, 'emitir'])->name('comprobantes.emitir');
        Route::post('/comprobantes/{comprobante}/nota-credito', [ComprobanteController::class, 'notaCredito'])->name('comprobantes.nota');
        Route::post('/comprobantes/{comprobante}/anular', [ComprobanteController::class, 'anular'])->name('comprobantes.anular');
        Route::post('/comprobantes/{comprobante}/consultar-baja', [ComprobanteController::class, 'consultarBaja'])->name('comprobantes.consultar-baja');

        // Resumen Diario de Boletas (RC · SUNAT en lote)
        Route::get('/resumenes', [ResumenController::class, 'index'])->name('resumenes.index');
        Route::post('/resumenes/generar', [ResumenController::class, 'generar'])->name('resumenes.generar');
        Route::post('/resumenes/{resumen}/consultar', [ResumenController::class, 'consultar'])->name('resumenes.consultar');
        Route::post('/resumenes/{resumen}/reenviar', [ResumenController::class, 'reenviar'])->name('resumenes.reenviar');

        // Inventario
        Route::resource('insumos', InsumoController::class)->except(['show']);
        Route::get('/insumos/{insumo}/movimientos', [InsumoController::class, 'movimientos'])->name('insumos.movimientos');
        Route::post('/insumos/{insumo}/movimientos', [InsumoController::class, 'registrarMovimiento'])->name('insumos.movimiento.store');

        // Laboratorio clinico
        Route::get('/laboratorio', [LaboratorioController::class, 'index'])->name('laboratorio.index');
        Route::get('/laboratorio/crear', [LaboratorioController::class, 'create'])->name('laboratorio.create');
        Route::post('/laboratorio', [LaboratorioController::class, 'store'])->name('laboratorio.store');
        Route::get('/laboratorio/{orden}', [LaboratorioController::class, 'show'])->name('laboratorio.show');
        Route::put('/laboratorio/{orden}/resultados', [LaboratorioController::class, 'guardarResultados'])->name('laboratorio.resultados');
        Route::post('/laboratorio/{orden}/entregar', [LaboratorioController::class, 'entregar'])->name('laboratorio.entregar');
        Route::get('/laboratorio/{orden}/pdf', [LaboratorioController::class, 'pdf'])->name('laboratorio.pdf');
        Route::delete('/laboratorio/{orden}', [LaboratorioController::class, 'destroy'])->name('laboratorio.destroy');

        // Hospitalizacion
        Route::get('/hospitalizacion', [HospitalizacionController::class, 'index'])->name('hospitalizacion.index');
        Route::get('/hospitalizacion/ingreso', [HospitalizacionController::class, 'create'])->name('hospitalizacion.create');
        Route::post('/hospitalizacion', [HospitalizacionController::class, 'store'])->name('hospitalizacion.store');
        Route::get('/hospitalizacion/{hospitalizacion}', [HospitalizacionController::class, 'show'])->name('hospitalizacion.show');
        Route::post('/hospitalizacion/{hospitalizacion}/evolucion', [HospitalizacionController::class, 'agregarEvolucion'])->name('hospitalizacion.evolucion');
        Route::post('/hospitalizacion/{hospitalizacion}/alta', [HospitalizacionController::class, 'alta'])->name('hospitalizacion.alta');
        Route::delete('/hospitalizacion/{hospitalizacion}', [HospitalizacionController::class, 'destroy'])->name('hospitalizacion.destroy');

        // Diagnostico por imagenes
        Route::get('/imagenes', [ImagenController::class, 'index'])->name('imagenes.index');
        Route::get('/imagenes/crear', [ImagenController::class, 'create'])->name('imagenes.create');
        Route::post('/imagenes', [ImagenController::class, 'store'])->name('imagenes.store');
        Route::get('/imagenes/{imagen}', [ImagenController::class, 'show'])->name('imagenes.show');
        Route::put('/imagenes/{imagen}/informe', [ImagenController::class, 'guardarInforme'])->name('imagenes.informe');
        Route::post('/imagenes/{imagen}/archivo', [ImagenController::class, 'subirArchivo'])->name('imagenes.archivo');
        Route::post('/imagenes/{imagen}/orden', [ImagenController::class, 'subirOrden'])->name('imagenes.orden');
        Route::get('/imagenes/{imagen}/pdf', [ImagenController::class, 'pdf'])->name('imagenes.pdf');
        Route::delete('/imagenes/{imagen}', [ImagenController::class, 'destroy'])->name('imagenes.destroy');

        // Emergencias / Triaje
        Route::get('/triaje', [TriajeController::class, 'index'])->name('triaje.index');
        Route::get('/triaje/registrar', [TriajeController::class, 'create'])->name('triaje.create');
        Route::post('/triaje', [TriajeController::class, 'store'])->name('triaje.store');
        Route::post('/triaje/{triaje}/atender', [TriajeController::class, 'atender'])->name('triaje.atender');
        Route::post('/triaje/{triaje}/finalizar', [TriajeController::class, 'finalizar'])->name('triaje.finalizar');
        Route::delete('/triaje/{triaje}', [TriajeController::class, 'destroy'])->name('triaje.destroy');

        // Farmacia / dispensacion
        Route::get('/farmacia', [FarmaciaController::class, 'index'])->name('farmacia.index');
        Route::get('/farmacia/crear', [FarmaciaController::class, 'create'])->name('farmacia.create');
        Route::post('/farmacia', [FarmaciaController::class, 'store'])->name('farmacia.store');
        Route::get('/farmacia/{dispensacion}/comprobante', [FarmaciaController::class, 'comprobante'])->name('farmacia.comprobante');
        Route::delete('/farmacia/{dispensacion}', [FarmaciaController::class, 'destroy'])->name('farmacia.destroy');

        // Banco de sangre
        Route::get('/banco-sangre', [BancoSangreController::class, 'index'])->name('bancosangre.index');
        Route::get('/banco-sangre/donantes', [BancoSangreController::class, 'donantes'])->name('bancosangre.donantes');
        Route::post('/banco-sangre/donantes', [BancoSangreController::class, 'donanteStore'])->name('bancosangre.donante.store');
        Route::post('/banco-sangre/unidades', [BancoSangreController::class, 'unidadStore'])->name('bancosangre.unidad.store');
        Route::post('/banco-sangre/unidades/{unidad}/descartar', [BancoSangreController::class, 'unidadDescartar'])->name('bancosangre.unidad.descartar');
        Route::post('/banco-sangre/solicitudes', [BancoSangreController::class, 'solicitudStore'])->name('bancosangre.solicitud.store');
        Route::post('/banco-sangre/solicitudes/{solicitud}/despachar', [BancoSangreController::class, 'despachar'])->name('bancosangre.despachar');
        Route::post('/banco-sangre/solicitudes/{solicitud}/cancelar', [BancoSangreController::class, 'solicitudCancelar'])->name('bancosangre.solicitud.cancelar');

        // Reportes
        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/clinico', [ReporteController::class, 'clinico'])->name('reportes.clinico');
        Route::get('/reportes/financiero', [ReporteController::class, 'financiero'])->name('reportes.financiero');
        Route::get('/reportes/pdf', [ReporteController::class, 'pdf'])->name('reportes.pdf');
        Route::get('/reportes/excel', [ReporteController::class, 'excel'])->name('reportes.excel');
    });

    // Modulos por especialidad
    Route::get('/modulo/{slug}', [ModuloController::class, 'show'])
        ->middleware('module')->name('modulo.show')->where('slug', '[a-z0-9\-]+');

    // Administracion de la empresa — solo admin
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('usuarios', UsuarioController::class)->except('show');
        Route::get('/empresa', [EmpresaConfigController::class, 'edit'])->name('empresa.edit');
        Route::put('/empresa', [EmpresaConfigController::class, 'update'])->name('empresa.update');
        Route::get('/auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index');

        // Facturación electrónica (SUNAT / Perú)
        Route::get('/facturacion/configuracion', [FacturacionController::class, 'configuracion'])->name('facturacion.configuracion');
        Route::post('/facturacion/configuracion', [FacturacionController::class, 'guardar'])->name('facturacion.guardar');
        Route::post('/facturacion/probar', [FacturacionController::class, 'probar'])->name('facturacion.probar');

        // Copia de seguridad y mantenimiento (por empresa)
        Route::get('/mantenimiento', [MantenimientoController::class, 'index'])->name('mantenimiento.index');
        Route::get('/mantenimiento/backup', [MantenimientoController::class, 'backup'])->name('mantenimiento.backup');
        Route::post('/mantenimiento/restore', [MantenimientoController::class, 'restore'])->name('mantenimiento.restore');
        Route::post('/mantenimiento/reset', [MantenimientoController::class, 'reset'])->name('mantenimiento.reset');

        Route::get('/horarios', [HorarioController::class, 'index'])->name('horarios.index');
        Route::post('/horarios', [HorarioController::class, 'store'])->name('horarios.store');
        Route::delete('/horarios/{horario}', [HorarioController::class, 'destroy'])->name('horarios.destroy');

        Route::get('/servicios', [ServicioController::class, 'index'])->name('servicios.index');
        Route::post('/servicios', [ServicioController::class, 'store'])->name('servicios.store');
        Route::put('/servicios/{servicio}', [ServicioController::class, 'update'])->name('servicios.update');
        Route::delete('/servicios/{servicio}', [ServicioController::class, 'destroy'])->name('servicios.destroy');

        Route::get('/lab-examenes', [LabExamenController::class, 'index'])->name('lab-examenes.index');
        Route::post('/lab-examenes', [LabExamenController::class, 'store'])->name('lab-examenes.store');
        Route::put('/lab-examenes/{examen}', [LabExamenController::class, 'update'])->name('lab-examenes.update');
        Route::delete('/lab-examenes/{examen}', [LabExamenController::class, 'destroy'])->name('lab-examenes.destroy');

        Route::get('/camas', [CamaController::class, 'index'])->name('camas.index');
        Route::post('/camas', [CamaController::class, 'store'])->name('camas.store');
        Route::delete('/camas/{cama}', [CamaController::class, 'destroy'])->name('camas.destroy');
    });

    // Panel del dueno del SaaS — solo superadmin
    Route::middleware('role:superadmin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('empresas', EmpresaController::class)->except('show');
        Route::resource('especialidades', EspecialidadController::class)->except('show');
        Route::get('/metricas', [MetricasController::class, 'index'])->name('metricas.index');

        // Planes y suscripciones (SaaS)
        Route::get('/planes', [PlanController::class, 'index'])->name('planes.index');
        Route::post('/planes', [PlanController::class, 'store'])->name('planes.store');
        Route::put('/planes/{plan}', [PlanController::class, 'update'])->name('planes.update');
        Route::delete('/planes/{plan}', [PlanController::class, 'destroy'])->name('planes.destroy');
        Route::get('/empresas/{empresa}/suscripcion', [SuscripcionController::class, 'show'])->name('suscripcion.show');
        Route::post('/empresas/{empresa}/suscripcion', [SuscripcionController::class, 'store'])->name('suscripcion.store');
        Route::get('/suscripciones/{suscripcion}/ticket', [SuscripcionController::class, 'ticket'])->name('suscripcion.ticket');
    });
});
