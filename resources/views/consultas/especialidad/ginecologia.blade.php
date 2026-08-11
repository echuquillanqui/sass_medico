<div class="card pink mb">
    <h3 class="mb"><i class="fa-solid fa-venus" style="color:#a855f7"></i> Control ginecológico / prenatal</h3>
    <div class="form-grid">
        <div class="field"><label>FUM (última regla)</label><input type="date" name="datos[fum]" value="{{ $d['fum'] ?? '' }}"></div>
        <div class="field"><label>Semanas de gestación</label><input type="number" name="datos[semanas_gestacion]" value="{{ $d['semanas_gestacion'] ?? '' }}"></div>
        <div class="field"><label>Gestaciones (G-P-A)</label><input name="datos[gpa]" value="{{ $d['gpa'] ?? '' }}" placeholder="G2 P1 A0"></div>
        <div class="field"><label>Altura uterina (cm)</label><input name="datos[altura_uterina]" value="{{ $d['altura_uterina'] ?? '' }}"></div>
        <div class="field"><label>Método anticonceptivo</label>
            <select name="datos[anticonceptivo]">
                @foreach(['Ninguno','Oral','Inyectable','DIU','Implante','Preservativo','Otro'] as $o)
                    <option @selected(($d['anticonceptivo'] ?? '')==$o)>{{ $o }}</option>
                @endforeach
            </select></div>
        <div class="field"><label>Papanicolaou</label>
            <select name="datos[papanicolaou]">
                @foreach(['No aplica','Tomado hoy','Pendiente','Normal previo','Anormal previo'] as $o)
                    <option @selected(($d['papanicolaou'] ?? '')==$o)>{{ $o }}</option>
                @endforeach
            </select></div>
    </div>
</div>
