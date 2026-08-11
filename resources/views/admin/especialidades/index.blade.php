@extends('layouts.app')
@section('title', 'Especialidades')

@section('content')
    <div class="page-head">
        <div><h1>Catálogo de especialidades</h1><p>Módulos disponibles en la plataforma para asignar a las empresas.</p></div>
        <a href="{{ route('admin.especialidades.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Nueva especialidad</a>
    </div>

    <div class="grid g-4">
        @foreach($especialidades as $e)
            <div class="card" style="text-align:center">
                <div style="width:56px;height:56px;border-radius:16px;background:{{ $e->color }}1a;color:{{ $e->color }};display:grid;place-items:center;font-size:24px;margin:0 auto 10px"><i class="fa-solid {{ $e->icono }}"></i></div>
                <h3 style="margin:0">{{ $e->nombre }}</h3>
                <p class="muted" style="min-height:34px">{{ \Illuminate\Support\Str::limit($e->descripcion, 60) }}</p>
                <div class="flex gap" style="justify-content:center;margin-bottom:10px">
                    <span class="pill {{ $e->activo ? 'green' : 'gray' }}">{{ $e->activo ? 'Activa' : 'Inactiva' }}</span>
                    <span class="pill violet">{{ $e->empresas_count }} empresas</span>
                </div>
                <div class="flex gap" style="justify-content:center">
                    <a href="{{ route('admin.especialidades.edit', $e) }}" class="btn btn-light btn-sm"><i class="fa-solid fa-pen"></i> Editar</a>
                    <form method="POST" action="{{ route('admin.especialidades.destroy', $e) }}" onsubmit="return confirm('¿Eliminar especialidad?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button></form>
                </div>
            </div>
        @endforeach
    </div>
@endsection
