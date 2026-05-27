// ============================================================
// DASHBOARD ESTUDIANTE — JavaScript
// Sidebar toggle, DataTable, Calendario con eventos reales
// ============================================================

// ─── Sidebar ────────────────────────────────────────────────
const leftSidebar  = document.getElementById('leftSidebar');
const appGrid      = document.getElementById('appGrid');
const toggleLeft   = document.getElementById('toggleLeft');

let leftVisible = localStorage.getItem('leftSidebarVisible') !== 'false';

function updateGridState() {
  appGrid.classList.remove('hide-left');
  if (!leftVisible) appGrid.classList.add('hide-left');
}

function toggleLeftSidebar() {
  leftVisible = !leftVisible;
  if (leftSidebar) leftSidebar.classList.toggle('hidden', !leftVisible);
  localStorage.setItem('leftSidebarVisible', leftVisible);
  updateGridState();
}

if (toggleLeft) toggleLeft.addEventListener('click', toggleLeftSidebar);

if (leftSidebar && !leftVisible) leftSidebar.classList.add('hidden');
updateGridState();

// ─── DataTable ──────────────────────────────────────────────
$(document).ready(function () {
  $('#studentsTable').DataTable({
    language: {
      url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
      emptyTable: '✓ ¡Excelente! No tienes materias con bajo rendimiento.'
    },
    pageLength: 5,
    lengthMenu: [[5, 10, 25], [5, 10, 25]],
    ordering: true,
    order: [[0, 'asc']],
    pagingType: 'simple_numbers',
    columnDefs: [
      { orderable: true,  targets: [0, 1, 2, 3] },
      { orderable: false, targets: [4]            },
      { searchable: false, targets: [4]           },
      { className: 'text-center', targets: [4]    }
    ],
    dom: "<'row align-items-center'<'col-sm-6'l><'col-sm-6 text-sm-end'f>>" +
         "<'row'<'col-12'tr>>" +
         "<'row align-items-center mt-2'<'col-sm-6'i><'col-sm-6 text-sm-end'p>>"
  });
});

// ─── Calendario ─────────────────────────────────────────────
const currentDate = new Date();
let   calMonth    = currentDate.getMonth();
let   calYear     = currentDate.getFullYear();

const MONTHS      = ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                     'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
const DAYS_HEADER = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];

// Mapa tipo → icono (mismo que docente)
const ICON_BY_TYPE = {
  'Examen':      'ri-file-edit-line',
  'Tarea':       'ri-file-list-3-line',
  'Proyecto':    'ri-folder-open-line',
  'Quiz':        'ri-questionnaire-line',
  'Taller':      'ri-tools-line',
  'Exposición':  'ri-presentation-line',
  'Exposicion':  'ri-presentation-line',
  'Laboratorio': 'ri-flask-line',
  'Actividad':   'ri-calendar-event-line',
  'Evento':      'ri-calendar-check-line'
};

function iconPorTipo(tipo) {
  return ICON_BY_TYPE[tipo] || 'ri-calendar-event-line';
}

