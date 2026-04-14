    // Sistema de toggle para sidebars con localStorage
    const leftSidebar = document.getElementById('leftSidebar');
    const rightSidebar = document.getElementById('rightSidebar');
    const appGrid = document.getElementById('appGrid');
    const toggleLeft = document.getElementById('toggleLeft');
    const toggleRight = document.getElementById('toggleRight');
    const hasRightSidebar = !!rightSidebar;

    // Cargar estado desde localStorage
    let leftVisible = localStorage.getItem('leftSidebarVisible') !== 'false';
    let rightVisible = hasRightSidebar && localStorage.getItem('rightSidebarVisible') === 'true';

    // ── Overlay para drawer en móvil ──────────────────────────
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    document.body.appendChild(overlay);

    function isMobile() { return window.innerWidth <= 768; }

    function openMobileDrawer() {
      if (!leftSidebar) return;
      leftSidebar.classList.add('mobile-open');
      leftSidebar.classList.remove('hidden');
      overlay.classList.add('active');
    }
    function closeMobileDrawer() {
      if (!leftSidebar) return;
      leftSidebar.classList.remove('mobile-open');
      leftSidebar.classList.add('hidden');
      overlay.classList.remove('active');
    }

    overlay.addEventListener('click', closeMobileDrawer);

    // Cerrar drawer al redimensionar a desktop
    window.addEventListener('resize', () => {
      if (!isMobile()) {
        overlay.classList.remove('active');
        if (leftSidebar) leftSidebar.classList.remove('mobile-open');
      }
    });


    function updateGridState() {
      if (!appGrid) return;
      appGrid.classList.remove('hide-left', 'hide-right', 'hide-both');
      if (isMobile()) return; // en móvil el grid siempre es 1fr

      if (!hasRightSidebar) {
        if (!leftVisible) {
          appGrid.classList.add('hide-both');
        } else {
          appGrid.classList.add('hide-right');
        }
        return;
      }

      if (!leftVisible && !rightVisible) {
        appGrid.classList.add('hide-both');
      } else if (!leftVisible) {
        appGrid.classList.add('hide-left');
      } else if (!rightVisible) {
        appGrid.classList.add('hide-right');
      }
    }

    function toggleLeftSidebar() {
      if (isMobile()) {
        // Comportamiento drawer
        const isOpen = leftSidebar.classList.contains('mobile-open');
        isOpen ? closeMobileDrawer() : openMobileDrawer();
        return;
      }
      leftVisible = !leftVisible;
      leftSidebar.classList.toggle('hidden', !leftVisible);
      localStorage.setItem('leftSidebarVisible', leftVisible);
      updateGridState();
    }

    function toggleRightSidebar() {
      if (!rightSidebar) return;
      rightVisible = !rightVisible;
      rightSidebar.classList.toggle('hidden', !rightVisible);
      localStorage.setItem('rightSidebarVisible', rightVisible);
      updateGridState();
    }

    // Event listeners
    if (toggleLeft) {
      toggleLeft.addEventListener('click', toggleLeftSidebar);
    }
    if (toggleRight) {
      toggleRight.addEventListener('click', toggleRightSidebar);
    }

    // Aplicar estado inicial
    if (leftSidebar && !leftVisible) leftSidebar.classList.add('hidden');
    if (rightSidebar && !rightVisible) rightSidebar.classList.add('hidden');
    if (!hasRightSidebar) localStorage.setItem('rightSidebarVisible', 'false');
    updateGridState();

    // Inicializar DataTables
    $(document).ready(function() {
      // Tabla de Estado de Escuelas
      $('#statusTable').DataTable({
        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        pageLength: 10,
        order: [[0, 'asc']]
      });

      // Tabla de Gestión de Pagos
      $('#paymentsTable').DataTable({
        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        pageLength: 10,
        order: [[0, 'asc']]
      });

      // Función para activar/bloquear escuelas
      $(document).on('click', '.toggle-status', function() {
        const btn = $(this);
        const row = btn.closest('tr');
        const statusBadge = row.find('.status-badge');
        const currentStatus = btn.data('status');
        const schoolId = row.data('id');
        const schoolName = row.find('strong').text();

        if (currentStatus === 'active') {
          // Bloquear escuela
          if (confirm(`¿Estás seguro de bloquear "${schoolName}"?\n\nLa escuela perderá acceso al sistema hasta que sea reactivada.`)) {
            statusBadge.removeClass('bg-success').addClass('bg-danger').text('Bloqueado');
            btn.removeClass('btn-danger').addClass('btn-success');
            btn.data('status', 'blocked');
            btn.html('<i class="ri-lock-unlock-line"></i> Activar');
            
            // Mostrar notificación
            alert(`✅ Escuela "${schoolName}" bloqueada correctamente.`);
          }
        } else {
          // Activar escuela
          if (confirm(`¿Estás seguro de activar "${schoolName}"?\n\nLa escuela recuperará acceso completo al sistema.`)) {
            statusBadge.removeClass('bg-danger').addClass('bg-success').text('Activo');
            btn.removeClass('btn-success').addClass('btn-danger');
            btn.data('status', 'active');
            btn.html('<i class="ri-lock-line"></i> Bloquear');
            
            // Mostrar notificación
            alert(`✅ Escuela "${schoolName}" activada correctamente.`);
          }
        }
      });
    });



    // Inicializar gráfico de comparativa anual
