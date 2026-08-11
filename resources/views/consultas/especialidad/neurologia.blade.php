<div class="card pink mb">
    <h3 class="mb"><i class="fa-solid fa-brain" style="color:#6d28d9"></i> Evaluación neurológica</h3>
    <div class="form-grid">
        <div class="field"><label>Glasgow (3-15)</label><input type="number" min="3" max="15" name="datos[glasgow]" value="{{ $d['glasgow'] ?? '' }}"></div>
        <div class="field"><label>Estado de conciencia</label>
            <select name="datos[conciencia]">@foreach(['Alerta','Somnoliento','Estuporoso','Coma'] as $o)<option @selected(($d['conciencia'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
        <div class="field"><label>Fuerza muscular</label>
            <select name="datos[fuerza]">@foreach(['5/5 Normal','4/5','3/5','2/5','1/5','0/5'] as $o)<option @selected(($d['fuerza'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
        <div class="field"><label>Reflejos</label>
            <select name="datos[reflejos]">@foreach(['Normales','Aumentados','Disminuidos','Ausentes'] as $o)<option @selected(($d['reflejos'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
        <div class="field"><label>Marcha</label>
            <select name="datos[marcha]">@foreach(['Normal','Ataxica','Hemiparetica','Con ayuda','No deambula'] as $o)<option @selected(($d['marcha'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
        <div class="field"><label>Pares craneales</label>
            <select name="datos[pares]">@foreach(['Normales','Alterados'] as $o)<option @selected(($d['pares'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
    </div>
</div>
