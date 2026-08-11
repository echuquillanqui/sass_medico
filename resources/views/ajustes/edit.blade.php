@extends('layouts.app')
@section('title', 'Ajustes')

@section('content')
    @php $pr = $usuario->preferencias ?? []; @endphp
    <div class="page-head"><div><h1>Ajustes generales</h1><p>Personaliza tu experiencia en el sistema.</p></div></div>

    <form method="POST" action="{{ route('ajustes.update') }}" class="card" style="max-width:640px">
        @csrf @method('PUT')

        <h3 class="mb"><i class="fa-solid fa-palette" style="color:var(--violet)"></i> Apariencia</h3>
        <div class="form-grid mb">
            <div class="field"><label>Tema por defecto</label>
                <select name="tema">
                    @foreach(['auto'=>'Automático','claro'=>'Claro','oscuro'=>'Oscuro'] as $k=>$v)
                        <option value="{{ $k }}" @selected(($pr['tema'] ?? 'auto')==$k)>{{ $v }}</option>
                    @endforeach
                </select></div>
            <div class="field"><label>Densidad</label>
                <select name="densidad">
                    @foreach(['comodo'=>'Cómoda','compacto'=>'Compacta'] as $k=>$v)
                        <option value="{{ $k }}" @selected(($pr['densidad'] ?? 'comodo')==$k)>{{ $v }}</option>
                    @endforeach
                </select></div>
            <div class="field"><label>Filas por página</label>
                <select name="items_por_pagina">
                    @foreach([10,15,25,50] as $n)
                        <option value="{{ $n }}" @selected(($pr['items_por_pagina'] ?? 10)==$n)>{{ $n }}</option>
                    @endforeach
                </select></div>
        </div>

        <h3 class="mb"><i class="fa-solid fa-bell" style="color:var(--pink)"></i> Notificaciones internas</h3>
        <label class="flex gap mb" style="cursor:pointer"><input type="checkbox" name="notif_citas" value="1" @checked($pr['notif_citas'] ?? true)> <span>Avisarme de nuevas citas</span></label>
        <label class="flex gap mb" style="cursor:pointer"><input type="checkbox" name="notif_pagos" value="1" @checked($pr['notif_pagos'] ?? true)> <span>Avisarme de nuevos pagos</span></label>

        <div class="mt"><button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar preferencias</button></div>
        <p class="muted mt" style="font-size:12px">El tema "Automático" respeta el interruptor de la barra superior. "Claro/Oscuro" fija tu preferencia al iniciar sesión.</p>
    </form>
@endsection
