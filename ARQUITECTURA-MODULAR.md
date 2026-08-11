# 🧩 Arquitectura Modular — Suite Salud Modular (SaaS Médico)

Principio de diseño: **un núcleo común reutilizable** (pacientes, citas, historia, facturación, usuarios) + **módulos por especialidad configurables**, todos vinculados a una **tabla maestra de especialidades** (`especialidades`). Así se reutiliza el mismo núcleo sin construir una app distinta por área, y se suman servicios de apoyo (laboratorio, imágenes, hospitalización, etc.) como módulos transversales.

Leyenda de estado: ✅ Implementado · 🟡 Parcial · 🔜 Pendiente (extensible con el mismo patrón)

---

## 1. Núcleo común (transversal a todas las especialidades)

| # | Módulo | Descripción | Estado |
|---|--------|-------------|--------|
| 1 | Autenticación y roles | SuperAdmin, Admin, Médico, Recepción; recuperación de contraseña | ✅ |
| 2 | Multi-tenant (Empresas/Clínicas) | Cada clínica ve solo sus datos y sus módulos habilitados | ✅ |
| 3 | **Tabla maestra de Especialidades** | Catálogo configurable (nombre, ícono, color); CRUD SuperAdmin | ✅ |
| 4 | Pacientes | Ficha única compartida por todas las especialidades | ✅ |
| 5 | Citas y Agenda | Calendario, arrastrar-soltar, reserva online desde el portal | ✅ |
| 6 | Sala de espera / Turnos | Cola del día: por llegar → en espera → en atención → atendido | ✅ |
| 7 | Historia clínica / Consultas | Motor común: signos vitales, IMC, diagnóstico, tratamiento | ✅ |
| 8 | Recetas / Certificados | Receta estructurada + firma digital; constancias y certificados PDF | ✅ |
| 9 | Facturación / Pagos | Cobros, estados de cuenta, recibos PDF, catálogo de servicios | ✅ |
| 10 | Inventario de insumos | Stock, entradas/salidas, alertas de bajo stock | ✅ |
| 11 | Reportes | Clínico, financiero, exportación PDF y Excel/CSV | ✅ |
| 12 | Notificaciones y recordatorios | Internas (campana) + correo + enlace WhatsApp | ✅ |
| 13 | Bitácora / Auditoría | Registro automático de altas, cambios y bajas | ✅ |
| 14 | Portal del paciente | Reserva, confirmar/reprogramar/cancelar, historia, pagos, encuesta | ✅ |
| 15 | Configuración de empresa | Branding (logo, color, moneda), horarios, datos | ✅ |
| 16 | Panel SaaS (dueño) | MRR/ARR, planes, ingresos por empresa, registro público (signup) | ✅ |
| 17 | Ajustes por usuario | Tema, densidad, filas por página, notificaciones | ✅ |
| 18 | Horarios/disponibilidad por médico | La reserva online respeta la agenda de cada profesional | ✅ |

---

## 2. Módulos por especialidad (configurables sobre el núcleo)

Cada especialidad es una **fila en `especialidades`** + una **ficha propia opcional** en la consulta. Cuando no tiene ficha específica, usa la ficha clínica general.

