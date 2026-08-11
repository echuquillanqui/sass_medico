<div class="card pink mb">
    <h3 class="mb"><i class="fa-solid fa-apple-whole" style="color:#22c55e"></i> Evaluación nutricional</h3>
    <div class="form-grid">
        <div class="field"><label>Circunferencia abdominal (cm)</label><input name="datos[cintura]" value="{{ $d['cintura'] ?? '' }}"></div>
        <div class="field"><label>% Grasa corporal</label><input name="datos[grasa]" value="{{ $d['grasa'] ?? '' }}"></div>
        <div class="field"><label>Masa muscular</label><input name="datos[musculo]" value="{{ $d['musculo'] ?? '' }}"></div>
        <div class="field"><label>Objetivo</label>
            <select name="datos[objetivo]">@foreach(['Bajar de peso','Mantener','Subir de peso','Ganar musculo','Control clinico'] as $o)<option @selected(($d['objetivo'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
        <div class="field"><label>Nivel de actividad</label>
            <select name="datos[actividad]">@foreach(['Sedentario','Ligero','Moderado','Intenso'] as $o)<option @selected(($d['actividad'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
    </div>
    <div class="field mt"><label>Plan alimentario / recomendaciones</label><textarea name="datos[plan]">{{ $d['plan'] ?? '' }}</textarea></div>
</div>
