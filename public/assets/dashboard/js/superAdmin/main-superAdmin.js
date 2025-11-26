    // Sistema de toggle para sidebars con localStorage
    const leftSidebar = document.getElementById('leftSidebar');
    const rightSidebar = document.getElementById('rightSidebar');
    const appGrid = document.getElementById('appGrid');
    const toggleLeft = document.getElementById('toggleLeft');
    const toggleRight = document.getElementById('toggleRight');

    // Cargar estado desde localStorage
    let leftVisible = localStorage.getItem('leftSidebarVisible') !== 'false';
    let rightVisible = localStorage.getItem('rightSidebarVisible') === 'true';





    function updateGridState() {
      appGrid.classList.remove('hide-left', 'hide-right', 'hide-both');

      if (!leftVisible && !rightVisible) {
        appGrid.classList.add('hide-both');
      } else if (!leftVisible) {
        appGrid.classList.add('hide-left');
      } else if (!rightVisible) {
        appGrid.classList.add('hide-right');
      }
    }

    function toggleLeftSidebar() {
      leftVisible = !leftVisible;
      leftSidebar.classList.toggle('hidden', !leftVisible);
      localStorage.setItem('leftSidebarVisible', leftVisible);
      updateGridState();
    }

    function toggleRightSidebar() {
      rightVisible = !rightVisible;
      rightSidebar.classList.toggle('hidden', !rightVisible);
      localStorage.setItem('rightSidebarVisible', rightVisible);
      updateGridState();
    }

    // Event listeners
    toggleLeft.addEventListener('click', toggleLeftSidebar);
    toggleRight.addEventListener('click', toggleRightSidebar);

    // Aplicar estado inicial
    if (!leftVisible) leftSidebar.classList.add('hidden');
    if (!rightVisible) rightSidebar.classList.add('hidden');
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
    const ctx = document.getElementById('institutionsChart').getContext('2d');
    const chartTypeSelect = document.getElementById('chartType');
    
    // Datos de comparativa anual
    const data = {
        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        datasets: [
            {
                label: 'Año Actual',
                data: [8, 10, 12, 14, 16, 18, 19, 20, 21, 22, 23, 24],
                backgroundColor: 'rgba(79, 70, 229, 0.8)',
                borderColor: '#4f46e5',
                borderWidth: 2,
                tension: 0.4,
                fill: false
            },
            {
                label: 'Año Anterior',
                data: [10, 11, 11, 12, 12, 13, 13, 14, 14, 15, 15, 16],
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
    chartTypeSelect.addEventListener('change', function() {
        chart.config.type = this.value;
        chart.update();
    });
}

// Calcular métricas de crecimiento
function calculateGrowthMetrics() {
    const currentYearTotal = 24; // Total año actual
    const previousYearTotal = 16; // Total año anterior
    
    const growthPercentage = ((currentYearTotal - previousYearTotal) / previousYearTotal * 100).toFixed(1);
    const newInstitutions = currentYearTotal - previousYearTotal;
    
    console.log(`Crecimiento: +${growthPercentage}%`);
    console.log(`Nuevas instituciones: +${newInstitutions}`);
}

// Inicializar cuando el documento esté listo
document.addEventListener('DOMContentLoaded', function() {
    initializeInstitutionsChart();
    calculateGrowthMetrics();
});

// También inicializar con jQuery para compatibilidad
$(document).ready(function() {
    initializeInstitutionsChart();
});