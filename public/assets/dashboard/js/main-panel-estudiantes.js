(() => {
  // ===============================
  // SISTEMA TOGGLE SIDEBAR
  // ===============================
  const leftSidebar = document.getElementById('leftSidebar');
  const appGrid = document.getElementById('appGrid');
  const toggleLeft = document.getElementById('toggleLeft');

  let leftVisible = localStorage.getItem('leftSidebarVisible') !== 'false';

  // ── Overlay para drawer en móvil ──────────────────────────
  const overlay = document.querySelector('.sidebar-overlay') || document.createElement('div');
  if (!overlay.parentElement) {
    overlay.className = 'sidebar-overlay';
    document.body.appendChild(overlay);
  }

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

  overlay.onclick = closeMobileDrawer;

  window.addEventListener('resize', () => {
    if (!isMobile()) {
      overlay.classList.remove('active');
      if (leftSidebar) leftSidebar.classList.remove('mobile-open');
    }
  });

  function updateGridState() {
    if (!appGrid) return;
    if (isMobile()) return; // en móvil el grid siempre es 1fr
    appGrid.classList.toggle('hide-left', !leftVisible);
  }

  function toggleLeftSidebar() {
    if (isMobile()) {
      const isOpen = leftSidebar && leftSidebar.classList.contains('mobile-open');
      isOpen ? closeMobileDrawer() : openMobileDrawer();
      return;
    }
    leftVisible = !leftVisible;
    if (leftSidebar) {
      leftSidebar.classList.toggle('hidden', !leftVisible);
    }
    localStorage.setItem('leftSidebarVisible', leftVisible);
    updateGridState();
  }

  if (toggleLeft) {
    toggleLeft.onclick = (event) => {
      event.preventDefault();
      toggleLeftSidebar();
    };
  }
  if (!isMobile() && !leftVisible && leftSidebar) leftSidebar.classList.add('hidden');
  updateGridState();


  // ===============================
  // DATATABLE UNIVERSAL
  // ===============================
  $(document).ready(function () {

    if (!$('#tablaEstudiantes').length) return;
    if ($.fn.DataTable.isDataTable('#tablaEstudiantes')) return;

    const tabla = $('#tablaEstudiantes').DataTable({
      language: {
        processing: "Procesando...",
        lengthMenu: "Mostrar _MENU_ registros",
        zeroRecords: "No se encontraron resultados",
        emptyTable: "Ningún dato disponible en esta tabla",
        info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
        infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
        infoFiltered: "(filtrado de un total de _MAX_ registros)",
        search: "Buscar:",
        paginate: {
          first: "Primero",
          last: "Último",
          next: "Siguiente",
          previous: "Anterior"
        }
      },
      pageLength: 5,
      ordering: true,
      searching: true,
      paging: true,
      info: true,
      autoWidth: false,
      responsive: false,
      columnDefs: [
        { orderable: false, targets: [0, -1] }
      ]
    });

    // ===============================
    // BUSCADOR SUPERIOR (Buscar Aquí)
    // ===============================
    $('.search input').on('keyup', function () {
      tabla.search(this.value).draw();
    });

    // ===============================
    // CHECKBOX GLOBAL
    // ===============================
    $('#selectAll').on('change', function () {
      $('.row-checkbox').prop('checked', this.checked);
    });

    $(document).on('change', '.row-checkbox', function () {
      $('#selectAll').prop(
        'checked',
        $('.row-checkbox:checked').length === $('.row-checkbox').length
      );
    });

  });

  // ===============================
  // DROPDOWN PERFIL
  // ===============================
  document.addEventListener('DOMContentLoaded', function() {
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userDropdown = document.getElementById('userDropdown');

    if (!userMenuBtn || !userDropdown) return;

    const dropdownOverlay = document.querySelector('.dropdown-overlay') || document.createElement('div');
    if (!dropdownOverlay.parentElement) {
      dropdownOverlay.className = 'dropdown-overlay';
      document.body.appendChild(dropdownOverlay);
    }

    function openDropdown() {
      userDropdown.classList.add('show');
      dropdownOverlay.classList.add('show');
    }

    function closeDropdown() {
      userDropdown.classList.remove('show');
      dropdownOverlay.classList.remove('show');
    }

    userMenuBtn.addEventListener('click', function(event) {
      event.preventDefault();
      event.stopPropagation();
      userDropdown.classList.contains('show') ? closeDropdown() : openDropdown();
    });

    dropdownOverlay.addEventListener('click', closeDropdown);

    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') closeDropdown();
    });

    userDropdown.addEventListener('click', function(event) {
      event.stopPropagation();
    });
  });
})();
