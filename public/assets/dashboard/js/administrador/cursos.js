var tablaCursosInited = false;

document.querySelectorAll('.view-btn').forEach(function (btn) {
  btn.addEventListener('click', function () {
    document.querySelectorAll('.view-btn').forEach(function (b) {
      b.classList.remove('active');
    });

    this.classList.add('active');
    var view = this.dataset.view;
    var grid = document.getElementById('cursosGrid');
    var tabla = document.getElementById('cursosTabla');

    if (view === 'list') {
      grid.style.display = 'none';
      tabla.style.display = 'block';

      if (!tablaCursosInited) {
        $('#tablaCursos').DataTable({
          language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
          },
          pageLength: 10,
          responsive: true
        });
        tablaCursosInited = true;
      }
    } else {
      tabla.style.display = 'none';
      grid.style.display = '';
    }
  });
});
