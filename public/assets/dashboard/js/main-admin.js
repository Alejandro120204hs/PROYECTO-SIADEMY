// ========================================
// SISTEMA DE TOGGLE PARA SIDEBAR IZQUIERDO
// ========================================
const leftSidebar = document.getElementById('leftSidebar');
const appGrid = document.getElementById('appGrid');
const toggleLeft = document.getElementById('toggleLeft');

let leftVisible = localStorage.getItem('leftSidebarVisible') !== 'false';

function updateGridState() {
    appGrid.classList.remove('hide-left');
    if (!leftVisible) {
        appGrid.classList.add('hide-left');
    }
}

function toggleLeftSidebar() {
    leftVisible = !leftVisible;
    leftSidebar.classList.toggle('hidden', !leftVisible);
    localStorage.setItem('leftSidebarVisible', leftVisible);
    updateGridState();
}

if (toggleLeft) toggleLeft.addEventListener('click', toggleLeftSidebar);

if (!leftVisible && leftSidebar) leftSidebar.classList.add('hidden');
updateGridState();


// ========================================
// GRÁFICO (solo si existe)
// ========================================
const ctx = document.getElementById('lineChart');
if (ctx) {
    const gradient1 = ctx.getContext('2d').createLinearGradient(0, 0, 0, 320);
    gradient1.addColorStop(0, 'rgba(255,107,107,.35)');
    gradient1.addColorStop(1, 'rgba(255,107,107,0)');

    const gradient2 = ctx.getContext('2d').createLinearGradient(0, 0, 0, 320);
    gradient2.addColorStop(0, 'rgba(255,176,32,.35)');
    gradient2.addColorStop(1, 'rgba(255,176,32,0)');

    const data = {
        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        datasets: [{
            label: 'Esta semana',
            data: [20, 35, 55, 25, 15, 48, 62, 30, 22, 70, 85, 58],
            borderColor: '#ff6b6b',
            backgroundColor: gradient1,
            borderWidth: 3,
            pointRadius: 5,
            pointBackgroundColor: '#ff6b6b',
            tension: .45,
            fill: true
        }, {
            label: 'La semana pasada',
            data: [5, 28, 90, 12, 10, 40, 60, 35, 45, 68, 70, 60],
            borderColor: '#ffb020',
            backgroundColor: gradient2,
            borderWidth: 3,
            pointRadius: 0,
            tension: .45,
            fill: true
        }]
    };

    new Chart(ctx, {
        type: 'line',
        data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    enabled: true,
                    backgroundColor: '#0e142e',
                    borderColor: 'rgba(255,255,255,.1)',
                    borderWidth: 1,
                    titleColor: '#fff',
                    bodyColor: '#e5e7eb'
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(255,255,255,.06)' },
                    ticks: { color: '#cbd5e1' }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255,255,255,.06)' },
                    ticks: { color: '#cbd5e1' }
                }
            }
        }
    });
}


// ========================================
// DATATABLE (solo si existe)
// ========================================
$(document).ready(function() {
    if ($('#studentsTable').length) {
        $('#studentsTable').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
            pageLength: 5,
            lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
            ordering: true,
            order: [[0, 'asc']],
            pagingType: 'simple_numbers'
        });
    }
});


// ========================================
// TABS - DETALLE ESTUDIANTE (SIN RIGHT SIDEBAR)
// ========================================
document.addEventListener('DOMContentLoaded', function() {

    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.dataset.tab;

            tabButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            tabPanes.forEach(pane => pane.classList.remove('active'));
            const targetPane = document.getElementById(targetTab);
            if (targetPane) targetPane.classList.add('active');
        });
    });

    // QUICK ACTIONS
    document.querySelectorAll('.quick-action-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            console.log('Acción:', btn.textContent.trim());
        });
    });

    // PRINT
    const printBtn = document.querySelector('.btn-secondary-action');
    if (printBtn) {
        printBtn.addEventListener('click', () => window.print());
    }

    // EDIT
    const editBtn = document.querySelector('.btn-primary-action');
    if (editBtn) {
        editBtn.addEventListener('click', () => {
            console.log('Editar perfil');
        });
    }

});


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