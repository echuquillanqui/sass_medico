<div class="card pink mb">
    <h3 class="mb"><i class="fa-solid fa-eye" style="color:#0ea5e9"></i> Evaluación oftalmológica</h3>
    <div class="form-grid">
        <div class="field"><label>Agudeza visual OD</label><input name="datos[av_od]" value="{{ $d['av_od'] ?? '' }}" placeholder="20/20"></div>
        <div class="field"><label>Agudeza visual OI</label><input name="datos[av_oi]" value="{{ $d['av_oi'] ?? '' }}" placeholder="20/20"></div>
        <div class="field"><label>PIO OD (mmHg)</label><input name="datos[pio_od]" value="{{ $d['pio_od'] ?? '' }}"></div>
        <div class="field"><label>PIO OI (mmHg)</label><input name="datos[pio_oi]" value="{{ $d['pio_oi'] ?? '' }}"></div>
        <div class="field"><label>Fondo de ojo</label>
            <select name="datos[fondo_ojo]">@foreach(['Normal','Retinopatia','Papiledema','Otro'] as $o)<option @selected(($d['fondo_ojo'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
        <div class="field"><label>Corrección</label>
            <select name="datos[correccion]">@foreach(['Ninguna','Lentes','Lentes de contacto','Cirugia'] as $o)<option @selected(($d['correccion'] ?? '')==$o)>{{ $o }}</option>@endforeach</select></div>
    </div>
    <div class="field mt"><label>Prescripción óptica</label><input name="datos[prescripcion]" value="{{ $d['prescripcion'] ?? '' }}" placeholder="OD -1.25 / OI -1.00"></div>
</div>