> **Fichas específicas ya implementadas** (formulario propio en la consulta): Pediatría (percentiles+vacunas), Ginecología, Obstetricia, Cardiología, Odontología (odontograma), Psicología, Psiquiatría, Dermatología, Oftalmología, Otorrinolaringología, Neumología, Endocrinología, Gastroenterología, Neurología, Traumatología, Urología y Nutrición. El resto usa la ficha clínica general (extensible con el mismo patrón).
>
> **Enlace entre módulos:** desde una consulta guardada se puede **pedir laboratorio, pedir imágenes y dispensar en farmacia** con el paciente precargado.
>
> **Proceso propio por especialidad (pantalla dedicada en el módulo):** cada especialidad tiene su "escenario" característico, accesible desde la tarjeta del módulo y un botón por paciente:
> - Odontología → **odontograma** interactivo (dientes anatómicos + 5 superficies + plan de tratamiento).
> - Pediatría → **curva de crecimiento** con percentiles OMS.
> - Ginecología/Obstetricia → **control prenatal** (FUM, edad gestacional, FPP, controles).
> - Cardiología → **evaluación cardiovascular** con riesgo CV a 10 años.
> - Dermatología y Traumatología → **mapa corporal interactivo** de lesiones.
> - Psicología → **sesiones** con escala de ánimo y progreso.
> - Oftalmología → **agudeza visual y refracción** (OD/OS, PIO).
> - Nutrición → **antropometría** con IMC y evolución.
> - Resto (endocrinología, neumología, neurología, urología, otorrino, psiquiatría, reumatología, nefrología, geriatría, infectología, oncología, fisioterapia, medicina general/interna) → **motor genérico de evaluación** dirigido por configuración (`config/evaluaciones.php`): campos clínicos, KPIs y curva de tendencia por especialidad, sin duplicar código. Agregar una especialidad nueva = añadir una entrada de config.

### Medicina y clínica del adulto
| Especialidad | Ficha específica sugerida | Estado |
|--------------|---------------------------|--------|
| Medicina General / Familiar | Ficha general (motivo, dx, tratamiento) | ✅ catálogo · 🟡 ficha general |
| Medicina Interna | Problemas activos, comorbilidades | ✅ catálogo · 🔜 ficha |
| Geriatría | Valoración geriátrica integral, escalas | ✅ catálogo · 🔜 ficha |
| Cardiología | ECG, ecocardiograma, riesgo cardiovascular | ✅ catálogo · ✅ ficha |
| Endocrinología | Glucosa, HbA1c, perfil tiroideo | ✅ catálogo · 🔜 ficha |
| Gastroenterología | Endoscopía, síntomas digestivos | ✅ catálogo · 🔜 ficha |
| Neumología | Espirometría, saturación | ✅ catálogo · 🔜 ficha |
| Nefrología | Función renal, diálisis | ✅ catálogo · 🔜 ficha |
| Neurología | Examen neurológico, escalas | ✅ catálogo · 🔜 ficha |
| Reumatología | Articulaciones, autoinmunidad | ✅ catálogo · 🔜 ficha |
| Hematología | Hemograma, coagulación | 🔜 catálogo · 🔜 ficha |
| Infectología | Cultivos, esquema antibiótico | ✅ catálogo · 🔜 ficha |
| Alergología | Pruebas cutáneas, inmunoterapia | 🔜 catálogo · 🔜 ficha |
| Oncología | Estadificación, ciclos de tratamiento | ✅ catálogo · 🔜 ficha |

### Materno-infantil
| Especialidad | Ficha específica sugerida | Estado |
|--------------|---------------------------|--------|
| Pediatría | Crecimiento (percentiles OMS) + Vacunación | ✅ catálogo · ✅ ficha |
| Neonatología | Apgar, peso al nacer, tamizaje neonatal | 🔜 catálogo · 🔜 ficha |
| Ginecología | Antecedentes ginecológicos, Papanicolaou | ✅ catálogo · ✅ ficha |
| Obstetricia | Control prenatal (FUM, semanas, altura uterina) | ✅ catálogo · 🟡 ficha (en ginecología) |

### Especialidades quirúrgicas y de órgano
| Especialidad | Ficha específica sugerida | Estado |
|--------------|---------------------------|--------|
| Cirugía General (+ subespecialidades) | Protocolo pre/post operatorio | 🔜 catálogo · 🔜 ficha |
| Traumatología | Localización de lesión, imágenes | ✅ catálogo · 🔜 ficha |
| Oftalmología | Agudeza visual, presión intraocular | ✅ catálogo · 🔜 ficha |
| Otorrinolaringología | Audiometría, exploración ORL | ✅ catálogo · 🔜 ficha |
| Urología | Vías urinarias, PSA | ✅ catálogo · 🔜 ficha |
| Dermatología | Localización y tipo de lesión, dermatoscopía | ✅ catálogo · 🔜 ficha |
| Odontología (+ ramas: ortodoncia, endodoncia…) | Odontograma interactivo | ✅ catálogo · ✅ ficha |

