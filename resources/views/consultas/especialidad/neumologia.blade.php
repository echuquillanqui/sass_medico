<div class="card pink mb">
    <h3 class="mb"><i class="fa-solid fa-lungs" style="color:#0891b2"></i> Evaluación neumológica</h3>
    <div class="form-grid">
        <div class="field"><label>Saturación O₂ (%)</label><input name="datos[saturacion]" value="{{ $d['saturacion'] ?? '' }}"></div>
        <div class="field"><label>Frec. respiratoria</label><input name="datos[fr]" value="{{ $d['fr'] ?? '' }}"></div>
        <div class="field"><label>FEV1 (%)</label><input name="datos[fev1]" value="{{ $d['fev1'] ?? '' }}"></div>
        <div class="field"><label>FVC (%)</label><input name="datos[fvc]" value="{{ $d['fvc'] ?? '' }}"></div>
        <div class="field"><label>Disnea (mMRC)</label>
            <select name="datos[disnea]">@foreach(['0','1','2','3','4'] as $o)<option @selected(($d['disnea'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
        <div class="field"><label>Auscultación</label>
            <select name="datos[auscultacion]">@foreach(['Normal','Sibilancias','Crepitos','Roncus','Hipoventilacion'] as $o)<option @selected(($d['auscultacion'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
        <div class="field"><label>Tos</label>
            <select name="datos[tos]">@foreach(['No','Seca','Productiva'] as $o)<option @selected(($d['tos'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
        <div class="field"><label>Tabaquismo</label>
            <select name="datos[tabaquismo]">@foreach(['No','Ex fumador','Si'] as $o)<option @selected(($d['tabaquismo'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
    </div>
</div>
