// Sistema de toggle para sidebars con localStorage
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

// Inicializar DataTable
$(document).ready(function () {
    $('#tablaEstudiantes').DataTable({
        language: {
            processing: "Procesando...",
            lengthMenu: "Mostrar _MENU_ registros",
            zeroRecords: "No se encontraron resultados",
            emptyTable: "Ningún dato disponible en esta tabla",
            info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
            infoFiltered: "(filtrado de un total de _MAX_ registros)",
            infoPostFix: "",
            search: "Buscar:",
            url: "",
            infoThousands: ",",
            loadingRecords: "Cargando...",
            paginate: {
                first: "Primero",
                last: "Último",
                next: "Siguiente",
                previous: "Anterior"
            }
        },
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
        ordering: true,
        searching: true,
        paging: true,
        info: true,
        responsive: true,
        columnDefs: [
            { orderable: false, targets: [0, 6, 8] }
        ]
    });

    // Seleccionar todos los checkboxes
    $('#selectAll').on('click', function () {
        $('.row-checkbox').prop('checked', this.checked);
    });

    // Actualizar el checkbox principal si se desmarcan checkboxes individuales
    $('.row-checkbox').on('click', function () {
        if ($('.row-checkbox:checked').length == $('.row-checkbox').length) {
            $('#selectAll').prop('checked', true);
        } else {
            $('#selectAll').prop('checked', false);
        }
    });
});


// La parte de el wizard
let currentStep = 0;
const steps = document.querySelectorAll(".step");
const indicators = [
    document.getElementById("stepIndicator1"),
    document.getElementById("stepIndicator2"),
    document.getElementById("stepIndicator3"),
    document.getElementById("stepIndicator4")
];

function showStep(index) {
    steps.forEach((s, i) => s.classList.toggle("active", i === index));
    indicators.forEach((ind, i) => ind.classList.toggle("active-step", i === index));
}

function nextStep() {
    if (currentStep < steps.length - 1) currentStep++;
    showStep(currentStep);
}

function prevStep() {
    if (currentStep > 0) currentStep--;
    showStep(currentStep);
}

