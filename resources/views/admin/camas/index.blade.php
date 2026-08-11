@extends('layouts.app')
@section('title', 'Camas')

@section('content')
    <div class="page-head"><div><h1>Gestión de camas</h1><p>Camas y áreas de hospitalización de la clínica.</p></div></div>

    <div class="grid g-2">
        <div class="card" style="height:fit-content">
            <h3 class="mb">Nueva cama</h3>
            <form method="POST" action="{{ route('admin.camas.store') }}">
                @csrf
                <div class="field mb"><label>Nombre *</label><input name="nombre" placeholder="Cama 101" required>@error('nombre')<span class="err">{{ $message }}</span>@enderror</div>
                <div class="field mb"><label>Área</label><input name="area" placeholder="Hospitalización, UCI, Pediatría..."></div>
                <button class="btn btn-primary"><i class="fa-solid fa-plus"></i> Agregar</button>
            </form>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Cama</th><th>Área</th><th>Estado</th><th></th></tr></thead>
                <tbody>
                @forelse($camas as $c)
                    <tr>
                        <td><b>{{ $c->nombre }}</b></td>
                        <td>{{ $c->area ?? '—' }}</td>
                        <td>@if($c->ocupada_count)<span class="pill red">Ocupada</span>@else<span class="pill green">Libre</span>@endif</td>
                        <td style="text-align:right">
                            <form method="POST" action="{{ route('admin.camas.destroy',$c) }}" onsubmit="return confirm('¿Eliminar cama?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4"><div class="empty"><i class="fa-solid fa-bed"></i><p>Sin camas registradas.</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
