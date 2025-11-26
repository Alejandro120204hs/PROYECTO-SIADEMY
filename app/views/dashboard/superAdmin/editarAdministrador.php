<?php

    // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
    require_once BASE_PATH . '/app/controllers/superAdmin/administradores.php';

    // LLAMAMOS EL ID QUE VIENE A TRAVES DEL METODO GET
    $id = $_GET['id'];

    // LLAMAMOS A LA FUNCION ESPECIFICA
    $administrador = mostrarAdministradoresID($id);

?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • Formulario • Editar • Administrador</title>
    <?php 
        include_once __DIR__ . '/../../layouts/header_coordinador.php'
    ?>
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
                    <div class="title">Editar Adminstrador</div>
                    
                </div>

                <div class="user">
                    <button class="btn" title="Notificaciones"><i class="ri-notification-3-line"></i></button>
                    <button class="btn" title="Configuración"><i class="ri-settings-3-line"></i></button>
                    <div class="avatar" title="Diego A.">DA</div>
                </div>
            </div>
            <div class="subtitulo"><p>Formulario de actualización, Completa los siguientes pasos para actualizar la institución en el sistema académico. <br> Al finalizar, revisa la información antes de confirmar el registro para evitar errores en la base de datos institucional.</p></div>

            <!-- Formulario Wizard -->
            <div class="container-fluid py-3">

                <div class="wizard-progress">
                    <div id="stepIndicator1" class="active-step">Paso 1</div>
                    <div id="stepIndicator2">Paso 2</div>
                    <div id="stepIndicator3">Confirmar</div>
                </div>

                <form id="formWizard" action="<?= BASE_URL ?>/superAdmin-actualizar-administrador" method="POST">
                <input type="text" class="form-control" name="id" value="<?= $administrador['id'] ?>" hidden>
                <input type="text" class="form-control" name="id_usuario" value="<?= $administrador['id_usuario'] ?>" hidden>

                <input type="text" class="form-control" name="accion" value="actualizar" hidden>


                    <!-- Paso 1 -->
                    <div class="step active">
                        <div class="tabla-titulo mb-3">
                            <h5>Datos de la escuela</h5>
                            
                        </div>

                        <div class="row g-3">
                            

                            <!-- Datos personales -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="">Nombres</label>
                                    <input type="text" class="form-control" name="nombres" value="<?= $administrador['nombres'] ?>">
                                </div>
                            
                                <div class="mb-3">
                                    <label for="">Apellidos</label>
                                    <input type="text" class="form-control" name="apellidos" value="<?= $administrador['apellidos'] ?>">
                                </div>
                               

                            </div>

                            <!-- Apellidos y teléfono -->
                            <div class="col-md-6">
                                 
                                 <div class="mb-3">
                                    <label for="">Edad</label>
                                    <input type="number" class="form-control" name="edad" value="<?= $administrador['edad'] ?>">
                                </div>

                                 <div class="mb-3">
                                    <label for="">Estado</label>
                                    <select class="selector" name="estado">
                                        <option value="<?= $administrador['estado'] ?>"><?= $administrador['estado'] ?></option>
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
                            <h5>Contacto</h5>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-1"></div>
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="">Correo</label>
                                    <input type="email" class="form-control" name="correo" value="<?= $administrador['correo'] ?>">
                                </div>
                                
                            </div>

                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="">Telefono</label>
                                    <input type="number" class="form-control" name="telefono" value="<?= $administrador['telefono'] ?>">
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
                        <p>Revisa los datos ingresados antes de actualizar la institución.</p>

                        <div class="botones mt-3">
                            <button type="button" class="btn btn-secondary" onclick="prevStep()">Anterior</button>
                            <button type="submit" class="btn btn-success">Editar Adminstrador</button>
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

    <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-formulario.js"></script>
</body>

</html>