function escapeHtml(str) {
  return String(str || '')
    .replace(/&/g,  '&amp;')
    .replace(/</g,  '&lt;')
    .replace(/>/g,  '&gt;')
    .replace(/"/g,  '&quot;')
    .replace(/'/g,  '&#39;');
}

// ── Leer eventos desde data-calendar-events del #appGrid ────
let rawEvents = [];
if (appGrid && appGrid.dataset.calendarEvents) {
  try {
    const parsed = JSON.parse(appGrid.dataset.calendarEvents);
    rawEvents = Array.isArray(parsed) ? parsed : [];
  } catch (e) {
    rawEvents = [];
  }
}

// Indexar por fecha (YYYY-MM-DD) para acceso O(1)
const eventsByDate = rawEvents.reduce((acc, item) => {
  const dateKey = String(item.fecha_evento || '').slice(0, 10);
  if (!dateKey) return acc;
  if (!acc[dateKey]) acc[dateKey] = [];

  const tipo = String(item.tipo_evento || 'Actividad').trim();
  acc[dateKey].push({
    title:       item.nombre_evento  || 'Actividad académica',
    description: item.descripcion    || '',
    type:        tipo,
    icon:        iconPorTipo(tipo),
    source:      item.fuente         || 'actividad',
    time:        item.hora_inicio    || ''
  });
  return acc;
}, {});

// ── Modal del día ────────────────────────────────────────────
const dayModalEl    = document.getElementById('estudianteCalendarDayModal');
const dayModalTitle = document.getElementById('estudianteCalendarDayModalLabel');
const dayModalBody  = document.getElementById('estudianteCalendarDayModalBody');

function mostrarEventosDia(dateString, events) {
  if (!dayModalEl) return;

  const [year, month, day] = dateString.split('-');
  dayModalTitle.textContent = `Eventos del ${day}/${month}/${year}`;

  if (!events || events.length === 0) {
    dayModalBody.innerHTML =
      '<div style="border:1px dashed rgba(255,255,255,.2); border-radius:12px; padding:22px; text-align:center; color:#c5d1ee;">' +
      '<i class="ri-calendar-line" style="font-size:28px;"></i><br>No hay actividades para este día.</div>';
  } else {
    const html = events.map(ev => {
      const timeBlock = ev.time
        ? `<span><i class="ri-time-line"></i> ${escapeHtml(String(ev.time).slice(0,5))}</span>`
        : '<span><i class="ri-time-line"></i> Sin hora</span>';
      const sourceLabel = ev.source === 'actividad' ? 'Tarea / Actividad' : 'Evento institucional';
      return `
        <article style="background:#171f45; border:1px solid rgba(255,255,255,.08); border-radius:12px; padding:14px; color:#e6e9f4; margin-bottom:10px;">
          <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
            <div style="width:34px; height:34px; border-radius:10px; display:grid; place-items:center; font-size:18px; background:#232e60; color:#a4b1ff; flex-shrink:0;">
              <i class="${escapeHtml(ev.icon)}"></i>
            </div>
            <span style="display:inline-flex; align-items:center; padding:4px 10px; border-radius:999px; font-size:12px; font-weight:600; color:#dbe2ff; background:rgba(79,70,229,.25); border:1px solid rgba(164,177,255,.25);">
              ${escapeHtml(ev.type)}
            </span>
          </div>
          <h6 style="margin:0 0 4px; font-size:15px; color:#fff;">${escapeHtml(ev.title)}</h6>
          <p style="margin:0; color:#b8c2df; font-size:13px; line-height:1.4;">${escapeHtml(ev.description)}</p>
          <div style="margin-top:8px; display:flex; gap:14px; flex-wrap:wrap; font-size:12px; color:#9daccc;">
            ${timeBlock}
            <span><i class="ri-information-line"></i> ${sourceLabel}</span>
          </div>
        </article>`;
    }).join('');

    dayModalBody.innerHTML =
      `<div style="color:#c5d1ee; font-size:13px; margin-bottom:10px;">Total: ${events.length} actividad(es)</div>` + html;
  }

  // Mostrar modal con Bootstrap 5
  if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
    const bsModal = bootstrap.Modal.getOrCreateInstance(dayModalEl);
    bsModal.show();
  }
}

// ── Generar calendario ───────────────────────────────────────
function generateCalendar(month, year) {
  const calendarGrid = document.getElementById('calendarGrid');
  if (!calendarGrid) return;

  const firstDay      = new Date(year, month, 1).getDay();
  const daysInMonth   = new Date(year, month + 1, 0).getDate();
  const daysInPrev    = new Date(year, month, 0).getDate();

  let html = '';

  // Cabeceras de días
  DAYS_HEADER.forEach(d => {
    html += `<div class="calendar-day-header">${d}</div>`;
  });

  // Días del mes anterior (relleno)
  for (let i = firstDay - 1; i >= 0; i--) {
    html += `<div class="calendar-day other-month">${daysInPrev - i}</div>`;
  }

  // Días del mes actual
  for (let day = 1; day <= daysInMonth; day++) {
    const dateString = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    const isToday    = (day === currentDate.getDate() &&
                        month === currentDate.getMonth() &&
                        year  === currentDate.getFullYear());
    const dayEvents  = eventsByDate[dateString];
    const hasEvent   = Array.isArray(dayEvents) && dayEvents.length > 0;

    let classes = 'calendar-day';
    if (isToday)   classes += ' today';
    if (hasEvent)  classes += ' has-event';

    const title = hasEvent
      ? `${dayEvents.length} actividad(es)`
      : 'Sin actividades';

    html += `<div class="${classes}" data-date="${dateString}" title="${title}" style="${hasEvent ? 'cursor:pointer;' : ''}">${day}</div>`;
  }

  // Relleno días del mes siguiente
  const totalCells    = Math.ceil((firstDay + daysInMonth) / 7) * 7;
  const remaining     = totalCells - (firstDay + daysInMonth);
  for (let day = 1; day <= remaining; day++) {
    html += `<div class="calendar-day other-month">${day}</div>`;
  }

  calendarGrid.innerHTML = html;

  // Actualizar el título del mes
  const headerTitle = document.querySelector('.calendar-header h3');
  if (headerTitle) {
    headerTitle.textContent = `${MONTHS[month]} ${year}`;
  }

  // Delegar clicks en días con eventos
  calendarGrid.querySelectorAll('.calendar-day.has-event').forEach(cell => {
    cell.addEventListener('click', function () {
      const date = this.dataset.date;
      mostrarEventosDia(date, eventsByDate[date] || []);
    });
  });
}

// ── Navegación del calendario ────────────────────────────────
document.getElementById('prevMonth').addEventListener('click', () => {
  calMonth--;
  if (calMonth < 0) { calMonth = 11; calYear--; }
  generateCalendar(calMonth, calYear);
});

document.getElementById('nextMonth').addEventListener('click', () => {
  calMonth++;
  if (calMonth > 11) { calMonth = 0; calYear++; }
  generateCalendar(calMonth, calYear);
});

// ── Inicializar ──────────────────────────────────────────────
generateCalendar(calMonth, calYear);
