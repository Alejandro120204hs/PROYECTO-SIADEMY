/**
 * ENTREGAS DE ACTIVIDAD - DOCENTE
 * Gestión de visualización y calificación de entregas estudiantiles
 */

$(document).ready(function() {
    
    // ===================================
    // FILTRADO DE ESTUDIANTES
    // ===================================
    
    $('.filter-btn-entrega').on('click', function() {
        // Remover clase active de todos los botones
        $('.filter-btn-entrega').removeClass('active');
        // Agregar clase active al botón clickeado
        $(this).addClass('active');
        
        const filtro = $(this).data('filtro');
        
        // Mostrar todas las filas primero
        $('.entregas-table tbody tr').show();
        
        // Filtrar según el estado seleccionado
        if (filtro !== 'todos') {
            $('.entregas-table tbody tr').each(function() {
                const estado = $(this).data('estado');
                if (estado !== filtro) {
                    $(this).hide();
                }
            });
        }
        
        actualizarContadorVisible();
    });
    
    
    // ===================================
    // BÚSQUEDA DE ESTUDIANTES
    // ===================================
    
    $('#searchEstudiante').on('keyup', function() {
        const valorBusqueda = $(this).val().toLowerCase();
        
        $('.entregas-table tbody tr').each(function() {
            const nombreEstudiante = $(this).find('.estudiante-info strong').text().toLowerCase();
            
            if (nombreEstudiante.includes(valorBusqueda)) {
                // Verificar también el filtro activo
                const filtroActivo = $('.filter-btn-entrega.active').data('filtro');
                const estadoFila = $(this).data('estado');
                
                if (filtroActivo === 'todos' || estadoFila === filtroActivo) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            } else {
                $(this).hide();
            }
        });
        
        actualizarContadorVisible();
    });
    
    
    // ===================================
    // MODAL DE CALIFICACIÓN
    // ===================================
    
    $('.btn-calificar').on('click', function() {
        const idEntrega = $(this).data('id-entrega');
        const nombreEstudiante = $(this).data('nombre');
        const notaActual = $(this).data('nota') || '';
        const observacionActual = $(this).data('observacion') || '';
        
        // Actualizar título del modal
        $('#modalCalificarLabel').text(`Calificar entrega de ${nombreEstudiante}`);
        
        // Llenar el formulario
        $('#idEntregaCalificar').val(idEntrega);
        $('#notaEntrega').val(notaActual);
        $('#observacionNota').val(observacionActual);
        
        // Mostrar el modal
        $('#modalCalificar').modal('show');
    });
    
    
    // ===================================
    // VALIDACIÓN DE NOTA (0-5)
    // ===================================
    
    $('#notaEntrega').on('input', function() {
        let valor = parseFloat($(this).val());
        
        if (isNaN(valor)) {
            $(this).val('');
            return;
        }
        
        if (valor < 0) {
            $(this).val(0);
        } else if (valor > 5) {
            $(this).val(5);
        }
        
        // Permitir solo 1 decimal
        if ($(this).val().includes('.')) {
            const partes = $(this).val().split('.');
            if (partes[1] && partes[1].length > 1) {
                $(this).val(parseFloat($(this).val()).toFixed(1));
            }
        }
    });
    
    
    // ===================================
    // GUARDAR CALIFICACIÓN
    // ===================================
    
    $('#formCalificar').on('submit', function(e) {
        e.preventDefault();
        
        const idEntrega = $('#idEntregaCalificar').val();
        const nota = parseFloat($('#notaEntrega').val());
        const observacion = $('#observacionNota').val();
        
        // Validación
        if (!nota || nota < 0 || nota > 5) {
            Swal.fire({
                icon: 'warning',
                title: 'Nota inválida',
                text: 'La nota debe estar entre 0 y 5',
                confirmButtonColor: '#4f46e5'
            });
            return;
        }
        
        // Deshabilitar botón para evitar doble envío
        const btnGuardar = $('#btnGuardarCalificacion');
        btnGuardar.prop('disabled', true).html('<i class="ri-loader-4-line rotating"></i> Guardando...');
        
        // Enviar datos al servidor
        $.ajax({
            url: '/siademy/docente/calificar-actividad',
            type: 'POST',
            data: {
                id_entrega: idEntrega,
                nota: nota,
                observacion: observacion
            },
            success: function(response) {
                const data = JSON.parse(response);
                
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Calificación guardada!',
                        text: 'La nota ha sido registrada exitosamente',
                        confirmButtonColor: '#4f46e5',
                        timer: 2000
                    }).then(() => {
                        // Recargar página para actualizar estadísticas
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'No se pudo guardar la calificación',
                        confirmButtonColor: '#ef4444'
                    });
                    btnGuardar.prop('disabled', false).html('<i class="ri-check-line"></i> Guardar Calificación');
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor',
                    confirmButtonColor: '#ef4444'
                });
                btnGuardar.prop('disabled', false).html('<i class="ri-check-line"></i> Guardar Calificación');
            }
        });
    });
    
    
    // ===================================
    // DESCARGAR ARCHIVO ENTREGA
    // ===================================
    
    $('.btn-descargar').on('click', function() {
        const idEntrega = $(this).data('id-entrega');
        
        // Abrir en nueva ventana para descargar
        window.open(`/siademy/docente/descargar-entrega?id=${idEntrega}`, '_blank');
    });
    
    
    // ===================================
    // VER COMENTARIO DEL ESTUDIANTE
    // ===================================
    
    $('.btn-ver-comentario').on('click', function() {
        const comentario = $(this).data('comentario');
        const nombreEstudiante = $(this).closest('tr').find('.estudiante-info strong').text();
        
        if (!comentario || comentario.trim() === '') {
            Swal.fire({
                icon: 'info',
                title: 'Sin comentarios',
                text: `${nombreEstudiante} no dejó comentarios en esta entrega`,
                confirmButtonColor: '#4f46e5'
            });
        } else {
            Swal.fire({
                icon: 'info',
                title: `Comentario de ${nombreEstudiante}`,
                html: `<div style="text-align: left; padding: 10px; background: #f3f4f6; border-radius: 8px;">${comentario}</div>`,
                confirmButtonColor: '#4f46e5',
                width: '600px'
            });
        }
    });
    
    
    // ===================================
    // CONTADOR DE RESULTADOS VISIBLES
    // ===================================
    
    function actualizarContadorVisible() {
        const totalFilas = $('.entregas-table tbody tr').length;
        const filasVisibles = $('.entregas-table tbody tr:visible').length;
        
        // Puedes mostrar esto en algún elemento si deseas
        console.log(`Mostrando ${filasVisibles} de ${totalFilas} estudiantes`);
    }
    
    
    // ===================================
    // ANIMACIÓN LOADING EN BOTONES
    // ===================================
    
    const style = document.createElement('style');
    style.textContent = `
        @keyframes rotating {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .rotating {
            animation: rotating 1s linear infinite;
        }
    `;
    document.head.appendChild(style);
    
    
    // ===================================
    // TOOLTIPS BOOTSTRAP
    // ===================================
    
    $('[data-bs-toggle="tooltip"]').tooltip();
    
});
