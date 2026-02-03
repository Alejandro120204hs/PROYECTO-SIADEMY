<?php 
    require_once BASE_PATH . '/app/helpers/session_administrador.php';
    require_once BASE_PATH . '/app/controllers/administrador/docente.php';
    require_once BASE_PATH . '/app/controllers/administrador/nivel_academico.php';
    
    $datos = mostrarDocentes();
    $nivel = mostrarNivelAcademico();


?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • Formulario • Acudiente</title>
    <?php
        include_once __DIR__ . '/../../layouts/header_coordinador.php'
    ?>
      <!-- CSS de Choices.js (colócalo en <head> o antes de tu CSS principal) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-tabla-formulario.css">

    <style>
                /* Igual altura */
    .select-similar {
        height: 45px;
        padding: 8px 12px;
        font-size: 15px;
        border-radius: 3px;
        border: 1px solid #ced4da;
        background-color: #fff;
        cursor: pointer;
    }

    /* Simular estilo de choices */
    .select-similar:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 3px rgba(13,110,253,.25);
    }

    /* Apariencia más parecida */
    .select-similar:hover {
        border-color: #999;
    }
</style>
</head>
<body>
    <div class="app" id="appGrid">
        <!-- LEFT SIDEBAR -->
   <?php
        include_once __DIR__ . '/../../layouts/sidebar_coordinador.php'
   ?>

        <!-- MAIN -->
        <main class="main">
            <div class="topbar">
                <div class="topbar-left">
                    <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
                        <i class="ri-menu-2-line"></i>
                    </button>
                    <div class="title">Agregar Curso</div>
                    
                </div>

                <div class="user">
                    <button class="btn" title="Notificaciones"><i class="ri-notification-3-line"></i></button>
                    <button class="btn" title="Configuración"><i class="ri-settings-3-line"></i></button>
                    <div class="avatar" title="Diego A.">DA</div>
                </div>
            </div>
            <div class="subtitulo"><p>Formulario de registro, Completa los siguientes pasos para registrar un nuevo Curso en el sistema académico. <br> Al finalizar, revisa la información antes de confirmar el registro para evitar errores en la base de datos institucional.</p></div>

            <!-- Formulario Wizard -->
            <div class="container-fluid py-3">

                <div class="wizard-progress">
                    <div id="stepIndicator1" class="active-step">Paso 1</div>
                    <div id="stepIndicator3">Confirmar</div>
                </div>

                <form id="formWizard" action="<?= BASE_URL ?>/administrador/guardar-curso" method="POST">

                    <!-- Paso 1 -->
                    <div class="step active">
                        <div class="tabla-titulo mb-3">
                            <h5>Datos del Curso</h5>
                            
                        </div>

                        <div class="row g-3">
                            <!-- Materia -->
                             <div class="col-md-1"></div>
                            <div class="col-md-5">
                                    <div class="mb-3">
                                         <label for="">Grado</label>
                                        <select class="selector" name="grado"  tabindex="1">
                                        <option selected>Seleccione un Grado</option>
                                        <option value="1">Primero</option>
                                        <option value="2">Segundo</option>
                                        <option value="3">Tercero</option>
                                        <option value="4">Cuarto</option>
                                        <option value="5">Quinto</option>
                                        <option value="6">Sexto</option>
                                        <option value="7">Septimo</option>
                                        <option value="8">Octavo</option>
                                        <option value="9">Noveno</option>
                                        <option value="10">Decimo</option>
                                        <option value="11">Once</option>
                                        <option value="12">Doce</option>
                                        <option value="13">Trece</option>

                                        </select>
                                    </div>

                                     <div class="mb-3">
                                        <label for="selectAcudiente">Docente</label>
                                        <select id="selectAcudiente" class="form-select"name="docente" required>
                                            <option value="" selected disabled>Escriba el nombre del docente</option>
                                            <?php if (!empty($datos)): ?>
                                            <?php foreach ($datos as $do): ?>
                                                <option value="<?= $do['id'] ?>">
                                                <?= $do['nombres'] ?> 
                                                </option>
                                            <?php endforeach; ?>
                                            <?php else: ?>
                                            <option disabled>No hay docentes registrados</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>

                                     <div class="mb-3">
                                    <label for="">Cupo</label>
                                    <input type="number" class="form-control" name="cupo"  tabindex="5">
                                </div>
                                </div>

                            <!-- Descripcion -->
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="">Curso</label>
                                    <input type="text" class="form-control" name="curso" placeholder="Ej: 601" tabindex="2">
                                </div>

                                 <div class="mb-3">
                                        <label for="selectAcudiente">Nivel academico</label>
                                        <select id="selectNivel" class="selector" tabindex="4" name="nivel" required>
                                            <option value="" selected disabled>Seleccione un nivel académico</option>
                                            <?php foreach ($nivel as $ni): ?>
                                                <option value="<?= $ni['id'] ?>">
                                                    <?= $ni['nombre'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                     <div class="mb-3">
                                         <label for="">Jornada</label>
                                        <select class="selector" name="jornada"  tabindex="6">
                                        <option selected>Seleccione una jornada</option>
                                        <option value="Mañana">Mañana</option>
                                        <option value="Tarde">Tarde</option>
                                        <option value="Noche">Noche</option>
                                        <option value="Fin_semana">Fin de semana</option>
                                       
                                        
                                        </select>
                                    </div>
                            </div>
                        </div>

                        <div class="botones mt-3">
                            <button type="button" class="btn btn-primary" onclick="nextStep()">Siguiente</button>
                        </div>
                    </div>

                    <!-- Paso 3 -->
                    <div class="step">
                        <div class="tabla-titulo mb-3">
                            <h5>Confirmar Registro</h5>
                        </div>
                        <p>Revisa los datos ingresados antes de registrar El Curso.</p>

                        <div class="botones mt-3">
                            <button type="button" class="btn btn-secondary" onclick="prevStep()">Anterior</button>
                            <button type="submit" class="btn btn-success">Agregar Curso</button>
                        </div>
                    </div>

                </form>
            </div>
        </main>
    </div>

    <!-- FOOTER -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="<?=BASE_URL ?>/public/assets/dashboard/js/main-formulario.js"></script>
    <script>
document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('selectAcudiente');

    // Todas las opciones reales
    const allChoices = Array.from(select.querySelectorAll('option'))
        .filter(opt => opt.value !== '' && !opt.disabled)
        .map(opt => ({
            value: opt.value,
            label: opt.textContent.trim()
        }));

    const choices = new Choices(select, {
        searchEnabled: true,
        shouldSort: false,
        placeholder: true,
        placeholderValue: 'Escriba el nombre del docente',
        itemSelectText: '',
        removeItemButton: false,
        choices: [],
        position: 'bottom'
    });

    /* ===========================
       TABINDEX REAL (CLAVE)
    =========================== */
    setTimeout(() => {
    const choicesWrapper = select.closest('.choices');
    if (!choicesWrapper) return;

    const inner = choicesWrapper.querySelector('.choices__inner');
    const input = choicesWrapper.querySelector('input');

    if (inner) {
        inner.setAttribute('tabindex', '3');
    }

    if (input) {
        input.setAttribute('tabindex', '-1'); // evita doble foco
    }
}, 50);


    /* ===========================
       MOSTRAR 4 AL ABRIR
    =========================== */
    select.addEventListener('showDropdown', function () {
        choices.clearChoices();
        choices.setChoices(allChoices.slice(0, 4), 'value', 'label', true);
    });

    /* ===========================
       BUSCAR ESCRIBIENDO
    =========================== */
    select.addEventListener('search', function (event) {
        const q = event.detail.value.trim().toLowerCase();
        choices.clearChoices();

        if (q.length === 0) {
            choices.setChoices(allChoices.slice(0, 4), 'value', 'label', true);
            return;
        }

        const filtered = allChoices
            .filter(c => c.label.toLowerCase().includes(q))
            .slice(0, 10);

        if (filtered.length > 0) {
            choices.setChoices(filtered, 'value', 'label', true);
        } else {
            choices.setChoices([
                { value: '__no_results__', label: 'No se encontraron resultados', disabled: true }
            ], 'value', 'label', true);
        }
    });

    /* ===========================
       BLOQUEAR "NO RESULTADOS"
    =========================== */
    select.addEventListener('choice', function (event) {
        if (event.detail.choice?.value === '__no_results__') {
            event.preventDefault();
            choices.removeActiveItems();
        }
    });
});
</script>

</body>

</html>