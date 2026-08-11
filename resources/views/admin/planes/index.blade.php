@extends('layouts.app')
@section('title', 'Planes')

@section('content')
    <div class="page-head">
        <div>
            <h1>Planes de suscripción</h1>
            <p>Define los planes que asignarás a las empresas: precio, ciclo, límites de especialidades y usuarios.</p>
        </div>
        <button type="button" class="btn btn-primary" onclick="planModal('create')"><i class="fa-solid fa-plus"></i> Nuevo plan</button>
    </div>

    @if(session('ok'))<div class="alert ok mb">{{ session('ok') }}</div>@endif
    @if(session('error'))<div class="alert mb" style="background:#fef2f2;border-left:4px solid #ef4444;color:#991b1b">{{ session('error') }}</div>@endif

    @php $colores = ['#7c3aed','#ec4899','#14b8a6','#f59e0b','#2563eb','#0ea5e9']; @endphp
    <div class="grid g-3" style="gap:16px">
        @forelse($planes as $i => $p)
            @php $col = $colores[$i % count($colores)]; @endphp
            <div class="plan-card" style="border-top:4px solid {{ $col }}">
                @if($p->destacado)<span class="pill" style="position:absolute;top:14px;right:14px;background:{{ $col }};color:#fff">Destacado</span>@endif
                <div class="flex between" style="align-items:flex-start">
                    <div style="font-size:16px;font-weight:800;color:#2b2b3a">{{ $p->nombre }}</div>
                    <div style="text-align:right;line-height:1"><span style="font-size:24px;font-weight:800;color:{{ $col }}">S/ {{ number_format($p->precio,0) }}</span><span style="font-size:12px;color:var(--ink-soft)">/{{ $p->ciclo }}</span></div>
                </div>
                <div style="margin:8px 0">
                    @if($p->activo)<span class="pill green">Activo</span>@else<span class="pill gray">Inactivo</span>@endif
                </div>
                <p class="muted" style="font-size:12.5px;min-height:34px">{{ $p->descripcion }}</p>
                <div class="plan-list">
                    <div><i class="fa-solid fa-users"></i> {{ $p->limite_usuarios ? $p->limite_usuarios.' usuarios' : 'Usuarios ilimitados' }}</div>
                    <div><i class="fa-solid fa-layer-group"></i> {{ $p->limite_especialidades ? $p->limite_especialidades.' especialidades' : 'Especialidades ilimitadas' }}</div>
                    <div><i class="fa-solid fa-building"></i> {{ $p->empresas_count }} empresa(s)</div>
                </div>
                <div class="flex gap" style="margin-top:14px">
                    <button type="button" class="btn btn-light btn-sm"
                        onclick='planModal("edit", {{ Illuminate\Support\Js::from([
                            "id"=>$p->id, "nombre"=>$p->nombre, "precio"=>$p->precio, "ciclo"=>$p->ciclo,
                            "le"=>$p->limite_especialidades, "lu"=>$p->limite_usuarios, "desc"=>$p->descripcion,
                            "orden"=>$p->orden, "dest"=>(bool)$p->destacado, "activo"=>(bool)$p->activo,
                            "action"=>route("admin.planes.update",$p),
                        ]) }})'><i class="fa-solid fa-pen"></i> Editar</button>
                    <form method="POST" action="{{ route('admin.planes.destroy',$p) }}" onsubmit="return confirm('¿Eliminar el plan {{ $p->nombre }}?')">
                        @csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i> Eliminar</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="card"><div class="empty"><i class="fa-solid fa-gem"></i><p>Aún no hay planes. Crea el primero con “Nuevo plan”.</p></div></div>
        @endforelse
    </div>

    {{-- Modal crear/editar --}}
    <div id="planOverlay" class="plan-overlay" onclick="if(event.target===this)planClose()">
        <div class="plan-modal">
            <div class="flex between mb"><h3 id="planTitle" style="margin:0">Nuevo plan</h3><button type="button" class="btn btn-light btn-sm" onclick="planClose()"><i class="fa-solid fa-xmark"></i></button></div>
            <form method="POST" id="planForm" action="{{ route('admin.planes.store') }}">
                @csrf
                <input type="hidden" name="_method" id="planMethod" value="">
                <div class="form-grid">
                    <div class="field full"><label>Nombre *</label><input name="nombre" id="pf_nombre" required placeholder="Profesional"></div>
                    <div class="field"><label>Precio *</label><input type="number" step="0.01" name="precio" id="pf_precio" required placeholder="99.00"></div>
                    <div class="field"><label>Ciclo *</label><select name="ciclo" id="pf_ciclo"><option value="mensual">Mensual</option><option value="anual">Anual</option></select></div>
                    <div class="field"><label>Límite especialidades</label><input type="number" name="limite_especialidades" id="pf_le" placeholder="Vacío = ilimitado"></div>
                    <div class="field"><label>Límite usuarios</label><input type="number" name="limite_usuarios" id="pf_lu" placeholder="Vacío = ilimitado"></div>
                    <div class="field full"><label>Descripción</label><input name="descripcion" id="pf_desc" placeholder="Para clínicas en crecimiento"></div>
                    <div class="field"><label>Orden</label><input type="number" name="orden" id="pf_orden" value="0"></div>
                    <div class="field"><label>&nbsp;</label>
                        <label class="flex gap" style="align-items:center;height:22px"><input type="hidden" name="destacado" value="0"><input type="checkbox" name="destacado" value="1" id="pf_dest" style="width:auto"> Destacado</label>
                        <label class="flex gap" style="align-items:center;height:22px"><input type="hidden" name="activo" value="0"><input type="checkbox" name="activo" value="1" id="pf_activo" style="width:auto" checked> Activo</label>
                    </div>
                </div>
                <div class="flex gap" style="margin-top:14px;justify-content:flex-end">
                    <button type="button" class="btn btn-light" onclick="planClose()">Cancelar</button>
                    <button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar plan</button>
                </div>
            </form>
        </div>
    </div>

    <style>
    .plan-card{position:relative;background:#fff;border:1px solid var(--line);border-radius:16px;padding:18px;box-shadow:0 4px 16px rgba(90,70,160,.05)}
    .plan-list{border-top:1px solid var(--line);padding-top:10px;margin-top:6px;font-size:12.5px;color:var(--ink-soft);display:flex;flex-direction:column;gap:6px}
    .plan-list i{width:16px;color:var(--violet)}
    .plan-overlay{display:none;position:fixed;inset:0;background:rgba(30,27,75,.45);z-index:999;align-items:flex-start;justify-content:center;padding:40px 16px;overflow:auto}
    .plan-overlay.open{display:flex}
    .plan-modal{background:#fff;border-radius:16px;padding:22px;width:100%;max-width:640px;box-shadow:0 20px 50px rgba(0,0,0,.25)}
    [data-theme="dark"] .plan-card,[data-theme="dark"] .plan-modal{background:#161428}
    </style>

    @push('scripts')
    <script>
    var PLAN_STORE = @json(route('admin.planes.store'));
    function planModal(mode, data){
        var f=document.getElementById('planForm');
        if(mode==='create'){
            document.getElementById('planTitle').textContent='Nuevo plan';
            f.action=PLAN_STORE; document.getElementById('planMethod').value='';
            f.reset(); document.getElementById('pf_activo').checked=true; document.getElementById('pf_orden').value=0;
        } else {
            document.getElementById('planTitle').textContent='Editar plan';
            f.action=data.action; document.getElementById('planMethod').value='PUT';
            document.getElementById('pf_nombre').value=data.nombre||'';
            document.getElementById('pf_precio').value=data.precio||'';
            document.getElementById('pf_ciclo').value=data.ciclo||'mensual';
            document.getElementById('pf_le').value=data.le||'';
            document.getElementById('pf_lu').value=data.lu||'';
            document.getElementById('pf_desc').value=data.desc||'';
            document.getElementById('pf_orden').value=data.orden||0;
            document.getElementById('pf_dest').checked=!!data.dest;
            document.getElementById('pf_activo').checked=!!data.activo;
        }
        document.getElementById('planOverlay').classList.add('open');
    }
    function planClose(){ document.getElementById('planOverlay').classList.remove('open'); }
    document.addEventListener('keydown',function(e){ if(e.key==='Escape')planClose(); });
    </script>
    @endpush
@endsection
