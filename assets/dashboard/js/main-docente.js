// ========================================
// MAIN DOCENTE - JAVASCRIPT
// ========================================

// Sistema de toggle para sidebars con localStorage
const leftSidebar = document.getElementById('leftSidebar');
const rightSidebar = document.getElementById('rightSidebar');
const appGrid = document.getElementById('appGrid');
const toggleLeft = document.getElementById('toggleLeft');
const toggleRight = document.getElementById('toggleRight');

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

// ========================================
// DATATABLES - PANEL PRINCIPAL
// ========================================
$(document).ready(function() {
  // DataTable para Cursos Asignados
  if ($('#coursesTable').length) {
    $('#coursesTable').DataTable({
      language: {
        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
      },
      pageLength: 5,
      lengthMenu: [
        [5, 10, 25, 50],
        [5, 10, 25, 50]
      ],
      ordering: true,
      order: [
        [0, 'asc']
      ],
      pagingType: 'simple_numbers',
      columnDefs: [{
          orderable: true,
          targets: [0, 1, 2, 3]
        },
        {
          orderable: false,
          targets: [4]
        },
        {
          searchable: false,
          targets: [4]
        },
        {
          className: 'text-center',
          targets: [4]
        }
      ],
      dom: "<'row align-items-center'<'col-sm-6'l><'col-sm-6 text-sm-end'f>>" +
        "<'row'<'col-12'tr>>" +
        "<'row align-items-center mt-2'<'col-sm-6'i><'col-sm-6 text-sm-end'p>>"
    });
  }

  // DataTable para Estudiantes con bajo rendimiento
  if ($('#studentsTable').length) {
    $('#studentsTable').DataTable({
      language: {
        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
      },
      pageLength: 5,
      lengthMenu: [
        [5, 10, 25, 50],
        [5, 10, 25, 50]
      ],
      ordering: true,
      order: [
        [0, 'asc']
      ],
      pagingType: 'simple_numbers',
      columnDefs: [{
          orderable: true,
          targets: [0, 1, 2, 3]
        },
        {
          orderable: false,
          targets: [4, 5]
        },
        {
          searchable: false,
          targets: [4, 5]
        },
        {
          className: 'text-center',
          targets: [4, 5]
        }
      ],
      dom: "<'row align-items-center'<'col-sm-6'l><'col-sm-6 text-sm-end'f>>" +
        "<'row'<'col-12'tr>>" +
        "<'row align-items-center mt-2'<'col-sm-6'i><'col-sm-6 text-sm-end'p>>"
    });
  }
});

// ========================================
// CALENDAR - PANEL PRINCIPAL (Pequeño)
// ========================================
if (document.getElementById('calendarGrid')) {
  let currentDate = new Date();
  let currentMonth = currentDate.getMonth();
  let currentYear = currentDate.getFullYear();

  const months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
  ];

  const daysOfWeek = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];

  const events = {
    '2024-10-28': 'Reunión de Padres',
    '2024-10-30': 'Examen de Matemáticas',
    '2024-11-02': 'Festival Cultural',
    '2024-11-05': 'Día del Deporte',
    '2024-11-10': 'Feria de Ciencias'
  };

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
      const hasEvent = events[dateString];

      let classes = 'calendar-day';
      if (isToday) classes += ' today';
      if (hasEvent) classes += ' has-event';

      calendarHTML += `<div class="${classes}" title="${hasEvent || ''}">${day}</div>`;
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

// ========================================
// EVENTOS PAGE - CALENDAR & FUNCTIONALITY
// ========================================
if (document.getElementById('calendarLargeGrid')) {
  (function() {
    'use strict';

    let currentDateEvents = new Date();
    let currentMonthEvents = currentDateEvents.getMonth();
    let currentYearEvents = currentDateEvents.getFullYear();

    const monthsEvents = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
      'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];

    const daysOfWeekEvents = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];

    const eventsData = {
      '2024-10-28': [
        { title: 'Reunión de Padres', category: 'meetings', icon: 'ri-user-voice-line' }
      ],
      '2024-10-30': [
        { title: 'Examen Matemáticas', category: 'exams', icon: 'ri-file-edit-line' }
      ],
      '2024-10-31': [
        { title: 'Clase Mat. Avanzadas', category: 'exams', icon: 'ri-book-line' },
        { title: 'Clase Física II', category: 'exams', icon: 'ri-flask-line' }
      ],
      '2024-11-02': [
        { title: 'Festival Cultural', category: 'activities', icon: 'ri-music-2-line' }
      ],
      '2024-11-05': [
        { title: 'Día del Deporte', category: 'activities', icon: 'ri-basketball-line' }
      ],
      '2024-11-08': [
        { title: 'Examen Física II', category: 'exams', icon: 'ri-file-edit-line' }
      ],
      '2024-11-10': [
        { title: 'Feria de Ciencias', category: 'activities', icon: 'ri-flask-line' }
      ],
      '2024-11-12': [
        { title: 'Reunión Docentes', category: 'meetings', icon: 'ri-user-voice-line' }
      ],
      '2024-11-15': [
        { title: 'Examen Cálculo', category: 'exams', icon: 'ri-file-edit-line' },
        { title: 'Entrega Notas', category: 'meetings', icon: 'ri-calendar-check-line' }
      ],
      '2024-11-18': [
        { title: 'Taller Padres', category: 'meetings', icon: 'ri-user-voice-line' }
      ],
      '2024-11-20': [
        { title: 'Olimpiadas Mat.', category: 'activities', icon: 'ri-trophy-line' }
      ],
      '2024-11-22': [
        { title: 'Examen Estadística', category: 'exams', icon: 'ri-file-edit-line' }
      ]
    };

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

        calendarHTML += `<div class="${classes}">
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

    filterTabs.forEach(tab => {
      tab.addEventListener('click', () => {
        filterTabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');

        const filter = tab.getAttribute('data-filter');

        eventCards.forEach(card => {
          if (filter === 'all') {
            card.style.display = 'block';
          } else {
            const category = card.getAttribute('data-category');
            card.style.display = (category === filter) ? 'block' : 'none';
          }
        });
      });
    });

    // Search Functionality
    const searchInput = document.getElementById('searchEvents');
    if (searchInput) {
      searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();

        eventCards.forEach(card => {
          const title = card.querySelector('h4')?.textContent.toLowerCase() || '';
          const description = card.querySelector('p')?.textContent.toLowerCase() || '';

          card.style.display = (title.includes(searchTerm) || description.includes(searchTerm)) ? 'block' : 'none';
        });
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

    // New Event Button
    const btnNewEvent = document.getElementById('btnNewEvent');
    if (btnNewEvent) {
      btnNewEvent.addEventListener('click', () => {
        alert('Funcionalidad de crear nuevo evento (próximamente)');
      });
    }

    // Event Card Actions
    document.querySelectorAll('.btn-event-primary, .btn-event-secondary').forEach(btn => {
      btn.addEventListener('click', () => {
        console.log(`Acción: ${btn.textContent.trim()}`);
      });
    });

    // Quick Actions
    document.querySelectorAll('.quick-action').forEach(action => {
      action.addEventListener('click', (e) => {
        const actionText = e.currentTarget.querySelector('span');
        if (actionText) {
          console.log(`Acción rápida: ${actionText.textContent}`);
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

    console.log('Eventos module loaded successfully');
  })();
}

console.log('Main Docente JS loaded successfully');