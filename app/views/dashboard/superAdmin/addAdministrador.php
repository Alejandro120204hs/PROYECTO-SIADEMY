<?php

// IMPORTAMOS LAS DEPENDECIAS NECESARIAS
require_once BASE_PATH . '/app/controllers/superAdmin/instituciones.php';

// LLAMAMOS LA FUNCION ESPECIFICA
$datos = mostrarInstituciones();

?>


<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • Formulario • Administradores</title>
    <?php
    include_once __DIR__ . '/../../layouts/header_coordinador.php'
    ?>
    <!-- CSS de Choices.js (colócalo en <head> o antes de tu CSS principal) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-tabla-formulario.css">

</head>

<body>
    <div class="app" id="appGrid">
        <!-- LEFT SIDEBAR -->
        <?php
        include_once __DIR__ . '/../../layouts/sidebar_superAdmin.php'
        ?>

        <!-- MAIN -->
        <main class="main">
            <div class="topbar">
                <div class="topbar-left">
                    <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
                        <i class="ri-menu-2-line"></i>
                    </button>
                    <div class="title">Agregar Administrador</div>

                </div>

                <div class="user">
                    <button class="btn" title="Notificaciones"><i class="ri-notification-3-line"></i></button>
                    <button class="btn" title="Configuración"><i class="ri-settings-3-line"></i></button>
                    <div class="avatar" title="Diego A.">DA</div>
                </div>
            </div>
            <div class="subtitulo">
                <p>
                    Estás a punto de registrar un nuevo administrador en la plataforma académica.
                    Por favor, completa cada paso con atención y asegúrate de que los datos ingresados sean correctos.
                    Al finalizar, revisa toda la información antes de confirmar el registro para garantizar la integridad de la base de datos institucional.
                </p>
            </div>


            <!-- Formulario Wizard -->
            <div class="container-fluid py-3">

                <div class="wizard-progress">
                    <div id="stepIndicator1" class="active-step">Paso 1</div>
                    <div id="stepIndicator2">Paso 2</div>
                    <div id="stepIndicator3">Confirmar</div>
                </div>

                <form id="formWizard" action="<?= BASE_URL ?>/superAdmin-registrar-administrador" method="POST" enctype="multipart/form-data">

                    <!-- Paso 1 -->
                    <div class="step active">
                        <div class="tabla-titulo mb-3">
                            <h5>Datos del administrador</h5>

                        </div>

                        <div class="row g-3">

                            <div class="col-md-3 poFoto">
                                <label for="">Foto*</label>
                                <div
                                    class=" esPhoto">
                                    <small>Selecciona un archivo</small>
                                    <input type="file" class="form-control mt-2" name="foto" accept=".jpg, .png, .jpeg, .svg, .gif" />
                                </div>
                            </div>

                            <!-- Datos personales -->
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="">Nombres</label>
                                    <input type="text" class="form-control" name="nombres" tabindex="1">
                                </div>
                                <div class="mb-3">
                                    <label for="">Documento</label>
                                    <input type="number" class="form-control" name="documento" tabindex="3">
                                </div>
                            </div>

                            <!-- Apellidos y teléfono -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="">Apellidos</label>
                                    <input type="text" class="form-control" name="apellidos" tabindex="2">
                                </div>

                                <div class="mb-3">
                                    <label for="">Edad</label>
                                    <input type="number" class="form-control" name="edad" tabindex="4">
                                </div>
                            </div>
                        </div>

                        <div class="botones mt-3">
                            <button type="button" class="btn btn-primary" onclick="nextStep()">Siguiente</button>
                        </div>
                    </div>

                    <!-- Paso 2 -->
                    <div class="step">
                        <div class="tabla-titulo mb-3">
                            <h5>Datos de Contacto</h5>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-1"></div>
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="">Correo</label>
                                    <input type="email" class="form-control" name="correo">
                                </div>
                                <div class="mb-3">
                                    <label for="">Telefono</label>
                                    <input type="number" class="form-control" name="telefono">
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="selectAcudiente">Institución</label>
                                    <select id="selectAcudiente" class="form-select" name="institucion" required>
                                        <option value="" selected disabled>Seleccione una institucion</option>
                                        <?php if (!empty($datos)): ?>
                                            <?php foreach ($datos as $institucion): ?>
                                                <option value="<?= $institucion['id'] ?>">
                                                    <?= $institucion['nombre'] ?> - <?= $institucion['direccion'] ?? '' ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option disabled>No hay instituciones registrados</option>
                                        <?php endif; ?>

                                    </select>
                                </div>

                            </div>
                        </div>

                        <div class="botones mt-3">
                            <button type="button" class="btn btn-secondary" onclick="prevStep()">Anterior</button>
                            <button type="button" class="btn btn-primary" onclick="nextStep()">Siguiente</button>
                        </div>
                    </div>

                    <!-- Paso 3 -->
                    <div class="step">
                        <div class="tabla-titulo mb-3">
                            <h5>Confirmar Registro</h5>
                        </div>
                        <p>Revisa los datos ingresados antes de agregar el administrador.</p>

                        <div class="botones mt-3">
                            <button type="button" class="btn btn-secondary" onclick="prevStep()">Anterior</button>
                            <button type="submit" class="btn btn-success">Agregar Administrador</button>
                        </div>
                    </div>

                </form>
            </div>
        </main>
    </div>

    <!-- Bootstrap and DataTables Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- JS de Choices.js (colócalo antes del cierre de body, tras otros scripts) -->
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const select = document.getElementById('selectAcudiente');

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
                placeholderValue: 'Escriba el número de documento del acudiente',
                itemSelectText: '',
                removeItemButton: false,
                choices: [],
                position: 'bottom' // <- fuerza siempre hacia abajo
            });

            select.addEventListener('showDropdown', function() {
                choices.clearChoices();
            });

            select.addEventListener('search', function(event) {
                const q = event.detail.value.trim().toLowerCase();
                if (q.length === 0) {
                    choices.clearChoices();
                    return;
                }
                const limit = 10;
                const filtered = allChoices
                    .filter(c => c.label.toLowerCase().includes(q))
                    .slice(0, limit);

                if (filtered.length > 0) {
                    choices.setChoices(filtered, 'value', 'label', true);
                } else {
                    choices.setChoices([{
                        value: '__no_results__',
                        label: 'No se encontraron resultados',
                        disabled: true
                    }], 'value', 'label', true);
                }
            });

            select.addEventListener('choice', function(event) {
                if (event.detail.choice && event.detail.choice.value === '__no_results__') {
                    event.preventDefault && event.preventDefault();
                    choices.removeActiveItems();
                }
            });
        });
    </script>

    <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-formulario.js"></script>
</body>

</html>