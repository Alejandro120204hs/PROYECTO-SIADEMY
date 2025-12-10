// ===========================================
// TOGGLE SIDEBARS CON LOCALSTORAGE
// ===========================================
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

toggleLeft.addEventListener('click', toggleLeftSidebar);
toggleRight.addEventListener('click', toggleRightSidebar);

if (!leftVisible) leftSidebar.classList.add('hidden');
if (!rightVisible) rightSidebar.classList.add('hidden');
updateGridState();

// ===========================================
// INITIALIZE DATATABLE
// ===========================================
let table;

$(document).ready(function () {
    table = $('#matriculasTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
        ordering: true,
        order: [[6, 'desc']],
        columnDefs: [
            { orderable: false, targets: [8] },
            { searchable: true, targets: [1, 2, 3, 4] },
            { className: 'text-center', targets: [7, 8] }
        ],
        dom: "<'row align-items-center'<'col-sm-6'l><'col-sm-6 text-sm-end'f>>" +
            "<'row'<'col-12'tr>>" +
            "<'row align-items-center mt-2'<'col-sm-6'i><'col-sm-6 text-sm-end'p>>"
    });
});

// ===========================================
// FILTROS POR TABS
// ===========================================
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        // Remover active de todos
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        const filter = this.getAttribute('data-filter');

        if (filter === 'todas') {
            table.search('').columns().search('').draw();
        } else {
            // Mapeo de filtros a estados
            const statusMap = {
                'pendientes': 'Pendiente',
                'revision': 'En Revisión',
                'aprobadas': 'Aprobada',
                'rechazadas': 'Rechazada'
            };

            table.column(7).search(statusMap[filter]).draw();
        }
    });
});

// ===========================================
// MODALES
// ===========================================
function openMatriculaModal() {
    document.getElementById('matriculaModal').classList.add('active');
}

function closeMatriculaModal() {
    document.getElementById('matriculaModal').classList.remove('active');
}

function openDetalleModal(id) {
    const modal = document.getElementById('detalleModal');
    const content = document.getElementById('detalleContent');

    // Datos de ejemplo (aquí harías una petición AJAX)
    const estudiantes = {
        1: {
            id: '#2024-045',
            nombre: 'Juan Martínez Pérez',
            documento: '1234567890',
            grado: '7° A',
            acudiente: 'María Pérez',
            telefono: '+57 300 123 4567',
            email: 'maria.perez@email.com',
            tipo: 'Nuevo ingreso',
            estado: 'Pendiente',
            fecha: '15 Nov 2024'
        }
    };

    const data = estudiantes[id] || estudiantes[1];

    content.innerHTML = `
        <div class="detalle-grid">
            <div class="detalle-section">
                <h4><i class="ri-user-line"></i> Información del Estudiante</h4>
                <div class="info-row">
                    <span class="label">ID de Matrícula:</span>
                    <span class="value"><strong>${data.id}</strong></span>
                </div>
                <div class="info-row">
                    <span class="label">Nombre Completo:</span>
                    <span class="value">${data.nombre}</span>
                </div>
                <div class="info-row">
                    <span class="label">Documento:</span>
                    <span class="value">${data.documento}</span>
                </div>
                <div class="info-row">
                    <span class="label">Grado:</span>
                    <span class="value"><span class="badge-grade">${data.grado}</span></span>
                </div>
                <div class="info-row">
                    <span class="label">Tipo de Matrícula:</span>
                    <span class="value">${data.tipo}</span>
                </div>
            </div>

            <div class="detalle-section">
                <h4><i class="ri-parent-line"></i> Información del Acudiente</h4>
                <div class="info-row">
                    <span class="label">Nombre:</span>
                    <span class="value">${data.acudiente}</span>
                </div>
                <div class="info-row">
                    <span class="label">Teléfono:</span>
                    <span class="value">${data.telefono}</span>
                </div>
                <div class="info-row">
                    <span class="label">Email:</span>
                    <span class="value">${data.email}</span>
                </div>
            </div>

            <div class="detalle-section">
                <h4><i class="ri-file-list-line"></i> Estado de la Solicitud</h4>
                <div class="info-row">
                    <span class="label">Estado:</span>
                    <span class="value">
                        <span class="status-badge pending">
                            <i class="ri-time-line"></i> ${data.estado}
                        </span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="label">Fecha de Solicitud:</span>
                    <span class="value">${data.fecha}</span>
                </div>
            </div>

            <div class="detalle-section">
                <h4><i class="ri-attachment-line"></i> Documentos Adjuntos</h4>
                <div class="documentos-list">
                    <div class="documento-item">
                        <i class="ri-file-pdf-line"></i>
                        <span>Registro civil.pdf</span>
                        <button class="btn-small"><i class="ri-download-line"></i></button>
                    </div>
                    <div class="documento-item">
                        <i class="ri-file-pdf-line"></i>
                        <span>Documento identidad.pdf</span>
                        <button class="btn-small"><i class="ri-download-line"></i></button>
                    </div>
                    <div class="documento-item">
                        <i class="ri-file-pdf-line"></i>
                        <span>Certificado estudios.pdf</span>
                        <button class="btn-small"><i class="ri-download-line"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-actions">
            <button class="btn-secondary" onclick="closeDetalleModal()">Cerrar</button>
            ${data.estado === 'Pendiente' ? `
                <button class="btn-secondary" style="background: #ef4444; color: white;">
                    <i class="ri-close-line"></i> Rechazar
                </button>
                <button class="btn-primary">
                    <i class="ri-check-line"></i> Aprobar Matrícula
                </button>
            ` : ''}
        </div>
    `;

    modal.classList.add('active');
}

