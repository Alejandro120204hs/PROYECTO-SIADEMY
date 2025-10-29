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

// Event listeners
toggleLeft.addEventListener('click', toggleLeftSidebar);
toggleRight.addEventListener('click', toggleRightSidebar);

// Aplicar estado inicial
if (!leftVisible) leftSidebar.classList.add('hidden');
if (!rightVisible) rightSidebar.classList.add('hidden');
updateGridState();

// Gráfico lineal estilo del mock
const ctx = document.getElementById('lineChart');
const gradient1 = (ctx.getContext('2d')).createLinearGradient(0, 0, 0, 320);
gradient1.addColorStop(0, 'rgba(255,107,107,.35)');
gradient1.addColorStop(1, 'rgba(255,107,107,0)');

const gradient2 = (ctx.getContext('2d')).createLinearGradient(0, 0, 0, 320);
gradient2.addColorStop(0, 'rgba(255,176,32,.35)');
gradient2.addColorStop(1, 'rgba(255,176,32,0)');

const data = {
    labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
    datasets: [{
        label: 'Esta semana',
        data: [20, 35, 55, 25, 15, 48, 62, 30, 22, 70, 85, 58],
        borderColor: '#ff6b6b',
        backgroundColor: gradient1,
        borderWidth: 3,
        pointRadius: 5,
        pointBackgroundColor: '#ff6b6b',
        tension: .45,
        fill: true
    },
    {
        label: 'La semana pasada',
        data: [5, 28, 90, 12, 10, 40, 60, 35, 45, 68, 70, 60],
        borderColor: '#ffb020',
        backgroundColor: gradient2,
        borderWidth: 3,
        pointRadius: 0,
        tension: .45,
        fill: true
    }
    ]
};
new Chart(ctx, {
    type: 'line',
    data,
    options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
        display: false
        },
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
        grid: {
            color: 'rgba(255,255,255,.06)'
        },
        ticks: {
            color: '#cbd5e1'
        }
        },
        y: {
        beginAtZero: true,
        grid: {
            color: 'rgba(255,255,255,.06)'
        },
        ticks: {
            color: '#cbd5e1'
        }
        }
    }
    }
});

// Initialize DataTable
$(document).ready(function() {
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
        }, // imprimir & opciones
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
});

// Calendar functionality
let currentDate = new Date();
let currentMonth = currentDate.getMonth();
let currentYear = currentDate.getFullYear();

const months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
];

const daysOfWeek = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];

// Sample events data
const events = {
    '2024-10-28': 'Reunión de Padres',
    '2024-10-30': 'Examen de Matemáticas',
    '2024-11-02': 'Festival Cultural',
    '2024-11-05': 'Día del Deporte',
    '2024-11-10': 'Feria de Ciencias'
};

function generateCalendar(month, year) {
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const daysInPrevMonth = new Date(year, month, 0).getDate();

    let calendarHTML = '';

    // Add day headers
    daysOfWeek.forEach(day => {
    calendarHTML += `<div class="calendar-day-header">${day}</div>`;
    });

    // Add empty cells for days before the first day of month
    for (let i = firstDay - 1; i >= 0; i--) {
    const day = daysInPrevMonth - i;
    calendarHTML += `<div class="calendar-day other-month">${day}</div>`;
    }

    // Add days of current month
    for (let day = 1; day <= daysInMonth; day++) {
    const dateString = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    const isToday = day === currentDate.getDate() && month === currentDate.getMonth() && year === currentDate.getFullYear();
    const hasEvent = events[dateString];

    let classes = 'calendar-day';
    if (isToday) classes += ' today';
    if (hasEvent) classes += ' has-event';

    calendarHTML += `<div class="${classes}" title="${hasEvent || ''}">${day}</div>`;
    }

    // Add empty cells for remaining days
    const totalCells = Math.ceil((firstDay + daysInMonth) / 7) * 7;
    const remainingCells = totalCells - (firstDay + daysInMonth);
    for (let day = 1; day <= remainingCells; day++) {
    calendarHTML += `<div class="calendar-day other-month">${day}</div>`;
    }

    document.getElementById('calendarGrid').innerHTML = calendarHTML;
    document.querySelector('.calendar-header h3').textContent = `${months[month]} ${year}`;
}

// Calendar navigation
document.getElementById('prevMonth').addEventListener('click', () => {
    currentMonth--;
    if (currentMonth < 0) {
    currentMonth = 11;
    currentYear--;
    }
    generateCalendar(currentMonth, currentYear);
});

document.getElementById('nextMonth').addEventListener('click', () => {
    currentMonth++;
    if (currentMonth > 11) {
    currentMonth = 0;
    currentYear++;
    }
    generateCalendar(currentMonth, currentYear);
});

// Initialize calendar
generateCalendar(currentMonth, currentYear);



