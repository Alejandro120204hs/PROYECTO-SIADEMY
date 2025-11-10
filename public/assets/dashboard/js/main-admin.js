// ========================================
// SISTEMA DE TOGGLE PARA SIDEBARS
// ========================================
const leftSidebar = document.getElementById('leftSidebar');
const rightSidebar = document.getElementById('rightSidebar');
const appGrid = document.getElementById('appGrid');
const toggleLeft = document.getElementById('toggleLeft');
const toggleRight = document.getElementById('toggleRight');

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

if (toggleLeft) toggleLeft.addEventListener('click', toggleLeftSidebar);
if (toggleRight) toggleRight.addEventListener('click', toggleRightSidebar);

if (!leftVisible && leftSidebar) leftSidebar.classList.add('hidden');
if (!rightVisible && rightSidebar) rightSidebar.classList.add('hidden');
updateGridState();

// ========================================
// GRÁFICO (solo si existe)
// ========================================
const ctx = document.getElementById('lineChart');
if (ctx) {
    const gradient1 = ctx.getContext('2d').createLinearGradient(0, 0, 0, 320);
    gradient1.addColorStop(0, 'rgba(255,107,107,.35)');
    gradient1.addColorStop(1, 'rgba(255,107,107,0)');

    const gradient2 = ctx.getContext('2d').createLinearGradient(0, 0, 0, 320);
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
        }, {
            label: 'La semana pasada',
            data: [5, 28, 90, 12, 10, 40, 60, 35, 45, 68, 70, 60],
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
            pagingType: 'simple_numbers',
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
});

// ========================================
// CALENDARIO (solo si existe)
// ========================================
const calendarGrid = document.getElementById('calendarGrid');
if (calendarGrid) {
    let currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();

    const months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    const daysOfWeek = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
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
        const header = document.querySelector('.calendar-header h3');
        if (header) header.textContent = `${months[month]} ${year}`;
    }

    const prevMonth = document.getElementById('prevMonth');
    const nextMonth = document.getElementById('nextMonth');

    if (prevMonth) {
        prevMonth.addEventListener('click', () => {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            generateCalendar(currentMonth, currentYear);
        });
    }

    if (nextMonth) {
        nextMonth.addEventListener('click', () => {
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
// MÓDULO DE CURSOS
// ========================================
document.addEventListener('DOMContentLoaded', function() {
    const courses = document.querySelectorAll('.course-card');
    if (courses.length === 0) {
        console.log('No hay cursos en esta página');
    } else {
        console.log('📚 Sistema de cursos cargado - ' + courses.length + ' cursos');

        const viewBtns = document.querySelectorAll('.courses-section .view-btn');
        const coursesGrid = document.querySelector('.courses-grid');
        
        viewBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                viewBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                const view = this.dataset.view;
                
                if (view === 'list') {
                    coursesGrid.style.gridTemplateColumns = '1fr';
                } else {
                    coursesGrid.style.gridTemplateColumns = 'repeat(auto-fill, minmax(340px, 1fr))';
                }
                localStorage.setItem('coursesView', view);
            });
        });

        const savedView = localStorage.getItem('coursesView') || 'grid';
        const savedBtn = document.querySelector(`.courses-section .view-btn[data-view="${savedView}"]`);
        if (savedBtn) savedBtn.click();

        courses.forEach(course => {
            const badge = course.querySelector('.course-badge').textContent.trim();
            const status = course.querySelector('.course-status').textContent.trim();
            const title = course.querySelector('h4').textContent.trim();
            const teacher = course.querySelector('.stat:nth-child(2) span').textContent.trim();
            const gradeText = course.querySelector('.performance-label strong').textContent.trim();
            const studentsText = course.querySelector('.stat:nth-child(1) span').textContent.trim();
            
            const gradoMatch = badge.match(/\d+/);
            course.dataset.grado = gradoMatch ? gradoMatch[0] : '0';
            course.dataset.badge = badge;
            course.dataset.periodo = 'Segundo Período';
            course.dataset.estado = status;
            course.dataset.titulo = title.toLowerCase();
            course.dataset.profesor = teacher.toLowerCase();
            course.dataset.promedio = parseFloat(gradeText);
            
            const estudiantesMatch = studentsText.match(/\d+/);
            course.dataset.estudiantes = estudiantesMatch ? parseInt(estudiantesMatch[0]) : 0;
        });
    }
});

// ========================================
// MÓDULO DE ASIGNATURAS
// ========================================
document.addEventListener('DOMContentLoaded', function() {
    const subjects = document.querySelectorAll('.subject-card');
    if (subjects.length === 0) {
        console.log('No hay asignaturas en esta página');
        return;
    }

    console.log('📖 Sistema de asignaturas cargado - ' + subjects.length + ' asignaturas');

    const subjectsGrid = document.querySelector('.subjects-grid');
    const viewBtns = document.querySelectorAll('.subjects-section .view-btn');
    
    if (viewBtns.length && subjectsGrid) {
        viewBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                viewBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                const view = this.dataset.view;
                
                console.log('Cambiando vista a:', view);
                
                if (view === 'list') {
                    subjectsGrid.classList.add('list-view');
                    subjectsGrid.style.gridTemplateColumns = '1fr';
                } else {
                    subjectsGrid.classList.remove('list-view');
                    subjectsGrid.style.gridTemplateColumns = 'repeat(auto-fill, minmax(300px, 1fr))';
                }
                
                localStorage.setItem('subjectsView', view);
            });
        });
        
        // Restaurar vista guardada
        const savedView = localStorage.getItem('subjectsView') || 'grid';
        const savedBtn = document.querySelector(`.subjects-section .view-btn[data-view="${savedView}"]`);
        if (savedBtn) {
            savedBtn.click();
        }
    }
    
    console.log('✅ Sistema de asignaturas listo');
});


