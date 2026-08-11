@extends('layouts.app')
@section('title', 'Odontograma · '.$paciente->nombre_completo)

@section('content')
    @php $mon = auth()->user()->empresa->moneda ?? 'S/'; @endphp
    <div class="page-head">
        <div>
            <h1><i class="fa-solid fa-tooth" style="color:#06b6d4"></i> Odontograma</h1>
            <p>{{ $paciente->nombre_completo }} · {{ $paciente->edad !== null ? $paciente->edad.' años' : 'Edad N/D' }}
               · Doc. {{ $paciente->documento ?? '—' }}</p>
        </div>
        <div class="flex gap">
            <a href="{{ route('pacientes.show',$paciente) }}" class="btn btn-ghost"><i class="fa-solid fa-user"></i> Ficha</a>
            <a href="{{ route('odontograma.index') }}" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Volver</a>
        </div>
    </div>

    @if(session('ok'))<div class="alert ok mb">{{ session('ok') }}</div>@endif

    <form method="POST" action="{{ route('odontograma.update',$paciente) }}" id="odoForm">
        @csrf @method('PUT')
        <input type="hidden" name="dientes" id="odo-data" value='@json($dientes)'>

        {{-- Barra de herramientas --}}
        <div class="card mb">
            <div class="odo-toolbar">
                <div class="odo-toolgrp">
                    <span class="odo-glabel">Superficie</span>
                    <div class="odo-tools" data-mode="surf" id="toolsSurf"></div>
                </div>
                <div class="odo-toolgrp">
                    <span class="odo-glabel">Diente completo</span>
                    <div class="odo-tools" data-mode="whole" id="toolsWhole"></div>
                </div>
            </div>
            <p class="muted" style="font-size:12px;margin:6px 2px 0">
                Elige una herramienta y haz clic: las de <b>superficie</b> pintan cada cara de la cruz; las de <b>diente completo</b> se aplican sobre la figura del diente.
            </p>
        </div>

        <div class="card mb">
            <div id="odontograma">
                <div class="oarch">
                    <div class="orow" data-row="sup"></div>
                    <div class="orow odo-decid" data-row="supd" hidden></div>
                </div>
                <div class="omid"><span>Permanente superior 1&nbsp;·&nbsp;8</span><span>Permanente inferior</span></div>
                <div class="oarch">
                    <div class="orow odo-decid" data-row="infd" hidden></div>
                    <div class="orow" data-row="inf"></div>
                </div>
            </div>
            <div class="flex between" style="flex-wrap:wrap;gap:10px;margin-top:12px">
                <label class="flex gap" style="font-size:12px;align-items:center;cursor:pointer">
                    <input type="checkbox" id="tglDecid" style="width:auto"> Mostrar dentición temporal (niños)
                </label>
                <div class="odo-summary" id="summary"></div>
            </div>
        </div>

        <div class="grid g-2e">
            {{-- Plan de tratamiento --}}
            <div class="card">
                <div class="flex between mb" style="flex-wrap:wrap;gap:10px">
                    <h3 style="margin:0"><i class="fa-solid fa-list-check" style="color:var(--pink)"></i> Plan de tratamiento</h3>
                    <button type="button" class="btn btn-light btn-sm" onclick="addPlan()"><i class="fa-solid fa-plus"></i> Procedimiento</button>
                </div>
                <div class="table-wrap" style="box-shadow:none">
                    <table>
                        <thead><tr><th style="width:70px">Pieza</th><th>Procedimiento</th><th style="width:130px">Estado</th><th style="width:110px">Costo</th><th style="width:40px"></th></tr></thead>
                        <tbody id="planBody"></tbody>
                        <tfoot><tr><td colspan="3" style="text-align:right"><b>Total estimado</b></td><td><b id="planTotal">{{ $mon }} 0.00</b></td><td></td></tr></tfoot>
                    </table>
                </div>
            </div>

            {{-- Notas + guardar --}}
            <div>
                <div class="card mb">
                    <h3 class="mb"><i class="fa-solid fa-note-sticky" style="color:var(--violet)"></i> Notas clínicas</h3>
                    <textarea name="notas" style="min-height:110px" placeholder="Higiene, indicaciones, observaciones…">{{ $odo->notas }}</textarea>
                </div>
                <div class="card">
                    <button class="btn btn-primary" style="width:100%;justify-content:center"><i class="fa-solid fa-floppy-disk"></i> Guardar odontograma</button>
                    @if($odo->exists)
                        <p class="muted" style="font-size:12px;margin-top:10px;text-align:center">Última actualización: {{ $odo->updated_at->locale('es')->isoFormat('D MMM YYYY, HH:mm') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </form>

    <style>
    .odo-toolbar{display:flex;gap:22px;flex-wrap:wrap}
    .odo-toolgrp{display:flex;flex-direction:column;gap:6px}
    .odo-glabel{font-size:10px;letter-spacing:.06em;text-transform:uppercase;color:#94a3b8;font-weight:600}
    .odo-tools{display:flex;gap:6px;flex-wrap:wrap}
    .odo-chip{display:flex;align-items:center;gap:6px;border:1.5px solid var(--line);background:#fff;border-radius:20px;padding:5px 11px;font-size:12px;cursor:pointer;transition:.12s;color:var(--ink)}
    .odo-chip .dot{width:13px;height:13px;border-radius:4px;border:1px solid #cbd5e1;display:inline-block}
    .odo-chip.active{border-color:var(--violet);box-shadow:0 0 0 3px rgba(139,92,246,.18);font-weight:600}
    #odontograma{background:#fff;border-radius:14px;padding:18px 12px;overflow-x:auto;border:1px solid var(--line)}
    .oarch{display:flex;justify-content:center}
    .orow{display:flex;gap:3px;min-width:560px}
    .orow.odo-decid{min-width:0}
    .omid{display:flex;justify-content:space-between;max-width:620px;margin:2px auto;padding:0 6px;font-size:9px;letter-spacing:.05em;text-transform:uppercase;color:#c0c6d4}
    .ocol{display:flex;flex-direction:column;align-items:center;width:34px}
    .otooth{cursor:pointer;line-height:0}
    .otooth svg{display:block}
    .otooth.faded{opacity:.4}
    .onum{font-size:9.5px;color:#64748b;font-weight:600;margin:1px 0}
    .ocross{cursor:pointer}
    .ocross polygon{transition:.1s}
    .ocross polygon:hover{stroke:#7c3aed;stroke-width:1.4}
    .odo-summary{display:flex;gap:7px;flex-wrap:wrap}
    .odo-summary .st{display:flex;align-items:center;gap:5px;font-size:12px;background:var(--bg-soft,#f6f6fb);border-radius:9px;padding:5px 9px}
    .odo-summary .st b{font-size:13px}
    [data-theme="dark"] #odontograma,[data-theme="dark"] .odo-chip{background:#161428}
    </style>

    @push('scripts')
    <script>
    (function(){
        // ---------- Configuración ----------
        const SURF = { // herramientas de superficie
            sano:      {label:'Borrar', color:'#ffffff'},
            caries:    {label:'Caries', color:'#ef4444'},
            obturado:  {label:'Obturado', color:'#2563eb'},
            sellante:  {label:'Sellante', color:'#22c55e'},
        };
        const WHOLE = { // herramientas de diente completo
            corona:     {label:'Corona', color:'#2563eb'},
            endodoncia: {label:'Endodoncia', color:'#8b5cf6'},
            extraccion: {label:'Extracción', color:'#ef4444'},
            ausente:    {label:'Ausente', color:'#64748b'},
            implante:   {label:'Implante', color:'#10b981'},
            limpiar:    {label:'Quitar', color:'#e2e8f0'},
        };
        const sup=[18,17,16,15,14,13,12,11,21,22,23,24,25,26,27,28];
        const inf=[48,47,46,45,44,43,42,41,31,32,33,34,35,36,37,38];
        const supd=[55,54,53,52,51,61,62,63,64,65];
        const infd=[85,84,83,82,81,71,72,73,74,75];

        const input=document.getElementById('odo-data');
        let data={}; try{ data=JSON.parse(input.value)||{}; }catch(e){ data={}; }
        // normaliza: cada diente => { s:{cara:estado}, w:estado }
        Object.keys(data).forEach(k=>{ if(typeof data[k]==='string'){ data[k]={s:{},w:data[k]}; } if(!data[k].s) data[k].s={}; });

        let tool={mode:'surf', key:'caries'};

        // ---------- Figura del diente (SVG) ----------
        function toothType(n){ const d=n%10; if(d===1||d===2)return 'incisor'; if(d===3)return 'canine'; if(d===4||d===5)return 'premolar'; return 'molar'; }
        function toothPaths(type){
            const f='#f6f1e2', s='#cdbf9c', l='#d8cba6';
            const crown={
                incisor:`<path d="M8,5 Q8,3 11,3 L23,3 Q26,3 26,6 L25,23 Q17,27 9,23 Z" fill="${f}" stroke="${s}"/>`,
                canine:`<path d="M17,2 L25,9 L24,23 Q17,27 10,23 L9,9 Z" fill="${f}" stroke="${s}"/>`,
                premolar:`<path d="M9,8 Q11,3 14,6 Q17,2 20,6 Q23,3 25,8 L24,23 Q17,27 10,23 Z" fill="${f}" stroke="${s}"/><path d="M17,6 L17,21" stroke="${l}" fill="none"/>`,
                molar:`<path d="M7,9 Q9,3 12,6 Q14,3 17,6 Q20,3 22,6 Q25,3 27,9 L25,24 Q17,28 9,24 Z" fill="${f}" stroke="${s}"/><path d="M17,7 L17,23 M11,10 L23,10" stroke="${l}" fill="none" opacity=".7"/>`,
            }[type];
            const root={
                incisor:`<path d="M13,24 Q12,39 16,49 Q17,50 18,49 Q22,39 21,24 Z" fill="${f}" stroke="${s}"/>`,
                canine:`<path d="M13,24 Q11,41 16,50 Q17,51 18,50 Q23,41 21,24 Z" fill="${f}" stroke="${s}"/>`,
                premolar:`<path d="M13,24 Q12,39 16,48 Q17,49 18,48 Q22,39 21,24 Z" fill="${f}" stroke="${s}"/>`,
                molar:`<path d="M11,24 Q9,39 12,47 Q13,48 14,47 Q16,38 15,24 Z" fill="${f}" stroke="${s}"/><path d="M19,24 Q18,38 20,47 Q21,48 22,47 Q25,39 23,24 Z" fill="${f}" stroke="${s}"/>`,
            }[type];
            return crown+root;
        }
        function wholeOverlay(state){
            if(state==='corona')     return `<circle cx="17" cy="13" r="12.5" fill="none" stroke="#2563eb" stroke-width="2"/>`;
            if(state==='extraccion') return `<path d="M6,3 L28,26 M28,3 L6,26" stroke="#ef4444" stroke-width="2.2" fill="none"/>`;
            if(state==='ausente')    return `<path d="M6,3 L28,26 M28,3 L6,26" stroke="#64748b" stroke-width="2.2" fill="none"/>`;
            if(state==='endodoncia') return `<line x1="17" y1="24" x2="17" y2="48" stroke="#8b5cf6" stroke-width="2.4"/>`;
            if(state==='implante')   return `<path d="M17,26 L17,48 M12,30 H22 M12,35 H22 M12,40 H22 M12,45 H22" stroke="#10b981" stroke-width="1.6" fill="none"/>`;
            return '';
        }
        function toothSVG(n, upper){
            const g = upper ? 'transform="scale(1,-1) translate(0,-52)"' : '';
            return `<svg width="34" height="50" viewBox="0 0 34 52"><g ${g}>${toothPaths(toothType(n))}<g class="wt"></g></g></svg>`;
        }

        // ---------- Cruz de superficies ----------
        const SURFPTS = {
            v:'5,1 29,1 21,9 13,9',   // vestibular (arriba)
            d:'29,1 29,25 21,17 21,9',// distal (derecha)
            l:'5,25 29,25 21,17 13,17',// lingual/palatino (abajo)
            m:'5,1 5,25 13,17 13,9',  // mesial (izquierda)
            o:'13,9 21,9 21,17 13,17',// oclusal/incisal (centro)
        };
        function crossSVG(n){
            let p='';
            for(const k in SURFPTS){ p+=`<polygon data-t="${n}" data-s="${k}" points="${SURFPTS[k]}" fill="#fff" stroke="#cbd5e1" stroke-width="1"/>`; }
            return `<svg class="ocross" width="34" height="26" viewBox="0 0 34 26">${p}</svg>`;
        }

        // ---------- Construcción de filas ----------
        function column(n, upper){
            const col=document.createElement('div'); col.className='ocol'; col.dataset.n=n;
            const tooth=`<div class="otooth" data-t="${n}">${toothSVG(n,upper)}</div>`;
            const cross=crossSVG(n);
            const num=`<div class="onum">${n}</div>`;
            col.innerHTML = upper ? (tooth+cross+num) : (num+cross+tooth);
            return col;
        }
        function buildRow(key, list, upper){
            const row=document.querySelector('.orow[data-row="'+key+'"]'); row.innerHTML='';
            list.forEach(n=>row.appendChild(column(n,upper)));
        }
        buildRow('sup',sup,true); buildRow('inf',inf,false);
        buildRow('supd',supd,true); buildRow('infd',infd,false);

        // ---------- Pintado desde datos ----------
        function applyTooth(n){
            const col=document.querySelector('.ocol[data-n="'+n+'"]'); if(!col) return;
            const d=data[n]||{s:{},w:null};
            // superficies
            col.querySelectorAll('polygon').forEach(pg=>{
                const st=d.s?d.s[pg.dataset.s]:null;
                pg.setAttribute('fill', st&&SURF[st]?SURF[st].color:'#fff');
            });
            // diente completo
            const tooth=col.querySelector('.otooth');
            tooth.classList.toggle('faded', d.w==='ausente');
            const wt=col.querySelector('.wt'); if(wt) wt.innerHTML = d.w?wholeOverlay(d.w):'';
        }
        function repaintAll(){ [...sup,...inf,...supd,...infd].forEach(applyTooth); }

        // ---------- Herramientas ----------
        function buildTools(container, set, mode){
            for(const k in set){ const {label,color}=set[k];
                const chip=document.createElement('div'); chip.className='odo-chip'; chip.dataset.k=k; chip.dataset.mode=mode;
                const ic = (mode==='whole') ? '' : `<span class="dot" style="background:${color}"></span>`;
                chip.innerHTML = ic+label;
                if(mode==='whole'){ chip.insertAdjacentHTML('afterbegin', `<span class="dot" style="background:${color};border:none"></span>`); }
                chip.addEventListener('click',()=>{ tool={mode, key:k}; document.querySelectorAll('.odo-chip').forEach(c=>c.classList.remove('active')); chip.classList.add('active'); });
                container.appendChild(chip);
            }
        }
        buildTools(document.getElementById('toolsSurf'), SURF, 'surf');
        buildTools(document.getElementById('toolsWhole'), WHOLE, 'whole');
        document.querySelector('#toolsSurf .odo-chip[data-k="caries"]').classList.add('active');

        // ---------- Interacción ----------
        document.getElementById('odontograma').addEventListener('click', function(e){
            const pg=e.target.closest('polygon');
            const tooth=e.target.closest('.otooth');
            if(pg && tool.mode==='surf'){
                const n=pg.dataset.t, s=pg.dataset.s;
                data[n]=data[n]||{s:{},w:null}; data[n].s=data[n].s||{};
                if(tool.key==='sano'){ delete data[n].s[s]; } else { data[n].s[s]=tool.key; }
                applyTooth(n); commit();
            } else if(tooth && tool.mode==='whole'){
                const n=tooth.dataset.t;
                data[n]=data[n]||{s:{},w:null};
                data[n].w = (tool.key==='limpiar') ? null : tool.key;
                applyTooth(n); commit();
            }
        });

        function clean(){ // no guardar dientes vacíos
            const out={};
            for(const n in data){ const d=data[n]; const hasS=d.s&&Object.keys(d.s).length; if(hasS||d.w) out[n]={s:d.s||{},w:d.w||null}; }
            return out;
        }
        function commit(){ input.value=JSON.stringify(clean()); summary(); }

        function summary(){
            const box=document.getElementById('summary'); box.innerHTML='';
            const c={};
            for(const n in data){ const d=data[n];
                if(d.s) for(const k in d.s){ c[d.s[k]]=(c[d.s[k]]||0)+1; }
                if(d.w) c[d.w]=(c[d.w]||0)+1;
            }
            const names={...Object.fromEntries(Object.entries(SURF).map(([k,v])=>[k,v])), ...WHOLE};
            const keys=Object.keys(c).filter(k=>k!=='sano');
            if(!keys.length){ box.innerHTML='<span class="muted" style="font-size:12px">Sin hallazgos marcados.</span>'; return; }
            keys.forEach(k=>{ const meta=names[k]; if(!meta) return;
                box.insertAdjacentHTML('beforeend',`<span class="st"><span style="width:12px;height:12px;border-radius:4px;background:${meta.color};display:inline-block;border:1px solid #cbd5e1"></span><b>${c[k]}</b> ${meta.label==='Borrar'?'Sano':meta.label}</span>`);
            });
        }

        document.getElementById('tglDecid').addEventListener('change',function(){
            document.querySelectorAll('.odo-decid').forEach(r=>r.hidden=!this.checked);
        });

        repaintAll(); commit();

        // ---------- Plan de tratamiento ----------
        const PLAN=@json($plan); const mon=@json($mon);
        const body=document.getElementById('planBody');
        function estadoSel(v){ return ['pendiente','en_proceso','realizado'].map(e=>`<option value="${e}"${v===e?' selected':''}>${({pendiente:'Pendiente',en_proceso:'En proceso',realizado:'Realizado'})[e]}</option>`).join(''); }
        function planRow(r={}){
            const i=body.querySelectorAll('tr').length; const tr=document.createElement('tr');
            tr.innerHTML=
                `<td><input name="plan[${i}][pieza]" value="${r.pieza||''}" style="width:60px" placeholder="16"></td>`+
                `<td><input name="plan[${i}][procedimiento]" value="${(r.procedimiento||'').replace(/"/g,'&quot;')}" placeholder="Ej. Resina, endodoncia…"></td>`+
                `<td><select name="plan[${i}][estado]">${estadoSel(r.estado||'pendiente')}</select></td>`+
                `<td><input type="number" step="0.01" min="0" name="plan[${i}][costo]" value="${r.costo!=null?r.costo:''}" class="pcosto" oninput="odoTotal()" style="width:95px"></td>`+
                `<td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove();odoTotal()"><i class="fa-solid fa-xmark"></i></button></td>`;
            body.appendChild(tr);
        }
        window.addPlan=()=>planRow();
        window.odoTotal=function(){ let t=0; document.querySelectorAll('.pcosto').forEach(i=>{const v=parseFloat(i.value); if(!isNaN(v))t+=v;}); document.getElementById('planTotal').textContent=mon+' '+t.toFixed(2); };
        if(PLAN&&PLAN.length) PLAN.forEach(planRow); else planRow();
        odoTotal();
    })();
    </script>
    @endpush
@endsection
