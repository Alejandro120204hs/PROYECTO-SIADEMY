<?php 
  require_once BASE_PATH . '/app/helpers/session_administrador.php';

  // ENLAZAMOS LA DEPENDENCIA, EN ESTE CASO EL CONTROLADOR QUE TIENE LA FUNCION DE COSULTAR LOS DATOS
  require_once BASE_PATH . '/app/controllers/administrador/acudiente.php';
  require_once BASE_PATH . '/app/controllers/administrador/estudiante_controller.php';


  // LLAMAMOS LA FUNCION ESPECIFICA QUE EXISTE EN DICHO CONTROLADOR
  $datos = mostrarAcudientes();
  // LLAMAMOS EL ID QUE VIENE ATRAVEZ DEL METODO GET
  $id = $_GET['id'];
  $estudiante = mostrarEstudianteId($id);


?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • Formulario • Estudiantes</title>
    <!-- CSS de Choices.js (colócalo en <head> o antes de tu CSS principal) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

    <?php
        include_once __DIR__ . '/../../layouts/header_coordinador.php'
    ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-tabla-formulario.css">
    


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
                    <div class="title">Editar Estudiante</div>
                    
                </div>

                <div class="user">
                    <button class="btn" title="Notificaciones"><i class="ri-notification-3-line"></i></button>
                    <button class="btn" title="Configuración"><i class="ri-settings-3-line"></i></button>
                    <div class="avatar" title="Diego A.">DA</div>
                </div>
            </div>
            <div class="subtitulo"><p>Formulario de Actualizar, Completa los siguientes pasos para actualizar el estudiante en el sistema académico. <br> Al finalizar, revisa la información antes de confirmar la actualización y evitar errores en la base de datos institucional.</p></div>

            <!-- Formulario Wizard -->
            <div class="container-fluid py-3">

                <div class="wizard-progress">
                    <div id="stepIndicator1" class="active-step">Paso 1</div>
                    <div id="stepIndicator2">Paso 2</div>
                    <div id="stepIndicator3">Confirmar</div>
                </div>

                <form id="formWizard" action="<?= BASE_URL ?>/administrador/actualizar_estudiante" method="POST">
                    <input type="hidden" class="form-control" name="id" value="<?= $estudiante['id'] ?>" required>
                    <input type="text" class="form-control" name="id_usuario" value="<?= $estudiante['id_usuario'] ?>" hidden>
                    <input type="hidden" class="form-control" name="accion" value="actualizar" required >
                    <!-- Paso 1 -->
                    <div class="step active">
                        <div class="tabla-titulo mb-3">
                            <h5>Datos del Estudiante</h5>
                            
                        </div>

                        <div class="row g-3">
                            <!-- Foto -->
                            

                            <!-- Datos personales -->
                             <div class="col-md-1"></div>
                            <div class="col-md-5">
                              
                                <div class="mb-3">
                                    <label for="">Nombres*</label>
                                    <input type="text" class="form-control" name="nombres" value="<?= $estudiante['nombres'] ?>" required tabindex="2">
                                </div>
                                <div class="mb-3">
                                    <label for="">Tipo de Documento*</label>
                                    <select class="selector" name="tipo_documento" required tabindex="4">
                                        <option value="<?= $estudiante['tipo_documento'] ?>"><?= $estudiante['tipo_documento'] ?></option>
                                        <option value="CC">CC</option>
                                        <option value="TI">TI</option>
                                        <option value="CE">CE</option>
                                        <option value="OTRO">OTRO</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="">Fecha de nacimiento*</label>
                                    <div class="d-flex gap-2">
                                        <input type="date" class="form-control" value="<?= $estudiante['fecha_de_nacimiento'] ?>" name="fecha_nacimiento" required tabindex="6">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="">Estado</label>
                                    <select class="form-select" aria-label="Default select example" name="estado">
                                    <option value="<?= $estudiante['estado'] ?>"><?= $estudiante['estado'] ?></option>
                                    <option value="Activo">Activo</option>
                                    <option value="Inactivo">Inactivo</option>
                                </select>
                                </div>
                                

                            </div>

                            <!-- Apellidos y teléfono -->
                            <div class="col-md-5">
                                 <div class="mb-3">
                                    <label for="">Apellidos*</label>
                                    <input type="text" class="form-control" value="<?= $estudiante['apellidos'] ?>" name="apellidos" required tabindex="3">
                                </div>
                                <div class="mb-3 parte2">
                                    <label for="">N° Documento*</label>
                                    <input type="number" class="form-control" value="<?= $estudiante['documento'] ?>" name="documento" required tabindex="5">
                                </div>
                               
                                  <div class="mb-3">
                                    <label for="">Genero</label>
                                    <select class="selector" name="genero" required tabindex="7">
                                        <option value="<?= $estudiante['genero'] ?>"><?= $estudiante['genero'] ?></option>
                                        <option value="Masculino">Masculino</option>
                                        <option value="Femenino">Femenino</option>
                                        <option value="Otro">Otro</option>
                                      
                                    </select>
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
                            <h5>Datos de contaco</h5>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-1"></div>
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="">Email*</label>
                                    <input type="email" class="form-control" value="<?= $estudiante['correo'] ?>" name="correo" required tabindex="1">
                                </div>

                                 <div class="mb-3">
                                    <label for="">Ciudad</label>
                                    <input type="text" class="form-control" value="<?= $estudiante['ciudad'] ?>" name="ciudad" required tabindex="3">
                                </div>

                                <div class="mb-3">
                                     <label for="selectAcudiente">Acudiente</label>
                                    <select id="selectAcudiente" class="form-select" name="acudiente" required tabindex="5">
                                        <option value="<?= $estudiante['id_acudiente'] ?>" selected><?= $estudiante['nombres_acudiente']. ' - ' .$estudiante['apellidos_acudiente'] ?></option>
                                        <?php if (!empty($datos)): ?>
                                        <?php foreach ($datos as $acudiente): ?>
                                            <option value="<?= $acudiente['id'] ?>">
                                            <?= $acudiente['documento'] ?> - <?= $acudiente['nombres'] ?? '' ?>
                                            </option>
                                        <?php endforeach; ?>
                                        <?php else: ?>
                                        <option disabled>No hay acudientes registrados</option>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                
                            </div>

                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="">N° Teléfono*</label>
                                    <input type="number" class="form-control" value="<?= $estudiante['telefono'] ?>" name="telefono" required tabindex="2">
                                </div>

                                 <div class="mb-3">
                                    <label for="">Dirección</label>
                                    <input type="text" class="form-control" value="<?= $estudiante['direccion'] ?>" name="direccion" required tabindex="4">
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
                        <p>Revisa los datos ingresados antes de agregar el estudiante.</p>

                        <div class="botones mt-3">
                            <button type="button" class="btn btn-secondary" onclick="prevStep()">Anterior</button>
                            <button type="submit" class="btn btn-success">Actualizar Estudiante</button>
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

    <script src="<?=BASE_URL ?>/public/assets/dashboard/js/main-formulario.js"></script>
    <!-- JS de Choices.js (colócalo antes del cierre de body, tras otros scripts) -->
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

   <script>
document.addEventListener('DOMContentLoaded', function() {
  const select = document.getElementById('selectAcudiente');

  const allChoices = Array.from(select.querySelectorAll('option'))
    .filter(opt => opt.value !== '' && !opt.disabled)
    .map(opt => ({ value: opt.value, label: opt.textContent.trim() }));

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
    if (q.length === 0) { choices.clearChoices(); return; }
    const limit = 10;
    const filtered = allChoices
      .filter(c => c.label.toLowerCase().includes(q))
      .slice(0, limit);

    if (filtered.length > 0) {
      choices.setChoices(filtered, 'value', 'label', true);
    } else {
      choices.setChoices([{ value: '__no_results__', label: 'No se encontraron resultados', disabled: true }], 'value', 'label', true);
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


</body>

</html>