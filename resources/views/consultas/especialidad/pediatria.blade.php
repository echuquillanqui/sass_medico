<div class="card pink mb">
    <h3 class="mb"><i class="fa-solid fa-baby" style="color:#ec4899"></i> Control de crecimiento</h3>
    <div class="form-grid">
        <div class="field"><label>Perímetro cefálico (cm)</label><input name="datos[perimetro_cefalico]" value="{{ $d['perimetro_cefalico'] ?? '' }}"></div>
        <div class="field"><label>Percentil peso</label><input name="datos[percentil_peso]" value="{{ $d['percentil_peso'] ?? '' }}" placeholder="p50"></div>
        <div class="field"><label>Percentil talla</label><input name="datos[percentil_talla]" value="{{ $d['percentil_talla'] ?? '' }}" placeholder="p50"></div>
        <div class="field"><label>Desarrollo psicomotor</label>
            <select name="datos[desarrollo]">
                @foreach(['Normal','En observación','Retraso leve','Derivar'] as $o)
                    <option @selected(($d['desarrollo'] ?? '')==$o)>{{ $o }}</option>
                @endforeach
            </select></div>
    </div>
    <div class="field mt"><label>Vacunas aplicadas hoy</label>
        <input name="datos[vacunas]" value="{{ $d['vacunas'] ?? '' }}" placeholder="BCG, Pentavalente, ...">
    </div>
    <div class="field mt"><label>Alimentación</label>
        <select name="datos[alimentacion]">
            @foreach(['Lactancia materna exclusiva','Fórmula','Mixta','Alimentación complementaria','Dieta familiar'] as $o)
                <option @selected(($d['alimentacion'] ?? '')==$o)>{{ $o }}</option>
            @endforeach
        </select></div>
</div>
