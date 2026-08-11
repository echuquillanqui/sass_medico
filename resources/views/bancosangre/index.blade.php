@extends('layouts.app')
@section('title', 'Banco de sangre')

@section('content')
    <div class="page-head">
        <div><h1>Banco de sangre</h1><p>Stock por grupo, donantes y solicitudes de transfusión.</p></div>
        <a href="{{ route('bancosangre.donantes') }}" class="btn btn-light"><i class="fa-solid fa-hand-holding-droplet"></i> Donantes</a>
    </div>

    <div class="grid g-3 mb">
        <div class="kpi k1"><div class="kpi-top"><span class="kpi-cap">Unidades disponibles</span><span class="kpi-ic"><i class="fa-solid fa-droplet"></i></span></div><div><div class="kpi-val">{{ $totalDisponible }}</div></div></div>
        <div class="kpi k2"><div class="kpi-top"><span class="kpi-cap">Donantes</span><span class="kpi-ic"><i class="fa-solid fa-users"></i></span></div><div><div class="kpi-val">{{ $totalDonantes }}</div></div></div>
        <div class="kpi k4"><div class="kpi-top"><span class="kpi-cap">Solicitudes pendientes</span><span class="kpi-ic"><i class="fa-solid fa-clipboard-list"></i></span></div><div><div class="kpi-val">{{ $solicitudes->count() }}</div></div></div>
    </div>

    <div class="card mb">
        <h3 class="mb">Stock por grupo sanguíneo</h3>
        <div class="grid g-4" style="gap:12px">
            @foreach($grupos as $g)
                @php $n = $stock[$g]; $c = $n === 0 ? '#ef4444' : ($n < 3 ? '#f59e0b' : '#22c55e'); @endphp
                <div class="card" style="text-align:center;padding:16px;box-shadow:none;border:1px solid var(--line)">
                    <div style="font-size:22px;font-weight:800;color:#dc2626"><i class="fa-solid fa-droplet"></i> {{ $g }}</div>
                    <div style="font-size:28px;font-weight:700;color:{{ $c }};margin-top:4px">{{ $n }}</div>
                    <div class="cap" style="font-size:11px;color:var(--ink-soft)">unidades</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid g-2 mb">
        <div class="card">
            <h3 class="mb"><i class="fa-solid fa-droplet" style="color:#ef4444"></i> Registrar unidad (extracción)</h3>
            <form method="POST" action="{{ route('bancosangre.unidad.store') }}">
                @csrf
                <div class="form-grid mb">
                    <div class="field"><label>Donante</label>
                        <select name="donante_id" onchange="if(this.value){document.getElementById('grpU').value=this.options[this.selectedIndex].dataset.g||''}">
                            <option value="">— Externo —</option>
                            @foreach($donantes as $d)<option value="{{ $d->id }}" data-g="{{ $d->grupo }}">{{ $d->nombre_completo }} ({{ $d->grupo }})</option>@endforeach
                        </select></div>
                    <div class="field"><label>Grupo *</label>
                        <select name="grupo" id="grpU" required>@foreach($grupos as $g)<option value="{{ $g }}">{{ $g }}</option>@endforeach</select></div>
                    <div class="field"><label>Volumen (ml)</label><input type="number" name="volumen" value="450"></div>
                    <div class="field"><label>Fecha extracción *</label><input type="date" name="fecha_extraccion" value="{{ now()->toDateString() }}" required></div>
                </div>
                <button class="btn btn-primary"><i class="fa-solid fa-plus"></i> Ingresar al stock</button>
            </form>
        </div>
        <div class="card">
            <h3 class="mb"><i class="fa-solid fa-clipboard-list" style="color:var(--violet)"></i> Solicitar sangre</h3>
            <form method="POST" action="{{ route('bancosangre.solicitud.store') }}">
                @csrf
                <div class="form-grid mb">
                    <div class="field"><label>Paciente *</label>
                        <select name="paciente_id" required><option value="">—</option>@foreach($pacientes as $p)<option value="{{ $p->id }}">{{ $p->nombre_completo }}</option>@endforeach</select></div>
                    <div class="field"><label>Médico</label>
                        <select name="medico_id"><option value="">— Yo —</option>@foreach($medicos as $m)<option value="{{ $m->id }}">{{ $m->name }}</option>@endforeach</select></div>
                    <div class="field"><label>Grupo *</label><select name="grupo" required>@foreach($grupos as $g)<option value="{{ $g }}">{{ $g }}</option>@endforeach</select></div>
                    <div class="field"><label>Cantidad (uds) *</label><input type="number" name="cantidad" value="1" min="1" required></div>
                    <div class="field"><label>Fecha *</label><input type="date" name="fecha" value="{{ now()->toDateString() }}" required></div>
                    <div class="field"><label>Motivo</label><input name="motivo"></div>
                </div>
                <button class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Registrar solicitud</button>
            </form>
        </div>
    </div>

    <div class="card mb" style="padding:0">
        <div style="padding:18px 22px 8px"><h3 style="margin:0">Solicitudes pendientes</h3></div>
        <div class="table-wrap" style="box-shadow:none;border-radius:0">
            <table>
                <thead><tr><th>Paciente</th><th>Grupo</th><th>Cantidad</th><th>Médico</th><th>Fecha</th><th></th></tr></thead>
                <tbody>
                @forelse($solicitudes as $s)
                    <tr>
                        <td>{{ $s->paciente->nombre_completo ?? '—' }}</td>
                        <td><span class="pill red">{{ $s->grupo }}</span></td>
                        <td>{{ $s->cantidad }}</td>
                        <td>{{ $s->medico->name ?? '—' }}</td>
                        <td>{{ $s->fecha->format('d/m/Y') }}</td>
                        <td style="text-align:right;white-space:nowrap">
                            <form method="POST" action="{{ route('bancosangre.despachar',$s) }}" style="display:inline">@csrf<button class="btn btn-primary btn-sm"><i class="fa-solid fa-truck-droplet"></i> Despachar</button></form>
                            <form method="POST" action="{{ route('bancosangre.solicitud.cancelar',$s) }}" style="display:inline" onsubmit="return confirm('¿Cancelar solicitud?')">@csrf<button class="btn btn-danger btn-sm"><i class="fa-solid fa-xmark"></i></button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6"><div class="empty"><i class="fa-solid fa-clipboard-check"></i><p>No hay solicitudes pendientes.</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($porVencer->isNotEmpty())
        <div class="card" style="background:#fff7ed">
            <h3 class="mb"><i class="fa-solid fa-triangle-exclamation" style="color:#f59e0b"></i> Unidades por vencer (7 días)</h3>
            <div class="flex gap" style="flex-wrap:wrap">
                @foreach($porVencer as $u)
                    <span class="pill amber">{{ $u->grupo }} · vence {{ $u->fecha_vencimiento->format('d/m') }}
                        <form method="POST" action="{{ route('bancosangre.unidad.descartar',$u) }}" style="display:inline" onsubmit="return confirm('¿Descartar unidad?')">@csrf<button style="border:none;background:none;cursor:pointer;color:#b45309"><i class="fa-solid fa-trash"></i></button></form>
                    </span>
                @endforeach
            </div>
        </div>
    @endif
@endsection
