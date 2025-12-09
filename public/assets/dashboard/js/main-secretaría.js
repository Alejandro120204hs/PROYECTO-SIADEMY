// ===========================================
// TOGGLE SIDEBARS CON LOCALSTORAGE
// ===========================================
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

// ===========================================
// INITIALIZE DATATABLE
// ===========================================
$(document).ready(function () {
    $('#matriculasTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        pageLength: 10,
        lengthMenu: [
            [5, 10, 25, 50],
            [5, 10, 25, 50]
        ],
        ordering: true,
        order: [
            [0, 'desc']
        ],
        pagingType: 'simple_numbers',
        columnDefs: [{
            orderable: true,
            targets: [0, 1, 2, 3, 4, 5, 6]
        },
        {
            orderable: false,
            targets: [7]
        },
        {
            searchable: true,
            targets: [1, 2, 4]
        },
        {
            className: 'text-center',
            targets: [6, 7]
        }
        ],
        dom: "<'row align-items-center'<'col-sm-6'l><'col-sm-6 text-sm-end'f>>" +
            "<'row'<'col-12'tr>>" +
            "<'row align-items-center mt-2'<'col-sm-6'i><'col-sm-6 text-sm-end'p>>"
    });
});

// ===========================================
// CHART.JS - GRÁFICO DE MATRÍCULAS
// ===========================================
const ctx = document.getElementById('matriculasChart');

if (ctx) {
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            datasets: [{
                label: 'Matrículas 2024',
                data: [12, 19, 15, 25, 22, 30, 28, 35, 32, 40, 38, 45],
                backgroundColor: 'rgba(79, 70, 229, 0.8)',
                borderColor: 'rgba(79, 70, 229, 1)',
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    labels: {
                        color: '#a4b1ff',
                        font: {
                            family: 'Poppins',
                            size: 12
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.08)'
                    },
                    ticks: {
                        color: '#8b91a3',
                        font: {
                            family: 'Poppins'
                        }
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(255, 255, 255, 0.08)'
                    },
                    ticks: {
                        color: '#8b91a3',
                        font: {
                            family: 'Poppins'
                        }
                    }
                }
            }
        }
    });
}

// ===========================================
// MODAL NUEVA MATRÍCULA
// ===========================================
function openMatriculaModal() {
    document.getElementById('matriculaModal').classList.add('active');
}

function closeMatriculaModal() {
    document.getElementById('matriculaModal').classList.remove('active');
}

// Cerrar modal al hacer click fuera
document.getElementById('matriculaModal')?.addEventListener('click', function (e) {
    if (e.target === this) {
        closeMatriculaModal();
    }
});

// Form submit
document.getElementById('matriculaForm')?.addEventListener('submit', function (e) {
    e.preventDefault();

    // Aquí iría la lógica para enviar el formulario
    alert('Matrícula registrada exitosamente');
    closeMatriculaModal();
    this.reset();
});

// ===========================================
// ACCIONES DE TABLA
// ===========================================

// Aprobar matrícula
$(document).on('click', '.btn-action.approve', function () {
    const row = $(this).closest('tr');
    const studentName = row.find('.student-info strong').text();

    if (confirm(`¿Aprobar la matrícula de ${studentName}?`)) {
        // Aquí iría la lógica para aprobar
        alert(`Matrícula de ${studentName} aprobada`);
        row.find('.badge-status')
            .removeClass('pending review')
            .addClass('approved')
            .text('Aprobada');
    }
});

// Ver detalles
$(document).on('click', '.btn-action.view', function () {
    const row = $(this).closest('tr');
    const studentName = row.find('.student-info strong').text();
    const studentId = row.find('td:first strong').text();

    console.log('Ver detalles:', studentId, studentName);
    alert(`Ver detalles completos de ${studentName}\nID: ${studentId}`);
    // Aquí iría la navegación a la página de detalles
});

// Rechazar matrícula
$(document).on('click', '.btn-action.reject', function () {
    const row = $(this).closest('tr');
    const studentName = row.find('.student-info strong').text();

    if (confirm(`¿Rechazar la matrícula de ${studentName}?\nEsta acción no se puede deshacer.`)) {
        const motivo = prompt('Motivo del rechazo:');
        if (motivo) {
            // Aquí iría la lógica para rechazar
            alert(`Matrícula de ${studentName} rechazada\nMotivo: ${motivo}`);
            row.fadeOut(300, function () {
                $(this).remove();
            });
        }
    }
});

// ===========================================
// CHECKBOXES DE TAREAS
// ===========================================
document.querySelectorAll('.task-checkbox input[type="checkbox"]').forEach(checkbox => {
    checkbox.addEventListener('change', function () {
        const taskItem = this.closest('.task-item');
        if (this.checked) {
            taskItem.style.opacity = '0.6';
            taskItem.querySelector('strong').style.textDecoration = 'line-through';
        } else {
            taskItem.style.opacity = '1';
            taskItem.querySelector('strong').style.textDecoration = 'none';
        }
    });
});

// ===========================================
// ANIMACIONES AL CARGAR
// ===========================================
document.addEventListener('DOMContentLoaded', function () {
    // Animar KPI cards
    const kpiCards = document.querySelectorAll('.kpi-card');
    kpiCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';

        setTimeout(() => {
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Animar action buttons
    const actionBtns = document.querySelectorAll('.action-btn');
    actionBtns.forEach((btn, index) => {
        btn.style.opacity = '0';
        btn.style.transform = 'translateY(10px)';

        setTimeout(() => {
            btn.style.transition = 'all 0.3s ease';
            btn.style.opacity = '1';
            btn.style.transform = 'translateY(0)';
        }, 200 + (index * 50));
    });
});

// ===========================================
// EXPORTAR TABLA
// ===========================================
document.querySelector('.btn-export')?.addEventListener('click', function () {
    alert('Exportando datos a Excel...');
    // Aquí iría la lógica para exportar
    console.log('Exportar tabla de matrículas');
});

console.log('✅ Panel de Secretaría Académica cargado correctamente');