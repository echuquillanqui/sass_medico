<div class="card pink mb">
    <h3 class="mb"><i class="fa-solid fa-tooth" style="color:#06b6d4"></i> Odontograma</h3>
    <p class="muted mb">Haz clic en un diente para cambiar su estado.</p>

    <div class="odo-legend">
        <span><i style="background:#ffffff;border:1px solid #cbd5e1"></i> Sano</span>
        <span><i style="background:#ef4444"></i> Caries</span>
        <span><i style="background:#3b82f6"></i> Obturado</span>
        <span><i style="background:#f59e0b"></i> Corona</span>
        <span><i style="background:#111827"></i> Ausente</span>
    </div>

    <div id="odontograma">
        <div class="odo-row" data-arch="sup"></div>
        <div class="odo-row" data-arch="inf"></div>
    </div>
    <input type="hidden" name="datos[odontograma]" id="odo-data" value='{{ $d["odontograma"] ?? "{}" }}'>

    <div class="field mt"><label>Notas odontológicas</label>
        <textarea name="datos[odo_notas]">{{ $d['odo_notas'] ?? '' }}</textarea></div>
</div>

<style>
.odo-legend{display:flex;gap:14px;flex-wrap:wrap;font-size:11px;color:var(--ink-soft);margin-bottom:12px}
.odo-legend span{display:flex;align-items:center;gap:5px}
.odo-legend i{width:13px;height:13px;border-radius:3px;display:inline-block}
#odontograma{background:#fff;border-radius:14px;padding:14px;overflow-x:auto}
.odo-row{display:flex;gap:4px;justify-content:center;margin:5px 0;min-width:520px}
.tooth{width:26px;text-align:center;cursor:pointer;user-select:none}
.tooth .crown{width:24px;height:26px;border:1.5px solid #cbd5e1;border-radius:5px;background:#fff;transition:.1s}
.tooth:hover .crown{transform:scale(1.12)}
.tooth small{font-size:9px;color:#94a3b8;display:block;margin-top:2px}
</style>

@push('scripts')
<script>
(function(){
    const STATES=[
        {k:'sano',c:'#ffffff',b:'#cbd5e1'},
        {k:'caries',c:'#ef4444',b:'#ef4444'},
        {k:'obturado',c:'#3b82f6',b:'#3b82f6'},
        {k:'corona',c:'#f59e0b',b:'#f59e0b'},
        {k:'ausente',c:'#111827',b:'#111827'},
    ];
    const sup=[18,17,16,15,14,13,12,11,21,22,23,24,25,26,27,28];
    const inf=[48,47,46,45,44,43,42,41,31,32,33,34,35,36,37,38];
    const input=document.getElementById('odo-data');
    let data={}; try{ data=JSON.parse(input.value)||{}; }catch(e){ data={}; }

    function paint(el,st){ const s=STATES.find(x=>x.k===st)||STATES[0];
        const cr=el.querySelector('.crown'); cr.style.background=s.c; cr.style.borderColor=s.b; }
    function build(arch,list){
        const row=document.querySelector('.odo-row[data-arch="'+arch+'"]');
        list.forEach(n=>{
            const t=document.createElement('div'); t.className='tooth'; t.dataset.n=n;
            t.innerHTML='<div class="crown"></div><small>'+n+'</small>';
            paint(t, data[n]||'sano');
            t.addEventListener('click',()=>{
                const cur=data[n]||'sano'; const i=STATES.findIndex(x=>x.k===cur);
                const nx=STATES[(i+1)%STATES.length].k; data[n]=nx; paint(t,nx);
                input.value=JSON.stringify(data);
            });
            row.appendChild(t);
        });
    }
    build('sup',sup); build('inf',inf);
    input.value=JSON.stringify(data);
})();
</script>
@endpush
