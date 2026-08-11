@extends('layouts.app')
@section('title', 'Oftalmología · '.$paciente->nombre_completo)

@section('content')
    <div class="page-head">
        <div>
            <h1><i class="fa-solid fa-eye" style="color:#0ea5e9"></i> Evaluación oftalmológica</h1>
            <p>{{ $paciente->nombre_completo }} · {{ $paciente->edad !== null ? $paciente->edad.' años' : 'Edad N/D' }}</p>
        </div>
        <div class="flex gap">
            <a href="{{ route('pacientes.show',$paciente) }}" class="btn btn-ghost"><i class="fa-solid fa-user"></i> Ficha</a>
            <a href="{{ route('oftalmo.index') }}" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Volver</a>
        </div>
    </div>

    @if(session('ok'))<div class="alert ok mb">{{ session('ok') }}</div>@endif

    @if($ultima)
    <div class="grid g-2 mb">
        <div class="card" style="border-top:3px solid #0ea5e9">
            <h3 class="mb"><i class="fa-solid fa-eye"></i> Ojo derecho (OD)</h3>
            <div class="grid g-4">
                <div class="metric"><div class="big">{{ $ultima->od_av ?? '—' }}</div><div class="cap">Agudeza</div></div>
                <div class="metric"><div class="big">{{ $ultima->od_esfera !== null ? sprintf('%+.2f',$ultima->od_esfera) : '—' }}</div><div class="cap">Esfera</div></div>
                <div class="metric"><div class="big">{{ $ultima->od_cilindro !== null ? sprintf('%+.2f',$ultima->od_cilindro) : '—' }}</div><div class="cap">Cilindro</div></div>
                <div class="metric"><div class="big">{{ $ultima->od_pio ?? '—' }}</div><div class="cap">PIO mmHg</div></div>
            </div>
        </div>
        <div class="card" style="border-top:3px solid #6366f1">
            <h3 class="mb"><i class="fa-solid fa-eye"></i> Ojo izquierdo (OS)</h3>
            <div class="grid g-4">
                <div class="metric"><div class="big">{{ $ultima->os_av ?? '—' }}</div><div class="cap">Agudeza</div></div>
                <div class="metric"><div class="big">{{ $ultima->os_esfera !== null ? sprintf('%+.2f',$ultima->os_esfera) : '—' }}</div><div class="cap">Esfera</div></div>
                <div class="metric"><div class="big">{{ $ultima->os_cilindro !== null ? sprintf('%+.2f',$ultima->os_cilindro) : '—' }}</div><div class="cap">Cilindro</div></div>
                <div class="metric"><div class="big">{{ $ultima->os_pio ?? '—' }}</div><div class="cap">PIO mmHg</div></div>
            </div>
        </div>
    </div>
    @endif

    <div class="card mb">
        <h3 class="mb"><i class="fa-solid fa-plus" style="color:var(--info)"></i> Nueva evaluación</h3>
        <form method="POST" action="{{ route('oftalmo.store',$paciente) }}">
            @csrf
            <div class="field mb" style="max-width:220px"><label>Fecha *</label><input type="date" name="fecha" value="{{ now()->toDateString() }}" required></div>
            <div class="table-wrap" style="box-shadow:none">
                <table>
                    <thead><tr><th>Ojo</th><th>Agudeza visual</th><th>Esfera</th><th>Cilindro</th><th>Eje</th><th>PIO (mmHg)</th></tr></thead>
                    <tbody>
                        <tr>
                            <td><b>OD</b></td>
                            <td><input name="od_av" placeholder="20/20" style="width:90px"></td>
                            <td><input type="number" step="0.25" name="od_esfera" style="width:90px"></td>
                            <td><input type="number" step="0.25" name="od_cilindro" style="width:90px"></td>
                            <td><input type="number" name="od_eje" min="0" max="180" style="width:80px"></td>
                            <td><input type="number" step="0.1" name="od_pio" style="width:90px"></td>
                        </tr>
                        <tr>
                            <td><b>OS</b></td>
                            <td><input name="os_av" placeholder="20/25" style="width:90px"></td>
                            <td><input type="number" step="0.25" name="os_esfera" style="width:90px"></td>
                            <td><input type="number" step="0.25" name="os_cilindro" style="width:90px"></td>
                            <td><input type="number" name="os_eje" min="0" max="180" style="width:80px"></td>
                            <td><input type="number" step="0.1" name="os_pio" style="width:90px"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="field mb" style="margin-top:12px"><label>Diagnóstico</label><input name="diagnostico" placeholder="Miopía, astigmatismo, glaucoma…"></div>
            <div class="field mb"><label>Observaciones</label><textarea name="observaciones"></textarea></div>
            <button class="btn btn-primary"><i class="fa-solid fa-check"></i> Guardar evaluación</button>
        </form>
    </div>

    <div class="card" style="padding:0">
        <div style="padding:18px 20px 6px"><h3 style="margin:0">Historial</h3></div>
        <div style="overflow-x:auto">
            <div class="table-wrap" style="box-shadow:none;border-radius:0;min-width:720px">
                <table>
                    <thead><tr><th>Fecha</th><th>OD (AV / Esf / Cil)</th><th>OS (AV / Esf / Cil)</th><th>PIO OD/OS</th><th>Diagnóstico</th><th></th></tr></thead>
                    <tbody>
                    @forelse($evaluaciones as $e)
                        <tr>
                            <td>{{ $e->fecha->locale('es')->isoFormat('D MMM YYYY') }}</td>
                            <td>{{ $e->od_av ?? '—' }} / {{ $e->od_esfera !== null ? sprintf('%+.2f',$e->od_esfera) : '—' }} / {{ $e->od_cilindro !== null ? sprintf('%+.2f',$e->od_cilindro) : '—' }}</td>
                            <td>{{ $e->os_av ?? '—' }} / {{ $e->os_esfera !== null ? sprintf('%+.2f',$e->os_esfera) : '—' }} / {{ $e->os_cilindro !== null ? sprintf('%+.2f',$e->os_cilindro) : '—' }}</td>
                            <td>{{ $e->od_pio ?? '—' }} / {{ $e->os_pio ?? '—' }}</td>
                            <td>{{ $e->diagnostico ?? '—' }}</td>
                            <td style="text-align:right">
                                <form method="POST" action="{{ route('oftalmo.destroy',$e) }}" onsubmit="return confirm('¿Eliminar esta evaluación?')">
                                    @csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fa-solid fa-xmark"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="empty"><i class="fa-solid fa-eye"></i><p>Aún no hay evaluaciones registradas.</p></div></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
