// ===========================================
// FILTROS POR TABS
// ===========================================
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        // Remover active de todos
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        const filter = this.getAttribute('data-filter');
        const table = $('#estudiantesTable').DataTable();

        if (filter === 'todos') {
            // Limpiar búsqueda en todas las columnas
            table.columns().search('').draw();
        } else {
            // Buscar en la columna 3 (Grado) que tiene índices 0, 1, 2, 3...
            // La columna "Grado" es la cuarta columna (índice 3)
            table.column(3).search('^' + filter + '°', true, false).draw();
        }
    });
});

// También necesitarás inicializar la DataTable correctamente:
$(document).ready(function() {
    const table = $('#estudiantesTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        "order": [[1, 'asc']],
        "responsive": true,
        "pageLength": 10,
        "columnDefs": [
            {
                "targets": [0, 8], // ID y Acciones
                "orderable": false
            }
        ]
    });

    // Búsqueda global
    $('#globalSearch').on('keyup', function() {
        table.search(this.value).draw();
    });
});

// ===========================================
// MODALES
// ===========================================
function openPerfilModal(id) {
    const modal = document.getElementById('perfilModal');
    const content = document.getElementById('perfilContent');

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

function closePerfilModal() {
    document.getElementById('perfilModal').classList.remove('active');
}

// Cerrar modales al hacer click fuera
document.getElementById('perfilModal')?.addEventListener('click', function (e) {
    if (e.target === this) closePerfilModal();
});