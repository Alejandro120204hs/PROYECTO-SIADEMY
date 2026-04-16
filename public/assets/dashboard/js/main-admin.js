// ========================================
// SISTEMA DE TOGGLE PARA SIDEBAR IZQUIERDO
// ========================================
const leftSidebar = document.getElementById('leftSidebar');
const appGrid = document.getElementById('appGrid');
const toggleLeft = document.getElementById('toggleLeft');

let leftVisible = localStorage.getItem('leftSidebarVisible') !== 'false';

// ── Overlay y drawer móvil ────────────────────────────────
const overlay = document.querySelector('.sidebar-overlay') || document.createElement('div');
if (!overlay.parentElement) {
  overlay.className = 'sidebar-overlay';
  document.body.appendChild(overlay);
}

function isMobile() { return window.innerWidth <= 768; }

function openMobileDrawer() {
    if (!leftSidebar) return;
    leftSidebar.classList.add('mobile-open');
    leftSidebar.classList.remove('hidden');
    overlay.classList.add('active');
}

function closeMobileDrawer() {
    if (!leftSidebar) return;
    leftSidebar.classList.remove('mobile-open');
    leftSidebar.classList.add('hidden');
    overlay.classList.remove('active');
}

overlay.onclick = closeMobileDrawer;

window.addEventListener('resize', () => {
    if (!isMobile()) {
        overlay.classList.remove('active');
        if (leftSidebar) leftSidebar.classList.remove('mobile-open');
    }
});

function updateGridState() {
    if (!appGrid) return;
    if (isMobile()) return;
    appGrid.classList.toggle('hide-left', !leftVisible);
}

function toggleLeftSidebar() {
    if (isMobile()) {
        const isOpen = leftSidebar && leftSidebar.classList.contains('mobile-open');
        isOpen ? closeMobileDrawer() : openMobileDrawer();
        return;
    }
    leftVisible = !leftVisible;
    if (leftSidebar) leftSidebar.classList.toggle('hidden', !leftVisible);
    localStorage.setItem('leftSidebarVisible', leftVisible);
    updateGridState();
}

if (toggleLeft) {
  toggleLeft.onclick = (event) => {
    event.preventDefault();
    toggleLeftSidebar();
  };
}
if (!isMobile() && !leftVisible && leftSidebar) leftSidebar.classList.add('hidden');
updateGridState();


// ========================================
// GRÁFICO (solo si existe)
// ========================================
let adminDashboardData = {};

const appGridDataNode = document.getElementById('appGrid');
const dashboardDataRaw = appGridDataNode ? appGridDataNode.getAttribute('data-dashboard') : '';

if (dashboardDataRaw) {
  try {
    adminDashboardData = JSON.parse(dashboardDataRaw);
  } catch (error) {
    adminDashboardData = {};
  }
} else if (typeof window !== 'undefined' && window.adminDashboardData) {
  // Backward compatibility for pages that still inject this payload globally.
  adminDashboardData = window.adminDashboardData;
}

