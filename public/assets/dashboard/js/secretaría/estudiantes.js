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