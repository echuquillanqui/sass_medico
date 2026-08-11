@extends('layouts.app')
@section('title', 'Lesiones óseas · '.$paciente->nombre_completo)

@section('content')
    <div class="page-head">
        <div>
            <h1><i class="fa-solid fa-bone" style="color:#f97316"></i> Mapa de lesiones óseas</h1>
            <p>{{ $paciente->nombre_completo }} · {{ $paciente->edad !== null ? $paciente->edad.' años' : 'Edad N/D' }} · Doc. {{ $paciente->documento ?? '—' }}</p>
        </div>
        <div class="flex gap">
            <a href="{{ route('pacientes.show',$paciente) }}" class="btn btn-ghost"><i class="fa-solid fa-user"></i> Ficha</a>
            <a href="{{ route('traumatograma.index') }}" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Volver</a>
        </div>
    </div>

    @if(session('ok'))<div class="alert ok mb">{{ session('ok') }}</div>@endif

    <form method="POST" action="{{ route('traumatograma.update',$paciente) }}" id="traumaForm">
        @csrf @method('PUT')
        <input type="hidden" name="lesiones" id="derma-data" value='@json($lesiones)'>

        <div class="card mb">
            <span class="odo-glabel" style="display:block;margin-bottom:6px">Tipo de lesión</span>
            <div class="odo-tools" id="tipos"></div>
            <p class="muted" style="font-size:12px;margin:6px 2px 0">Elige un tipo y haz clic sobre el cuerpo para marcar la lesión ósea o musculoesquelética.</p>
        </div>

        <div class="grid g-2e">
            <div class="card mb">
                <div class="derma-stage">
                    <div class="derma-fig" data-vista="frente">
                        <div class="derma-cap">Frente</div>
                        @include('dermatograma._silueta')
                        <div class="derma-markers"></div>
                    </div>
                    <div class="derma-fig" data-vista="espalda">
                        <div class="derma-cap">Espalda</div>
                        @include('dermatograma._silueta')
                        <div class="derma-markers"></div>
                    </div>
                </div>
            </div>

            <div>
                <div class="card mb">
                    <h3 class="mb"><i class="fa-solid fa-note-sticky" style="color:var(--violet)"></i> Notas</h3>
                    <textarea name="notas" style="min-height:110px" placeholder="Mecanismo de lesión, inmovilización, rehabilitación…">{{ $trauma->notas }}</textarea>
                </div>
                <div class="card">
                    <button class="btn btn-primary" style="width:100%;justify-content:center"><i class="fa-solid fa-floppy-disk"></i> Guardar mapa</button>
                    @if($trauma->exists)
                        <p class="muted" style="font-size:12px;margin-top:10px;text-align:center">Última actualización: {{ $trauma->updated_at->locale('es')->isoFormat('D MMM YYYY, HH:mm') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="card">
            <h3 class="mb"><i class="fa-solid fa-list" style="color:var(--pink)"></i> Lesiones registradas</h3>
            <div class="table-wrap" style="box-shadow:none">
                <table>
                    <thead><tr><th style="width:40px">#</th><th style="width:90px">Vista</th><th style="width:180px">Tipo</th><th>Descripción</th><th style="width:44px"></th></tr></thead>
                    <tbody id="leslist"></tbody>
                </table>
            </div>
            <p class="muted" style="font-size:12px" id="lesempty">Sin lesiones marcadas.</p>
        </div>
    </form>

    <style>
    .odo-glabel{font-size:10px;letter-spacing:.06em;text-transform:uppercase;color:#94a3b8;font-weight:600}
    .odo-tools{display:flex;gap:6px;flex-wrap:wrap}
    .odo-chip{display:flex;align-items:center;gap:6px;border:1.5px solid var(--line);background:#fff;border-radius:20px;padding:5px 11px;font-size:12px;cursor:pointer;transition:.12s;color:var(--ink)}
    .odo-chip .dot{width:13px;height:13px;border-radius:50%;border:1px solid #cbd5e1;display:inline-block}
    .odo-chip.active{border-color:var(--violet);box-shadow:0 0 0 3px rgba(139,92,246,.18);font-weight:600}
    .derma-stage{display:flex;gap:18px;justify-content:center;flex-wrap:wrap}
    .derma-fig{position:relative;width:180px;max-width:46%;cursor:crosshair}
    .derma-fig svg{display:block;width:100%;height:auto}
    .derma-cap{text-align:center;font-size:11px;letter-spacing:.05em;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;font-weight:600}
    .derma-markers{position:absolute;left:0;right:0;top:0;bottom:0;pointer-events:none}
    .derma-dot{position:absolute;width:15px;height:15px;border-radius:50%;transform:translate(-50%,-50%);border:2px solid #fff;box-shadow:0 0 0 1px rgba(0,0,0,.25);pointer-events:auto;cursor:pointer;font-size:8px;color:#fff;display:grid;place-items:center;font-weight:700}
    [data-theme="dark"] .odo-chip{background:#1e1b33}
    </style>

    @push('scripts')
    <script>
    (function(){
        const TIPOS=@json($tipos);
        const input=document.getElementById('derma-data');
        let les=[]; try{ les=JSON.parse(input.value)||[]; }catch(e){ les=[]; }
        if(!Array.isArray(les)) les=[];
        let tool=Object.keys(TIPOS)[0];

        const tools=document.getElementById('tipos');
        for(const k in TIPOS){ const [label,color]=TIPOS[k];
            const chip=document.createElement('div'); chip.className='odo-chip'+(k===tool?' active':''); chip.dataset.k=k;
            chip.innerHTML='<span class="dot" style="background:'+color+'"></span>'+label;
            chip.addEventListener('click',()=>{ tool=k; document.querySelectorAll('.odo-chip').forEach(c=>c.classList.toggle('active',c.dataset.k===k)); });
            tools.appendChild(chip);
        }

        function commit(){ input.value=JSON.stringify(les); render(); }

        function render(){
            document.querySelectorAll('.derma-fig').forEach(fig=>{
                const vista=fig.dataset.vista; const layer=fig.querySelector('.derma-markers'); layer.innerHTML='';
                les.forEach((l,i)=>{ if(l.vista!==vista) return;
                    const [label,color]=TIPOS[l.tipo]||['',''];
                    const d=document.createElement('div'); d.className='derma-dot'; d.style.left=l.x+'%'; d.style.top=l.y+'%';
                    d.style.background=color; d.title=(label||'')+(l.descripcion?': '+l.descripcion:''); d.textContent=(i+1);
                    d.addEventListener('click',ev=>{ ev.stopPropagation(); const row=document.getElementById('les-'+i); if(row){ row.style.background='#fef08a'; setTimeout(()=>row.style.background='',900); row.scrollIntoView({block:'center'}); } });
                    layer.appendChild(d);
                });
            });
            const body=document.getElementById('leslist'); body.innerHTML='';
            document.getElementById('lesempty').style.display = les.length ? 'none' : 'block';
            les.forEach((l,i)=>{ const [label,color]=TIPOS[l.tipo]||['',''];
                const tr=document.createElement('tr'); tr.id='les-'+i;
                tr.innerHTML='<td><b>'+(i+1)+'</b></td>'+
                    '<td>'+(l.vista==='espalda'?'Espalda':'Frente')+'</td>'+
                    '<td><span class="dot" style="width:12px;height:12px;border-radius:50%;background:'+color+';display:inline-block;vertical-align:-2px;margin-right:6px"></span>'+label+'</td>'+
                    '<td><input value="'+(l.descripcion||'').replace(/"/g,'&quot;')+'" data-i="'+i+'" class="lesdesc" placeholder="Lado, hueso, grado…"></td>'+
                    '<td><button type="button" class="btn btn-danger btn-sm" data-del="'+i+'"><i class="fa-solid fa-xmark"></i></button></td>';
                body.appendChild(tr);
            });
            body.querySelectorAll('.lesdesc').forEach(inp=>inp.addEventListener('input',e=>{ les[+e.target.dataset.i].descripcion=e.target.value; input.value=JSON.stringify(les); }));
            body.querySelectorAll('[data-del]').forEach(b=>b.addEventListener('click',e=>{ les.splice(+e.currentTarget.dataset.del,1); commit(); }));
        }

        document.querySelectorAll('.derma-fig').forEach(fig=>{
            fig.addEventListener('click',function(e){
                if(e.target.classList.contains('derma-dot')) return;
                const r=fig.getBoundingClientRect();
                const x=((e.clientX-r.left)/r.width)*100;
                const y=((e.clientY-r.top)/r.height)*100;
                if(y<6) return;
                les.push({vista:fig.dataset.vista, x:+x.toFixed(2), y:+y.toFixed(2), tipo:tool, descripcion:''});
                commit();
            });
        });

        render();
    })();
    </script>
    @endpush
@endsection