const ctx = document.getElementById('lineChart');
if (ctx) {
  const defaultMonths = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
  const chartData = adminDashboardData.chart || {};
  const totalsData = adminDashboardData.totals || {};
  const labels = Array.isArray(chartData.labels) && chartData.labels.length === 12 ? chartData.labels : defaultMonths;
  const currentSeries = Array.isArray(chartData.currentSeries) && chartData.currentSeries.length === 12
    ? chartData.currentSeries
    : new Array(12).fill(0);
  const previousSeries = Array.isArray(chartData.previousSeries) && chartData.previousSeries.length === 12
    ? chartData.previousSeries
    : new Array(12).fill(0);

  const weekTotalEl = document.getElementById('weekTotal');
  const lastWeekTotalEl = document.getElementById('lastWeekTotal');
  const numberFormatter = new Intl.NumberFormat('es-CO');

  if (weekTotalEl && Number.isFinite(Number(totalsData.currentWeek))) {
    weekTotalEl.textContent = numberFormatter.format(Number(totalsData.currentWeek));
  }

  if (lastWeekTotalEl && Number.isFinite(Number(totalsData.previousWeek))) {
    lastWeekTotalEl.textContent = numberFormatter.format(Number(totalsData.previousWeek));
  }

    const gradient1 = ctx.getContext('2d').createLinearGradient(0, 0, 0, 320);
    gradient1.addColorStop(0, 'rgba(255,107,107,.35)');
    gradient1.addColorStop(1, 'rgba(255,107,107,0)');

    const gradient2 = ctx.getContext('2d').createLinearGradient(0, 0, 0, 320);
    gradient2.addColorStop(0, 'rgba(255,176,32,.35)');
    gradient2.addColorStop(1, 'rgba(255,176,32,0)');

    const data = {
      labels,
        datasets: [{
        label: `Eventos ${chartData.currentYear || 'Año actual'}`,
        data: currentSeries,
            borderColor: '#ff6b6b',
            backgroundColor: gradient1,
            borderWidth: 3,
            pointRadius: 5,
            pointBackgroundColor: '#ff6b6b',
            tension: .45,
            fill: true
        }, {
            label: `Eventos ${chartData.previousYear || 'Año anterior'}`,
            data: previousSeries,
            borderColor: '#ffb020',
            backgroundColor: gradient2,
            borderWidth: 3,
            pointRadius: 0,
            tension: .45,
            fill: true
        }]
    };

    new Chart(ctx, {
        type: 'line',
        data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    enabled: true,
                    backgroundColor: '#0e142e',
                    borderColor: 'rgba(255,255,255,.1)',
                    borderWidth: 1,
                    titleColor: '#fff',
                    bodyColor: '#e5e7eb'
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(255,255,255,.06)' },
                    ticks: { color: '#cbd5e1' }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255,255,255,.06)' },
                    ticks: { color: '#cbd5e1' }
                }
            }
        }
    });
}

    // ========================================
    // CALENDARIO ACADÉMICO - DASHBOARD ADMIN
    // ========================================
    const dashboardCalendarGrid = document.getElementById('calendarGrid');
    if (dashboardCalendarGrid) {
      const calendarEvents = Array.isArray(adminDashboardData?.calendar?.events)
        ? adminDashboardData.calendar.events
        : [];
      const monthLabelElement = document.getElementById('calendarMonthLabel');
      const dayEventsModalEl = document.getElementById('adminCalendarDayModal');
      const dayEventsModalTitle = document.getElementById('adminCalendarDayModalLabel');
      const dayEventsModalBody = document.getElementById('adminCalendarDayModalBody');
      const prevMonthButton = document.getElementById('prevMonth');
      const nextMonthButton = document.getElementById('nextMonth');
      const dayHeaders = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
      const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

      let currentDate = new Date();
      let currentMonth = currentDate.getMonth();
      let currentYear = currentDate.getFullYear();

      function escapeHtml(value) {
        return String(value ?? '')
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#39;');
      }

      const eventsByDate = calendarEvents.reduce((acc, eventItem) => {
        const dateKey = String(eventItem?.date || '').slice(0, 10);
        if (!dateKey) return acc;

        if (!acc[dateKey]) acc[dateKey] = [];
        acc[dateKey].push({
          title: String(eventItem?.title || 'Evento académico'),
          type: String(eventItem?.type || 'evento'),
          timeStart: String(eventItem?.timeStart || ''),
          timeEnd: String(eventItem?.timeEnd || ''),
          location: String(eventItem?.location || '')
        });
        return acc;
      }, {});

      function toDisplayType(typeValue) {
        const normalized = String(typeValue || '').trim();
        if (!normalized) return 'Evento';
        return normalized.charAt(0).toUpperCase() + normalized.slice(1).toLowerCase();
      }

      function getTypeIcon(typeValue) {
        const type = String(typeValue || '').toLowerCase();
        const iconMap = {
          reuniones: 'ri-user-voice-line',
          meeting: 'ri-user-voice-line',
          meetings: 'ri-user-voice-line',
          examen: 'ri-file-edit-line',
          exam: 'ri-file-edit-line',
          exams: 'ri-file-edit-line',
          actividad: 'ri-basketball-line',
          activities: 'ri-basketball-line',
          taller: 'ri-briefcase-line',
          conferencia: 'ri-presentation-line'
        };

        return iconMap[type] || 'ri-calendar-event-line';
      }

      function renderDayEvents(dateString) {
        if (!dayEventsModalEl || !dayEventsModalTitle || !dayEventsModalBody) return;

        const events = eventsByDate[dateString] || [];
        const [year, month, day] = dateString.split('-');
        dayEventsModalTitle.textContent = `Eventos del ${day}/${month}/${year}`;

        if (events.length === 0) {
          dayEventsModalBody.innerHTML =
            '<div class="calendar-empty-day">'
            + '<i class="ri-calendar-line" style="font-size:24px;"></i><br>No hay eventos para este día.</div>';

          if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(dayEventsModalEl).show();
          }
          return;
        }

        const items = events.map((eventItem) => {
          const timeLabel = eventItem.timeStart
            ? `${escapeHtml(eventItem.timeStart)}${eventItem.timeEnd ? ` - ${escapeHtml(eventItem.timeEnd)}` : ''}`
            : 'Sin hora definida';
          const typeLabel = toDisplayType(eventItem.type);
          const typeIcon = getTypeIcon(eventItem.type);

          return `
            <article class="calendar-day-event-item">
              <div class="event-top">
                <div class="event-icon"><i class="${escapeHtml(typeIcon)}"></i></div>
                <span class="event-type">${escapeHtml(typeLabel)}</span>
              </div>
              <h6>${escapeHtml(eventItem.title)}</h6>
              <div class="calendar-day-event-meta">
                <span><i class="ri-time-line"></i> ${timeLabel}</span>
                <span><i class="ri-map-pin-line"></i> ${escapeHtml(eventItem.location || 'Ubicación por confirmar')}</span>
              </div>
            </article>
          `;
        }).join('');

        dayEventsModalBody.innerHTML = `<div class="calendar-day-events-title">Total de eventos: ${events.length}</div><div class="calendar-day-events-list">${items}</div>`;

        if (window.bootstrap && window.bootstrap.Modal) {
          window.bootstrap.Modal.getOrCreateInstance(dayEventsModalEl).show();
        }
      }

      function renderCalendar(month, year) {
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const daysInPrevMonth = new Date(year, month, 0).getDate();
        let html = '';

        dayHeaders.forEach((day) => {
          html += `<div class="calendar-day-header">${day}</div>`;
        });

        for (let offset = firstDay - 1; offset >= 0; offset--) {
          html += `<div class="calendar-day other-month">${daysInPrevMonth - offset}</div>`;
        }

        for (let day = 1; day <= daysInMonth; day++) {
          const dateString = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
          const hasEvent = Array.isArray(eventsByDate[dateString]) && eventsByDate[dateString].length > 0;
          const isToday = day === currentDate.getDate()
            && month === currentDate.getMonth()
            && year === currentDate.getFullYear();

          let classes = 'calendar-day';
          if (isToday) classes += ' today';
          if (hasEvent) classes += ' has-event';

          html += `<div class="${classes}" data-date="${dateString}">${day}</div>`;
        }

        const totalCells = Math.ceil((firstDay + daysInMonth) / 7) * 7;
        const trailingCells = totalCells - (firstDay + daysInMonth);
        for (let day = 1; day <= trailingCells; day++) {
          html += `<div class="calendar-day other-month">${day}</div>`;
        }

        dashboardCalendarGrid.innerHTML = html;

        if (monthLabelElement) {
          monthLabelElement.textContent = `${monthNames[month]} ${year}`;
        }

        dashboardCalendarGrid.querySelectorAll('.calendar-day[data-date]').forEach((dayElement) => {
          dayElement.addEventListener('click', () => {
            renderDayEvents(dayElement.getAttribute('data-date') || '');
          });
        });
      }

      if (prevMonthButton) {
        prevMonthButton.addEventListener('click', () => {
          currentMonth--;
          if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
          }
          renderCalendar(currentMonth, currentYear);
        });
      }

      if (nextMonthButton) {
        nextMonthButton.addEventListener('click', () => {
          currentMonth++;
          if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
          }
          renderCalendar(currentMonth, currentYear);
        });
      }

      renderCalendar(currentMonth, currentYear);
    }


