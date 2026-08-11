<div class="card pink mb">
    <h3 class="mb"><i class="fa-solid fa-ear-listen" style="color:#14b8a6"></i> Evaluación ORL</h3>
    <div class="form-grid">
        <div class="field"><label>Otoscopía</label>
            <select name="datos[otoscopia]">@foreach(['Normal','Otitis','Tapon cerumen','Perforacion timpanica','Otro'] as $o)<option @selected(($d['otoscopia'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
        <div class="field"><label>Audiometría</label>
            <select name="datos[audiometria]">@foreach(['No realizada','Normal','Hipoacusia leve','Hipoacusia moderada','Hipoacusia severa'] as $o)<option @selected(($d['audiometria'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
        <div class="field"><label>Rinoscopía</label>
            <select name="datos[rinoscopia]">@foreach(['Normal','Rinitis','Desviacion septal','Poliposis'] as $o)<option @selected(($d['rinoscopia'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
        <div class="field"><label>Faringe / amígdalas</label>
            <select name="datos[faringe]">@foreach(['Normal','Faringitis','Amigdalitis','Hipertrofia amigdalina'] as $o)<option @selected(($d['faringe'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
    </div>
</div>
