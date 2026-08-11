<div class="card pink mb">
    <h3 class="mb"><i class="fa-solid fa-baby-carriage" style="color:#d946ef"></i> Control obstétrico</h3>
    <div class="form-grid">
        <div class="field"><label>FUM</label><input type="date" name="datos[fum]" value="{{ $d['fum'] ?? '' }}"></div>
        <div class="field"><label>Semanas de gestación</label><input type="number" name="datos[semanas]" value="{{ $d['semanas'] ?? '' }}"></div>
        <div class="field"><label>Altura uterina (cm)</label><input name="datos[altura_uterina]" value="{{ $d['altura_uterina'] ?? '' }}"></div>
        <div class="field"><label>FCF (lpm)</label><input type="number" name="datos[fcf]" value="{{ $d['fcf'] ?? '' }}"></div>
        <div class="field"><label>Presentación</label>
            <select name="datos[presentacion]">@foreach(['Cefalica','Podalica','Transversa','No definida'] as $o)<option @selected(($d['presentacion'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
        <div class="field"><label>Movimientos fetales</label>
            <select name="datos[movimientos]">@foreach(['Presentes','Ausentes','No aplica'] as $o)<option @selected(($d['movimientos'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
        <div class="field"><label>Gestaciones (G-P-A)</label><input name="datos[gpa]" value="{{ $d['gpa'] ?? '' }}" placeholder="G2 P1 A0"></div>
    </div>
</div>
