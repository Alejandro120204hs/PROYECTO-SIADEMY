<?php 
  require_once BASE_PATH . '/app/helpers/session_administrador.php';
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • Formulario • Estudiantes</title>
    <?php 
        include_once __DIR__ . '/../../layouts/header_coordinador.php'
    ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-form-docente.css">

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
                    <div class="title">Agregar Profesor</div>
                    
                </div>

                <div class="user">
                    <button class="btn" title="Notificaciones"><i class="ri-notification-3-line"></i></button>
                    <button class="btn" title="Configuración"><i class="ri-settings-3-line"></i></button>
                    <div class="avatar" title="Diego A.">DA</div>
                </div>
            </div>
            <div class="subtitulo"><p>Formulario de registro, Completa los siguientes pasos para registrar un Profesor en el sistema académico. <br> Al finalizar, revisa la información antes de confirmar el registro para evitar errores en la base de datos institucional.</p></div>

            <!-- Formulario Wizard -->
            <div class="container-fluid py-3">

                <div class="wizard-progress">
                    <div id="stepIndicator1" class="active-step">Paso 1</div>
                    <div id="stepIndicator2">Paso 2</div>
                    <div id="stepIndicator3">Paso 3</div>
                    <div id="stepIndicator4">Confirmar</div>
                </div>

                <form id="formWizard" action="<?= BASE_URL ?>/administrador/guardar_docente" method="POST" enctype="multipart/form-data">

                    <!-- Paso 1 -->
                    <div class="step active">
                        <div class="tabla-titulo mb-3">
                            <h5>Datos del Profesor</h5>
                            
                        </div>

                        <div class="row g-3">
                           <div class="col-md-3 poFoto">
                                <label for="">Foto*</label>
                                <div
                                    class=" esPhoto">
                                    <small>Selecciona un archivo</small>
                                    <input type="file" class="form-control mt-2"  name="foto" accept=".jpg, .png, .jpeg,"  tabindex="1"/>
                                </div>
                            </div>

                            <!-- Datos personales -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="">Nombres</label>
                                    <input type="text" class="form-control" name="nombres" required tabindex="2">
                                </div>
                                 <div class="mb-3">
                                    <label for="">tipo de documento</label>
                                    <select class="selector" name="tipo_documento" required tabindex="4">
                                        <option selected>Seleccione un tipo de documento</option>
                                        <option value="CC">CC</option>
                                        <option value="CE">CE</option>
                                        <option value="PPT">PPT</option>
                                      
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="">Fecha de nacimiento</label>
                                    <div class="d-flex gap-2">
                                        <input type="date" class="form-control" name="fecha_nacimiento" required tabindex="6">
                                    </div>
                                </div>
                                
     
                            </div>

                            <!-- Apellidos y teléfono -->
                            <div class="col-md-4">
                               
                                <div class="mb-3">
                                    <label for="">Apellidos</label>
                                    <input type="text" class="form-control" name="apellidos" required tabindex="3">
                                </div>
                                 <div class="mb-3 parte2">
                                    <label for="">N° Documento*</label>
                                    <input type="number" class="form-control" name="documento" required tabindex="5">
                                </div>
                            <div class="mb-3">
                                    <label for="">Genero</label>
                                    <select class="selector" name="genero" required tabindex="8">
                                        <option selected>Seleccione un genero</option>
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
                            <h5>Datos De Contacto</h5>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-1"></div>
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="">Email*</label>
                                    <input type="email" class="form-control" name="correo" required tabindex="1">
                                </div>

                                <div class="mb-3">
                                    <label for="">Ciudad</label>
                                    <input type="text" class="form-control" name="ciudad" required tabindex="3">
                                </div>
                                
                                
                            </div>

                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="">N° Teléfono*</label>
                                    <input type="number" class="form-control" name="telefono" required tabindex="2">
                                </div>
                                <div class="mb-3">
                                    <label for="">Dirección</label>
                                    <input type="text" class="form-control" name="direccion" required tabindex="4">
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
                            <h5>Datos Laborales</h5>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>Profesión</label>
                                    <input type="text" class="form-control" name="profesion" required tabindex="1">
                                </div>

                                <div class="mb-3">
                                    <label>Tipo de contrato</label>
                                    <select class="selector" name="tipo_contrato" required tabindex="3">
                                        <option>Seleccionar...</option>
                                        <option value="Fijo">Fijo</option>
                                        <option value="Indefinido">Indefinido</option>
                                        <option value="Servicios">Prestación de servicios</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>Fecha ingreso</label>
                                    <input type="date" class="form-control" name="fecha_ingreso" required tabindex="2">
                                </div>

                                <div class="mb-3">
                                    <label>Fecha fin contrato</label>
                                    <input type="date" class="form-control" name="fecha_fin_contrato" tabindex="4">
                                </div>
                            </div>
                        </div>

                        <div class="botones mt-3">
                            <button type="button" class="btn btn-secondary" onclick="prevStep()">Anterior</button>
                            <button type="button" class="btn btn-primary" onclick="nextStep()">Siguiente</button>
                        </div>
                    </div>


                    <!-- Paso 4 -->
                    <div class="step">
                        <div class="tabla-titulo mb-3">
                            <h5>Confirmar Registro</h5>
                        </div>
                        <p>Revisa los datos ingresados antes de agregar el estudiante.</p>

                        <div class="botones mt-3">
                            <button type="button" class="btn btn-secondary" onclick="prevStep()">Anterior</button>
                            <button type="submit" class="btn btn-success">Agregar Profesor</button>
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