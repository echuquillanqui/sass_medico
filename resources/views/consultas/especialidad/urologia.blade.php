<div class="card pink mb">
    <h3 class="mb"><i class="fa-solid fa-droplet" style="color:#2563eb"></i> Evaluación urológica</h3>
    <div class="form-grid">
        <div class="field"><label>PSA (ng/mL)</label><input name="datos[psa]" value="{{ $d['psa'] ?? '' }}"></div>
        <div class="field"><label>Síntomas urinarios (IPSS)</label>
            <select name="datos[ipss]">@foreach(['Leve (0-7)','Moderado (8-19)','Severo (20-35)'] as $o)<option @selected(($d['ipss'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
        <div class="field"><label>Tacto rectal</label>
            <select name="datos[tacto]">@foreach(['No realizado','Prostata normal','Aumentada','Nodular','Dolorosa'] as $o)<option @selected(($d['tacto'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
        <div class="field"><label>Chorro urinario</label>
            <select name="datos[chorro]">@foreach(['Normal','Debil','Intermitente','Goteo'] as $o)<option @selected(($d['chorro'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
    </div>
</div>
