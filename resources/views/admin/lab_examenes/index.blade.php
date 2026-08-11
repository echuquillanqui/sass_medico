@extends('layouts.app')
@section('title', 'Catálogo de laboratorio')

@section('content')
    @php $mon = auth()->user()->empresa->moneda ?? 'S/'; @endphp
    <div class="page-head">
        <div><h1>Catálogo de exámenes</h1><p>Crea exámenes individuales o agrupa varios componentes bajo un solo título.</p></div>
        <button type="button" class="btn btn-primary" onclick="abrirExamenAgrupado()"><i class="fa-solid fa-layer-group"></i> Crear examen agrupado</button>
    </div>

    <div class="exam-tabs" role="tablist" aria-label="Gestión del catálogo de exámenes">
        <button type="button" class="exam-tab active" id="tab-registro" role="tab" aria-selected="true" aria-controls="panel-registro" onclick="cambiarTabExamen('registro')">
            <i class="fa-solid fa-circle-plus"></i><span><b>Registrar examen</b><small>Agrega un examen al catálogo</small></span>
        </button>
        <button type="button" class="exam-tab" id="tab-listado" role="tab" aria-selected="false" aria-controls="panel-listado" onclick="cambiarTabExamen('listado')">
            <i class="fa-solid fa-vials"></i><span><b>Listado de exámenes</b><small>{{ $examenes->count() }} {{ $examenes->count() === 1 ? 'examen registrado' : 'exámenes registrados' }}</small></span>
        </button>
    </div>

    <section class="exam-tab-panel active" id="panel-registro" role="tabpanel" aria-labelledby="tab-registro">
        <div class="card exam-register-card">
            <div class="exam-section-head"><span class="exam-section-icon"><i class="fa-solid fa-flask-vial"></i></span><div><h3>Nuevo examen</h3><p>Completa la información para incorporarlo al catálogo.</p></div></div>
            <form method="POST" action="{{ route('admin.lab-examenes.store') }}">
                @csrf
                <div class="field mb"><label>Nombre *</label><input name="nombre" required>@error('nombre')<span class="err">{{ $message }}</span>@enderror</div>
                <div class="field mb"><label>Forma parte de</label>
                    <select name="padre_id">
                        <option value="">— Examen individual o título principal —</option>
                        @foreach($examenesPrincipales as $principal)
                            <option value="{{ $principal->id }}" @selected(old('padre_id') == $principal->id)>{{ $principal->nombre }}</option>
                        @endforeach
                    </select>
                    <small class="muted">Ejemplo: crea “Examen de orina” y luego agrega color, pH y proteínas como sus componentes.</small>
                    @error('padre_id')<span class="err">{{ $message }}</span>@enderror
                </div>
                <div class="field mb"><label>Categoría</label><input name="categoria" placeholder="Hematología, Bioquímica..."></div>
                <div class="form-grid mb">
                    <div class="field"><label>Unidad</label><input name="unidad" placeholder="mg/dL"></div>
                    <div class="field"><label>Valor referencia</label><input name="valor_referencia" placeholder="70-110"></div>
                </div>
                <div class="field mb"><label>Precio</label><input type="number" step="0.01" name="precio" value="0"></div>
                <div class="exam-form-actions"><button class="btn btn-primary"><i class="fa-solid fa-plus"></i> Registrar examen</button></div>
            </form>
        </div>
    </section>

    <section class="exam-tab-panel" id="panel-listado" role="tabpanel" aria-labelledby="tab-listado" hidden>
        <div class="exam-list-head"><div><h3>Exámenes registrados</h3><p class="muted">Consulta los detalles o elimina registros que ya no necesites.</p></div><button type="button" class="btn btn-light" onclick="cambiarTabExamen('registro')"><i class="fa-solid fa-plus"></i> Nuevo examen</button></div>
        <div class="table-wrap exam-table">
            <table>
                <thead><tr><th>Examen</th><th>Categoría</th><th>Referencia</th><th>Precio</th><th>Acciones</th></tr></thead>
                <tbody>
                @forelse($examenes as $e)
                    <tr>
                        <td>
                            <b>{{ $e->nombre }}</b>@if($e->unidad)<br><small class="muted">{{ $e->unidad }}</small>@endif
                            @if($e->componentes->isNotEmpty())<br><small class="muted"><i class="fa-solid fa-layer-group"></i> {{ $e->componentes->count() }} componentes</small>@endif
                        </td>
                        <td>{{ $e->categoria ?? '—' }}</td>
                        <td>{{ $e->valor_referencia ?? '—' }}</td>
                        <td>@money($e->precio, null, 2)</td>
                        <td style="text-align:right">
                            <div class="flex gap" style="justify-content:flex-end">
                                <button type="button" class="btn btn-light btn-sm" onclick="verExamen({{ $e->id }})" title="Ver detalles del examen"><i class="fa-solid fa-eye"></i> Ver</button>
                                @if($e->componentes->isNotEmpty())<button type="button" class="btn btn-light btn-sm" onclick='abrirEditarExamen(@json($e))' title="Editar examen agrupado"><i class="fa-solid fa-pen"></i> Editar</button>@endif
                                <form method="POST" action="{{ route('admin.lab-examenes.destroy',$e) }}" onsubmit="return confirm('¿Eliminar este examen? Esta acción no se puede deshacer.')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm" title="Eliminar examen"><i class="fa-solid fa-trash"></i> Eliminar</button></form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5"><div class="empty"><i class="fa-solid fa-vials"></i><p>Sin exámenes en el catálogo.</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div id="verExamenOverlay" class="exam-overlay" onclick="if(event.target===this)cerrarVerExamen()" aria-hidden="true">
        <div class="exam-modal exam-detail-modal" role="dialog" aria-modal="true" aria-labelledby="verExamenTitulo">
            <div class="flex between mb"><div><span class="exam-detail-label">Detalle del examen</span><h3 id="verExamenTitulo"></h3></div><button type="button" class="btn btn-light btn-sm" onclick="cerrarVerExamen()" aria-label="Cerrar"><i class="fa-solid fa-xmark"></i></button></div>
            <div class="exam-detail-grid">
                <div><small>Categoría</small><strong id="verExamenCategoria"></strong></div><div><small>Precio</small><strong id="verExamenPrecio"></strong></div><div><small>Unidad</small><strong id="verExamenUnidad"></strong></div><div><small>Valor de referencia</small><strong id="verExamenReferencia"></strong></div>
            </div>
            <div id="verComponentesBloque"><div class="exam-detail-components-head"><b>Componentes incluidos</b><span id="verComponentesCantidad"></span></div><div id="verExamenComponentes" class="exam-detail-components"></div></div>
            <div class="exam-actions"><button type="button" class="btn btn-primary" onclick="cerrarVerExamen()">Cerrar detalle</button></div>
        </div>
    </div>

    <div id="editarExamenOverlay" class="exam-overlay" onclick="if(event.target===this)cerrarEditarExamen()" aria-hidden="true">
        <div class="exam-modal" role="dialog" aria-modal="true" aria-labelledby="editarExamenTitulo">
            <div class="flex between mb">
                <div><h3 id="editarExamenTitulo" style="margin:0">Editar examen agrupado</h3><small class="muted">Modifica sus datos, agrega componentes o cambia sus rangos.</small></div>
                <button type="button" class="btn btn-light btn-sm" onclick="cerrarEditarExamen()" aria-label="Cerrar"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form method="POST" id="editarExamenForm">
                @csrf @method('PUT')
                <input type="hidden" name="modo" value="grupo"><input type="hidden" name="editando_id" id="editandoId">
                <div class="form-grid mb">
                    <div class="field"><label>Nombre del examen *</label><input name="nombre" required maxlength="120"></div>
                    <div class="field"><label>Categoría</label><input name="categoria" maxlength="60"></div>
                    <div class="field full"><label>Precio del examen agrupado</label><input type="number" min="0" step="0.01" name="precio" value="0"></div>
                </div>
                <div class="flex between mb"><div><b>Exámenes incluidos</b><br><small class="muted">Edita la unidad o rango y agrega los componentes que necesites.</small></div><button type="button" class="btn btn-light btn-sm" onclick="agregarComponenteEditar()"><i class="fa-solid fa-plus"></i> Agregar examen</button></div>
                <div id="componentesEditar" class="exam-components"></div>
                <div class="flex gap exam-actions"><button type="button" class="btn btn-light" onclick="cerrarEditarExamen()">Cancelar</button><button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar cambios</button></div>
            </form>
        </div>
    </div>

    <div id="examenAgrupadoOverlay" class="exam-overlay" onclick="if(event.target===this)cerrarExamenAgrupado()" aria-hidden="true">
        <div class="exam-modal" role="dialog" aria-modal="true" aria-labelledby="examenAgrupadoTitulo">
            <div class="flex between mb">
                <div><h3 id="examenAgrupadoTitulo" style="margin:0">Nuevo examen agrupado</h3><small class="muted">Define el título y agrega todos sus exámenes o componentes.</small></div>
                <button type="button" class="btn btn-light btn-sm" onclick="cerrarExamenAgrupado()" aria-label="Cerrar"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form method="POST" action="{{ route('admin.lab-examenes.store') }}" id="examenAgrupadoForm">
                @csrf
                <input type="hidden" name="modo" value="grupo">
                <div class="form-grid mb">
                    <div class="field"><label>Nombre del examen *</label><input name="nombre" value="{{ old('modo') === 'grupo' ? old('nombre') : '' }}" required placeholder="Ej. Perfil lipídico"></div>
                    <div class="field"><label>Categoría</label><input name="categoria" value="{{ old('modo') === 'grupo' ? old('categoria') : '' }}" placeholder="Bioquímica, Hematología..."></div>
                    <div class="field full"><label>Precio del examen agrupado</label><input type="number" min="0" step="0.01" name="precio" value="{{ old('modo') === 'grupo' ? old('precio', 0) : 0 }}"></div>
                </div>

                <div class="flex between mb"><div><b>Exámenes incluidos</b><br><small class="muted">Cada fila admite su propia unidad, rango de referencia y precio.</small></div><button type="button" class="btn btn-light btn-sm" onclick="agregarComponente()"><i class="fa-solid fa-plus"></i> Agregar examen</button></div>
                <div id="componentesExamen" class="exam-components"></div>
                @error('componentes')<span class="err">{{ $message }}</span>@enderror

                <div class="flex gap exam-actions">
                    <button type="button" class="btn btn-light" onclick="cerrarExamenAgrupado()">Cancelar</button>
                    <button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar examen agrupado</button>
                </div>
            </form>
        </div>
    </div>

    <style>
    .exam-tabs{display:flex;gap:10px;margin-bottom:18px;padding:6px;background:var(--card);border:1px solid var(--line);border-radius:16px;box-shadow:var(--shadow)}
    .exam-tab{flex:1;display:flex;align-items:center;gap:12px;padding:14px 18px;border:0;border-radius:12px;background:transparent;color:var(--ink-soft);text-align:left;cursor:pointer;transition:.2s}
    .exam-tab>i{width:38px;height:38px;display:grid;place-items:center;border-radius:11px;background:var(--bg-pink);color:#a855f7;font-size:16px}.exam-tab span{display:flex;flex-direction:column;gap:2px}.exam-tab b{color:var(--ink);font-size:14px}.exam-tab small{font-size:11px}
    .exam-tab.active{background:linear-gradient(135deg,#8b5cf6,#ec4899);color:#fff;box-shadow:0 8px 20px rgba(168,85,247,.22)}.exam-tab.active>i{background:rgba(255,255,255,.2);color:#fff}.exam-tab.active b{color:#fff}
    .exam-tab-panel{display:none}.exam-tab-panel.active{display:block}.exam-register-card{max-width:980px;margin:auto}.exam-section-head{display:flex;align-items:center;gap:13px;margin-bottom:22px;padding-bottom:18px;border-bottom:1px solid var(--line)}.exam-section-head h3,.exam-list-head h3{margin:0 0 3px}.exam-section-head p,.exam-list-head p{margin:0;font-size:12px;color:var(--ink-soft)}.exam-section-icon{width:44px;height:44px;display:grid;place-items:center;border-radius:13px;color:#fff;background:var(--grad)}.exam-form-actions{display:flex;justify-content:flex-end;padding-top:4px}.exam-list-head{display:flex;align-items:center;justify-content:space-between;gap:15px;margin-bottom:14px}.exam-table{box-shadow:var(--shadow)}
    .exam-overlay{display:none;position:fixed;inset:0;background:rgba(30,27,75,.48);z-index:999;align-items:flex-start;justify-content:center;padding:32px 16px;overflow:auto}
    .exam-overlay.open{display:flex}
    .exam-modal{background:#fff;border-radius:18px;padding:22px;width:100%;max-width:920px;box-shadow:0 20px 50px rgba(0,0,0,.25)}
    .exam-components{display:flex;flex-direction:column;gap:10px;max-height:45vh;overflow:auto;padding-right:3px}
    .exam-component{display:grid;grid-template-columns:1.4fr .75fr 1fr .55fr auto;gap:9px;align-items:end;background:var(--bg);border:1px solid var(--line);border-radius:12px;padding:12px}
    .exam-actions{margin-top:18px;justify-content:flex-end}
    .exam-detail-modal{max-width:680px}.exam-detail-label{display:block;color:#9333ea;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;margin-bottom:4px}.exam-detail-modal h3{margin:0;font-size:22px}.exam-detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:1px;background:var(--line);border:1px solid var(--line);border-radius:13px;overflow:hidden;margin:18px 0}.exam-detail-grid>div{background:var(--card);padding:15px}.exam-detail-grid small{display:block;color:var(--ink-soft);margin-bottom:5px}.exam-detail-grid strong{font-size:14px}.exam-detail-components-head{display:flex;justify-content:space-between;margin-bottom:9px}.exam-detail-components-head span{font-size:11px;color:#9333ea;background:var(--bg-pink);padding:4px 9px;border-radius:20px}.exam-detail-components{display:flex;flex-direction:column;gap:7px}.exam-detail-component{display:grid;grid-template-columns:1.4fr .7fr 1fr .6fr;gap:10px;padding:11px 13px;border:1px solid var(--line);border-radius:10px;font-size:12px}.exam-detail-component span:not(:first-child){color:var(--ink-soft)}
    [data-theme="dark"] .exam-modal{background:#161428}
    @media(max-width:760px){.exam-tab small{display:none}.exam-tab{padding:11px}.exam-list-head{align-items:flex-end}.exam-component{grid-template-columns:1fr 1fr}.exam-component .component-name{grid-column:1/-1}.exam-component .remove-component{grid-column:2;justify-self:end}.exam-detail-component{grid-template-columns:1fr 1fr}.exam-table{overflow-x:auto}}
    </style>

    @push('scripts')
    @php
        $reabrirExamenAgrupado = old('modo') === 'grupo' && !old('editando_id');
        $componentesAnteriores = $reabrirExamenAgrupado ? old('componentes', []) : [];
        $examenEditadoAnterior = old('editando_id') ? [
            'id' => old('editando_id'),
            'nombre' => old('nombre'),
            'categoria' => old('categoria'),
            'precio' => old('precio'),
            'componentes' => old('componentes', []),
        ] : null;
    @endphp
    <script>
    var componenteIndice = 0;
    var reabrirExamenAgrupado = @json($reabrirExamenAgrupado);
    var componentesAnteriores = @json($componentesAnteriores);
    var examenEditadoAnterior = @json($examenEditadoAnterior);
    var examenesCatalogo = @json($examenes->keyBy('id'));
    var editarIndice = 0;
    var monedaExamen = @json($mon);

    function cambiarTabExamen(tab){
        ['registro','listado'].forEach(function(nombre){
            var activo = nombre === tab;
            var boton = document.getElementById('tab-' + nombre);
            var panel = document.getElementById('panel-' + nombre);
            boton.classList.toggle('active', activo);
            boton.setAttribute('aria-selected', activo ? 'true' : 'false');
            panel.classList.toggle('active', activo);
            panel.hidden = !activo;
        });
        window.history.replaceState(null, '', '#' + tab);
    }
    function textoDetalle(id, valor){ document.getElementById(id).textContent = valor || '—'; }
    function precioDetalle(valor){ return monedaExamen + ' ' + Number(valor || 0).toFixed(2); }
    function verExamen(examenId){
        var examen = examenesCatalogo[examenId];
        if(!examen) return;
        textoDetalle('verExamenTitulo', examen.nombre);
        textoDetalle('verExamenCategoria', examen.categoria);
        textoDetalle('verExamenPrecio', precioDetalle(examen.precio));
        textoDetalle('verExamenUnidad', examen.unidad);
        textoDetalle('verExamenReferencia', examen.valor_referencia);
        var componentes = examen.componentes || [];
        var bloque = document.getElementById('verComponentesBloque');
        var contenedor = document.getElementById('verExamenComponentes');
        bloque.style.display = componentes.length ? 'block' : 'none';
        textoDetalle('verComponentesCantidad', componentes.length + (componentes.length === 1 ? ' componente' : ' componentes'));
        contenedor.innerHTML = '';
        componentes.forEach(function(componente){
            var fila = document.createElement('div');
            fila.className = 'exam-detail-component';
            [componente.nombre || '—', componente.unidad || 'Sin unidad', componente.valor_referencia || 'Sin referencia', precioDetalle(componente.precio)].forEach(function(valor){
                var celda = document.createElement('span'); celda.textContent = valor; fila.appendChild(celda);
            });
            contenedor.appendChild(fila);
        });
        var overlay = document.getElementById('verExamenOverlay');
        overlay.classList.add('open'); overlay.setAttribute('aria-hidden','false');
    }
    function cerrarVerExamen(){
        var overlay = document.getElementById('verExamenOverlay');
        overlay.classList.remove('open'); overlay.setAttribute('aria-hidden','true');
    }

    function agregarComponente(datos){
        datos = datos || {};
        var indice = componenteIndice++;
        var fila = document.createElement('div');
        fila.className = 'exam-component';
        fila.innerHTML = '<div class="field component-name"><label>Nombre *</label><input name="componentes['+indice+'][nombre]" required maxlength="120" placeholder="Ej. Colesterol total"></div>'+
            '<div class="field"><label>Unidad</label><input name="componentes['+indice+'][unidad]" maxlength="30" placeholder="mg/dL"></div>'+
            '<div class="field"><label>Rango de referencia</label><input name="componentes['+indice+'][valor_referencia]" maxlength="60" placeholder="Ej. 70-110"></div>'+
            '<div class="field"><label>Precio</label><input type="number" min="0" step="0.01" name="componentes['+indice+'][precio]" value="0"></div>'+
            '<button type="button" class="btn btn-danger btn-sm remove-component" onclick="eliminarComponente(this)" title="Quitar examen"><i class="fa-solid fa-trash"></i></button>';
        fila.querySelector('[name$="[nombre]"]').value = datos.nombre || '';
        fila.querySelector('[name$="[unidad]"]').value = datos.unidad || '';
        fila.querySelector('[name$="[valor_referencia]"]').value = datos.valor_referencia || '';
        fila.querySelector('[name$="[precio]"]').value = datos.precio || 0;
        document.getElementById('componentesExamen').appendChild(fila);
    }
    function eliminarComponente(boton){
        var contenedor = document.getElementById('componentesExamen');
        boton.closest('.exam-component').remove();
        if(!contenedor.children.length) agregarComponente();
    }
    function agregarComponenteEditar(datos){
        datos = datos || {};
        var indice = editarIndice++;
        var fila = document.createElement('div');
        fila.className = 'exam-component';
        fila.innerHTML = '<input type="hidden" name="componentes['+indice+'][id]">'+
            '<div class="field component-name"><label>Nombre *</label><input name="componentes['+indice+'][nombre]" required maxlength="120"></div>'+
            '<div class="field"><label>Unidad</label><input name="componentes['+indice+'][unidad]" maxlength="30"></div>'+
            '<div class="field"><label>Rango de referencia</label><input name="componentes['+indice+'][valor_referencia]" maxlength="60"></div>'+
            '<div class="field"><label>Precio</label><input type="number" min="0" step="0.01" name="componentes['+indice+'][precio]"></div>'+
            '<button type="button" class="btn btn-danger btn-sm remove-component" onclick="this.closest(\'.exam-component\').remove()" title="Quitar examen"><i class="fa-solid fa-trash"></i></button>';
        ['id','nombre','unidad','valor_referencia','precio'].forEach(function(campo){ fila.querySelector('[name$="['+campo+']"]').value = datos[campo] || (campo === 'precio' ? 0 : ''); });
        document.getElementById('componentesEditar').appendChild(fila);
    }
    function abrirEditarExamen(examen){
        editarIndice = 0;
        document.getElementById('componentesEditar').innerHTML = '';
        var form = document.getElementById('editarExamenForm');
        form.action = @json(route('admin.lab-examenes.index')) + '/' + examen.id;
        document.getElementById('editandoId').value = examen.id;
        form.querySelector('[name="nombre"]').value = examen.nombre || '';
        form.querySelector('[name="categoria"]').value = examen.categoria || '';
        form.querySelector('[name="precio"]').value = examen.precio || 0;
        (examen.componentes || []).forEach(agregarComponenteEditar);
        if(!(examen.componentes || []).length) agregarComponenteEditar();
        var overlay = document.getElementById('editarExamenOverlay');
        overlay.classList.add('open'); overlay.setAttribute('aria-hidden','false');
    }
    function cerrarEditarExamen(){
        var overlay = document.getElementById('editarExamenOverlay');
        overlay.classList.remove('open'); overlay.setAttribute('aria-hidden','true');
    }
    function abrirExamenAgrupado(){
        var overlay = document.getElementById('examenAgrupadoOverlay');
        if(!document.getElementById('componentesExamen').children.length) agregarComponente();
        overlay.classList.add('open'); overlay.setAttribute('aria-hidden','false');
        setTimeout(function(){ overlay.querySelector('input[name="nombre"]').focus(); }, 0);
    }
    function cerrarExamenAgrupado(){
        var overlay = document.getElementById('examenAgrupadoOverlay');
        overlay.classList.remove('open'); overlay.setAttribute('aria-hidden','true');
    }
    componentesAnteriores.forEach(agregarComponente);
    if(window.location.hash === '#listado') cambiarTabExamen('listado');
    if(reabrirExamenAgrupado) abrirExamenAgrupado();
    if(examenEditadoAnterior) abrirEditarExamen(examenEditadoAnterior);
    document.addEventListener('keydown',function(event){ if(event.key==='Escape'){ cerrarExamenAgrupado(); cerrarEditarExamen(); cerrarVerExamen(); } });
    </script>
    @endpush
@endsection