// ========================================
// JAVASCRIPT PARA TABS - DETALLE ESTUDIANTE
// Agregar al archivo main-panel-estudiantes.js
// ========================================

document.addEventListener('DOMContentLoaded', function() {
    
    // SISTEMA DE TABS
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Remover clase active de todos los botones
            tabButtons.forEach(btn => btn.classList.remove('active'));
            
            // Agregar clase active al botón clickeado
            this.classList.add('active');
            
            // Ocultar todos los tab panes
            tabPanes.forEach(pane => pane.classList.remove('active'));
            
            // Mostrar el tab pane correspondiente
            const targetPane = document.getElementById(targetTab);
            if (targetPane) {
                targetPane.classList.add('active');
            }
        });
    });

    // TOGGLE SIDEBAR (si no está ya implementado)
    const toggleLeft = document.getElementById('toggleLeft');
    const toggleRight = document.getElementById('toggleRight');
    const appGrid = document.getElementById('appGrid');
    const leftSidebar = document.getElementById('leftSidebar');
    const rightSidebar = document.getElementById('rightSidebar');

    if (toggleLeft) {
        toggleLeft.addEventListener('click', function() {
            leftSidebar.classList.toggle('hidden');
            if (rightSidebar.classList.contains('hidden')) {
                appGrid.classList.toggle('hide-both');
            } else {
                appGrid.classList.toggle('hide-left');
            }
        });
    }

    if (toggleRight) {
        toggleRight.addEventListener('click', function() {
            rightSidebar.classList.toggle('hidden');
            if (leftSidebar.classList.contains('hidden')) {
                appGrid.classList.toggle('hide-both');
            } else {
                appGrid.classList.toggle('hide-right');
            }
        });
    }

    // QUICK ACTIONS - SIDEBAR
    const quickActionButtons = document.querySelectorAll('.quick-action-btn');
    quickActionButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const action = this.querySelector('span').textContent;
            console.log('Acción: ' + action);
            // Aquí puedes agregar la funcionalidad específica para cada botón
        });
    });

    // DOCUMENT ITEMS - SIDEBAR
    const documentItems = document.querySelectorAll('.document-item');
    documentItems.forEach(item => {
        item.addEventListener('click', function() {
            const docName = this.querySelector('strong').textContent;
            console.log('Documento clickeado: ' + docName);
            // Aquí puedes agregar descarga o visualización de documentos
        });
    });

    // OBSERVACIONES - Expandir/Colapsar (opcional)
    const observationItems = document.querySelectorAll('.observation-item');
    observationItems.forEach(item => {
        item.style.cursor = 'pointer';
        item.addEventListener('click', function() {
            this.classList.toggle('expanded');
        });
    });

    // PRINT BUTTON
    const printBtn = document.querySelector('.btn-secondary-action');
    if (printBtn) {
        printBtn.addEventListener('click', function() {
            window.print();
        });
    }

    // EDIT PROFILE BUTTON
    const editBtn = document.querySelector('.btn-primary-action');
    if (editBtn) {
        editBtn.addEventListener('click', function() {
            console.log('Editar perfil clickeado');
            // Aquí puedes redirigir a la página de edición
            // window.location.href = 'editar-estudiante.html?id=123';
        });
    }

});

// ========================================
// FUNCIONES AUXILIARES
// ========================================

// Función para formatear fechas
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('es-ES', options);
}

// Función para calcular edad
function calculateAge(birthDate) {
    const today = new Date();
    const birth = new Date(birthDate);
    let age = today.getFullYear() - birth.getFullYear();
    const monthDiff = today.getMonth() - birth.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
        age--;
    }
    
    return age;
}

// Función para animar números (contador)
function animateValue(element, start, end, duration) {
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        element.textContent = Math.floor(progress * (end - start) + start);
        if (progress < 1) {
            window.requestAnimationFrame(step);
        }
    };
    window.requestAnimationFrame(step);
}

// Función para inicializar gráficos (si usas Chart.js)
function initPerformanceChart() {
    const canvas = document.getElementById('performanceChart');
    if (canvas && typeof Chart !== 'undefined') {
        const ctx = canvas.getContext('2d');
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Período 1', 'Período 2', 'Período 3'],
                datasets: [{
                    label: 'Promedio General',
                    data: [4.0, 4.1, 4.2],
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: '#e6e9f4'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5,
                        ticks: {
                            color: '#97a1b6'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.08)'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#97a1b6'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.08)'
                        }
                    }
                }
            }
        });
    }
}

// Inicializar gráfico cuando se cargue el tab académico
document.addEventListener('DOMContentLoaded', function() {
    const academicTab = document.querySelector('[data-tab="academico"]');
    if (academicTab) {
        academicTab.addEventListener('click', function() {
            setTimeout(() => {
                initPerformanceChart();
            }, 100);
        });
    }
});