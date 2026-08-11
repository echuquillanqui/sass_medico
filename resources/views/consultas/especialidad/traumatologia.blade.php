<div class="card pink mb">
    <h3 class="mb"><i class="fa-solid fa-bone" style="color:#64748b"></i> Evaluación traumatológica</h3>
    <div class="form-grid">
        <div class="field"><label>Segmento afectado</label><input name="datos[segmento]" value="{{ $d['segmento'] ?? '' }}" placeholder="Rodilla derecha, hombro..."></div>
        <div class="field"><label>Mecanismo de lesión</label>
            <select name="datos[mecanismo]">@foreach(['Caida','Golpe directo','Torsion','Sobreuso','Accidente','Otro'] as $o)<option @selected(($d['mecanismo'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
        <div class="field"><label>Dolor (0-10)</label><input type="number" min="0" max="10" name="datos[dolor]" value="{{ $d['dolor'] ?? '' }}"></div>
        <div class="field"><label>Movilidad</label>
            <select name="datos[movilidad]">@foreach(['Conservada','Limitada','Muy limitada','Impotencia funcional'] as $o)<option @selected(($d['movilidad'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
        <div class="field"><label>Radiografía</label>
            <select name="datos[rx]">@foreach(['No realizada','Normal','Fractura','Luxacion','Artrosis','Otro'] as $o)<option @selected(($d['rx'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
        <div class="field"><label>Inmovilización</label>
            <select name="datos[inmovilizacion]">@foreach(['No','Ferula','Yeso','Cabestrillo','Vendaje'] as $o)<option @selected(($d['inmovilizacion'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
    </div>
</div>
