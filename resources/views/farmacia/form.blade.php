@extends('layouts.app')
@section('title', 'Nueva dispensación')

@section('content')
    @php $mon = auth()->user()->empresa->moneda ?? 'S/'; @endphp
    <div class="page-head">
        <div><h1>Nueva dispensación</h1><p>Entrega de medicamentos e insumos; descuenta stock automáticamente.</p></div>
        <a href="{{ route('farmacia.index') }}" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>

    @if($errors->any())<div class="alert error"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}</div>@endif

    <form method="POST" action="{{ route('farmacia.store') }}" class="card">
        @csrf
        <div class="form-grid mb">
            <div class="field"><label>Paciente</label>
                <select name="paciente_id">
                    <option value="">— Venta libre —</option>
                    @foreach($pacientes as $p)<option value="{{ $p->id }}" @selected($pacienteSel==$p->id)>{{ $p->nombre_completo }}</option>@endforeach
                </select></div>
            <div class="field"><label>Fecha *</label><input type="date" name="fecha" value="{{ now()->toDateString() }}" required></div>
            <div class="field full"><label>Observaciones</label><input name="observaciones"></div>
        </div>

        <div class="flex between mb"><h3 style="margin:0"><i class="fa-solid fa-prescription-bottle-medical" style="color:var(--pink)"></i> Medicamentos / insumos</h3>
            <button type="button" class="btn btn-light btn-sm" onclick="addItem()"><i class="fa-solid fa-plus"></i> Agregar</button></div>
        <div id="items"></div>
        <div class="flex between mt" style="border-top:1px solid var(--line);padding-top:12px">
            <b>Total estimado</b><b id="totalTxt" style="font-size:18px">{{ $mon }} 0.00</b>
        </div>

        <div class="mt"><button class="btn btn-primary"><i class="fa-solid fa-pills"></i> Registrar dispensación</button></div>
    </form>

    @push('scripts')
    <script>
    const MON = @json($mon);
    @php
        $catalogoInsumos = $insumos->map(fn ($i) => ['id' => $i->id, 'nombre' => $i->nombre, 'precio' => (float) $i->precio, 'stock' => (float) $i->stock, 'unidad' => $i->unidad]);
    @endphp
    const INS = @json($catalogoInsumos);
    function opts(){ return INS.map(i=>'<option value="'+i.id+'" data-p="'+i.precio+'" data-s="'+i.stock+'">'+i.nombre+' (stock '+i.stock+' '+i.unidad+')</option>').join(''); }
    function addItem(){
        const i = document.querySelectorAll('#items .disp-item').length;
        const div=document.createElement('div');
        div.className='disp-item';
        div.style.cssText='border:1px dashed var(--line);border-radius:12px;padding:12px;margin-bottom:10px';
        div.innerHTML =
            '<div class="flex between" style="margin-bottom:8px"><b style="font-size:12px;color:var(--ink-soft)">Ítem '+(i+1)+'</b>'+
            '<button type="button" class="btn btn-danger btn-sm" onclick="this.closest(&quot;.disp-item&quot;).remove();calc()"><i class="fa-solid fa-xmark"></i></button></div>'+
            '<div class="form-grid">'+
            '<div class="field"><label>Insumo *</label><select name="items['+i+'][insumo_id]" onchange="calc()" required><option value="">—</option>'+opts()+'</select></div>'+
            '<div class="field"><label>Cantidad *</label><input type="number" step="0.01" min="0.01" name="items['+i+'][cantidad]" value="1" oninput="calc()" required></div>'+
            '<div class="field full"><label>Indicaciones</label><input name="items['+i+'][indicaciones]" placeholder="1 cada 8h..."></div>'+
            '</div>';
        document.getElementById('items').appendChild(div);
    }
    function calc(){
        let total=0;
        document.querySelectorAll('#items .disp-item').forEach(row=>{
            const sel=row.querySelector('select'); const opt=sel.options[sel.selectedIndex];
            const cant=parseFloat(row.querySelector('input[type=number]').value)||0;
            const precio=opt&&opt.dataset.p?parseFloat(opt.dataset.p):0;
            total+=precio*cant;
        });
        document.getElementById('totalTxt').textContent = MON+' '+total.toFixed(2);
    }
    document.addEventListener('DOMContentLoaded', ()=>{ addItem(); });
    </script>
    @endpush
@endsection
