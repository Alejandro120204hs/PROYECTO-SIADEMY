<?php 
  require_once BASE_PATH . '/app/helpers/session_administrador.php';
    //ENLAZAMOS LA DEPENDENCIA DEL CONTROLADOR QUE TIENE LA FUNCION PARA MOSTRAR LOS DATOS
    require_once BASE_PATH . '/app/controllers/administrador/asignatura.php';

    // LLAMAMOS EL ID QUE VIENE ATRAVEZ DEL METODO GET
    $id = $_GET['id'];
    // LLAMAMOS LA FUNCION ESPECIFICA DEL CONTROLADOR
    $asignatura = mostrarAsignaturaId($id);

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
                    <div class="title">Agregar Asignatura</div>
                    
                </div>

                <div class="user">
                    <button class="btn" title="Notificaciones"><i class="ri-notification-3-line"></i></button>
                    <button class="btn" title="Configuración"><i class="ri-settings-3-line"></i></button>
                    <div class="avatar" title="Diego A.">DA</div>
                </div>
            </div>
            <div class="subtitulo"><p>Formulario de registro, Completa los siguientes pasos para registrar una nueva Asignatura en el sistema académico. <br> Al finalizar, revisa la información antes de confirmar el registro para evitar errores en la base de datos institucional.</p></div>

            <!-- Formulario Wizard -->
            <div class="container-fluid py-3">

                <div class="wizard-progress">
                    <div id="stepIndicator1" class="active-step">Paso 1</div>
                    <div id="stepIndicator3">Confirmar</div>
                </div>

                <form id="formWizard" action="<?= BASE_URL ?>/administrador/actualizar-asignatura" method="POST" enctype="multipart/form-data">
                    <input type="hidden" class="form-control" name="id" value="<?= $asignatura['id'] ?>" required>
                    <input type="hidden" class="form-control" name="accion" value="actualizar" required >

                    <!-- Paso 1 -->
                    <div class="step active">
                        <div class="tabla-titulo mb-3">
                            <h5>Datos de la Asignatura</h5>
                            
                        </div>

                        <div class="row g-3">
                            <!-- Materia -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="">Nombre</label>
                                    <input type="text" class="form-control" name="nombre" value="<?= $asignatura['nombre'] ?>" required tabindex="1">
                                </div>                            
                            </div>

                            <!-- Descripcion -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="">Descripcion</label>
                                    <input type="text" class="form-control" name="descripcion" value="<?= $asignatura['descripcion'] ?>" tabindex="2">
                                </div>
                            </div>

                            <!-- Estado -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="">Estado</label>
                                    <select class="form-select" aria-label="Default select example" name="estado">
                                    <option value="<?= $asignatura['estado'] ?>"><?= $asignatura['estado'] ?></option>
                                    <option value="Activo">Activo</option>
                                    <option value="Inactivo">Inactivo</option>
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
                        <p>Revisa los datos ingresados antes de registrar la Asignatura.</p>

                        <div class="botones mt-3">
                            <button type="button" class="btn btn-secondary" onclick="prevStep()">Anterior</button>
                            <button type="submit" class="btn btn-success">Actualizar Asignatura</button>
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
</body>

</html>