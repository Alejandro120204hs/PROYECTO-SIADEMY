$(document).ready(function () {
  $('.select2').select2({
    theme: 'default',
    width: '100%',
    placeholder: function () {
      return $(this).data('placeholder') || 'Seleccione una opcion...';
    }
  });

  const cursoInput = document.getElementById('inputCursoDisplay');
  if (cursoInput && cursoInput.dataset.cursoPreseleccionado === '1') {
    $('#inputCursoDisplay').css({
      border: '2px solid #10b981',
      'box-shadow': '0 0 0 3px rgba(16, 185, 129, 0.1)'
    });

    setTimeout(function () {
      $('#inputCursoDisplay').css({
        border: '',
        'box-shadow': ''
      });
    }, 3000);
  }
});
