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
    const planSelect = $('.filters-grid select').eq(1);
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
            
            // Filtro por PLAN
            if (plan !== 'Todos los planes') {
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
            if (plan !== 'Todos los planes') filtrosAplicados.push('Plan: ' + plan);
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
        planSelect.val('Todos los planes');
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
});