// ========================================
// DATATABLE (solo si existe)
// ========================================
$(document).ready(function() {
    if ($('#studentsTable').length) {
        $('#studentsTable').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
            pageLength: 5,
            lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
            ordering: true,
            order: [[0, 'asc']],
            pagingType: 'simple_numbers'
        });
    }
});


// ========================================
// TABS - DETALLE ESTUDIANTE (SIN RIGHT SIDEBAR)
// ========================================
document.addEventListener('DOMContentLoaded', function() {

    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.dataset.tab;

            tabButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            tabPanes.forEach(pane => pane.classList.remove('active'));
            const targetPane = document.getElementById(targetTab);
            if (targetPane) targetPane.classList.add('active');
        });
    });

    // QUICK ACTIONS
    document.querySelectorAll('.quick-action-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            console.log('Acción:', btn.textContent.trim());
        });
    });

    // PRINT
    const printBtn = document.querySelector('.btn-secondary-action');
    if (printBtn) {
        printBtn.addEventListener('click', () => window.print());
    }

    // EDIT
    const editBtn = document.querySelector('.btn-primary-action');
    if (editBtn) {
        editBtn.addEventListener('click', () => {
            console.log('Editar perfil');
        });
    }

});


/* ========================================
   JAVASCRIPT PARA DROPDOWN DE USUARIO
   Agregar al final de main-admin.js o en un archivo separado
   ======================================== */

