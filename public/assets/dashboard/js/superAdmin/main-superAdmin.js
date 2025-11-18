    // Sistema de toggle para sidebars con localStorage
    const leftSidebar = document.getElementById('leftSidebar');
    const rightSidebar = document.getElementById('rightSidebar');
    const appGrid = document.getElementById('appGrid');
    const toggleLeft = document.getElementById('toggleLeft');
    const toggleRight = document.getElementById('toggleRight');

    // Cargar estado desde localStorage
    let leftVisible = localStorage.getItem('leftSidebarVisible') !== 'false';
    let rightVisible = localStorage.getItem('rightSidebarVisible') !== 'false';

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
