const estudianteSelect = new Choices('#selectEstudiante', {
    searchEnabled: true,
    searchPlaceholderValue: 'Buscar estudiante...',
    noResultsText: 'No se encontraron resultados',
    itemSelectText: 'Click para seleccionar'
});

const cursoSelect = new Choices('#selectCurso', {
    searchEnabled: true,
    searchPlaceholderValue: 'Buscar curso...',
    noResultsText: 'No se encontraron resultados',
    itemSelectText: 'Click para seleccionar'
});

document.getElementById('formEditarMatricula').addEventListener('submit', function (e) {
    const estudiante = document.getElementById('selectEstudiante').value;
    const curso = document.getElementById('selectCurso').value;

    if (!estudiante || !curso) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Campos incompletos',
            text: 'Por favor complete todos los campos requeridos'
        });
    }
});
