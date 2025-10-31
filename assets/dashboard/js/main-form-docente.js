// Sistema de toggle para sidebars con localStorage
const leftSidebar = document.getElementById('leftSidebar');
const appGrid = document.getElementById('appGrid');
const toggleLeft = document.getElementById('toggleLeft');

// Cargar estado desde localStorage
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

// Event listeners
toggleLeft.addEventListener('click', toggleLeftSidebar);

// Aplicar estado inicial
if (!leftVisible) leftSidebar.classList.add('hidden');
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
    document.getElementById("stepIndicator3")
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

document.getElementById("formWizard").addEventListener("submit", function (e) {
    e.preventDefault();
    alert("Profesor agregado con éxito");
});