// Inicialización de DataTables y funcionalidades
$(document).ready(function() {
    // Inicializar DataTable
    $('#schoolsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        pageLength: 10,
        responsive: true
    });

    // Toggle sidebars
    $('#toggleLeft').click(function() {
        $('.app').toggleClass('hide-left');
        $('#leftSidebar').toggleClass('hidden');
    });

    $('#toggleRight').click(function() {
        $('.app').toggleClass('hide-right');
        $('#rightSidebar').toggleClass('hidden');
    });

    // Simular selección de escuela
    $('#schoolsTable tbody tr').click(function() {
        $('#schoolsTable tbody tr').removeClass('selected');
        $(this).addClass('selected');
        
        // Aquí podrías cargar los datos reales de la escuela seleccionada
        console.log('Escuela seleccionada:', $(this).find('td').eq(1).text());
    });

    // Efectos hover en botones de acción
    $('.action-btn').hover(
        function() {
            $(this).css('transform', 'translateY(-2px)');
        },
        function() {
            $(this).css('transform', 'translateY(0)');
        }
    );
});