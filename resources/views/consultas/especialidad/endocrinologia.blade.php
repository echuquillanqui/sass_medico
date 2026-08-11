<div class="card pink mb">
    <h3 class="mb"><i class="fa-solid fa-dna" style="color:#db2777"></i> Evaluación endocrinológica</h3>
    <div class="form-grid">
        <div class="field"><label>Glucosa (mg/dL)</label><input name="datos[glucosa]" value="{{ $d['glucosa'] ?? '' }}"></div>
        <div class="field"><label>HbA1c (%)</label><input name="datos[hba1c]" value="{{ $d['hba1c'] ?? '' }}"></div>
        <div class="field"><label>TSH</label><input name="datos[tsh]" value="{{ $d['tsh'] ?? '' }}"></div>
        <div class="field"><label>T4 libre</label><input name="datos[t4]" value="{{ $d['t4'] ?? '' }}"></div>
        <div class="field"><label>Estado tiroideo</label>
            <select name="datos[tiroides]">@foreach(['Eutiroideo','Hipotiroidismo','Hipertiroidismo'] as $o)<option @selected(($d['tiroides'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
        <div class="field"><label>Control diabético</label>
            <select name="datos[control_dm]">@foreach(['No aplica','Bueno','Regular','Malo'] as $o)<option @selected(($d['control_dm'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
    </div>
</div>
