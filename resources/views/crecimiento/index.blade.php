@extends('layouts.app')
@section('title', 'Control de crecimiento')

@section('content')
    <div class="page-head">
        <div class="flex gap">
            <div style="width:56px;height:56px;border-radius:16px;background:#f59e0b;color:#fff;display:grid;place-items:center;font-size:24px">
                <i class="fa-solid fa-child-reaching"></i>
            </div>
            <div>
                <h1>Control de crecimiento</h1>
                <p>Curvas de peso, talla e IMC por edad con percentiles OMS.</p>
            </div>
        </div>
        <a href="{{ route('pacientes.create') }}" class="btn btn-primary"><i class="fa-solid fa-user-plus"></i> Nuevo paciente</a>
    </div>

    <div class="card mb">
        <form method="GET" class="flex gap" style="flex-wrap:wrap">
            <input name="q" value="{{ $buscar }}" placeholder="Buscar niño/a por nombre o documento…" style="flex:1;min-width:220px">
            <button class="btn btn-light"><i class="fa-solid fa-magnifying-glass"></i> Buscar</button>
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Paciente</th><th>Documento</th><th>Edad</th><th>Sexo</th><th></th></tr></thead>
            <tbody>
            @forelse($pacientes as $p)
                @php $apto = $p->fecha_nacimiento && in_array($p->sexo, ['M','F']); @endphp
                <tr>
                    <td><span class="avatar-sm">{{ mb_substr($p->nombres,0,1) }}{{ mb_substr($p->apellidos,0,1) }}</span>{{ $p->nombre_completo }}</td>
                    <td>{{ $p->documento ?? '—' }}</td>
                    <td>{{ $p->edad !== null ? $p->edad.' años' : '—' }}</td>
                    <td>{{ ['M'=>'Masculino','F'=>'Femenino'][$p->sexo] ?? '—' }}</td>
                    <td style="text-align:right">
                        @if($apto)
                            <a href="{{ route('pacientes.crecimiento',$p) }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-chart-line"></i> Ver curva</a>
                        @else
                            <span class="pill gray" title="Requiere fecha de nacimiento y sexo">Datos incompletos</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="empty"><i class="fa-solid fa-child-reaching"></i><p>No hay pacientes pediátricos todavía.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $pacientes->links() }}
@endsection
