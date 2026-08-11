@extends('portal.layout')
@section('title','Califica tu atención')
@section('content')
    <div class="flex between mb"><h1 style="margin:0">¿Cómo fue tu atención?</h1><a href="{{ route('portal.dashboard') }}" class="btn btn-ghost btn-sm">Volver</a></div>

    <div class="card mb"><p class="muted" style="margin:0">Cita del {{ $cita->fecha->locale('es')->isoFormat('D MMM YYYY') }} · {{ $cita->especialidad->nombre ?? 'General' }} · {{ $cita->medico->name ?? '' }}</p></div>

    <form method="POST" action="{{ route('portal.cita.encuesta.guardar', $cita) }}" class="card">
        @csrf
        <div class="field mb">
            <label>Tu calificación</label>
            <div id="stars" style="font-size:34px;color:#d1d5db;cursor:pointer">
                @for($i=1;$i<=5;$i++)<span data-v="{{ $i }}" onclick="setStar({{ $i }})">★</span>@endfor
            </div>
            <input type="hidden" name="puntuacion" id="puntuacion" value="5">
        </div>
        <div class="field mb"><label>Comentario (opcional)</label><textarea name="comentario" placeholder="Cuéntanos tu experiencia..."></textarea></div>
        <button class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Enviar</button>
    </form>

    @push('scripts')
    <script>
    function setStar(v){
        document.getElementById('puntuacion').value=v;
        document.querySelectorAll('#stars span').forEach(s=>{ s.style.color = (+s.dataset.v<=v)?'#f59e0b':'#d1d5db'; });
    }
    setStar(5);
    </script>
    @endpush
@endsection
