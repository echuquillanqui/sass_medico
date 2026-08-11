@extends('layouts.app')
@section('title', 'Control prenatal · '.$paciente->nombre_completo)

@section('content')
    <div class="page-head">
        <div>
            <h1><i class="fa-solid fa-baby-carriage" style="color:#ec4899"></i> Control prenatal</h1>
            <p>{{ $paciente->nombre_completo }} · {{ $paciente->edad !== null ? $paciente->edad.' años' : 'Edad N/D' }} · Doc. {{ $paciente->documento ?? '—' }}</p>
        </div>
        <div class="flex gap">
            <a href="{{ route('pacientes.show',$paciente) }}" class="btn btn-ghost"><i class="fa-solid fa-user"></i> Ficha</a>
            <a href="{{ route('prenatal.index') }}" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Volver</a>
        </div>
    </div>

    @if(session('ok'))<div class="alert ok mb">{{ session('ok') }}</div>@endif

    @php
        $sem = $embarazo->exists ? $embarazo->semanas : null;
        $fpp = $embarazo->exists ? $embarazo->fpp_calculada : null;
        $trim = $sem ? ($sem < 14 ? '1.º trimestre' : ($sem < 28 ? '2.º trimestre' : '3.º trimestre')) : '—';
    @endphp

    <div class="grid g-4 mb">
        <div class="kpi k1"><div class="kpi-top"><span class="kpi-cap">Edad gestacional</span><span class="kpi-ic"><i class="fa-solid fa-calendar-week"></i></span></div>
            <div><div class="kpi-val">{{ $sem !== null ? $sem.' sem' : '—' }}</div><div class="kpi-foot">{{ $trim }}</div></div></div>
        <div class="kpi k2"><div class="kpi-top"><span class="kpi-cap">Fecha probable de parto</span><span class="kpi-ic"><i class="fa-solid fa-baby"></i></span></div>
            <div><div class="kpi-val" style="font-size:20px">{{ $fpp ? $fpp->locale('es')->isoFormat('D MMM YYYY') : '—' }}</div><div class="kpi-foot">Regla de Naegele</div></div></div>
        <div class="kpi k3"><div class="kpi-top"><span class="kpi-cap">Fórmula obstétrica</span><span class="kpi-ic"><i class="fa-solid fa-list-ol"></i></span></div>
            <div><div class="kpi-val" style="font-size:20px">G{{ $embarazo->gestas ?? 0 }} P{{ $embarazo->partos ?? 0 }} A{{ $embarazo->abortos ?? 0 }} C{{ $embarazo->cesareas ?? 0 }}</div><div class="kpi-foot">Gestas·Partos·Abortos·Cesáreas</div></div></div>
        <div class="kpi k4"><div class="kpi-top"><span class="kpi-cap">Clasificación</span><span class="kpi-ic"><i class="fa-solid fa-triangle-exclamation"></i></span></div>
            <div><div class="kpi-val" style="font-size:20px">{{ $embarazo->riesgo_alto ? 'Alto riesgo' : 'Bajo riesgo' }}</div><div class="kpi-foot">{{ $embarazo->grupo_sanguineo ? 'Grupo '.$embarazo->grupo_sanguineo : 'Grupo N/D' }}</div></div></div>
    </div>

    <div class="grid g-2e">
        {{-- Datos del embarazo --}}
        <div class="card mb">
            <h3 class="mb"><i class="fa-solid fa-heart" style="color:#ec4899"></i> Datos del embarazo</h3>
            <form method="POST" action="{{ route('prenatal.embarazo',$paciente) }}">
                @csrf
                <div class="form-grid">
                    <div class="field"><label>FUM (última menstruación)</label><input type="date" name="fum" value="{{ optional($embarazo->fum)->format('Y-m-d') }}"></div>
                    <div class="field"><label>FPP (opcional)</label><input type="date" name="fpp" value="{{ optional($embarazo->fpp)->format('Y-m-d') }}" placeholder="Auto"></div>
                    <div class="field"><label>Grupo sanguíneo</label><input name="grupo_sanguineo" value="{{ $embarazo->grupo_sanguineo }}" placeholder="O+"></div>
                    <div class="field"><label>Estado</label>
                        <select name="estado"><option value="activo" @selected($embarazo->estado==='activo')>Activo</option><option value="finalizado" @selected($embarazo->estado==='finalizado')>Finalizado</option></select></div>
                    <div class="field"><label>Gestas</label><input type="number" min="0" name="gestas" value="{{ $embarazo->gestas }}"></div>
                    <div class="field"><label>Partos</label><input type="number" min="0" name="partos" value="{{ $embarazo->partos }}"></div>
                    <div class="field"><label>Abortos</label><input type="number" min="0" name="abortos" value="{{ $embarazo->abortos }}"></div>
                    <div class="field"><label>Cesáreas</label><input type="number" min="0" name="cesareas" value="{{ $embarazo->cesareas }}"></div>
                </div>
                <label class="flex gap" style="align-items:center;margin:10px 0;font-size:13px;cursor:pointer">
                    <input type="hidden" name="riesgo_alto" value="0">
                    <input type="checkbox" name="riesgo_alto" value="1" style="width:auto" @checked($embarazo->riesgo_alto)> Embarazo de alto riesgo
                </label>
                <div class="field mb"><label>Antecedentes obstétricos</label><textarea name="antecedentes">{{ $embarazo->antecedentes }}</textarea></div>
                <button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar embarazo</button>
            </form>
        </div>

        {{-- Curva altura uterina --}}
        <div class="card mb">
            <h3 class="mb"><i class="fa-solid fa-chart-line" style="color:var(--violet)"></i> Altura uterina por semana</h3>
            <div class="chart-hold" style="height:240px"><canvas id="chAltura"></canvas></div>
            <p class="muted" style="font-size:12px">Referencia aproximada: la altura uterina en cm ≈ semanas de gestación (20–34 sem).</p>
        </div>
    </div>

    {{-- Registrar control --}}
    @if($embarazo->exists)
    <div class="card mb">
        <h3 class="mb"><i class="fa-solid fa-plus" style="color:var(--info)"></i> Nuevo control prenatal</h3>
        <form method="POST" action="{{ route('prenatal.control',$paciente) }}">
            @csrf
            <div class="form-grid">
                <div class="field"><label>Fecha *</label><input type="date" name="fecha" value="{{ now()->toDateString() }}" required></div>
                <div class="field"><label>Peso (kg)</label><input type="number" step="0.01" name="peso"></div>
                <div class="field"><label>Presión arterial</label><input name="presion_arterial" placeholder="110/70"></div>
                <div class="field"><label>Altura uterina (cm)</label><input type="number" step="0.1" name="altura_uterina"></div>
                <div class="field"><label>FCF (lpm)</label><input type="number" name="fcf" placeholder="140"></div>
                <div class="field"><label>Presentación</label><input name="presentacion" placeholder="Cefálica"></div>
            </div>
            <div class="flex gap" style="margin:10px 0;flex-wrap:wrap">
                <label class="flex gap" style="align-items:center;font-size:13px;cursor:pointer"><input type="hidden" name="movimientos_fetales" value="0"><input type="checkbox" name="movimientos_fetales" value="1" style="width:auto" checked> Movimientos fetales +</label>
                <label class="flex gap" style="align-items:center;font-size:13px;cursor:pointer"><input type="hidden" name="edema" value="0"><input type="checkbox" name="edema" value="1" style="width:auto"> Edema</label>
            </div>
            <div class="field mb"><label>Observaciones</label><textarea name="observaciones"></textarea></div>
            <button class="btn btn-primary"><i class="fa-solid fa-check"></i> Registrar control</button>
        </form>
    </div>

    <div class="card" style="padding:0">
        <div style="padding:18px 20px 6px"><h3 style="margin:0">Historial de controles</h3></div>
        <div style="overflow-x:auto">
            <div class="table-wrap" style="box-shadow:none;border-radius:0;min-width:720px">
                <table>
                    <thead><tr><th>Fecha</th><th>Sem</th><th>Peso</th><th>PA</th><th>AU (cm)</th><th>FCF</th><th>Present.</th><th>MF</th><th></th></tr></thead>
                    <tbody>
                    @forelse($embarazo->controles as $c)
                        <tr>
                            <td>{{ $c->fecha->locale('es')->isoFormat('D MMM YYYY') }}</td>
                            <td>{{ $c->semanas ?? '—' }}</td>
                            <td>{{ $c->peso ?? '—' }}</td>
                            <td>{{ $c->presion_arterial ?? '—' }}</td>
                            <td>{{ $c->altura_uterina ?? '—' }}</td>
                            <td>{{ $c->fcf ?? '—' }}</td>
                            <td>{{ $c->presentacion ?? '—' }}</td>
                            <td>{{ $c->movimientos_fetales ? '✓' : '—' }}</td>
                            <td style="text-align:right">
                                <form method="POST" action="{{ route('prenatal.control.destroy',$c) }}" onsubmit="return confirm('¿Eliminar este control?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm"><i class="fa-solid fa-xmark"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9"><div class="empty"><i class="fa-regular fa-calendar"></i><p>Aún no hay controles registrados.</p></div></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @push('scripts')
    <script>
    window.addEventListener('load', function(){
        if(!window.Chart) return;
        @php
            $datosControles = $embarazo->exists ? $embarazo->controles->map(fn ($c) => ['x' => $c->semanas, 'y' => $c->altura_uterina])->filter(fn ($p) => $p['x'] && $p['y'])->values() : [];
        @endphp
        const ctrl = @json($datosControles);
        const ref = []; for(let s=20;s<=36;s++){ ref.push({x:s,y:s}); }
        const el = document.getElementById('chAltura'); if(!el) return;
        new Chart(el, { type:'line',
            data:{ datasets:[
                {label:'Referencia', data:ref, borderColor:'#cbd5e1', borderDash:[5,5], pointRadius:0, tension:.2},
                {label:'Paciente', data:ctrl, borderColor:'#a855f7', backgroundColor:'#a855f7', pointRadius:5, tension:.3, showLine:true, borderWidth:3}
            ]},
            options:{ responsive:true, maintainAspectRatio:false,
                scales:{ x:{type:'linear', title:{display:true,text:'Semanas'}, min:18, max:38}, y:{title:{display:true,text:'Altura uterina (cm)'}, beginAtZero:false} },
                plugins:{legend:{position:'bottom', labels:{boxWidth:12,font:{size:11}}}} } });
    });
    </script>
    @endpush
@endsection
