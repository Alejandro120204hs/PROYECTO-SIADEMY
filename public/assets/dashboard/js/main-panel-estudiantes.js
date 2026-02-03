// ===============================
// SISTEMA TOGGLE SIDEBAR
// ===============================
const leftSidebar = document.getElementById('leftSidebar');
const appGrid = document.getElementById('appGrid');
const toggleLeft = document.getElementById('toggleLeft');

let leftVisible = localStorage.getItem('leftSidebarVisible') !== 'false';

function updateGridState() {
  appGrid.classList.toggle('hide-left', !leftVisible);
}

function toggleLeftSidebar() {
  leftVisible = !leftVisible;
  leftSidebar.classList.toggle('hidden', !leftVisible);
  localStorage.setItem('leftSidebarVisible', leftVisible);
  updateGridState();
}

toggleLeft?.addEventListener('click', toggleLeftSidebar);
updateGridState();


// ===============================
// DATATABLE UNIVERSAL
// ===============================
$(document).ready(function () {

  if (!$('#tablaEstudiantes').length) return;

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

    // ✅ SIEMPRE FUNCIONA
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