function closeDetalleModal() {
    document.getElementById('detalleModal').classList.remove('active');
}

// Cerrar modales al hacer click fuera
document.getElementById('matriculaModal')?.addEventListener('click', function (e) {
    if (e.target === this) closeMatriculaModal();
});

document.getElementById('detalleModal')?.addEventListener('click', function (e) {
    if (e.target === this) closeDetalleModal();
});

// ===========================================
// FORMULARIO DE MATRÍCULA
// ===========================================
document.getElementById('matriculaForm')?.addEventListener('submit', function (e) {
    e.preventDefault();

    // Aquí iría la lógica para enviar el formulario
    alert('Matrícula registrada exitosamente');
    closeMatriculaModal();
    this.reset();

    // Opcional: recargar la tabla
    // table.ajax.reload();
});

// ===========================================
// ACCIONES DE TABLA
// ===========================================

// Aprobar matrícula
$(document).on('click', '.btn-icon.approve', function () {
    const row = $(this).closest('tr');
    const studentName = row.find('.student-cell strong').text();

    if (confirm(`¿Aprobar la matrícula de ${studentName}?`)) {
        alert(`Matrícula de ${studentName} aprobada`);

        // Actualizar estado visual
        row.find('.status-badge')
            .removeClass('pending review')
            .addClass('approved')
            .html('<i class="ri-checkbox-circle-line"></i> Aprobada');

        // Ocultar botones de aprobar/rechazar
        $(this).remove();
        row.find('.btn-icon.reject').remove();
        row.find('.btn-icon.edit').remove();
    }
});

// Editar matrícula
$(document).on('click', '.btn-icon.edit', function () {
    const row = $(this).closest('tr');
    const studentName = row.find('.student-cell strong').text();

    console.log('Editar matrícula:', studentName);
    alert(`Editar matrícula de ${studentName}`);
    // Aquí abriría un modal con el formulario pre-llenado
});

// Rechazar matrícula
$(document).on('click', '.btn-icon.reject', function () {
    const row = $(this).closest('tr');
    const studentName = row.find('.student-cell strong').text();

    if (confirm(`¿Rechazar la matrícula de ${studentName}?\nEsta acción no se puede deshacer.`)) {
        const motivo = prompt('Motivo del rechazo:');
        if (motivo) {
            alert(`Matrícula de ${studentName} rechazada\nMotivo: ${motivo}`);

            // Actualizar estado visual
            row.find('.status-badge')
                .removeClass('pending review')
                .addClass('rejected')
                .html('<i class="ri-close-circle-line"></i> Rechazada');

            // Ocultar botones de aprobar/rechazar/editar
            $(this).remove();
            row.find('.btn-icon.approve').remove();
            row.find('.btn-icon.edit').remove();
        }
    }
});

// Imprimir
$(document).on('click', '.btn-icon.print', function () {
    const row = $(this).closest('tr');
    const studentName = row.find('.student-cell strong').text();

    console.log('Imprimir matrícula:', studentName);
    alert(`Generando documento de matrícula para ${studentName}...`);
    // Aquí iría la lógica para generar el PDF
});

// ===========================================
// BÚSQUEDA GLOBAL
// ===========================================
document.getElementById('globalSearch')?.addEventListener('input', function (e) {
    table.search(e.target.value).draw();
});

// ===========================================
// ESTILOS ADICIONALES PARA MODAL DETALLE
// ===========================================
const style = document.createElement('style');
style.textContent = `
    .detalle-section h4 {
        font-size: 18px;
        color: #4f46e5;
        display: flex;
        align-items: center;
    }
    
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 5px 0;
        border-bottom: 1px solid var(--border);
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-row .label {
        color: #8b91a3;
        font-size: 14px;
    }
    
    .info-row .value {
        color: #2b3147ff;
        font-size: 18px;
        font-weight: 500;
    }
    
    .documentos-list {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    
    .documento-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        background: #fff;
        border-radius: 8px;
        border: 1px solid black;
    }
    
    .documento-item i:first-child {
        font-size: 20px;
        color: #ef4444;
    }
    
    .documento-item span {
        flex: 1;
        color: #151a2cff;
        font-size: 14px;
    }
    
    .btn-small {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        border: 1px solid var(--border);
        color: #a4b1ff;
        cursor: pointer;
        display: grid;
        place-items: center;
        transition: all 0.2s ease;
    }
    
    .btn-small:hover {
        background: #4f46e5;
        color: white;
    }
`;
document.head.appendChild(style);

console.log('✅ Módulo de Matrículas cargado correctamente');