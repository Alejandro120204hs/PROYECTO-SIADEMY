// ========================================
// MAIN DOCENTE - JAVASCRIPT
// ========================================

$(document).ready(function() {

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

  function escapeHtml(value) {
    return String(value ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function applyDayModalFallbackTheme(modalEl) {
    if (!modalEl) return;

    const contentEl = modalEl.querySelector('.modal-content');
    const headerEl = modalEl.querySelector('.modal-header');
    const bodyEl = modalEl.querySelector('.modal-body');
    const titleEl = modalEl.querySelector('.modal-title');

    if (contentEl) {
      contentEl.style.background = '#11193a';
      contentEl.style.color = '#e6e9f4';
      contentEl.style.border = '1px solid rgba(255,255,255,.08)';
      contentEl.style.borderRadius = '18px';
    }

    if (headerEl) {
      headerEl.style.background = '#0e142e';
      headerEl.style.borderBottom = '1px solid rgba(255,255,255,.08)';
    }

    if (bodyEl) {
      bodyEl.style.background = '#11193a';
      bodyEl.style.color = '#e6e9f4';
    }

    if (titleEl) {
      titleEl.style.color = '#ffffff';
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
    if (backdrop) {
      backdrop.remove();
    }
  }

  // ========================================
  // SISTEMA DE TOGGLE PARA SIDEBARS
  // ========================================
  const leftSidebar = document.getElementById('leftSidebar');
  const rightSidebar = document.getElementById('rightSidebar');
  const appGrid = document.getElementById('appGrid');
  const toggleLeft = document.getElementById('toggleLeft');
  const toggleRight = document.getElementById('toggleRight');

  if (leftSidebar && rightSidebar && appGrid) {
    // Cargar estado desde localStorage
    let leftVisible = localStorage.getItem('leftSidebarVisible') !== 'false';
    let rightVisible = localStorage.getItem('rightSidebarVisible') !== 'false';

    function updateGridState() {
      appGrid.classList.remove('hide-left', 'hide-right', 'hide-both');

      if (!leftVisible && !rightVisible) {
        appGrid.classList.add('hide-both');
      } else if (!leftVisible) {
        appGrid.classList.add('hide-left');
      } else if (!rightVisible) {
        appGrid.classList.add('hide-right');
      }
    }

    function toggleLeftSidebar() {
      leftVisible = !leftVisible;
      leftSidebar.classList.toggle('hidden', !leftVisible);
      localStorage.setItem('leftSidebarVisible', leftVisible);
      updateGridState();
    }

    function toggleRightSidebar() {
      rightVisible = !rightVisible;
      rightSidebar.classList.toggle('hidden', !rightVisible);
      localStorage.setItem('rightSidebarVisible', rightVisible);
      updateGridState();
    }

    // Event listeners para toggles
    if (toggleLeft) {
      toggleLeft.addEventListener('click', toggleLeftSidebar);
    }

    if (toggleRight) {
      toggleRight.addEventListener('click', toggleRightSidebar);
    }

    // Aplicar estado inicial
    if (!leftVisible) leftSidebar.classList.add('hidden');
    if (!rightVisible) rightSidebar.classList.add('hidden');
    updateGridState();
  }

  // ========================================
  // DATATABLES - PANEL PRINCIPAL
  // ========================================
  setTimeout(function() {
    
    // DataTable para Cursos Asignados
    if ($('#coursesTable').length) {
      const hasColspan = $('#coursesTable tbody td[colspan]').length > 0;
      const hasRealData = $('#coursesTable tbody tr').length > 0 && !hasColspan;
      
      if (hasRealData) {
        if ($.fn.DataTable.isDataTable('#coursesTable')) {
          $('#coursesTable').DataTable().destroy();
        }
        
        $('#coursesTable').DataTable({
          language: {
            "decimal": "",
            "emptyTable": "No hay datos disponibles en la tabla",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "infoEmpty": "Mostrando 0 a 0 de 0 registros",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ registros",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "No se encontraron registros coincidentes",
            "paginate": {
              "first": "Primero",
              "last": "Último",
              "next": "Siguiente",
              "previous": "Anterior"
            },
            "aria": {
              "sortAscending": ": activar para ordenar la columna ascendente",
              "sortDescending": ": activar para ordenar la columna descendente"
            }
          },
          pageLength: 5,
          lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
          ordering: true,
          order: [[0, 'asc']],
          pagingType: 'simple_numbers',
          autoWidth: false,
          columnDefs: [
            { orderable: true, targets: [0, 1, 2, 3] },
            { orderable: false, targets: [4] },
            { searchable: false, targets: [4] },
            { className: 'text-center', targets: [4] }
          ],
          dom: "<'row align-items-center'<'col-sm-6'l><'col-sm-6 text-sm-end'f>>" +
               "<'row'<'col-12'tr>>" +
               "<'row align-items-center mt-2'<'col-sm-6'i><'col-sm-6 text-sm-end'p>>"
        });
      }
    }

    // DataTable para Estudiantes con bajo rendimiento
    if ($('#studentsTable').length) {
      const hasColspan = $('#studentsTable tbody td[colspan]').length > 0;
      const hasRealData = $('#studentsTable tbody tr').length > 0 && !hasColspan;
      
      if (hasRealData) {
        if ($.fn.DataTable.isDataTable('#studentsTable')) {
          $('#studentsTable').DataTable().destroy();
        }
        
        $('#studentsTable').DataTable({
          language: {
            "decimal": "",
            "emptyTable": "No hay datos disponibles en la tabla",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "infoEmpty": "Mostrando 0 a 0 de 0 registros",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ registros",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "No se encontraron registros coincidentes",
            "paginate": {
              "first": "Primero",
              "last": "Último",
              "next": "Siguiente",
              "previous": "Anterior"
            },
            "aria": {
              "sortAscending": ": activar para ordenar la columna ascendente",
              "sortDescending": ": activar para ordenar la columna descendente"
            }
          },
          pageLength: 5,
          lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
          ordering: true,
          order: [[0, 'asc']],
          pagingType: 'simple_numbers',
          autoWidth: false,
          columnDefs: [
            { orderable: true, targets: [0, 1, 2, 3] },
            { orderable: false, targets: [4, 5] },
            { searchable: false, targets: [4, 5] },
            { className: 'text-center', targets: [4, 5] }
          ],
          dom: "<'row align-items-center'<'col-sm-6'l><'col-sm-6 text-sm-end'f>>" +
               "<'row'<'col-12'tr>>" +
               "<'row align-items-center mt-2'<'col-sm-6'i><'col-sm-6 text-sm-end'p>>"
        });
      }
    }
  }, 300);

  // ========================================
  // CALENDAR - PANEL PRINCIPAL (Pequeño)
  // ========================================
  if (document.getElementById('calendarGrid')) {
    let currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();
    const dayEventsModalEl = document.getElementById('dayEventsModal');
    const dayEventsModalTitle = document.getElementById('dayEventsModalLabel');
    const dayEventsModalBody = document.getElementById('dayEventsModalBody');

    if (dayEventsModalEl) {
      dayEventsModalEl.querySelectorAll('[data-bs-dismiss="modal"]').forEach((btn) => {
        btn.addEventListener('click', () => hideModalSafe(dayEventsModalEl));
      });

      dayEventsModalEl.addEventListener('click', (event) => {
        if (event.target === dayEventsModalEl) {
          hideModalSafe(dayEventsModalEl);
        }
      });
    }

    const months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
      'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];

    const daysOfWeek = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];

    let rawCalendarEvents = Array.isArray(window.docenteCalendarEvents) ? window.docenteCalendarEvents : [];
    if ((!rawCalendarEvents || rawCalendarEvents.length === 0) && appGrid && appGrid.dataset.calendarEvents) {
      try {
        const parsedCalendarEvents = JSON.parse(appGrid.dataset.calendarEvents);
        rawCalendarEvents = Array.isArray(parsedCalendarEvents) ? parsedCalendarEvents : [];
      } catch (error) {
        rawCalendarEvents = [];
      }
    }

    const categoryByType = {
      examen: 'Examen',
      tarea: 'Tarea',
      proyecto: 'Proyecto',
      quiz: 'Quiz',
      taller: 'Taller',
      exposicion: 'Exposición',
      exposición: 'Exposición',
      laboratorio: 'Laboratorio',
      reunion: 'Reunión',
      reunión: 'Reunión',
      actividad: 'Actividad',
      evento: 'Evento'
    };

    const iconByCategory = {
      Examen: 'ri-file-edit-line',
      Tarea: 'ri-file-list-3-line',
      Proyecto: 'ri-folder-open-line',
      Quiz: 'ri-questionnaire-line',
      Taller: 'ri-tools-line',
      Exposición: 'ri-presentation-line',
      Laboratorio: 'ri-flask-line',
      Reunión: 'ri-user-voice-line',
      Actividad: 'ri-calendar-event-line',
      Evento: 'ri-calendar-check-line'
    };

    function normalizarTipo(tipo) {
      const base = String(tipo || '').trim();
      const key = base.toLowerCase();
      return categoryByType[key] || (base !== '' ? base : 'Evento');
    }

    const eventsByDate = rawCalendarEvents.reduce((acc, item) => {
      const dateKey = String(item.fecha_evento || '').slice(0, 10);
      if (!dateKey) return acc;

      if (!acc[dateKey]) acc[dateKey] = [];
      const tipo = normalizarTipo(item.tipo_evento);
      acc[dateKey].push({
        title: item.nombre_evento || 'Evento académico',
        description: item.descripcion || 'Sin descripción',
        type: tipo,
        time: item.hora_inicio || '',
        source: item.fuente || 'evento',
        icon: iconByCategory[tipo] || 'ri-calendar-event-line'
      });
      return acc;
    }, {});

    function renderDayEventsModal(dateString, events) {
      if (!dayEventsModalEl || !dayEventsModalTitle || !dayEventsModalBody) return;

      applyDayModalFallbackTheme(dayEventsModalEl);

      const safeEvents = Array.isArray(events) ? events : [];

      const [year, month, day] = dateString.split('-');
      dayEventsModalTitle.textContent = `Eventos del ${day}/${month}/${year}`;

      if (safeEvents.length === 0) {
        dayEventsModalBody.innerHTML =
          '<div style="border:1px dashed rgba(255,255,255,.25); border-radius:12px; padding:22px; text-align:center; color:#c5d1ee; background:#171f45;">' +
          '<i class="ri-calendar-line" style="font-size:24px;"></i><br>No hay eventos para este día.' +
          '</div>';
        showModalSafe(dayEventsModalEl);
        return;
      }

      const html = safeEvents.map((event) => {
        const timeBlock = event.time
          ? `<span><i class="ri-time-line"></i> ${escapeHtml(String(event.time).slice(0, 5))}</span>`
          : '<span><i class="ri-time-line"></i> Sin hora</span>';

        const sourceLabel = event.source === 'actividad' ? 'Tarea/Actividad' : 'Evento institucional';
        const eventType = escapeHtml(event.type || 'Evento');
        const eventTitle = escapeHtml(event.title || 'Evento académico');
        const eventDescription = escapeHtml(event.description || 'Sin descripción');
        const eventIcon = escapeHtml(event.icon || 'ri-calendar-event-line');

        return `
          <article class="calendar-day-event-item" style="background:#171f45; border:1px solid rgba(255,255,255,.08); border-radius:12px; padding:14px; color:#e6e9f4;">
            <div class="event-top" style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
              <div class="event-icon" style="width:34px; height:34px; border-radius:10px; display:grid; place-items:center; font-size:18px; background:#232e60; color:#a4b1ff;"><i class="${eventIcon}"></i></div>
              <span class="event-type" style="display:inline-flex; align-items:center; padding:4px 10px; border-radius:999px; font-size:12px; font-weight:600; color:#dbe2ff; background:rgba(79,70,229,.25); border:1px solid rgba(164,177,255,.25);">${eventType}</span>
            </div>
            <h6 style="margin:0 0 6px; font-size:16px; color:#fff;">${eventTitle}</h6>
            <p style="margin:0; color:#b8c2df; font-size:14px; line-height:1.4;">${eventDescription}</p>
            <div class="calendar-day-event-meta" style="margin-top:8px; display:flex; gap:14px; flex-wrap:wrap; font-size:12px; color:#9daccc;">
              ${timeBlock}
              <span><i class="ri-information-line"></i> ${sourceLabel}</span>
            </div>
          </article>
        `;
      }).join('');

      dayEventsModalBody.innerHTML = `<div style="margin-bottom:10px; color:#c5d1ee; font-size:13px;">Total de eventos: ${safeEvents.length}</div><div class="calendar-day-events-list" style="display:grid; gap:12px;">${html}</div>`;
      showModalSafe(dayEventsModalEl);
    }

    function generateCalendar(month, year) {
      const calendarGrid = document.getElementById('calendarGrid');
      if (!calendarGrid) return;

      const firstDay = new Date(year, month, 1).getDay();
      const daysInMonth = new Date(year, month + 1, 0).getDate();
      const daysInPrevMonth = new Date(year, month, 0).getDate();

      let calendarHTML = '';

      daysOfWeek.forEach(day => {
        calendarHTML += `<div class="calendar-day-header">${day}</div>`;
      });

      for (let i = firstDay - 1; i >= 0; i--) {
        const day = daysInPrevMonth - i;
        calendarHTML += `<div class="calendar-day other-month">${day}</div>`;
      }

      for (let day = 1; day <= daysInMonth; day++) {
        const dateString = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const isToday = day === currentDate.getDate() && month === currentDate.getMonth() && year === currentDate.getFullYear();
        const hasEvent = Array.isArray(eventsByDate[dateString]) && eventsByDate[dateString].length > 0;

        let classes = 'calendar-day';
        if (isToday) classes += ' today';
        if (hasEvent) classes += ' has-event';

        const title = hasEvent ? `${eventsByDate[dateString].length} evento(s)` : 'Sin eventos';
        calendarHTML += `<div class="${classes}" data-date="${dateString}" title="${title}">${day}</div>`;
      }

      const totalCells = Math.ceil((firstDay + daysInMonth) / 7) * 7;
      const remainingCells = totalCells - (firstDay + daysInMonth);
      for (let day = 1; day <= remainingCells; day++) {
        calendarHTML += `<div class="calendar-day other-month">${day}</div>`;
      }

      calendarGrid.innerHTML = calendarHTML;
      const headerElement = document.querySelector('.calendar-header h3');
      if (headerElement) {
        headerElement.textContent = `${months[month]} ${year}`;
      }

      calendarGrid.querySelectorAll('.calendar-day[data-date]').forEach((dayEl) => {
        dayEl.addEventListener('click', () => {
          const dateString = dayEl.getAttribute('data-date');
          const dayEvents = eventsByDate[dateString] || [];
          renderDayEventsModal(dateString, dayEvents);
        });
      });
    }

    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');

    if (prevMonthBtn) {
      prevMonthBtn.addEventListener('click', () => {
        currentMonth--;
        if (currentMonth < 0) {
          currentMonth = 11;
          currentYear--;
        }
        generateCalendar(currentMonth, currentYear);
      });
    }

    if (nextMonthBtn) {
      nextMonthBtn.addEventListener('click', () => {
        currentMonth++;
        if (currentMonth > 11) {
          currentMonth = 0;
          currentYear++;
        }
        generateCalendar(currentMonth, currentYear);
      });
    }

    generateCalendar(currentMonth, currentYear);
  }

});

// ========================================
// EVENTOS PAGE - CALENDAR & FUNCTIONALITY
// ========================================
if (document.getElementById('calendarLargeGrid')) {
  $(document).ready(function() {
    'use strict';

    let currentDateEvents = new Date();
    let currentMonthEvents = currentDateEvents.getMonth();
    let currentYearEvents = currentDateEvents.getFullYear();

    const monthsEvents = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
      'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];

    const daysOfWeekEvents = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];

    let rawEvents = Array.isArray(window.docenteEventosData) ? window.docenteEventosData : [];
    const eventosAppGrid = document.getElementById('appGrid');
    if ((!rawEvents || rawEvents.length === 0) && eventosAppGrid && eventosAppGrid.dataset.eventos) {
      try {
        const parsedEventos = JSON.parse(eventosAppGrid.dataset.eventos);
        rawEvents = Array.isArray(parsedEventos) ? parsedEventos : [];
      } catch (error) {
        rawEvents = [];
      }
    }
    const dayEventsModalEl = document.getElementById('dayEventsModal');
    const dayEventsModalTitle = document.getElementById('dayEventsModalLabel');
    const dayEventsModalBody = document.getElementById('dayEventsModalBody');

    if (dayEventsModalEl) {
      dayEventsModalEl.querySelectorAll('[data-bs-dismiss="modal"]').forEach((btn) => {
        btn.addEventListener('click', () => hideModalSafe(dayEventsModalEl));
      });

      dayEventsModalEl.addEventListener('click', (event) => {
        if (event.target === dayEventsModalEl) {
          hideModalSafe(dayEventsModalEl);
        }
      });
    }

    const eventsData = rawEvents.reduce((acc, item) => {
      const dateKey = String(item.fecha_evento || '').slice(0, 10);
      if (!dateKey) return acc;

      if (!acc[dateKey]) {
        acc[dateKey] = [];
      }

      const category = item.category || 'activities';
      const iconByCategory = {
        meetings: 'ri-user-voice-line',
        exams: 'ri-file-edit-line',
        activities: 'ri-calendar-event-line'
      };

      acc[dateKey].push({
        title: item.nombre_evento || 'Evento académico',
        description: item.descripcion || 'Sin descripción',
        category,
        icon: item.icon || iconByCategory[category] || 'ri-calendar-event-line',
        time: item.hora_inicio || '',
        source: item.fuente || 'evento'
      });

      return acc;
    }, {});

    function renderDayEventsModal(dateString, events) {
      if (!dayEventsModalEl || !dayEventsModalTitle || !dayEventsModalBody) return;

      applyDayModalFallbackTheme(dayEventsModalEl);

      const safeEvents = Array.isArray(events) ? events : [];

      const [year, month, day] = dateString.split('-');
      dayEventsModalTitle.textContent = `Eventos del ${day}/${month}/${year}`;

      if (safeEvents.length === 0) {
        dayEventsModalBody.innerHTML =
          '<div style="border:1px dashed rgba(255,255,255,.25); border-radius:12px; padding:22px; text-align:center; color:#c5d1ee; background:#171f45;">' +
          '<i class="ri-calendar-line" style="font-size:24px;"></i><br>No hay eventos para este día.' +
          '</div>';
        showModalSafe(dayEventsModalEl);
        return;
      }

      const html = safeEvents.map((event) => {
        const hour = event.time ? `<span><i class="ri-time-line"></i> ${escapeHtml(String(event.time).slice(0, 5))}</span>` : '<span><i class="ri-time-line"></i> Sin hora</span>';
        const sourceLabel = event.source === 'actividad' ? 'Actividad docente' : 'Evento institucional';
        const eventCategory = escapeHtml(event.category || 'activities');
        const eventTitle = escapeHtml(event.title || 'Evento académico');
        const eventDescription = escapeHtml(event.description || 'Sin descripción');
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
              ${hour}
              <span><i class="ri-information-line"></i> ${sourceLabel}</span>
            </div>
          </article>
        `;
      }).join('');

      dayEventsModalBody.innerHTML = `<div style="margin-bottom:10px; color:#c5d1ee; font-size:13px;">Total de eventos: ${safeEvents.length}</div><div class="calendar-day-events-list" style="display:grid; gap:12px;">${html}</div>`;
      showModalSafe(dayEventsModalEl);
    }

    function generateEventsCalendar(month, year) {
      const calendarGrid = document.getElementById('calendarLargeGrid');
      if (!calendarGrid) return;

      const firstDay = new Date(year, month, 1).getDay();
      const daysInMonth = new Date(year, month + 1, 0).getDate();
      const daysInPrevMonth = new Date(year, month, 0).getDate();

      let calendarHTML = '';

      daysOfWeekEvents.forEach(day => {
        calendarHTML += `<div class="calendar-large-header">${day}</div>`;
      });

      for (let i = firstDay - 1; i >= 0; i--) {
        const day = daysInPrevMonth - i;
        calendarHTML += `<div class="calendar-large-day other-month">
          <div class="calendar-day-number">${day}</div>
        </div>`;
      }

      for (let day = 1; day <= daysInMonth; day++) {
        const dateString = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const isToday = day === currentDateEvents.getDate() && 
                        month === currentDateEvents.getMonth() && 
                        year === currentDateEvents.getFullYear();
        const dayEvents = eventsData[dateString] || [];

        let classes = 'calendar-large-day';
        if (isToday) classes += ' today';
        if (dayEvents.length > 0) classes += ' has-events';

        let eventsHTML = '';
        if (dayEvents.length > 0) {
          eventsHTML = '<div class="calendar-day-events">';
          dayEvents.slice(0, 2).forEach(event => {
            eventsHTML += `<div class="calendar-mini-event ${event.category}">
              <i class="${event.icon}"></i>
              <span>${event.title}</span>
            </div>`;
          });
          if (dayEvents.length > 2) {
            eventsHTML += `<div class="calendar-mini-event more-events">
              +${dayEvents.length - 2} más
            </div>`;
          }
          eventsHTML += '</div>';
        }

        calendarHTML += `<div class="${classes}" data-date="${dateString}">
          <div class="calendar-day-number">${day}</div>
          ${eventsHTML}
        </div>`;
      }

      const totalCells = Math.ceil((firstDay + daysInMonth) / 7) * 7;
      const remainingCells = totalCells - (firstDay + daysInMonth);
      for (let day = 1; day <= remainingCells; day++) {
        calendarHTML += `<div class="calendar-large-day other-month">
          <div class="calendar-day-number">${day}</div>
        </div>`;
      }

      calendarGrid.innerHTML = calendarHTML;
      
      const monthYearElement = document.getElementById('calendarMonthYear');
      if (monthYearElement) {
        monthYearElement.textContent = `${monthsEvents[month]} ${year}`;
      }

      calendarGrid.querySelectorAll('.calendar-large-day[data-date]').forEach((dayEl) => {
        dayEl.addEventListener('click', () => {
          const selectedDate = dayEl.getAttribute('data-date');
          const dayEvents = eventsData[selectedDate] || [];
          renderDayEventsModal(selectedDate, dayEvents);
        });
      });
    }

    const prevMonthBtn = document.getElementById('prevMonthEvents');
    const nextMonthBtn = document.getElementById('nextMonthEvents');
    const todayBtn = document.getElementById('todayBtn');

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
        const today = new Date();
        currentMonthEvents = today.getMonth();
        currentYearEvents = today.getFullYear();
        generateEventsCalendar(currentMonthEvents, currentYearEvents);
      });
    }

    generateEventsCalendar(currentMonthEvents, currentYearEvents);

    // Filter Functionality
    const filterTabs = document.querySelectorAll('.filter-tab-event');
    const eventCards = document.querySelectorAll('.event-card');
    let activeFilter = 'all';

    function applyEventFilters() {
      const searchTerm = (document.getElementById('searchEvents')?.value || '').toLowerCase().trim();

      eventCards.forEach((card) => {
        const category = (card.getAttribute('data-category') || '').toLowerCase();
        const isUpcoming = card.getAttribute('data-upcoming') === '1';
        const title = card.querySelector('h4')?.textContent.toLowerCase() || '';
        const description = card.querySelector('p')?.textContent.toLowerCase() || '';

        let passesFilter = true;
        if (activeFilter === 'upcoming') {
          passesFilter = isUpcoming;
        } else if (activeFilter !== 'all') {
          passesFilter = category === activeFilter;
        }

        const passesSearch = searchTerm === '' || title.includes(searchTerm) || description.includes(searchTerm);
        card.style.display = (passesFilter && passesSearch) ? 'block' : 'none';
      });
    }

    filterTabs.forEach(tab => {
      tab.addEventListener('click', () => {
        filterTabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        activeFilter = tab.getAttribute('data-filter') || 'all';
        applyEventFilters();
      });
    });

    // Search Functionality
    const searchInput = document.getElementById('searchEvents');
    if (searchInput) {
      searchInput.addEventListener('input', (e) => {
        applyEventFilters();
      });
    }

    // View Toggle
    const viewButtons = document.querySelectorAll('.btn-view');
    const eventsContainer = document.getElementById('eventsContainer');

    viewButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        viewButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const view = btn.getAttribute('data-view');
        if (eventsContainer) {
          eventsContainer.classList.toggle('list-view', view === 'list');
        }
      });
    });

    // Sort events by date
    function sortEventsByDate() {
      const container = document.getElementById('eventsContainer');
      if (!container) return;

      const cards = Array.from(container.querySelectorAll('.event-card'));
      cards.sort((a, b) => {
        const dateA = new Date(a.getAttribute('data-date'));
        const dateB = new Date(b.getAttribute('data-date'));
        return dateA - dateB;
      });

      cards.forEach(card => container.appendChild(card));
    }

    sortEventsByDate();
  applyEventFilters();

    // Scroll animations
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = '0';
          entry.target.style.transform = 'translateY(20px)';
          
          setTimeout(() => {
            entry.target.style.transition = 'all 0.5s ease';
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
          }, 100);
          
          observer.unobserve(entry.target);
        }
      });
    }, observerOptions);

    eventCards.forEach(card => observer.observe(card));
  });
}

// Perfil: permite que el avatar del panel derecho abra el perfil si no viene como enlace.
document.addEventListener('DOMContentLoaded', function() {
  const profileAvatar = document.querySelector('.rightbar .user .avatar');
  if (!profileAvatar || profileAvatar.tagName === 'A') {
    return;
  }

  profileAvatar.style.cursor = 'pointer';
  profileAvatar.addEventListener('click', function() {
    window.location.href = (typeof BASE_URL !== 'undefined' ? BASE_URL : '') + '/dashboard-perfil';
  });
});