<div class="card pink mb">
    <h3 class="mb"><i class="fa-solid fa-disease" style="color:#d97706"></i> Evaluación gastroenterológica</h3>
    <div class="form-grid">
        <div class="field"><label>Síntoma principal</label>
            <select name="datos[sintoma]">@foreach(['Dolor abdominal','Reflujo','Nauseas','Diarrea','Estrenimiento','Sangrado'] as $o)<option @selected(($d['sintoma'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
        <div class="field"><label>Hábito intestinal</label>
            <select name="datos[habito]">@foreach(['Normal','Estrenimiento','Diarrea','Alternante'] as $o)<option @selected(($d['habito'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
        <div class="field"><label>H. pylori</label>
            <select name="datos[hpylori]">@foreach(['No estudiado','Negativo','Positivo','En tratamiento'] as $o)<option @selected(($d['hpylori'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
        <div class="field"><label>Endoscopía</label>
            <select name="datos[endoscopia]">@foreach(['No realizada','Normal','Gastritis','Ulcera','Otro'] as $o)<option @selected(($d['endoscopia'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
    </div>
    <div class="field mt"><label>Hallazgos del examen abdominal</label><input name="datos[abdomen]" value="{{ $d['abdomen'] ?? '' }}"></div>
</div>
