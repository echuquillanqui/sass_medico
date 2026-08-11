@extends('layouts.app')
@section('title', $insumo->exists ? 'Editar insumo' : 'Nuevo insumo')

@section('content')
    <div class="page-head">
        <div><h1>{{ $insumo->exists ? 'Editar insumo' : 'Nuevo insumo' }}</h1><p>Datos del insumo o medicamento.</p></div>
        <a href="{{ route('insumos.index') }}" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>

    <form method="POST" action="{{ $insumo->exists ? route('insumos.update',$insumo) : route('insumos.store') }}" class="card">
        @csrf
        @if($insumo->exists) @method('PUT') @endif
        <div class="form-grid">
            <div class="field"><label>Nombre *</label><input name="nombre" value="{{ old('nombre',$insumo->nombre) }}" required>@error('nombre')<span class="err">{{ $message }}</span>@enderror</div>
            <div class="field"><label>Categoría</label><input name="categoria" value="{{ old('categoria',$insumo->categoria) }}" placeholder="Medicamento, material..."></div>
            <div class="field"><label>Código</label><input name="codigo" value="{{ old('codigo',$insumo->codigo) }}"></div>
            <div class="field"><label>Unidad</label><input name="unidad" value="{{ old('unidad',$insumo->unidad ?? 'unidad') }}" placeholder="caja, ml, unidad"></div>
            <div class="field"><label>Stock inicial</label><input type="number" step="0.01" name="stock" value="{{ old('stock',$insumo->stock ?? 0) }}"></div>
            <div class="field"><label>Stock mínimo</label><input type="number" step="0.01" name="stock_minimo" value="{{ old('stock_minimo',$insumo->stock_minimo ?? 0) }}"></div>
            <div class="field"><label>Precio unitario</label><input type="number" step="0.01" name="precio" value="{{ old('precio',$insumo->precio ?? 0) }}"></div>
        </div>
        <div class="mt"><button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar insumo</button></div>
    </form>
@endsection
