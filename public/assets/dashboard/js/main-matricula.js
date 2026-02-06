// Sistema de toggle para sidebars con localStorage
const leftSidebar = document.querySelector('.sidebar');
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
  if (leftSidebar) {
    leftSidebar.classList.toggle('hidden', !leftVisible);
  }
  localStorage.setItem('leftSidebarVisible', leftVisible);
  updateGridState();
}

// Event listeners
if (toggleLeft) {
  toggleLeft.addEventListener('click', toggleLeftSidebar);
}

// Aplicar estado inicial
if (!leftVisible && leftSidebar) {
  leftSidebar.classList.add('hidden');
}
updateGridState();

// Búsqueda en tiempo real
const searchInput = document.getElementById('searchMatricula');
if (searchInput) {
  searchInput.addEventListener('keyup', function() {
    const searchValue = this.value.toLowerCase();
    const rows = document.querySelectorAll('#tableMatriculas tbody tr');
    
    let visibleCount = 0;
    rows.forEach(row => {
      // Verificar si la fila tiene data-anio (para excluir la fila de "No hay matrículas")
      if (row.getAttribute('data-anio')) {
        const text = row.textContent.toLowerCase();
        const shouldShow = text.includes(searchValue);
        row.style.display = shouldShow ? '' : 'none';
        if (shouldShow) visibleCount++;
      }
    });
    
    // Actualizar contador si existe
    updateVisibleCount(visibleCount);
  });
}

// Filtros
function aplicarFiltros() {
  const anio = document.getElementById('filterAnio')?.value || '';
  const curso = document.getElementById('filterCurso')?.value || '';
  const nivel = document.getElementById('filterNivel')?.value || '';
  const rows = document.querySelectorAll('#tableMatriculas tbody tr');
  
  let visibleCount = 0;
  rows.forEach(row => {
    // Verificar si la fila tiene data-anio (para excluir la fila de "No hay matrículas")
    if (!row.getAttribute('data-anio')) {
      return;
    }

    const rowAnio = row.getAttribute('data-anio');
    const rowCurso = row.getAttribute('data-curso');
    const rowNivel = row.getAttribute('data-nivel');
    
    let mostrar = true;
    
    if (anio && rowAnio !== anio) mostrar = false;
    if (curso && rowCurso !== curso) mostrar = false;
    if (nivel && rowNivel !== nivel) mostrar = false;
    
    // También verificar búsqueda activa
    const searchValue = searchInput?.value.toLowerCase() || '';
    if (searchValue && !row.textContent.toLowerCase().includes(searchValue)) {
      mostrar = false;
    }
    
    row.style.display = mostrar ? '' : 'none';
    if (mostrar) visibleCount++;
  });
  
  updateVisibleCount(visibleCount);
}

// Actualizar contador de registros visibles
function updateVisibleCount(count) {
  const headerTitle = document.querySelector('.table-header h3');
  if (headerTitle) {
    const baseText = 'Listado de Matrículas';
    headerTitle.textContent = `${baseText} (${count})`;
  }
}

// Event listeners para filtros
const filterAnio = document.getElementById('filterAnio');
const filterCurso = document.getElementById('filterCurso');
const filterNivel = document.getElementById('filterNivel');

if (filterAnio) {
  filterAnio.addEventListener('change', aplicarFiltros);
}

if (filterCurso) {
  filterCurso.addEventListener('change', aplicarFiltros);
}

if (filterNivel) {
  filterNivel.addEventListener('change', aplicarFiltros);
}

// Función para confirmar eliminación (compatible con SweetAlert2)
function confirmarEliminacion(id) {
  if (typeof Swal !== 'undefined') {
    Swal.fire({
      title: '¿Estás seguro?',
      text: "Esta acción eliminará la matrícula del estudiante",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        // Obtener BASE_URL del meta tag o usar una variable global
        const baseUrl = document.querySelector('meta[name="base-url"]')?.content || 
                       window.BASE_URL || 
                       window.location.origin;
        window.location.href = `${baseUrl}/administrador/eliminar-matricula?id=${id}&accion=eliminar`;
      }
    });
  } else {
    // Fallback si SweetAlert no está disponible
    if (confirm('¿Estás seguro de que deseas eliminar esta matrícula?')) {
      const baseUrl = document.querySelector('meta[name="base-url"]')?.content || 
                     window.BASE_URL || 
                     window.location.origin;
      window.location.href = `${baseUrl}/administrador/eliminar-matricula?id=${id}&accion=eliminar`;
    }
  }
}

// Hacer la función global para que pueda ser llamada desde el HTML
window.confirmarEliminacion = confirmarEliminacion;

