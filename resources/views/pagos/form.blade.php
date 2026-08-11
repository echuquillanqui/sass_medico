@extends('layouts.app')
@section('title', $pago->exists ? 'Editar pago' : 'Registrar pago')

@section('content')
    <div class="page-head">
        <div><h1>{{ $pago->exists ? 'Editar pago' : 'Registrar pago' }}</h1><p>Registro de cobro.</p></div>
        <a href="{{ route('pagos.index') }}" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>

    <form method="POST" action="{{ $pago->exists ? route('pagos.update',$pago) : route('pagos.store') }}" class="card">
        @csrf
        @if($pago->exists) @method('PUT') @endif
        <div class="form-grid">
            <div class="field"><label>Paciente *</label>
                <select name="paciente_id" required>
                    <option value="">— Selecciona —</option>
                    @foreach($pacientes as $p)
                        <option value="{{ $p->id }}" @selected(old('paciente_id',$pacienteSel)==$p->id)>{{ $p->nombre_completo }}</option>
                    @endforeach
                </select></div>
            <div class="field"><label>Servicio (autocompleta)</label>
                <select id="servicioSel" onchange="aplicarServicio()">
                    <option value="">— Manual —</option>
                    @foreach(($servicios ?? []) as $sv)
                        <option value="{{ $sv->nombre }}" data-precio="{{ $sv->precio }}">{{ $sv->nombre }} · {{ auth()->user()->empresa->moneda ?? 'S/' }} {{ number_format($sv->precio,2) }}</option>
                    @endforeach
                </select></div>
            <div class="field"><label>Concepto *</label><input name="concepto" value="{{ old('concepto',$pago->concepto) }}" placeholder="Consulta, tratamiento..." required></div>
            <div class="field"><label>Monto *</label><input type="number" step="0.01" name="monto" value="{{ old('monto',$pago->monto) }}" required></div>
            <div class="field"><label>Método</label>
                <select name="metodo">
                    @foreach(['efectivo'=>'Efectivo','tarjeta'=>'Tarjeta','transferencia'=>'Transferencia','yape_plin'=>'Yape / Plin','otro'=>'Otro'] as $k=>$v)
                        <option value="{{ $k }}" @selected(old('metodo',$pago->metodo)==$k)>{{ $v }}</option>
                    @endforeach
                </select></div>
            <div class="field"><label>Estado</label>
                <select name="estado">
                    @foreach(['pagado'=>'Pagado','pendiente'=>'Pendiente','anulado'=>'Anulado'] as $k=>$v)
                        <option value="{{ $k }}" @selected(old('estado',$pago->estado ?? 'pagado')==$k)>{{ $v }}</option>
                    @endforeach
                </select></div>
            <div class="field"><label>Fecha *</label><input type="date" name="fecha" value="{{ old('fecha', optional($pago->fecha)->format('Y-m-d') ?? now()->toDateString()) }}" required></div>
            <div class="field full"><label>Notas</label><textarea name="notas">{{ old('notas',$pago->notas) }}</textarea></div>
        </div>
        <div class="mt"><button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar pago</button></div>
    </form>

    @push('scripts')
    <script>
    function aplicarServicio(){
        const o=document.getElementById('servicioSel');
        const opt=o.options[o.selectedIndex];
        if(!opt.value) return;
        document.querySelector('[name=concepto]').value = opt.value;
        document.querySelector('[name=monto]').value = opt.dataset.precio;
    }
    </script>
    @endpush

@endsection
