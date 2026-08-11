@extends('layouts.app')
@section('title', 'Nueva orden de laboratorio')

@section('content')
    <div class="page-head">
        <div><h1>Nueva orden de laboratorio</h1><p>Selecciona el paciente y los exámenes solicitados.</p></div>
        <a href="{{ route('laboratorio.index') }}" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>

    @if($errors->any())<div class="alert error"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}</div>@endif

    <form method="POST" action="{{ route('laboratorio.store') }}" class="card">
        @csrf
        <div class="form-grid mb">
            <div class="field"><label>Paciente *</label>
                <select name="paciente_id" required>
                    <option value="">— Selecciona —</option>
                    @foreach($pacientes as $p)<option value="{{ $p->id }}" @selected(($pacienteSel ?? null)==$p->id)>{{ $p->nombre_completo }}</option>@endforeach
                </select></div>
            <div class="field"><label>Médico solicitante</label>
                <select name="medico_id">
                    <option value="">— Yo —</option>
                    @foreach($medicos as $m)<option value="{{ $m->id }}">{{ $m->name }}</option>@endforeach
                </select></div>
            <div class="field"><label>Fecha *</label><input type="date" name="fecha" value="{{ now()->toDateString() }}" required></div>
            <div class="field full"><label>Observaciones</label><input name="observaciones"></div>
        </div>

        <div class="flex between mb"><h3 style="margin:0"><i class="fa-solid fa-vials" style="color:var(--info)"></i> Exámenes</h3>
            <button type="button" class="btn btn-light btn-sm" onclick="addItem()"><i class="fa-solid fa-plus"></i> Agregar examen</button></div>
        <div id="items"></div>

        <div class="mt"><button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Crear orden</button></div>
    </form>

    @push('scripts')
    <script>
    const CATALOGO = @json($examenes->map(fn($e)=>['id'=>$e->id,'nombre'=>$e->nombre,'unidad'=>$e->unidad,'ref'=>$e->valor_referencia]));
    function addItem(){
        const i = document.querySelectorAll('#items .lab-item').length;
        const opts = CATALOGO.map(e=>'<option value="'+e.id+'" data-u="'+(e.unidad||'')+'" data-r="'+(e.ref||'')+'">'+e.nombre+'</option>').join('');
        const div = document.createElement('div');
        div.className='lab-item';
        div.style.cssText='border:1px dashed var(--line);border-radius:12px;padding:12px;margin-bottom:10px';
        div.innerHTML =
            '<div class="flex between" style="margin-bottom:8px"><b style="font-size:12px;color:var(--ink-soft)">Examen '+(i+1)+'</b>'+
            '<button type="button" class="btn btn-danger btn-sm" onclick="this.closest(&quot;.lab-item&quot;).remove()"><i class="fa-solid fa-xmark"></i></button></div>'+
            '<div class="form-grid">'+
            '<div class="field"><label>Del catálogo</label><select onchange="fromCat(this,'+i+')"><option value="">— Manual —</option>'+opts+'</select></div>'+
            '<div class="field"><label>Nombre *</label><input name="items['+i+'][nombre]" id="n'+i+'"></div>'+
            '<div class="field"><label>Unidad</label><input name="items['+i+'][unidad]" id="u'+i+'"></div>'+
            '<div class="field"><label>Valor referencia</label><input name="items['+i+'][valor_referencia]" id="r'+i+'"></div>'+
            '</div>'+
            '<input type="hidden" name="items['+i+'][lab_examen_id]" id="e'+i+'">';
        document.getElementById('items').appendChild(div);
    }
    function fromCat(sel,i){
        const o=sel.options[sel.selectedIndex];
        document.getElementById('e'+i).value = o.value||'';
        document.getElementById('n'+i).value = o.value ? o.text : '';
        document.getElementById('u'+i).value = o.dataset.u||'';
        document.getElementById('r'+i).value = o.dataset.r||'';
    }
    document.addEventListener('DOMContentLoaded', ()=>addItem());
    </script>
    @endpush
@endsection