// Funcionalidad del dropdown de usuario
document.addEventListener('DOMContentLoaded', function() {
  const userMenuBtn = document.getElementById('userMenuBtn');
  const userDropdown = document.getElementById('userDropdown');
  
  // Crear overlay
  const overlay = document.createElement('div');
  overlay.className = 'dropdown-overlay';
  document.body.appendChild(overlay);
  
  // Toggle del dropdown
  if (userMenuBtn && userDropdown) {
    userMenuBtn.dataset.dropdownInit = '1'; // marca para evitar doble registro
    userMenuBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      const isOpen = userDropdown.classList.contains('show');
      
      if (isOpen) {
        closeDropdown();
      } else {
        openDropdown();
      }
    });
    
    // Cerrar al hacer click en el overlay
    overlay.addEventListener('click', closeDropdown);
    
    // Cerrar con tecla Escape
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && userDropdown.classList.contains('show')) {
        closeDropdown();
      }
    });
    
    // Prevenir cierre al hacer click dentro del dropdown
    userDropdown.addEventListener('click', function(e) {
      e.stopPropagation();
    });
  }
  
  // Funciones auxiliares
  function openDropdown() {
    userDropdown.classList.add('show');
    overlay.classList.add('show');
    
    // Animación suave de los items
    const items = userDropdown.querySelectorAll('.dropdown-item');
    items.forEach((item, index) => {
      item.style.opacity = '0';
      item.style.transform = 'translateX(-10px)';
      setTimeout(() => {
        item.style.transition = 'all 0.2s ease';
        item.style.opacity = '1';
        item.style.transform = 'translateX(0)';
      }, 50 * index);
    });
  }
  
  function closeDropdown() {
    userDropdown.classList.remove('show');
    overlay.classList.remove('show');
  }
  
  // Funcionalidad del botón de cambiar tema
  const toggleThemeBtn = document.getElementById('toggleThemeBtn');
  if (toggleThemeBtn) {
    toggleThemeBtn.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Aquí puedes implementar el cambio de tema
      document.body.classList.toggle('light-mode');
      
      // Cambiar icono según el modo
      const icon = this.querySelector('i:first-child');
      if (document.body.classList.contains('light-mode')) {
        icon.className = 'ri-sun-line';
      } else {
        icon.className = 'ri-contrast-2-line';
      }
      
      // Guardar preferencia en localStorage
      const currentMode = document.body.classList.contains('light-mode') ? 'light' : 'dark';
      localStorage.setItem('theme-mode', currentMode);
      
      // Mostrar notificación (opcional)
      showNotification('Tema cambiado correctamente');
    });
  }
  
  // Cargar tema guardado al iniciar
  const savedTheme = localStorage.getItem('theme-mode');
  if (savedTheme === 'light') {
    document.body.classList.add('light-mode');
    const icon = toggleThemeBtn ? toggleThemeBtn.querySelector('i:first-child') : null;
    if (icon) icon.className = 'ri-sun-line';
  }
  
  // Función para mostrar notificaciones (opcional)
  function showNotification(message) {
    // Crear elemento de notificación
    const notification = document.createElement('div');
    notification.className = 'toast-notification';
    notification.innerHTML = `
      <i class="ri-check-line"></i>
      <span>${message}</span>
    `;
    notification.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      background: #10b981;
      color: white;
      padding: 14px 20px;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: 500;
      z-index: 10000;
      animation: slideInRight 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remover después de 3 segundos
    setTimeout(() => {
      notification.style.animation = 'slideOutRight 0.3s ease';
      setTimeout(() => notification.remove(), 300);
    }, 3000);
  }
});

