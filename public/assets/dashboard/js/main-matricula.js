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