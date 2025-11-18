<?php 
    require_once BASE_PATH . '/app/helpers/session_coordinador.php';
    //ENLAZAMOS LA DEPENDENCIA DEL CONTROLADOR QUE TIENE LA FUNCION PARA MOSTRAR LOS DATOS
    require_once BASE_PATH . '/app/controllers/acudiente.php';
    
    // LLAMAMOS EL ID QUE VIENE ATRAVEZ DEL METODO GET
    $id = $_GET['id'];
    // LLAMAMOS LA FUNCION ESPECIFICA DEL CONTROLADOR
    $acudiente = mostrarAcudienteId($id);

?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • Formulario • Editar • Acudiente</title>
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
                    <div class="title">Editar Acudiente</div>
                    
                </div>

                <div class="user">
                    <button class="btn" title="Notificaciones"><i class="ri-notification-3-line"></i></button>
                    <button class="btn" title="Configuración"><i class="ri-settings-3-line"></i></button>
                    <div class="avatar" title="Diego A.">DA</div>
                </div>
            </div>
            <div class="subtitulo"><p>Formulario de actualizar, Completa los siguientes campos que desea actualizar. <br> Al finalizar, revisa la información antes de confirmar la actualización para evitar errores en la base de datos institucional.</p></div>

            <!-- Formulario Wizard -->
            <div class="container-fluid py-3">

                <div class="wizard-progress">
                    <div id="stepIndicator1" class="active-step">Paso 1</div>
                    <div id="stepIndicator2">Paso 2</div>
                    <div id="stepIndicator3">Confirmar</div>
                </div>

                <form id="formWizard" action="<?= BASE_URL ?>/coordinador/actualizar_acudiente" method="POST">
                     <input type="hidden" class="form-control" name="id" value="<?= $acudiente['id'] ?>" required>
                     <input type="hidden" class="form-control" name="accion" value="actualizar" required >

                    <!-- Paso 1 -->
                    <div class="step active">
                        <div class="tabla-titulo mb-3">
                            <h5>Datos del Acudiente</h5>
                            
                        </div>

                        <div class="row g-3">
                           

                            <!-- Datos personales -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="">Nombres</label>
                                    <input type="text" class="form-control" name="nombres" value="<?= $acudiente['nombres'] ?>" required>
                                   

                                </div>
                                <div class="mb-3">
                                    <label for="">Apellidos</label>
                                    <input type="text" class="form-control" name="apellidos" value="<?= $acudiente['apellidos'] ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="">Edad</label>
                                    <div class="d-flex gap-2">
                                        <input type="number" class="form-control" name="edad" value="<?= $acudiente['edad'] ?>" required>
                                    </div>
                                </div>
                                

                            </div>

                            <!-- Apellidos y teléfono -->
                            <div class="col-md-6">
                               <div class="mb-3">
                                    <label for="">Documento</label>
                                    <input type="text" class="form-control" name="documento" value="<?= $acudiente['documento'] ?>" readonly required>
                                </div> 
                                <div class="mb-3">
                                    <label for="">Parentesco</label>
                                    <input type="text" class="form-control" name="parentesco" value="<?= $acudiente['parentesco'] ?>" required>
                                </div> 

                                <div class="mb-3">
                                    <label for="">Estado</label>
                                    <select class="form-select" aria-label="Default select example" name="estado">
                                    <option value="<?= $acudiente['estado'] ?>"><?= $acudiente['estado'] ?></option>
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
                                    <input type="email" class="form-control" name="correo" value="<?= $acudiente['correo'] ?>" required>
                                </div>
                                
                                
                            </div>

                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="">N° Teléfono*</label>
                                    <input type="tel" class="form-control" name="telefono" value="<?= $acudiente['telefono'] ?>" required>
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
                            <h5>Actualizar Registro</h5>
                        </div>
                        <p>Revisa los datos ingresados antes de actualizar el acudiente.</p>

                        <div class="botones mt-3">
                            <button type="button" class="btn btn-secondary" onclick="prevStep()">Anterior</button>
                            <button type="submit" class="btn btn-success">Actualizar acudiente</button>
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