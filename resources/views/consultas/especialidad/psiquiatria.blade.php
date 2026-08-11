<div class="card pink mb">
    <h3 class="mb"><i class="fa-solid fa-head-side-virus" style="color:#6366f1"></i> Evaluación psiquiátrica</h3>
    <div class="form-grid">
        <div class="field"><label>Estado de ánimo</label>
            <select name="datos[animo]">@foreach(['Eutimico','Deprimido','Ansioso','Irritable','Eufórico'] as $o)<option @selected(($d['animo'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
        <div class="field"><label>Riesgo suicida</label>
            <select name="datos[riesgo]">@foreach(['Ninguno','Bajo','Moderado','Alto'] as $o)<option @selected(($d['riesgo'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
        <div class="field"><label>PHQ-9 (depresión)</label><input type="number" min="0" max="27" name="datos[phq9]" value="{{ $d['phq9'] ?? '' }}"></div>
        <div class="field"><label>Pensamiento</label>
            <select name="datos[pensamiento]">@foreach(['Normal','Rumiativo','Delirante','Desorganizado'] as $o)<option @selected(($d['pensamiento'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
        <div class="field"><label>Adherencia al tratamiento</label>
            <select name="datos[adherencia]">@foreach(['No aplica','Buena','Parcial','Nula'] as $o)<option @selected(($d['adherencia'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
    </div>
</div>
