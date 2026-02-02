// ========================================
// ACTIVIDADES - JAVASCRIPT (SOLO FILTROS)
// ========================================

$(document).ready(function() {
  inicializarFiltros();
  inicializarSidebar();
  inicializarModales();
  inicializarToggleVista(); // ← AGREGAR ESTO

  // Restaurar vista guardada (opcional)
  const vistaGuardada = localStorage.getItem('vistaActividades');
  if (vistaGuardada) {
    cambiarVista(vistaGuardada);
  }
});

// ===== SIDEBAR TOGGLE =====
function inicializarSidebar() {
  $('#toggleLeft').click(function() {
    $('#sidebar').toggleClass('hidden');
    $('#appGrid').toggleClass('hide-left');
  });
}

// ===== INICIALIZAR FILTROS =====
function inicializarFiltros() {
  
  // Filtro por Estado (Tabs)
  $('.filter-tab-actividad').click(function() {
    $('.filter-tab-actividad').removeClass('active');
    $(this).addClass('active');
    
    const estado = $(this).data('estado');
    filtrarPorEstado(estado);
  });

  // Filtro por Período
  $('#periodFilter').change(function() {
    aplicarFiltros();
  });

  // Filtro por Curso
  $('#cursoFilter').change(function() {
    aplicarFiltros();
  });

  // Búsqueda en tiempo real
  $('#searchActividades').on('input', function() {
    aplicarFiltros();
  });
}

// ===== FILTRAR POR ESTADO =====
function filtrarPorEstado(estado) {
  const cards = $('.actividad-card');
  
  if (estado === 'todas') {
    cards.show();
  } else {
    cards.hide();
    $(`.actividad-card[data-estado="${estado}"]`).show();
  }

  // Aplicar también los otros filtros activos
  aplicarFiltros();
  verificarResultados();
}

// ===== APLICAR TODOS LOS FILTROS =====
// ===== APLICAR TODOS LOS FILTROS (actualizada) =====
function aplicarFiltros() {
  const estadoActivo = $('.filter-tab-actividad.active').data('estado');
  const periodoSeleccionado = $('#periodFilter').val();
  const cursoSeleccionado = $('#cursoFilter').val();
  const textoBusqueda = $('#searchActividades').val().toLowerCase();

  // Filtrar CARDS
  $('.actividad-card').each(function() {
    const $card = $(this);
    const estado = $card.data('estado');
    const periodo = $card.data('periodo');
    const curso = $card.data('curso');
    const titulo = $card.data('titulo') || '';
    const descripcion = $card.data('descripcion') || '';

    let cumpleEstado = (estadoActivo === 'todas') || (estado === estadoActivo);
    let cumplePeriodo = (periodoSeleccionado === 'todos') || (periodo == periodoSeleccionado);
    let cumpleCurso = (cursoSeleccionado === 'todos') || (curso === cursoSeleccionado);
    let cumpleBusqueda = (textoBusqueda === '') || 
                         titulo.includes(textoBusqueda) || 
                         descripcion.includes(textoBusqueda);

    if (cumpleEstado && cumplePeriodo && cumpleCurso && cumpleBusqueda) {
      $card.show();
    } else {
      $card.hide();
    }
  });

  verificarResultados();

  // Filtrar TABLA también
  aplicarFiltrosTabla();
}

// ===== VERIFICAR SI HAY RESULTADOS =====
function verificarResultados() {
  const visibles = $('.actividad-card:visible').length;
  const grid = $('#actividadesGrid');
  
  // Remover mensaje anterior si existe
  $('.actividades-empty').remove();

  if (visibles === 0) {
    grid.append(`
      <div class="actividades-empty">
        <i class="ri-search-line"></i>
        <h3>No se encontraron actividades</h3>
        <p>Intenta cambiar los filtros o el término de búsqueda</p>
      </div>
    `);
  }
}

// ===== MODALES =====
function inicializarModales() {
  
  // Abrir modal Nueva Actividad
  $('#btnNuevaActividad').click(function() {
    $('#modalNuevaActividad').modal('show');
  });

  // Guardar nueva actividad (aquí solo cerrar modal, el backend lo harás después)
  $('#btnGuardarActividad').click(function() {
    // Validación básica
    const titulo = $('#tituloActividad').val();
    const descripcion = $('#descripcionActividad').val();
    
    if (!titulo || !descripcion) {
      alert('Por favor completa todos los campos obligatorios');
      return;
    }

    // Aquí iría tu lógica de backend
    console.log('Guardar nueva actividad');
    
    // Cerrar modal y limpiar form
    $('#modalNuevaActividad').modal('hide');
    $('#formNuevaActividad')[0].reset();
    
    // Mostrar mensaje de éxito
    mostrarNotificacion('Actividad creada exitosamente', 'success');
  });
}

