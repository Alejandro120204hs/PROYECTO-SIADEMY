// ========================================
// SISTEMA DE TOGGLE PARA SIDEBAR IZQUIERDO
// ========================================
const leftSidebar = document.getElementById('leftSidebar');
const appGrid = document.getElementById('appGrid');
const toggleLeft = document.getElementById('toggleLeft');

let leftVisible = localStorage.getItem('leftSidebarVisible') !== 'false';

function updateGridState() {
    appGrid.classList.remove('hide-left');
    if (!leftVisible) {
        appGrid.classList.add('hide-left');
    }
}

function toggleLeftSidebar() {
    leftVisible = !leftVisible;
    leftSidebar.classList.toggle('hidden', !leftVisible);
    localStorage.setItem('leftSidebarVisible', leftVisible);
    updateGridState();
}

if (toggleLeft) toggleLeft.addEventListener('click', toggleLeftSidebar);

if (!leftVisible && leftSidebar) leftSidebar.classList.add('hidden');
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
