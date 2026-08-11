@php
    $map = [
        'pendiente'  => ['amber', 'Pendiente', 'fa-clock'],
        'confirmada' => ['blue',  'Confirmada', 'fa-circle-check'],
        'atendida'   => ['green', 'Atendida', 'fa-user-check'],
        'cancelada'  => ['red',   'Cancelada', 'fa-ban'],
        'no_asistio' => ['gray',  'No asistió', 'fa-user-xmark'],
    ];
    [$color, $label, $icon] = $map[$estado] ?? ['gray', ucfirst($estado), 'fa-circle'];
@endphp
<span class="pill {{ $color }}"><i class="fa-solid {{ $icon }}"></i> {{ $label }}</span>