// Animaciones para las notificaciones
const style = document.createElement('style');
style.textContent = `
  @keyframes slideInRight {
    from {
      transform: translateX(100%);
      opacity: 0;
    }
    to {
      transform: translateX(0);
      opacity: 1;
    }
  }
  
  @keyframes slideOutRight {
    from {
      transform: translateX(0);
      opacity: 1;
    }
    to {
      transform: translateX(100%);
      opacity: 0;
    }
  }
  
  .toast-notification i {
    font-size: 20px;
  }
`;
document.head.appendChild(style);

// ========================================
// EVENTOS ADMIN - CALENDARIO, FILTROS Y VISTA
// ========================================
document.addEventListener('DOMContentLoaded', function () {
  const eventsContainer = document.getElementById('eventsContainer');
  const calendarGrid = document.getElementById('calendarLargeGrid');
  const dayEventsModalEl = document.getElementById('dayEventsModal');
  const dayEventsModalTitle = document.getElementById('dayEventsModalLabel');
  const dayEventsModalBody = document.getElementById('dayEventsModalBody');
  const monthYearElement = document.getElementById('calendarMonthYear');
  const prevMonthBtn = document.getElementById('prevMonthEvents');
  const nextMonthBtn = document.getElementById('nextMonthEvents');
  const todayBtn = document.getElementById('todayBtn');
  const filterTabs = Array.from(document.querySelectorAll('.filter-tab-event'));
  const searchInput = document.getElementById('searchEvents');
  const viewButtons = Array.from(document.querySelectorAll('.btn-view'));
  const eventCards = Array.from(document.querySelectorAll('.event-card'));

  if (!eventsContainer || !calendarGrid) return;

  const monthsEvents = [
    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
  ];
  const daysOfWeekEvents = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
  const categoryLabels = {
    all: () => eventCards.length,
    upcoming: (cardData) => cardData.filter((item) => item.isUpcoming).length,
    meetings: (cardData) => cardData.filter((item) => item.category === 'meetings').length,
    exams: (cardData) => cardData.filter((item) => item.category === 'exams').length,
    activities: (cardData) => cardData.filter((item) => item.category === 'activities').length
  };

  const today = new Date();
  const todayStart = new Date(today.getFullYear(), today.getMonth(), today.getDate());
  let currentMonthEvents = today.getMonth();
  let currentYearEvents = today.getFullYear();
  const activeTab = filterTabs.find((tab) => tab.classList.contains('active'));
  let activeFilter = activeTab ? activeTab.dataset.filter : 'all';

  function escapeHtml(value) {
    return String(value ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function showModalSafe(modalEl) {
    if (!modalEl) return;

    if (window.bootstrap && window.bootstrap.Modal) {
      window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
      return;
    }

    modalEl.style.display = 'block';
    modalEl.classList.add('show');
    modalEl.removeAttribute('aria-hidden');
    modalEl.setAttribute('aria-modal', 'true');
    document.body.classList.add('modal-open');

    if (!document.querySelector('.modal-backdrop')) {
      const backdrop = document.createElement('div');
      backdrop.className = 'modal-backdrop fade show';
      backdrop.addEventListener('click', () => hideModalSafe(modalEl));
      document.body.appendChild(backdrop);
    }
  }

  function hideModalSafe(modalEl) {
    if (!modalEl) return;

    if (window.bootstrap && window.bootstrap.Modal) {
      const instance = window.bootstrap.Modal.getInstance(modalEl);
      if (instance) {
        instance.hide();
        return;
      }
    }

    modalEl.classList.remove('show');
    modalEl.style.display = 'none';
    modalEl.setAttribute('aria-hidden', 'true');
    modalEl.removeAttribute('aria-modal');
    document.body.classList.remove('modal-open');

    const backdrop = document.querySelector('.modal-backdrop');
    if (backdrop) backdrop.remove();
  }

  function renderDayEventsModal(dateString, events) {
    if (!dayEventsModalEl || !dayEventsModalTitle || !dayEventsModalBody) return;

    const safeEvents = Array.isArray(events) ? events : [];
    const [year, month, day] = String(dateString).split('-');
    dayEventsModalTitle.textContent = `Eventos del ${day}/${month}/${year}`;

    if (safeEvents.length === 0) {
      dayEventsModalBody.innerHTML =
        '<div style="border:1px dashed rgba(255,255,255,.25); border-radius:12px; padding:22px; text-align:center; color:#c5d1ee; background:#171f45;">'
        + '<i class="ri-calendar-line" style="font-size:24px;"></i><br>No hay eventos para este día.</div>';
      showModalSafe(dayEventsModalEl);
      return;
    }

    const html = safeEvents.map((event) => {
      const timeBlock = event.time
        ? `<span><i class="ri-time-line"></i> ${escapeHtml(String(event.time).slice(0, 5))}</span>`
        : '<span><i class="ri-time-line"></i> Sin hora</span>';

      const eventTitle = escapeHtml(event.title || 'Evento académico');
      const eventDescription = escapeHtml(event.description || 'Sin descripción');
      const eventCategory = escapeHtml(event.category || 'event');
      const eventIcon = escapeHtml(event.icon || 'ri-calendar-event-line');

      return `
        <article class="calendar-day-event-item" style="background:#171f45; border:1px solid rgba(255,255,255,.08); border-radius:12px; padding:14px; color:#e6e9f4;">
          <div class="event-top" style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
            <div class="event-icon" style="width:34px; height:34px; border-radius:10px; display:grid; place-items:center; font-size:18px; background:#232e60; color:#a4b1ff;"><i class="${eventIcon}"></i></div>
            <span class="event-type" style="display:inline-flex; align-items:center; padding:4px 10px; border-radius:999px; font-size:12px; font-weight:600; color:#dbe2ff; background:rgba(79,70,229,.25); border:1px solid rgba(164,177,255,.25);">${eventCategory}</span>
          </div>
          <h6 style="margin:0 0 6px; font-size:16px; color:#fff;">${eventTitle}</h6>
          <p style="margin:0; color:#b8c2df; font-size:14px; line-height:1.4;">${eventDescription}</p>
          <div class="calendar-day-event-meta" style="margin-top:8px; display:flex; gap:14px; flex-wrap:wrap; font-size:12px; color:#9daccc;">
            ${timeBlock}
          </div>
        </article>
      `;
    }).join('');

    dayEventsModalBody.innerHTML = `<div style="margin-bottom:10px; color:#c5d1ee; font-size:13px;">Total de eventos: ${safeEvents.length}</div><div class="calendar-day-events-list" style="display:grid; gap:12px;">${html}</div>`;
    showModalSafe(dayEventsModalEl);
  }

  function normalizeDate(dateValue) {
    if (!dateValue) return null;

    const [year, month, day] = dateValue.split('-').map(Number);
    if (!year || !month || !day) return null;

    return new Date(year, month - 1, day);
  }

  function getCategoryIcon(category) {
    const icons = {
      meetings: 'ri-user-voice-line',
      exams: 'ri-file-edit-line',
      activities: 'ri-basketball-line'
    };

    return icons[category] || 'ri-calendar-line';
  }

  const cardData = eventCards.map((card) => {
    const titleElement = card.querySelector('h4');
    const descriptionElement = card.querySelector('.event-card-body p');
    const timeElement = card.querySelector('.event-meta .meta-item:nth-child(2) span');
    const title = titleElement ? titleElement.textContent.trim() : 'Evento';
    const description = descriptionElement ? descriptionElement.textContent.trim() : '';
    const time = timeElement ? timeElement.textContent.trim() : '';
    const category = card.dataset.category || 'all';
    const dateValue = card.dataset.date || '';
    const dateObject = normalizeDate(dateValue);

    return {
      card,
      title,
      description,
      time,
      category,
      dateValue,
      dateObject,
      icon: getCategoryIcon(category),
      isUpcoming: Boolean(dateObject) && dateObject >= todayStart
    };
  });

  function updateBadgeCounts() {
    filterTabs.forEach((tab) => {
      const badge = tab.querySelector('.badge-count');
      const filter = tab.dataset.filter || 'all';
      const countResolver = categoryLabels[filter];

      if (!badge || !countResolver) return;
      badge.textContent = String(countResolver(cardData));
    });
  }

  function matchesFilter(item) {
    if (activeFilter === 'all') return true;
    if (activeFilter === 'upcoming') return item.isUpcoming;
    return item.category === activeFilter;
  }

  function matchesSearch(item, searchTerm) {
    if (!searchTerm) return true;

    const normalizedSearch = searchTerm.toLowerCase();
    return item.title.toLowerCase().includes(normalizedSearch)
      || item.description.toLowerCase().includes(normalizedSearch);
  }

  function applyEventFilters() {
    const searchTerm = searchInput ? searchInput.value.trim() : '';

    cardData.forEach((item) => {
      const isVisible = matchesFilter(item) && matchesSearch(item, searchTerm);
      item.card.style.display = isVisible ? '' : 'none';
    });
  }

  function buildEventsMap() {
    return cardData.reduce((accumulator, item) => {
      if (!item.dateValue) return accumulator;

      if (!accumulator[item.dateValue]) {
        accumulator[item.dateValue] = [];
      }

      accumulator[item.dateValue].push({
        title: item.title,
        description: item.description,
        time: item.time,
        category: item.category,
        icon: item.icon
      });

      return accumulator;
    }, {});
  }

  const eventsData = buildEventsMap();

  function generateEventsCalendar(month, year) {
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const daysInPrevMonth = new Date(year, month, 0).getDate();
    let calendarHTML = '';

    daysOfWeekEvents.forEach((day) => {
      calendarHTML += `<div class="calendar-large-header">${day}</div>`;
    });

    for (let dayOffset = firstDay - 1; dayOffset >= 0; dayOffset--) {
      const dayNumber = daysInPrevMonth - dayOffset;
      calendarHTML += `<div class="calendar-large-day other-month"><div class="calendar-day-number">${dayNumber}</div></div>`;
    }

    for (let day = 1; day <= daysInMonth; day++) {
      const dateString = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
      const dayEvents = eventsData[dateString] || [];
      const isToday = day === today.getDate()
        && month === today.getMonth()
        && year === today.getFullYear();

      let classes = 'calendar-large-day';
      if (isToday) classes += ' today';
      if (dayEvents.length > 0) classes += ' has-events';

      let eventsHTML = '';
      if (dayEvents.length > 0) {
        eventsHTML = '<div class="calendar-day-events">';
        dayEvents.slice(0, 2).forEach((eventItem) => {
          eventsHTML += `<div class="calendar-mini-event ${eventItem.category}"><i class="${eventItem.icon}"></i><span>${eventItem.title}</span></div>`;
        });

        if (dayEvents.length > 2) {
          eventsHTML += `<div class="calendar-mini-event more-events">+${dayEvents.length - 2} más</div>`;
        }

        eventsHTML += '</div>';
      }

      calendarHTML += `<div class="${classes}" data-date="${dateString}"><div class="calendar-day-number">${day}</div>${eventsHTML}</div>`;
    }

    const totalCells = Math.ceil((firstDay + daysInMonth) / 7) * 7;
    const remainingCells = totalCells - (firstDay + daysInMonth);

    for (let day = 1; day <= remainingCells; day++) {
      calendarHTML += `<div class="calendar-large-day other-month"><div class="calendar-day-number">${day}</div></div>`;
    }

    calendarGrid.innerHTML = calendarHTML;
    if (monthYearElement) {
      monthYearElement.textContent = `${monthsEvents[month]} ${year}`;
    }

    calendarGrid.querySelectorAll('.calendar-large-day[data-date]').forEach((dayElement) => {
      dayElement.addEventListener('click', () => {
        const selectedDate = dayElement.getAttribute('data-date') || '';
        renderDayEventsModal(selectedDate, eventsData[selectedDate] || []);
      });
    });
  }

  if (dayEventsModalEl) {
    dayEventsModalEl.querySelectorAll('[data-bs-dismiss="modal"]').forEach((button) => {
      button.addEventListener('click', () => hideModalSafe(dayEventsModalEl));
    });

    dayEventsModalEl.addEventListener('click', (event) => {
      if (event.target === dayEventsModalEl) {
        hideModalSafe(dayEventsModalEl);
      }
    });
  }

  if (prevMonthBtn) {
    prevMonthBtn.addEventListener('click', () => {
      currentMonthEvents--;
      if (currentMonthEvents < 0) {
        currentMonthEvents = 11;
        currentYearEvents--;
      }

      generateEventsCalendar(currentMonthEvents, currentYearEvents);
    });
  }

  if (nextMonthBtn) {
    nextMonthBtn.addEventListener('click', () => {
      currentMonthEvents++;
      if (currentMonthEvents > 11) {
        currentMonthEvents = 0;
        currentYearEvents++;
      }

      generateEventsCalendar(currentMonthEvents, currentYearEvents);
    });
  }

  if (todayBtn) {
    todayBtn.addEventListener('click', () => {
      currentMonthEvents = today.getMonth();
      currentYearEvents = today.getFullYear();
      generateEventsCalendar(currentMonthEvents, currentYearEvents);
    });
  }

  filterTabs.forEach((tab) => {
    tab.addEventListener('click', () => {
      filterTabs.forEach((item) => item.classList.remove('active'));
      tab.classList.add('active');
      activeFilter = tab.dataset.filter || 'all';
      applyEventFilters();
    });
  });

  if (searchInput) {
    searchInput.addEventListener('input', applyEventFilters);
  }

  viewButtons.forEach((button) => {
    button.addEventListener('click', () => {
      viewButtons.forEach((item) => item.classList.remove('active'));
      button.classList.add('active');
      eventsContainer.classList.toggle('list-view', button.dataset.view === 'list');
    });
  });

  updateBadgeCounts();
  applyEventFilters();
  generateEventsCalendar(currentMonthEvents, currentYearEvents);
});

// ========================================
// ASIGNATURAS - TOGGLE DE VISTA
// ========================================
document.addEventListener('DOMContentLoaded', function () {
  const subjectViewButtons = document.querySelectorAll('.view-btn');
  const subjectsGrid = document.querySelector('.subjects-grid');

  if (!subjectViewButtons.length || !subjectsGrid) return;

  subjectViewButtons.forEach((button) => {
    button.addEventListener('click', function () {
      subjectViewButtons.forEach((item) => item.classList.remove('active'));
      this.classList.add('active');

      const view = this.getAttribute('data-view');
      subjectsGrid.classList.toggle('list-view', view === 'list');
    });
  });
});