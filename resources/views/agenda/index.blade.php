@extends('layouts.app')
@section('title', 'Agenda')

@section('content')
    <div class="page-head">
        <div><h1>Agenda</h1><p>Calendario de citas · arrastra una cita para reprogramarla.</p></div>
        <a href="{{ route('citas.create') }}" class="btn btn-primary"><i class="fa-solid fa-calendar-plus"></i> Nueva cita</a>
    </div>

    {{-- Resumen rápido --}}
    <div class="grid g-4 mb ag-stats">
        <div class="ag-stat"><div class="ag-ic" style="background:#ede9fe;color:#7c3aed"><i class="fa-solid fa-calendar-day"></i></div>
            <div><div class="ag-num" id="stHoy">0</div><div class="ag-cap">Citas hoy</div></div></div>
        <div class="ag-stat"><div class="ag-ic" style="background:#dbeafe;color:#2563eb"><i class="fa-solid fa-calendar-week"></i></div>
            <div><div class="ag-num" id="stSemana">0</div><div class="ag-cap">Esta semana</div></div></div>
        <div class="ag-stat"><div class="ag-ic" style="background:#fef3c7;color:#b45309"><i class="fa-solid fa-hourglass-half"></i></div>
            <div><div class="ag-num" id="stPend">0</div><div class="ag-cap">Pendientes</div></div></div>
        <div class="ag-stat"><div class="ag-ic" style="background:#fce7f3;color:#be185d"><i class="fa-solid fa-calendar-check"></i></div>
            <div><div class="ag-num" id="stMes">0</div><div class="ag-cap">En el mes</div></div></div>
    </div>

    {{-- Leyenda --}}
    <div class="ag-legend mb">
        <span><i style="background:#f59e0b"></i> Pendiente</span>
        <span><i style="background:#3b82f6"></i> Confirmada</span>
        <span><i style="background:#22c55e"></i> Atendida</span>
        <span><i style="background:#ef4444"></i> Cancelada</span>
        <span><i style="background:#94a3b8"></i> No asistió</span>
    </div>

    <div class="card ag-card"><div id="calendar"></div></div>

    <style>
    .ag-stats{gap:14px}
    .ag-stat{display:flex;align-items:center;gap:14px;background:#fff;border:1px solid var(--line);border-radius:16px;padding:16px 18px;box-shadow:0 4px 14px rgba(90,70,160,.05)}
    .ag-ic{width:46px;height:46px;border-radius:13px;display:grid;place-items:center;font-size:19px;flex:0 0 46px}
    .ag-num{font-size:24px;font-weight:700;color:var(--ink);line-height:1}
    .ag-cap{font-size:12px;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.4px;margin-top:3px}
    .ag-legend{display:flex;gap:16px;flex-wrap:wrap;font-size:12.5px;color:var(--ink-soft);font-weight:500}
    .ag-legend span{display:flex;align-items:center;gap:6px}
    .ag-legend i{width:12px;height:12px;border-radius:4px;display:inline-block}
    .ag-card{padding:18px 18px 8px}

    /* ---- FullCalendar tematizado ---- */
    .fc{--fc-border-color:#efeaf7;--fc-today-bg-color:#faf5ff;--fc-page-bg-color:#fff;font-family:inherit}
    .fc .fc-toolbar-title{font-size:20px;font-weight:700;color:var(--ink);text-transform:capitalize}
    .fc .fc-toolbar.fc-header-toolbar{margin-bottom:16px;flex-wrap:wrap;gap:8px}
    .fc .fc-button{background:var(--bg-pink);border:none;color:var(--violet);font-weight:600;font-size:13px;
        padding:8px 14px;border-radius:12px;text-transform:capitalize;box-shadow:none;transition:.15s}
    .fc .fc-button:hover{background:#f0e7ff;color:var(--violet)}
    .fc .fc-button-primary:not(:disabled).fc-button-active,
    .fc .fc-button-primary:not(:disabled):active{background:linear-gradient(135deg,var(--violet-2),var(--pink));color:#fff}
    .fc .fc-today-button{background:linear-gradient(135deg,var(--violet-2),var(--pink));color:#fff}
    .fc .fc-today-button:disabled{opacity:.5}
    .fc .fc-button .fc-icon{font-size:15px}
    .fc .fc-button-group{gap:6px;display:inline-flex}
    .fc-theme-standard .fc-scrollgrid{border-radius:14px;overflow:hidden;border:1px solid var(--line)}
    /* Encabezados de día: cada día con su color */
    .fc .fc-col-header-cell{padding:0;border-top:3px solid transparent}
    .fc .fc-col-header-cell-cushion{font-size:11.5px;font-weight:800;text-transform:uppercase;letter-spacing:.7px;padding:10px 6px;display:block;width:100%}
    .fc .fc-col-header-cell.fc-day-sun{background:#fef2f2;border-top-color:#ef4444}
    .fc .fc-col-header-cell.fc-day-mon{background:#f5f3ff;border-top-color:#7c3aed}
    .fc .fc-col-header-cell.fc-day-tue{background:#eff6ff;border-top-color:#2563eb}
    .fc .fc-col-header-cell.fc-day-wed{background:#f0fdfa;border-top-color:#0d9488}
    .fc .fc-col-header-cell.fc-day-thu{background:#fffbeb;border-top-color:#d97706}
    .fc .fc-col-header-cell.fc-day-fri{background:#fdf2f8;border-top-color:#db2777}
    .fc .fc-col-header-cell.fc-day-sat{background:#f0fdf4;border-top-color:#16a34a}
    .fc .fc-day-sun  .fc-col-header-cell-cushion{color:#dc2626}
    .fc .fc-day-mon  .fc-col-header-cell-cushion{color:#7c3aed}
    .fc .fc-day-tue  .fc-col-header-cell-cushion{color:#2563eb}
    .fc .fc-day-wed  .fc-col-header-cell-cushion{color:#0d9488}
    .fc .fc-day-thu  .fc-col-header-cell-cushion{color:#d97706}
    .fc .fc-day-fri  .fc-col-header-cell-cushion{color:#db2777}
    .fc .fc-day-sat  .fc-col-header-cell-cushion{color:#16a34a}

    /* Número de día como chip notorio */
    .fc .fc-daygrid-day-top{flex-direction:row;justify-content:flex-end}
    .fc .fc-daygrid-day-number{font-size:13px;font-weight:800;margin:6px;padding:3px 9px;border-radius:9px;min-width:26px;text-align:center;color:#5b4b86;background:#f2eefb;line-height:1.2}
    .fc .fc-day-sun  .fc-daygrid-day-number{color:#dc2626;background:#fde3e3}
    .fc .fc-day-mon  .fc-daygrid-day-number{color:#6d28d9;background:#ece7fd}
    .fc .fc-day-tue  .fc-daygrid-day-number{color:#1d4ed8;background:#dbeafe}
    .fc .fc-day-wed  .fc-daygrid-day-number{color:#0f766e;background:#cdf5ec}
    .fc .fc-day-thu  .fc-daygrid-day-number{color:#b45309;background:#fdecc8}
    .fc .fc-day-fri  .fc-daygrid-day-number{color:#be185d;background:#fbdcec}
    .fc .fc-day-sat  .fc-daygrid-day-number{color:#15803d;background:#d6f5df}

    /* Tinte muy suave de columna para seguir cada día */
    .fc .fc-daygrid-day.fc-day-sun{background:#fffbfb}
    .fc .fc-daygrid-day.fc-day-sat{background:#fbfefc}

    /* HOY prevalece sobre los estilos por día */
    .fc .fc-daygrid-day.fc-day-today{background:#faf5ff!important}
    .fc .fc-day-today .fc-daygrid-day-number{background:linear-gradient(135deg,var(--violet-2),var(--pink))!important;color:#fff!important;border-radius:50%;width:28px;height:28px;padding:0;display:inline-flex;align-items:center;justify-content:center;margin:5px;box-shadow:0 3px 8px rgba(168,85,247,.4)}
    .fc .fc-daygrid-day-frame{min-height:96px}

    /* Eventos como chips suaves */
    .fc .fc-daygrid-event,.fc .fc-timegrid-event{background:transparent!important;border:none!important;box-shadow:none!important;margin:2px 4px!important}
    .fc .fc-daygrid-event-harness{margin-top:1px}
    .ev{display:flex;align-items:center;gap:6px;padding:4px 8px;border-radius:8px;font-size:11.5px;font-weight:600;overflow:hidden;white-space:nowrap;transition:.12s}
    .ev:hover{transform:translateX(2px)}
    .ev .ev-dot{width:7px;height:7px;border-radius:50%;flex:0 0 7px}
    .ev .ev-time{font-weight:700;opacity:.9}
    .ev .ev-title{overflow:hidden;text-overflow:ellipsis}
    .ev-pendiente{background:#fef3c7;color:#92400e}
    .ev-confirmada{background:#dbeafe;color:#1e40af}
    .ev-atendida{background:#dcfce7;color:#166534}
    .ev-cancelada{background:#fee2e2;color:#991b1b}
    .ev-no_asistio{background:#f1f5f9;color:#475569}
    .fc .fc-timegrid-event .ev{white-space:normal}
    .fc .fc-more-link{color:var(--violet);font-weight:600;font-size:11px}

    /* ---- Vistas Semana y Día (timeGrid) ---- */
    .fc .fc-timegrid-col.fc-day-sun{background:#fffbfb}
    .fc .fc-timegrid-col.fc-day-mon{background:#fbfaff}
    .fc .fc-timegrid-col.fc-day-tue{background:#fafcff}
    .fc .fc-timegrid-col.fc-day-wed{background:#f8fefd}
    .fc .fc-timegrid-col.fc-day-thu{background:#fffdf6}
    .fc .fc-timegrid-col.fc-day-fri{background:#fffafd}
    .fc .fc-timegrid-col.fc-day-sat{background:#fbfefc}
    .fc .fc-timegrid-col.fc-day-today{background:#faf5ff!important}
    .fc .fc-timegrid-slot-label-cushion,.fc .fc-timegrid-axis-cushion{color:#9a8fbf;font-size:11px;font-weight:700}
    .fc .fc-timegrid-slot{height:2.4em}
    .fc .fc-timegrid-now-indicator-line{border-color:#ec4899}
    .fc .fc-timegrid-now-indicator-arrow{border-color:#ec4899;background:#ec4899}
    .fc .fc-timegrid-event{border-radius:8px;padding:1px 2px}
    .fc .fc-timegrid-event .ev{padding:3px 6px}
    /* En Semana/Día el número del encabezado se ve más grande */
    .fc .fc-timeGridWeek-view .fc-col-header-cell-cushion,
    .fc .fc-timeGridDay-view .fc-col-header-cell-cushion{font-size:12.5px;line-height:1.4}
    [data-theme="dark"] .ag-stat,[data-theme="dark"] .fc{background:#161428}
    @media(max-width:640px){ .fc .fc-toolbar.fc-header-toolbar{justify-content:center} .fc .fc-toolbar-title{font-size:16px} }
    </style>

    @push('scripts')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.11/index.global.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.11/index.global.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const el = document.getElementById('calendar');
        const token = document.querySelector('meta[name=csrf-token]').content;

        function esHoy(d){ const t=new Date(); return d.getFullYear()===t.getFullYear() && d.getMonth()===t.getMonth() && d.getDate()===t.getDate(); }
        function enSemana(d){ const t=new Date(); const day=(t.getDay()+6)%7; const ini=new Date(t); ini.setHours(0,0,0,0); ini.setDate(t.getDate()-day); const fin=new Date(ini); fin.setDate(ini.getDate()+7); return d>=ini && d<fin; }
        function actualizarStats(cal){
            let hoy=0,sem=0,pend=0,mes=0;
            cal.getEvents().forEach(function(e){ if(!e.start) return; mes++; if(esHoy(e.start))hoy++; if(enSemana(e.start))sem++; if(e.extendedProps.estado==='pendiente')pend++; });
            document.getElementById('stHoy').textContent=hoy;
            document.getElementById('stSemana').textContent=sem;
            document.getElementById('stPend').textContent=pend;
            document.getElementById('stMes').textContent=mes;
        }

        const cal = new FullCalendar.Calendar(el, {
            initialView: 'dayGridMonth',
            locale: 'es',
            height: 760,
            expandRows: true,
            dayMaxEvents: 3,
            fixedWeekCount: false,
            headerToolbar: { left:'prev,next today', center:'title', right:'dayGridMonth,timeGridWeek,timeGridDay' },
            buttonText: { today:'Hoy', month:'Mes', week:'Semana', day:'Día' },
            editable: true,
            events: '{{ route('agenda.eventos') }}',
            eventClassNames: function(arg){ return ['ev-'+(arg.event.extendedProps.estado||'pendiente')]; },
            eventContent: function(arg){
                const p = arg.event.extendedProps;
                const dot = '<span class="ev-dot" style="background:'+(arg.event.backgroundColor||'#7c3aed')+'"></span>';
                const time = p.hora ? '<span class="ev-time">'+p.hora+'</span>' : '';
                const title = '<span class="ev-title">'+arg.event.title+'</span>';
                return { html: '<div class="ev">'+dot+time+title+'</div>' };
            },
            eventDidMount: function(arg){
                const p = arg.event.extendedProps;
                arg.el.setAttribute('title', (p.hora?p.hora+' · ':'')+arg.event.title+' · '+(p.especialidad||'')+(p.medico?' · Dr(a). '+p.medico:'')+' — '+(p.estadoLabel||''));
            },
            eventsSet: function(){ actualizarStats(cal); },
            eventClick: function (info) { info.jsEvent.preventDefault(); if (info.event.url) window.location = info.event.url; },
            eventDrop: function (info) {
                const e = info.event;
                const fecha = e.start.getFullYear()+'-'+String(e.start.getMonth()+1).padStart(2,'0')+'-'+String(e.start.getDate()).padStart(2,'0');
                const hora = e.start.toTimeString().slice(0,5);
                fetch('{{ url('agenda/citas') }}/' + e.id + '/mover', {
                    method:'PUT',
                    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':token,'Accept':'application/json'},
                    body: JSON.stringify({ fecha: fecha, hora: hora })
                }).then(r => { if(!r.ok){ alert('No se pudo mover la cita'); info.revert(); } });
            }
        });
        cal.render();
    });
    </script>
    @endpush
@endsection
