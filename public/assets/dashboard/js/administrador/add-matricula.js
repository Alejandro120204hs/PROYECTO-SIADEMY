$(document).ready(function () {
    $('.select2').select2({
        theme: 'default',
        width: '100%',
        placeholder: function () {
            return $(this).data('placeholder') || 'Seleccione una opcion...';
        }
    });

    $('#selectCurso').on('change', function () {
        const selectedOption = this.options[this.selectedIndex];

        if (selectedOption.value) {
            document.getElementById('infoCursoGrado').textContent = selectedOption.dataset.grado + '°';
            document.getElementById('infoCursoNivel').textContent = selectedOption.dataset.nivel;
            document.getElementById('infoCursoJornada').textContent = selectedOption.dataset.jornada;
            document.getElementById('infoCursoDocente').textContent = selectedOption.dataset.docente;
            document.getElementById('infoCursoCupo').textContent = selectedOption.dataset.cupo + ' estudiantes';

            $('#cursoInfo').addClass('active');
        } else {
            $('#cursoInfo').removeClass('active');
        }
    });

    const form = document.getElementById('formMatricula');
    const hasCursoPreseleccionado = form && form.dataset.cursoPreseleccionado === '1';

    if (hasCursoPreseleccionado) {
        $('#selectCurso').trigger('change');

        $('#selectCurso').parent().find('.select2-container').css({
            border: '2px solid #10b981',
            'border-radius': '10px',
            'box-shadow': '0 0 0 3px rgba(16, 185, 129, 0.1)'
        });

        setTimeout(function () {
            $('#selectCurso').parent().find('.select2-container').css({
                border: '',
                'box-shadow': ''
            });
        }, 3000);
    }

    $('#formMatricula').on('submit', function (e) {
        const estudiante = $('#selectEstudiante').val();
        const curso = $('#selectCurso').val();

        if (!estudiante || !curso) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Campos incompletos',
                text: 'Por favor seleccione un estudiante y un curso',
                confirmButtonColor: '#4f46e5'
            });
        }
    });
});
