<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • Formulario • Escuelas</title>
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
                    <div class="title">Agregar Institución</div>
                    
                </div>

                <div class="user">
                    <button class="btn" title="Notificaciones"><i class="ri-notification-3-line"></i></button>
                    <button class="btn" title="Configuración"><i class="ri-settings-3-line"></i></button>
                    <div class="avatar" title="Diego A.">DA</div>
                </div>
            </div>
            <div class="subtitulo"><p>Formulario de registro, Completa los siguientes pasos para registrar una nueva institución en el sistema académico. <br> Al finalizar, revisa la información antes de confirmar el registro para evitar errores en la base de datos institucional.</p></div>

            <!-- Formulario Wizard -->
            <div class="container-fluid py-3">

                <div class="wizard-progress">
                    <div id="stepIndicator1" class="active-step">Paso 1</div>
                    <div id="stepIndicator2">Paso 2</div>
                    <div id="stepIndicator3">Confirmar</div>
                </div>

                <form id="formWizard" action="<?= BASE_URL ?>/superAdmin-registrar-institucion" method="POST">

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
                                    <input type="text" class="form-control" name="nombre">
                                </div>
                            
                            <div class="mb-3">
                                    <label for="">Tipo</label>
                                    <select class="selector" name="tipo">
                                        <option selected>Selecciona el tipo de intitucion</option>
                                        <option value="Publica">Publica</option>
                                        <option value="Privada">Privada</option>
                                        <option value="Rural">Rural</option>
                                        
                                    </select>
                                </div>
                               

                            </div>

                            <!-- Apellidos y teléfono -->
                            <div class="col-md-6">
                                 
                                 <div class="mb-3">
                                    <label for="">Jornada</label>
                                    <select class="selector" name="jornada">
                                        <option selected>Selecciona el tipo de Joornada</option>
                                        <option value="Mañana">Mañana</option>
                                        <option value="Tarde">Tarde</option>
                                        <option value="Noche">Noche</option>
                                        <option value="Diurna">Diurna</option>
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
                                    <label for="">Dirección</label>
                                    <input type="text" class="form-control" name="direccion">
                                </div>
                                <div class="mb-3">
                                    <label for="">Telefono</label>
                                    <input type="number" class="form-control" name="telefono">
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="">Correo</label>
                                    <input type="email" class="form-control" name="correo">
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
                        <p>Revisa los datos ingresados antes de agregar la institución.</p>

                        <div class="botones mt-3">
                            <button type="button" class="btn btn-secondary" onclick="prevStep()">Anterior</button>
                            <button type="submit" class="btn btn-success">Agregar institucion</button>
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