// ===== NOTIFICACIONES =====
function mostrarNotificacion(mensaje, tipo) {
  const iconos = {
    success: 'ri-check-line',
    error: 'ri-close-line',
    info: 'ri-information-line'
  };

  const colores = {
    success: '#4ade80',
    error: '#ef4444',
    info: '#60a5fa'
  };

  const notif = $(`
    <div class="notificacion-flotante" style="
      position: fixed;
      top: 20px;
      right: 20px;
      background: #11193a;
      border: 1px solid ${colores[tipo]};
      border-radius: 12px;
      padding: 16px 20px;
      display: flex;
      align-items: center;
      gap: 12px;
      z-index: 9999;
      box-shadow: 0 8px 24px rgba(0,0,0,0.3);
      animation: slideIn 0.3s ease;
    ">
      <i class="${iconos[tipo]}" style="font-size: 24px; color: ${colores[tipo]};"></i>
      <span style="color: #e6e9f4; font-weight: 500;">${mensaje}</span>
    </div>
  `);

  $('body').append(notif);

  setTimeout(() => {
    notif.fadeOut(300, function() {
      $(this).remove();
    });
  }, 3000);
}

// Animación para notificaciones
const style = document.createElement('style');
style.textContent = `
  @keyframes slideIn {
    from {
      transform: translateX(400px);
      opacity: 0;
    }
    to {
      transform: translateX(0);
      opacity: 1;
    }
  }
`;
document.head.appendChild(style);


// ===== TOGGLE ENTRE VISTA CARDS Y TABLA =====
function inicializarToggleVista() {
  $('.btn-view-toggle').click(function() {
    const vista = $(this).data('view');
    cambiarVista(vista);
  });
}

function cambiarVista(vista) {
  // Actualizar botones activos
  $('.btn-view-toggle').removeClass('active');
  $(`.btn-view-toggle[data-view="${vista}"]`).addClass('active');

  if (vista === 'cards') {
    // Mostrar cards, ocultar tabla
    $('#actividadesGrid').show();
    $('#actividadesTabla').hide();
  } else if (vista === 'table') {
    // Mostrar tabla, ocultar cards
    $('#actividadesGrid').hide();
    $('#actividadesTabla').show();
  }

  // Guardar preferencia en localStorage (opcional)
  localStorage.setItem('vistaActividades', vista);
}

// Aplicar filtros también a la tabla
function aplicarFiltrosTabla() {
  const estadoActivo = $('.filter-tab-actividad.active').data('estado');
  const periodoSeleccionado = $('#periodFilter').val();
  const cursoSeleccionado = $('#cursoFilter').val();
  const textoBusqueda = $('#searchActividades').val().toLowerCase();

  $('#tbodyActividades tr').each(function() {
    const $row = $(this);
    const estado = $row.data('estado');
    const periodo = $row.data('periodo');
    const curso = $row.data('curso');
    const titulo = $row.data('titulo') || '';
    const descripcion = $row.data('descripcion') || '';

    let cumpleEstado = (estadoActivo === 'todas') || (estado === estadoActivo);
    let cumplePeriodo = (periodoSeleccionado === 'todos') || (periodo == periodoSeleccionado);
    let cumpleCurso = (cursoSeleccionado === 'todos') || (curso === cursoSeleccionado);
    let cumpleBusqueda = (textoBusqueda === '') || 
                         titulo.includes(textoBusqueda) || 
                         descripcion.includes(textoBusqueda);

    if (cumpleEstado && cumplePeriodo && cumpleCurso && cumpleBusqueda) {
      $row.show();
    } else {
      $row.hide();
    }
  });

  verificarResultadosTabla();
}

function verificarResultadosTabla() {
  const visibles = $('#tbodyActividades tr:visible').length;
  
  if (visibles === 0 && $('#actividadesTabla').is(':visible')) {
    if (!$('#tbodyActividades .empty-row').length) {
      $('#tbodyActividades').append(`
        <tr class="empty-row">
          <td colspan="9" class="text-center" style="padding: 40px; color: #97a1b6;">
            <i class="ri-search-line" style="font-size: 48px; opacity: 0.5; display: block; margin-bottom: 12px;"></i>
            <strong style="font-size: 18px; color: #fff;">No se encontraron actividades</strong>
            <br>
            <small>Intenta cambiar los filtros o el término de búsqueda</small>
          </td>
        </tr>
      `);
    }
  } else {
    $('#tbodyActividades .empty-row').remove();
  }
}

// ===== BOTÓN VER ENTREGAS =====
$(document).on('click', '.btn-ver-entregas', function() {
  const idActividad = $(this).data('id');
  window.location.href = `/siademy/docente/ver-entregas?id_actividad=${idActividad}`;
});
