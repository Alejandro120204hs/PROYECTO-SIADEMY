$(document).ready(function () {
  // Inicializar todos los selects con Select2
  $('.select2').select2({
    theme: 'default',
    width: '100%',
    placeholder: function () {
      return $(this).data('placeholder') || 'Seleccione una opción...';
    }
  });

  // Si el curso está preseleccionado (viene de ?curso=), resaltar el campo brevemente
  const cursoSelect = document.getElementById('inputCurso');
  if (cursoSelect && cursoSelect.value !== '') {
    $('#inputCurso').next('.select2-container').find('.select2-selection').css({
      border: '2px solid #10b981',
      'box-shadow': '0 0 0 3px rgba(16, 185, 129, 0.1)'
    });

    setTimeout(function () {
      $('#inputCurso').next('.select2-container').find('.select2-selection').css({
        border: '',
        'box-shadow': ''
      });
    }, 3000);
  }
});
