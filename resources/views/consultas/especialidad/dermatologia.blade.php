<div class="card pink mb">
    <h3 class="mb"><i class="fa-solid fa-hand-dots" style="color:#f59e0b"></i> Evaluación dermatológica</h3>
    <div class="form-grid">
        <div class="field"><label>Localización de la lesión</label><input name="datos[localizacion]" value="{{ $d['localizacion'] ?? '' }}" placeholder="Cara, tronco, brazo..."></div>
        <div class="field"><label>Tipo de lesión</label>
            <select name="datos[tipo_lesion]">@foreach(['Macula','Papula','Placa','Nodulo','Vesicula','Pustula','Ulcera','Otro'] as $o)<option @selected(($d['tipo_lesion'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
        <div class="field"><label>Distribución</label>
            <select name="datos[distribucion]">@foreach(['Localizada','Diseminada','Generalizada','Simetrica'] as $o)<option @selected(($d['distribucion'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
        <div class="field"><label>Tiempo de evolución</label><input name="datos[evolucion]" value="{{ $d['evolucion'] ?? '' }}" placeholder="2 semanas"></div>
        <div class="field"><label>Dermatoscopía</label>
            <select name="datos[dermatoscopia]">@foreach(['No realizada','Benigna','Sospechosa','Derivar'] as $o)<option @selected(($d['dermatoscopia'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
        <div class="field"><label>Prurito</label>
            <select name="datos[prurito]">@foreach(['No','Leve','Moderado','Intenso'] as $o)<option @selected(($d['prurito'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
    </div>
</div>
