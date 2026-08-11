<div class="card pink mb">
    <h3 class="mb"><i class="fa-solid fa-heart-pulse" style="color:#ef4444"></i> Evaluación cardiovascular</h3>
    <div class="form-grid">
        <div class="field"><label>Saturación O₂ (%)</label><input type="number" name="datos[saturacion]" value="{{ $d['saturacion'] ?? '' }}"></div>
        <div class="field"><label>Colesterol total</label><input name="datos[colesterol]" value="{{ $d['colesterol'] ?? '' }}"></div>
        <div class="field"><label>Glucosa</label><input name="datos[glucosa]" value="{{ $d['glucosa'] ?? '' }}"></div>
        <div class="field"><label>Ritmo (ECG)</label>
            <select name="datos[ecg]">
                @foreach(['Sinusal normal','Taquicardia','Bradicardia','Arritmia','Fibrilación auricular','Otro'] as $o)
                    <option @selected(($d['ecg'] ?? '')==$o)>{{ $o }}</option>
                @endforeach
            </select></div>
        <div class="field"><label>Riesgo cardiovascular</label>
            <select name="datos[riesgo]">
                @foreach(['Bajo','Moderado','Alto','Muy alto'] as $o)
                    <option @selected(($d['riesgo'] ?? '')==$o)>{{ $o }}</option>
                @endforeach
            </select></div>
        <div class="field"><label>Tabaquismo</label>
            <select name="datos[tabaquismo]">
                @foreach(['No','Ex fumador','Sí'] as $o)
                    <option @selected(($d['tabaquismo'] ?? '')==$o)>{{ $o }}</option>
                @endforeach
            </select></div>
    </div>
</div>