// Inicializar contador al cargar la página
document.addEventListener('DOMContentLoaded', function() {
  const totalRows = document.querySelectorAll('#tableMatriculas tbody tr[data-anio]').length;
  updateVisibleCount(totalRows);
  
  // Aplicar filtro de año actual si está preseleccionado
  if (filterAnio && filterAnio.value) {
    aplicarFiltros();
  }
});

// Prevenir scroll horizontal en el body
document.body.style.overflowX = 'hidden';


/* ========================================
   JAVASCRIPT PARA DROPDOWN DE USUARIO
   Agregar al final de main-admin.js o en un archivo separado
   ======================================== */

// Funcionalidad del dropdown de usuario
document.addEventListener('DOMContentLoaded', function() {
  const userMenuBtn = document.getElementById('userMenuBtn');
  const userDropdown = document.getElementById('userDropdown');
  
  // Crear overlay
  const overlay = document.createElement('div');
  overlay.className = 'dropdown-overlay';
  document.body.appendChild(overlay);
  
  // Toggle del dropdown
  if (userMenuBtn && userDropdown) {
    userMenuBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      const isOpen = userDropdown.classList.contains('show');
      
      if (isOpen) {
        closeDropdown();
      } else {
        openDropdown();
      }
    });
    
    // Cerrar al hacer click en el overlay
    overlay.addEventListener('click', closeDropdown);
    
    // Cerrar con tecla Escape
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && userDropdown.classList.contains('show')) {
        closeDropdown();
      }
    });
    
    // Prevenir cierre al hacer click dentro del dropdown
    userDropdown.addEventListener('click', function(e) {
      e.stopPropagation();
    });
  }
  
  // Funciones auxiliares
  function openDropdown() {
    userDropdown.classList.add('show');
    overlay.classList.add('show');
    
    // Animación suave de los items
    const items = userDropdown.querySelectorAll('.dropdown-item');
    items.forEach((item, index) => {
      item.style.opacity = '0';
      item.style.transform = 'translateX(-10px)';
      setTimeout(() => {
        item.style.transition = 'all 0.2s ease';
        item.style.opacity = '1';
        item.style.transform = 'translateX(0)';
      }, 50 * index);
    });
  }
  
  function closeDropdown() {
    userDropdown.classList.remove('show');
    overlay.classList.remove('show');
  }
  
  // Funcionalidad del botón de cambiar tema
  const toggleThemeBtn = document.getElementById('toggleThemeBtn');
  if (toggleThemeBtn) {
    toggleThemeBtn.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Aquí puedes implementar el cambio de tema
      document.body.classList.toggle('light-mode');
      
      // Cambiar icono según el modo
      const icon = this.querySelector('i:first-child');
      if (document.body.classList.contains('light-mode')) {
        icon.className = 'ri-sun-line';
      } else {
        icon.className = 'ri-contrast-2-line';
      }
      
      // Guardar preferencia en localStorage
      const currentMode = document.body.classList.contains('light-mode') ? 'light' : 'dark';
      localStorage.setItem('theme-mode', currentMode);
      
      // Mostrar notificación (opcional)
      showNotification('Tema cambiado correctamente');
    });
  }
  
  // Cargar tema guardado al iniciar
  const savedTheme = localStorage.getItem('theme-mode');
  if (savedTheme === 'light') {
    document.body.classList.add('light-mode');
    const icon = toggleThemeBtn?.querySelector('i:first-child');
    if (icon) icon.className = 'ri-sun-line';
  }
  
  // Función para mostrar notificaciones (opcional)
  function showNotification(message) {
    // Crear elemento de notificación
    const notification = document.createElement('div');
    notification.className = 'toast-notification';
    notification.innerHTML = `
      <i class="ri-check-line"></i>
      <span>${message}</span>
    `;
    notification.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      background: #10b981;
      color: white;
      padding: 14px 20px;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: 500;
      z-index: 10000;
      animation: slideInRight 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remover después de 3 segundos
    setTimeout(() => {
      notification.style.animation = 'slideOutRight 0.3s ease';
      setTimeout(() => notification.remove(), 300);
    }, 3000);
  }
});

// Animaciones para las notificaciones
const style = document.createElement('style');
style.textContent = `
  @keyframes slideInRight {
    from {
      transform: translateX(100%);
      opacity: 0;
    }
    to {
      transform: translateX(0);
      opacity: 1;
    }
  }
  
  @keyframes slideOutRight {
    from {
      transform: translateX(0);
      opacity: 1;
    }
    to {
      transform: translateX(100%);
      opacity: 0;
    }
  }
  
  .toast-notification i {
    font-size: 20px;
  }
`;
document.head.appendChild(style);