@extends('layouts.app')
@section('title', 'Donantes')

@section('content')
    <div class="page-head">
        <div><h1>Donantes de sangre</h1><p>Registro de donantes de la clínica.</p></div>
        <a href="{{ route('bancosangre.index') }}" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Banco de sangre</a>
    </div>

    <div class="grid g-2">
        <div class="card" style="height:fit-content">
            <h3 class="mb">Nuevo donante</h3>
            <form method="POST" action="{{ route('bancosangre.donante.store') }}">
                @csrf
                <div class="form-grid mb">
                    <div class="field"><label>Nombres *</label><input name="nombres" required></div>
                    <div class="field"><label>Apellidos *</label><input name="apellidos" required></div>
                    <div class="field"><label>Documento</label><input name="documento"></div>
                    <div class="field"><label>Grupo *</label><select name="grupo" required>@foreach($grupos as $g)<option value="{{ $g }}">{{ $g }}</option>@endforeach</select></div>
                    <div class="field full"><label>Teléfono</label><input name="telefono"></div>
                </div>
                <button class="btn btn-primary"><i class="fa-solid fa-plus"></i> Registrar</button>
            </form>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Donante</th><th>Grupo</th><th>Última donación</th><th>Donaciones</th></tr></thead>
                <tbody>
                @forelse($donantes as $d)
                    <tr>
                        <td><b>{{ $d->nombre_completo }}</b>@if($d->telefono)<br><small class="muted">{{ $d->telefono }}</small>@endif</td>
                        <td><span class="pill red">{{ $d->grupo }}</span></td>
                        <td>{{ optional($d->fecha_ultima_donacion)->format('d/m/Y') ?? '—' }}</td>
                        <td>{{ $d->unidades_count }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4"><div class="empty"><i class="fa-solid fa-hand-holding-droplet"></i><p>Sin donantes registrados.</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