function initializeInstitutionsChart() {
        const canvas = document.getElementById('institutionsChart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
    const chartTypeSelect = document.getElementById('chartType');
        const chartSource = window.superAdminChartData || {};
        const labels = Array.isArray(chartSource.labels) && chartSource.labels.length === 12
          ? chartSource.labels
          : ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        const serieActual = Array.isArray(chartSource.serieActual) && chartSource.serieActual.length === 12
          ? chartSource.serieActual
          : Array(12).fill(0);
        const serieAnterior = Array.isArray(chartSource.serieAnterior) && chartSource.serieAnterior.length === 12
          ? chartSource.serieAnterior
          : Array(12).fill(0);
        const etiquetaActual = chartSource.anioActual ? `Ano ${chartSource.anioActual}` : 'Ano actual';
        const etiquetaAnterior = chartSource.anioAnterior ? `Ano ${chartSource.anioAnterior}` : 'Ano anterior';
    
    const data = {
            labels,
        datasets: [
            {
                    label: etiquetaActual,
                    data: serieActual,
                backgroundColor: 'rgba(79, 70, 229, 0.8)',
                borderColor: '#4f46e5',
                borderWidth: 2,
                tension: 0.4,
                fill: false
            },
            {
              label: etiquetaAnterior,
              data: serieAnterior,
                backgroundColor: 'rgba(148, 163, 184, 0.6)',
                borderColor: '#94a3b8',
                borderWidth: 2,
                borderDash: [5, 5],
                tension: 0.4,
                fill: false
            }
        ]
    };

    // Configuración del gráfico
    const config = {
        type: 'bar', // Tipo inicial
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        color: '#e6e9f4',
                        font: {
                            size: 12,
                            family: "'Poppins', sans-serif"
                        },
                        padding: 15,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(17, 25, 58, 0.95)',
                    titleColor: '#e6e9f4',
                    bodyColor: '#a4b1ff',
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return `${context.dataset.label}: ${context.parsed.y} instituciones`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#a4b1ff',
                        font: {
                            family: "'Poppins', sans-serif"
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#a4b1ff',
                        font: {
                            family: "'Poppins', sans-serif"
                        },
                        callback: function(value) {
                            return value + ' inst';
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            },
            animation: {
                duration: 1000,
                easing: 'easeOutQuart'
            }
        }
    };

    // Crear el gráfico
    const chart = new Chart(ctx, config);

    // Cambiar tipo de gráfico
    if (chartTypeSelect) {
      chartTypeSelect.addEventListener('change', function() {
        chart.config.type = this.value;
        chart.update();
      });
    }
}

// Inicializar cuando el documento esté listo
document.addEventListener('DOMContentLoaded', function() {
    initializeInstitutionsChart();
});

// Dropdown de perfil (igual al panel de administrador)
document.addEventListener('DOMContentLoaded', function() {
  const userMenuBtn = document.getElementById('userMenuBtn');
  const userDropdown = document.getElementById('userDropdown');

  if (!userMenuBtn || !userDropdown) return;

  let dropdownOverlay = document.querySelector('.dropdown-overlay');
  if (!dropdownOverlay) {
    dropdownOverlay = document.createElement('div');
    dropdownOverlay.className = 'dropdown-overlay';
    document.body.appendChild(dropdownOverlay);
  }

  userMenuBtn.addEventListener('click', function(e) {
    e.stopPropagation();
    const isOpen = userDropdown.classList.contains('show');
    if (isOpen) {
      closeDropdown();
    } else {
      openDropdown();
    }
  });

  dropdownOverlay.addEventListener('click', closeDropdown);

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && userDropdown.classList.contains('show')) {
      closeDropdown();
    }
  });

  userDropdown.addEventListener('click', function(e) {
    e.stopPropagation();
  });

  function openDropdown() {
    userDropdown.classList.add('show');
    dropdownOverlay.classList.add('show');
  }

  function closeDropdown() {
    userDropdown.classList.remove('show');
    dropdownOverlay.classList.remove('show');
  }

  const toggleThemeBtn = document.getElementById('toggleThemeBtn');
  if (toggleThemeBtn) {
    toggleThemeBtn.addEventListener('click', function(e) {
      e.preventDefault();
      document.body.classList.toggle('light-mode');

      const icon = this.querySelector('i:first-child');
      if (icon) {
        icon.className = document.body.classList.contains('light-mode')
          ? 'ri-sun-line'
          : 'ri-contrast-2-line';
      }

      const currentMode = document.body.classList.contains('light-mode') ? 'light' : 'dark';
      localStorage.setItem('theme-mode', currentMode);
    });

    const savedTheme = localStorage.getItem('theme-mode');
    if (savedTheme === 'light') {
      document.body.classList.add('light-mode');
      const icon = toggleThemeBtn.querySelector('i:first-child');
      if (icon) icon.className = 'ri-sun-line';
    }
  }
});