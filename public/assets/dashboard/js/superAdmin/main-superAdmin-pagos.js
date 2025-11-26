// Inicialización de DataTables y funcionalidades
$(document).ready(function() {
    // Inicializar DataTable
    const paymentsTable = $('#paymentsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        pageLength: 10,
        responsive: true,
        dom: '<"table-header"lf>rt<"table-footer"ip>'
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

    // FUNCIONALIDAD DE FILTROS CORREGIDA
    const filterBtn = $('.filter-btn');
    const estadoSelect = $('.filters-grid select').eq(0);
    const planSelect = $('#planFilter');
    const fechaSelect = $('.filters-grid select').eq(2);
    const filterResults = $('<div class="filter-results"></div>');
    
    // Insertar mensaje de resultados después de los filtros
    $('.filters-grid').after(filterResults);

    // Aplicar filtros
    filterBtn.click(function() {
        aplicarFiltros();
    });

    // Función para aplicar filtros CORREGIDA
    function aplicarFiltros() {
        const estado = estadoSelect.val();
        const plan = planSelect.val();
        const fecha = fechaSelect.val();
        
        // Mostrar estado de carga
        filterBtn.addClass('loading').html('<i class="ri-loader-4-line"></i> Aplicando...');
        
        // Simular procesamiento
        setTimeout(() => {
            // Limpiar filtros anteriores de DataTable
            paymentsTable.columns().search('').draw();
            
            // Aplicar nuevos filtros
            $.fn.dataTable.ext.search = [];
            
            // Filtro por ESTADO
            if (estado !== 'Todos los estados') {
                $.fn.dataTable.ext.search.push(
                    function(settings, data, dataIndex) {
                        const estadoCelda = data[6] || ''; // Columna 6 es ESTADO
                        return estadoCelda.includes(estado);
                    }
                );
            }
            
            // Filtro por PERIODICIDAD
            if (plan !== 'Todas las periodicidades') {
                $.fn.dataTable.ext.search.push(
                    function(settings, data, dataIndex) {
                        const planCelda = data[2] || ''; // Columna 2 es PLAN
                        return planCelda.includes(plan);
                    }
                );
            }
            
            // Filtro por FECHA (simplificado para demostración)
            if (fecha !== 'Últimos 30 días') {
                $.fn.dataTable.ext.search.push(
                    function(settings, data, dataIndex) {
                        const fechaCelda = data[3] || ''; // Columna 3 es FECHA PAGO
                        // Aquí iría la lógica real de filtrado por fecha
                        return true; // Por ahora mostramos todos
                    }
                );
            }
            
            // Redibujar la tabla con los filtros aplicados
            paymentsTable.draw();
            
            // Obtener número de registros filtrados
            const resultados = paymentsTable.rows({ filter: 'applied' }).count();
            const total = paymentsTable.rows().count();
            
            // Mostrar mensaje de resultados
            let filtrosAplicados = [];
            if (estado !== 'Todos los estados') filtrosAplicados.push('Estado: ' + estado);
            if (plan !== 'Todas las periodicidades') filtrosAplicados.push('Periodicidad: ' + plan);
            if (fecha !== 'Últimos 30 días') filtrosAplicados.push('Fecha: ' + fecha);
            
            if (filtrosAplicados.length > 0) {
                filterResults.html(`
                    <div class="results-info">
                        <i class="ri-filter-line"></i>
                        ${filtrosAplicados.join(' • ')}
                        | Mostrando ${resultados} de ${total} registros
                    </div>
                    <button class="clear-filters">
                        <i class="ri-close-line"></i> Limpiar filtros
                    </button>
                `).addClass('show');
            } else {
                filterResults.removeClass('show');
            }
            
            // Quitar estado de carga
            filterBtn.removeClass('loading').html('<i class="ri-filter-line"></i> Aplicar Filtros');
            
            // Agregar evento para limpiar filtros
            $('.clear-filters').click(limpiarFiltros);
            
        }, 800);
    }

    // Función para limpiar filtros
    function limpiarFiltros() {
        // Restablecer selects
        estadoSelect.val('Todos los estados');
        planSelect.val('Todas las periodicidades');
        fechaSelect.val('Últimos 30 días');
        
        // Remover clases activas
        $('.filters-grid select').removeClass('filter-active');
        
        // Limpiar filtros de DataTable
        $.fn.dataTable.ext.search = [];
        paymentsTable.draw();
        
        // Ocultar mensaje de resultados
        filterResults.removeClass('show');
        
        // Mostrar mensaje de éxito temporal
        const tempMessage = $('<div class="filter-results show" style="background: #10b98120; border-color: #10b981;"></div>')
            .html(`<div class="results-info" style="color: #10b981;"><i class="ri-checkbox-circle-line"></i> Filtros limpiados correctamente</div>`);
        
        $('.filters-grid').after(tempMessage);
        
        setTimeout(() => {
            tempMessage.remove();
        }, 2000);
    }

    // Simular selección de pago
    $('#paymentsTable tbody').on('click', 'tr', function() {
        $('#paymentsTable tbody tr').removeClass('selected');
        $(this).addClass('selected');
        
        // Aquí puedes agregar lógica para cargar los detalles en el panel derecho
        const referencia = $(this).find('td:first strong').text();
        const escuela = $(this).find('td:eq(1)').text();
        const plan = $(this).find('td:eq(2) .badge').text();
        const fechaPago = $(this).find('td:eq(3)').text();
        const vencimiento = $(this).find('td:eq(4)').text();
        const monto = $(this).find('td:eq(5) .amount').text();
        const estado = $(this).find('td:eq(6) .badge').text();
        
        // Actualizar panel derecho con los datos (esto es un ejemplo)
        console.log('Pago seleccionado:', {
            referencia,
            escuela,
            plan,
            fechaPago,
            vencimiento,
            monto,
            estado
        });
    });

    // Efectos hover en botones
    $('.action-btn, .filter-btn').hover(
        function() {
            $(this).css('transform', 'translateY(-2px)');
        },
        function() {
            $(this).css('transform', 'translateY(0)');
        }
    );

    // Efectos visuales para selects
    $('.filters-grid select').focus(function() {
        $(this).addClass('filter-active');
    }).blur(function() {
        const defaultValue = $(this).find('option:first').val();
        if ($(this).val() === defaultValue) {
            $(this).removeClass('filter-active');
        }
    }).change(function() {
        if ($(this).val() !== $(this).find('option:first').val()) {
            $(this).addClass('filter-active');
        } else {
            $(this).removeClass('filter-active');
        }
    });

    // Funcionalidad para botones de acción en la tabla
    $('.action-buttons .btn-info').click(function(e) {
        e.stopPropagation();
        const fila = $(this).closest('tr');
        const referencia = fila.find('td:first strong').text();
        alert(`Ver detalles del pago: ${referencia}`);
        // Aquí puedes abrir un modal o cargar más detalles
    });

    $('.action-buttons .btn-warning').click(function(e) {
        e.stopPropagation();
        const fila = $(this).closest('tr');
        const referencia = fila.find('td:first strong').text();
        alert(`Descargar comprobante: ${referencia}`);
        // Aquí puedes implementar la descarga
    });

    $('.action-buttons .btn-danger').click(function(e) {
        e.stopPropagation();
        const fila = $(this).closest('tr');
        const referencia = fila.find('td:first strong').text();
        const escuela = fila.find('td:eq(1)').text();
        
        if (confirm(`¿Enviar recordatorio de pago a ${escuela} (${referencia})?`)) {
            alert(`Recordatorio enviado a ${escuela}`);
            // Aquí puedes implementar el envío del recordatorio
        }
    });

    // Funcionalidad para los botones del panel derecho
    $('.payment-actions .btn-warning').click(function() {
        alert('Descargando factura...');
        // Implementar descarga de factura
    });

    $('.payment-actions .btn-info').click(function() {
        alert('Mostrando comprobante...');
        // Implementar visualización de comprobante
    });

    // Auto-aplicar filtros al cambiar selects (opcional)
    $('.filters-grid select').change(function() {
        // Descomenta la siguiente línea si quieres que se apliquen automáticamente
        // aplicarFiltros();
    });

    // Inicializar tooltips de Bootstrap si están disponibles
    if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});