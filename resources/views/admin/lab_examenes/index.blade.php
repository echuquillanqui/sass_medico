@extends('layouts.app')
@section('title', 'Catálogo de laboratorio')

@section('content')
    @php $mon = auth()->user()->empresa->moneda ?? 'S/'; @endphp
    <div class="page-head"><div><h1>Catálogo de exámenes</h1><p>Exámenes de laboratorio disponibles para las órdenes.</p></div></div>

    <div class="grid g-2">
        <div class="card" style="height:fit-content">
            <h3 class="mb">Nuevo examen</h3>
            <form method="POST" action="{{ route('admin.lab-examenes.store') }}">
                @csrf
                <div class="field mb"><label>Nombre *</label><input name="nombre" required>@error('nombre')<span class="err">{{ $message }}</span>@enderror</div>
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
                <thead><tr><th>Examen</th><th>Categoría</th><th>Referencia</th><th>Precio</th><th></th></tr></thead>
                <tbody>
                @forelse($examenes as $e)
                    <tr>
                        <td><b>{{ $e->nombre }}</b>@if($e->unidad)<br><small class="muted">{{ $e->unidad }}</small>@endif</td>
                        <td>{{ $e->categoria ?? '—' }}</td>
                        <td>{{ $e->valor_referencia ?? '—' }}</td>
                        <td>@money($e->precio, null, 2)</td>
                        <td style="text-align:right">
                            <form method="POST" action="{{ route('admin.lab-examenes.destroy',$e) }}" onsubmit="return confirm('¿Eliminar examen?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5"><div class="empty"><i class="fa-solid fa-vials"></i><p>Sin exámenes en el catálogo.</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
