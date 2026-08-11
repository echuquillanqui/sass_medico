@extends('layouts.app')
@section('title', 'Catálogo de laboratorio')

@section('content')
    @php $mon = auth()->user()->empresa->moneda ?? 'S/'; @endphp
    <div class="page-head">
        <div><h1>Catálogo de exámenes</h1><p>Crea exámenes individuales o agrupa varios componentes bajo un solo título.</p></div>
        <button type="button" class="btn btn-primary" onclick="abrirExamenAgrupado()"><i class="fa-solid fa-layer-group"></i> Crear examen agrupado</button>
    </div>

    <div class="grid g-2">
        <div class="card" style="height:fit-content">
            <h3 class="mb">Nuevo examen</h3>
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
                <button class="btn btn-primary"><i class="fa-solid fa-plus"></i> Agregar</button>
            </form>
        </div>
        <div class="table-wrap">
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
                                @if($e->componentes->isNotEmpty())<button type="button" class="btn btn-light btn-sm" onclick='abrirEditarExamen(@json($e))' title="Editar examen agrupado"><i class="fa-solid fa-pen"></i> Editar</button>@endif
                                <form method="POST" action="{{ route('admin.lab-examenes.destroy',$e) }}" onsubmit="return confirm('¿Eliminar examen?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm" title="Eliminar examen"><i class="fa-solid fa-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5"><div class="empty"><i class="fa-solid fa-vials"></i><p>Sin exámenes en el catálogo.</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
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
    .exam-overlay{display:none;position:fixed;inset:0;background:rgba(30,27,75,.48);z-index:999;align-items:flex-start;justify-content:center;padding:32px 16px;overflow:auto}
    .exam-overlay.open{display:flex}
    .exam-modal{background:#fff;border-radius:18px;padding:22px;width:100%;max-width:920px;box-shadow:0 20px 50px rgba(0,0,0,.25)}
    .exam-components{display:flex;flex-direction:column;gap:10px;max-height:45vh;overflow:auto;padding-right:3px}
    .exam-component{display:grid;grid-template-columns:1.4fr .75fr 1fr .55fr auto;gap:9px;align-items:end;background:var(--bg);border:1px solid var(--line);border-radius:12px;padding:12px}
    .exam-actions{margin-top:18px;justify-content:flex-end}
    [data-theme="dark"] .exam-modal{background:#161428}
    @media(max-width:760px){.exam-component{grid-template-columns:1fr 1fr}.exam-component .component-name{grid-column:1/-1}.exam-component .remove-component{grid-column:2;justify-self:end}}
    </style>

    @push('scripts')
    <script>
    var componenteIndice = 0;
    var reabrirExamenAgrupado = @json(old('modo') === 'grupo' && !old('editando_id'));
    var componentesAnteriores = @json(old('modo') === 'grupo' && !old('editando_id') ? old('componentes', []) : []);
    var examenEditadoAnterior = @json(old('editando_id') ? [
        'id' => old('editando_id'), 'nombre' => old('nombre'), 'categoria' => old('categoria'),
        'precio' => old('precio'), 'componentes' => old('componentes', []),
    ] : null);
    var editarIndice = 0;

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
    if(reabrirExamenAgrupado) abrirExamenAgrupado();
    if(examenEditadoAnterior) abrirEditarExamen(examenEditadoAnterior);
    document.addEventListener('keydown',function(event){ if(event.key==='Escape'){ cerrarExamenAgrupado(); cerrarEditarExamen(); } });
    </script>
    @endpush
@endsection
