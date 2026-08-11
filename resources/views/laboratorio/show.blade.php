@extends('layouts.app')
@section('title', 'Orden #'.$orden->id)

@section('content')
    <div class="page-head">
        <div>
            <h1>Orden #{{ str_pad($orden->id,5,'0',STR_PAD_LEFT) }}</h1>
            <p>{{ $orden->paciente->nombre_completo }} · {{ $orden->fecha->format('d/m/Y') }} · Solicita: {{ $orden->medico->name ?? '—' }}
            @php $c=['solicitada'=>'amber','en_proceso'=>'blue','completada'=>'green','entregada'=>'violet'][$orden->estado]??'gray'; @endphp
            · <span class="pill {{ $c }}">{{ $orden->estado_label }}</span></p>
        </div>
        <div class="flex gap">
            <a href="{{ route('laboratorio.pdf',$orden) }}" target="_blank" class="btn btn-light"><i class="fa-solid fa-file-pdf"></i> Informe PDF</a>
            @if($orden->estado === 'completada')
                <form method="POST" action="{{ route('laboratorio.entregar',$orden) }}">@csrf<button class="btn btn-primary"><i class="fa-solid fa-check-double"></i> Marcar entregada</button></form>
            @endif
            <a href="{{ route('laboratorio.index') }}" class="btn btn-ghost">Volver</a>
        </div>
    </div>

    @if($orden->observaciones)<div class="card mb"><p class="muted" style="margin:0"><b>Observaciones:</b> {{ $orden->observaciones }}</p></div>@endif

    <form method="POST" action="{{ route('laboratorio.resultados',$orden) }}" class="table-wrap">
        @csrf @method('PUT')
        <table>
            <thead><tr><th>Examen</th><th>Resultado</th><th>Unidad</th><th>Referencia</th><th>Fuera de rango</th><th>Notas</th></tr></thead>
            <tbody>
            @php $grupoActual = null; @endphp
            @foreach($orden->items as $it)
                @if($it->grupo && $it->grupo !== $grupoActual)
                    <tr><td colspan="6" style="background:var(--surface-2)"><b><i class="fa-solid fa-vials"></i> {{ $it->grupo }}</b></td></tr>
                    @php $grupoActual = $it->grupo; @endphp
                @elseif(!$it->grupo)
                    @php $grupoActual = null; @endphp
                @endif
                <tr>
                    <td><b>{{ $it->nombre }}</b></td>
                    <td><input name="items[{{ $it->id }}][resultado]" value="{{ $it->resultado }}" style="width:110px;border:1.5px solid var(--line);border-radius:8px;padding:6px 8px"></td>
                    <td>{{ $it->unidad ?? '—' }}</td>
                    <td>{{ $it->valor_referencia ?? '—' }}</td>
                    <td style="text-align:center"><input type="checkbox" name="items[{{ $it->id }}][fuera_rango]" value="1" @checked($it->fuera_rango)></td>
                    <td><input name="items[{{ $it->id }}][notas]" value="{{ $it->notas }}" style="width:100%;border:1.5px solid var(--line);border-radius:8px;padding:6px 8px"></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="padding:16px 18px"><button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar resultados</button></div>
    </form>
@endsection
