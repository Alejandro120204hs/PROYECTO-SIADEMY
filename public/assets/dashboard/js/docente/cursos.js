/**
 * CURSOS DOCENTE - JAVASCRIPT (CORREGIDO Y OPTIMIZADO)
 * Funcionalidad para filtrado y búsqueda de cursos
 */

(function() {
  'use strict';

  // ===== ELEMENTOS DEL DOM =====
  const app = document.getElementById('appGrid');
  const toggleLeft = document.getElementById('toggleLeft');
  const leftSidebar = document.getElementById('leftSidebar');
  const courseFilter = document.getElementById('courseFilter');
  const searchInput = document.getElementById('searchInput');
  const courseCards = document.querySelectorAll('.curso-card');

  // ===== VERIFICACIÓN DE ELEMENTOS =====
  if (!app) {
    console.error('❌ No se encontró el elemento #appGrid');
    return;
  }

  // ===== TOGGLE SIDEBAR IZQUIERDO =====
  if (toggleLeft && leftSidebar) {
    toggleLeft.addEventListener('click', function() {
      leftSidebar.classList.toggle('hidden');
      app.classList.toggle('hide-left');
      
      // Guardar preferencia en localStorage
      try {
        localStorage.setItem('leftSidebarHidden', leftSidebar.classList.contains('hidden'));
      } catch (e) {
        console.warn('No se pudo guardar en localStorage:', e);
      }
    });
  }

  // Restaurar estado del sidebar desde localStorage
  if (leftSidebar) {
    try {
      if (localStorage.getItem('leftSidebarHidden') === 'true') {
        leftSidebar.classList.add('hidden');
        app.classList.add('hide-left');
      }
    } catch (e) {
      console.warn('No se pudo leer localStorage:', e);
    }
  }

  // ===== FUNCIONES DE FILTRADO =====
  
  /**
   * Filtra las tarjetas de cursos por grado
   * @param {string} grado - Grado a filtrar ('all', '10', '11', etc.)
   */
  function filterByGrado(grado) {
    if (!courseCards.length) return;
    
    let visibleCount = 0;

    courseCards.forEach(function(card) {
      const cardGrado = card.getAttribute('data-grado');
      
      if (grado === 'all' || cardGrado === grado) {
        showCard(card);
        visibleCount++;
      } else {
        hideCard(card);
      }
    });

    updateNoResultsMessage(visibleCount);
  }

  /**
   * Filtra las tarjetas de cursos por término de búsqueda
   * @param {string} searchTerm - Término de búsqueda
   */
  function filterBySearch(searchTerm) {
    if (!courseCards.length) return;
    
    const term = searchTerm.toLowerCase().trim();
    let visibleCount = 0;

    if (term === '') {
      // Si no hay término de búsqueda, mostrar todos según el filtro actual
      const currentFilter = courseFilter ? courseFilter.value : 'all';
      filterByGrado(currentFilter);
      return;
    }

    courseCards.forEach(function(card) {
      const courseName = card.querySelector('.curso-nombre')?.textContent.toLowerCase() || '';
      const courseIcon = card.querySelector('.curso-icon')?.textContent.toLowerCase() || '';
      const courseUbicacion = card.querySelector('.curso-ubicacion span')?.textContent.toLowerCase() || '';
      const courseJornada = card.querySelector('.curso-badge-jornada')?.textContent.toLowerCase() || '';
      const cursoCodigo = card.querySelector('.curso-codigo')?.textContent.toLowerCase() || '';

      // Buscar en todos los campos relevantes
      if (courseName.includes(term) || 
          courseIcon.includes(term) || 
          cursoCodigo.includes(term) ||
          courseUbicacion.includes(term) ||
          courseJornada.includes(term)) {
        showCard(card);
        visibleCount++;
      } else {
        hideCard(card);
      }
    });

    updateNoResultsMessage(visibleCount);
  }

  /**
   * Muestra una tarjeta de curso con animación
   * @param {HTMLElement} card - Elemento de tarjeta a mostrar
   */
  function showCard(card) {
    card.style.display = 'flex';
    requestAnimationFrame(function() {
      card.style.opacity = '1';
      card.style.transform = 'translateY(0)';
    });
  }

  /**
   * Oculta una tarjeta de curso con animación
   * @param {HTMLElement} card - Elemento de tarjeta a ocultar
   */
  function hideCard(card) {
    card.style.opacity = '0';
    card.style.transform = 'translateY(20px)';
    setTimeout(function() {
      card.style.display = 'none';
    }, 300);
  }

  /**
   * Actualiza o crea mensaje de "No hay resultados"
   * @param {number} visibleCount - Cantidad de tarjetas visibles
   */
  function updateNoResultsMessage(visibleCount) {
    const grid = document.querySelector('.cursos-grid');
    if (!grid) return;
    
    let noResultsMsg = document.getElementById('no-results-message');

    if (visibleCount === 0) {
      if (!noResultsMsg) {
        noResultsMsg = document.createElement('div');
        noResultsMsg.id = 'no-results-message';
        noResultsMsg.className = 'no-results-message';
        noResultsMsg.innerHTML = `
          <div class="no-results-content">
            <i class="ri-search-line"></i>
            <h3>No se encontraron cursos</h3>
            <p>Intenta con otros términos de búsqueda o filtros</p>
          </div>
        `;
        grid.parentNode.insertBefore(noResultsMsg, grid.nextSibling);
      }
      noResultsMsg.style.display = 'flex';
    } else {
      if (noResultsMsg) {
        noResultsMsg.style.display = 'none';
      }
    }
  }

  // ===== EVENT LISTENERS =====

  // Filtro por grado
  if (courseFilter) {
    courseFilter.addEventListener('change', function() {
      const grado = this.value;
      filterByGrado(grado);
      
      // Si hay texto en la búsqueda, aplicar ambos filtros
      if (searchInput && searchInput.value.trim() !== '') {
        filterBySearch(searchInput.value);
      }
    });
  }

  // Búsqueda en tiempo real
  if (searchInput) {
    searchInput.addEventListener('input', debounce(function() {
      filterBySearch(this.value);
    }, 300));
  }

  /**
   * Función debounce para optimizar la búsqueda
   * @param {Function} func - Función a ejecutar
   * @param {number} wait - Tiempo de espera en ms
   */
  function debounce(func, wait) {
    let timeout;
    return function executedFunction() {
      const context = this;
      const args = arguments;
      const later = function() {
        clearTimeout(timeout);
        func.apply(context, args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  }

  // ===== INICIALIZACIÓN =====
  
  /**
   * Inicializa los estilos de las tarjetas para animaciones
   */
  function initCardStyles() {
    if (!courseCards.length) {
      console.warn('⚠️ No se encontraron tarjetas de cursos');
      return;
    }

    courseCards.forEach(function(card) {
      card.style.opacity = '0';
      card.style.transform = 'translateY(20px)';
      card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
    });

    // Mostrar tarjetas con delay escalonado
    setTimeout(function() {
      courseCards.forEach(function(card, index) {
        setTimeout(function() {
          card.style.opacity = '1';
          card.style.transform = 'translateY(0)';
        }, index * 50);
      });
    }, 100);
  }

  // ===== RESPONSIVE - AJUSTES ADICIONALES =====
  
  function handleResize() {
    const width = window.innerWidth;
    
    // En móviles, ocultar sidebar por defecto
    if (width <= 980 && leftSidebar) {
      if (!leftSidebar.classList.contains('hidden')) {
        leftSidebar.classList.add('hidden');
        app.classList.add('hide-left');
      }
    }
  }

  // ===== ACCESIBILIDAD =====
  
  // Permitir navegación con teclado en las tarjetas
  courseCards.forEach(function(card) {
    card.setAttribute('tabindex', '0');
    
    card.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        const primaryBtn = card.querySelector('.btn-curso-primary');
        if (primaryBtn) {
          primaryBtn.click();
        }
      }
    });
  });

  // ===== ESTILO DINÁMICO PARA MENSAJE DE NO RESULTADOS =====
  const style = document.createElement('style');
  style.textContent = `
    .no-results-message {
      display: none;
      justify-content: center;
      align-items: center;
      padding: 60px 20px;
      margin: 20px 0;
      background: #11193a;
      border: 1px solid rgba(255, 255, 255, .08);
      border-radius: 18px;
      min-height: 300px;
    }

    .no-results-content {
      text-align: center;
      max-width: 400px;
    }

    .no-results-content i {
      font-size: 64px;
      color: #a4b1ff;
      margin-bottom: 20px;
      opacity: 0.5;
    }

    .no-results-content h3 {
      margin: 0 0 12px 0;
      font-size: 24px;
      font-weight: 700;
      color: #fff;
    }

    .no-results-content p {
      margin: 0;
      font-size: 15px;
      color: #97a1b6;
    }
  `;
  document.head.appendChild(style);

  // ===== EJECUTAR INICIALIZACIÓN =====
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
      initCardStyles();
      handleResize();
    });
  } else {
    initCardStyles();
    handleResize();
  }

  // Event listener para resize
  window.addEventListener('resize', debounce(handleResize, 250));

  // ===== CONSOLA DE DEPURACIÓN =====
  console.log('✅ Script de Cursos Docente cargado correctamente');
  console.log('📊 Total de cursos:', courseCards.length);
  
  // Debug: Mostrar los grados disponibles
  if (courseCards.length > 0) {
    const gradosDisponibles = [];
    courseCards.forEach(function(card) {
      const grado = card.getAttribute('data-grado');
      if (grado && !gradosDisponibles.includes(grado)) {
        gradosDisponibles.push(grado);
      }
    });
    console.log('📚 Grados disponibles:', gradosDisponibles.sort());
  }

})();