### Salud mental y rehabilitación
| Especialidad | Ficha específica sugerida | Estado |
|--------------|---------------------------|--------|
| Psicología | Sesiones, técnica, tareas | ✅ catálogo · ✅ ficha |
| Psiquiatría | Estado mental, medicación | ✅ catálogo · 🔜 ficha |
| Nutrición | Antropometría, plan alimentario | ✅ catálogo · 🔜 ficha |
| Fisioterapia | Plan de rehabilitación, sesiones | ✅ catálogo · 🔜 ficha |
| Terapia Ocupacional | Objetivos funcionales | 🔜 catálogo · 🔜 ficha |
| Terapia de Lenguaje | Evaluación fonoaudiológica | 🔜 catálogo · 🔜 ficha |

---

## 3. Servicios de apoyo / diagnóstico (módulos transversales)

No son "consultas" de especialidad, sino servicios que atienden a todas. Se conectan al núcleo (paciente, orden, resultado, cobro).

| Módulo | Descripción | Estado |
|--------|-------------|--------|
| Laboratorio clínico | Catálogo de exámenes + órdenes + captura de resultados (fuera de rango) + informe PDF | ✅ |
| Diagnóstico por imágenes | Radiología, ecografía, TAC/RM: orden, informe radiológico y archivo adjunto + PDF | ✅ |
| Banco de sangre | Donantes, stock por grupo sanguíneo, solicitudes y despacho de unidades | ✅ |
| Farmacia / Dispensación | Entrega de medicamentos con descuento automático de stock y comprobante PDF | ✅ |
| Emergencias / Triaje | Clasificación Manchester (5 niveles) con colores, cola por prioridad y atención | ✅ |
| Hospitalización | Camas, ingresos/altas, mapa de ocupación y evolución diaria | ✅ |
| UCI / Cuidados intensivos | Monitoreo, balances, escalas | 🔜 |
| Quirófano / Cirugía programada | Programación de sala, parte operatorio | 🔜 |

---

## 4. Cómo se agrega un módulo nuevo (contrato de extensión)

El sistema ya está diseñado para esto sin duplicar núcleo:

1. **Registrar la especialidad** en la tabla maestra (`especialidades`) — desde el panel SuperAdmin (*Especialidades → Nueva*) o el seeder. Con eso ya aparece en el menú de las empresas que la habiliten.
2. **(Opcional) Ficha específica**: crear la vista parcial `resources/views/consultas/especialidad/{slug}.blade.php`. La consulta la incluye automáticamente (`@includeIf`), y si no existe usa la ficha general.
3. **Dónde guardar los datos**:
   - Datos simples/no consultables → columna JSON `consultas.datos_especialidad` (patrón usado por ginecología, cardiología, psicología).
   - Datos estructurados/consultables o con historial → **tabla propia** (patrón usado por `odontograma` dentro del JSON, `vacunas`, `receta_items`). Ej.: `laboratorio_ordenes`, `imagenes_estudios`, `hospitalizaciones`.
4. **Habilitar por empresa**: cada clínica activa solo los módulos que contrató (*Empresas → Editar → Especialidades habilitadas*). El middleware `module` bloquea el acceso a lo no contratado.

---

## 5. Resumen del patrón

- **Un solo núcleo** para pacientes, citas, historia, facturación, usuarios, reportes.
- **Tabla maestra `especialidades`** como punto único de configuración.
- **Fichas por especialidad** enchufables (Blade parcial + JSON o tabla propia).
- **Servicios de apoyo** como módulos transversales que reutilizan el mismo paciente y facturación.
- **Habilitación por empresa** (multi-tenant) para vender por planes/módulos sin bifurcar el código.
