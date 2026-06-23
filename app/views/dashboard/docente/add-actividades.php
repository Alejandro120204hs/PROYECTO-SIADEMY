
<?php
require_once BASE_PATH . '/app/helpers/session_docente.php';
require_once BASE_PATH . '/app/controllers/docente/view_data.php';

$dataVistaDocenteAgregarActividad = obtenerDataVistaDocenteAgregarActividad();
extract($dataVistaDocenteAgregarActividad, EXTR_SKIP);
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • Nueva Actividad</title>
    <?php
        include_once __DIR__ . '/../../layouts/header_coordinador.php'
    ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-tabla-formulario.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/modo-claro-docente.css">
</head>
<body>
    <div class="app" id="appGrid">
   <!-- LEFT SIDEBAR -->
      <?php 
        include_once __DIR__ . '/../../layouts/sidebar_docente.php'
      ?>

        <!-- MAIN -->
        <main class="main">
            <div class="topbar">
                <div class="topbar-left">
                    <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
                        <i class="ri-menu-2-line"></i>
                    </button>
                    <div class="title">Agregar Actividad</div>
                    
                </div>

                <div class="user">
                    <button class="btn" title="Notificaciones"><i class="ri-notification-3-line"></i></button>
                    <button class="btn" title="Configuración"><i class="ri-settings-3-line"></i></button>
                    <div class="avatar" title="Diego A.">DA</div>
                </div>
            </div>
            <div class="subtitulo"><p>Formulario de registro, Completa los siguientes pasos para registrar una nueva actividad en el sistema académico. <br> Al finalizar, revisa la información antes de confirmar el registro para evitar errores en la base de datos institucional.</p></div>

            <!-- Formulario Wizard -->
            <div class="container-fluid py-3">

                <div class="wizard-progress">
                    <div id="stepIndicator1" class="active-step">Paso 1</div>
                    <div id="stepIndicator3">Confirmar</div>
                </div>

                <form id="formWizard" action="<?= BASE_URL ?>/docente/guardar_actividad" method="POST" enctype="multipart/form-data">

                    <!-- Paso 1 -->
                    <div class="step active">
                        <div class="tabla-titulo mb-3">
                            <h5>Información de la actividad - <?= $curso['grado'] ?>° <?= $curso['curso'] ?> (<?= $curso['nombre_asignatura'] ?>)</h5>
                            <p style="color: #64748b; font-size: 14px; margin-top: 8px;">
                                <i class="ri-book-line"></i> Asignatura: <strong><?= $curso['nombre_asignatura'] ?></strong> | 
                                <i class="ri-calendar-line"></i> Año: <strong><?= $curso['anio'] ?></strong> | 
                                <i class="ri-time-line"></i> Jornada: <strong><?= $curso['jornada'] ?></strong>
                            </p>
                        </div>

                        <!-- Campo hidden para enviar el id_curso y id_asignatura -->
                        <input type="hidden" name="id_curso" value="<?= $curso['id'] ?>">
                        <input type="hidden" name="id_asignatura" value="<?= $curso['id_asignatura'] ?>">
                        <input type="hidden" name="id_asignatura_curso" value="<?= $curso['id_asignatura_curso'] ?>">

                        <div class="row g-3">
                           <div class="col-md-1">
                            </div>

                            <!-- Datos de la actividad -->
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="">Título de la actividad*</label>
                                    <input type="text" class="form-control" name="titulo_actividad" required tabindex="1">
                                </div>
                                 <div class="mb-3">
                                    <label for="">Tipo de actividad*</label>
                                    <select class="selector" name="tipo_actividad" required tabindex="2">
                                        <option value="" selected disabled>Seleccione el tipo de actividad</option>
                                        <option value="Taller">Taller</option>
                                        <option value="Quiz">Quiz</option>
                                        <option value="Examen">Examen</option>
                                        <option value="Proyecto">Proyecto</option>
                                        <option value="Exposición">Exposición</option>
                                        <option value="Laboratorio">Laboratorio</option>
                                        <option value="Tarea">Tarea</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="">Descripción</label>
                                    <textarea class="form-control" name="descripcion" rows="4" tabindex="5"></textarea>
                                </div>

                               
                            </div>

                            <!-- Ponderación y fecha -->
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="">Ponderación / Valor porcentual (%)*</label>
                                    <input type="number" class="form-control" name="ponderacion"
                                           min="0" max="<?= $ponderacion_disponible ?>"
                                           step="0.01" required tabindex="3"
                                           id="inputPonderacion">
                                    <?php if ($ponderacion_usada > 0): ?>
                                        <!-- Barra de ponderación usada -->
                                        <div style="margin-top:8px;">
                                            <div style="display:flex; justify-content:space-between; font-size:12px; color:#94a3b8; margin-bottom:4px;">
                                                <span>Usado: <strong style="color:#f59e0b;"><?= number_format($ponderacion_usada, 1) ?>%</strong></span>
                                                <span>Disponible: <strong style="color:#10b981;"><?= number_format($ponderacion_disponible, 1) ?>%</strong></span>
                                            </div>
                                            <div style="background:#1e293b; border-radius:6px; height:8px; overflow:hidden;">
                                                <div style="background: linear-gradient(90deg,#f59e0b,#ef4444); width:<?= min(100, $ponderacion_usada) ?>%; height:100%; border-radius:6px; transition:width 0.3s;"></div>
                                            </div>
                                        </div>
                                        <?php if ($ponderacion_disponible <= 0): ?>
                                            <small class="form-text" style="color:#ef4444;">
                                                <i class="ri-error-warning-line"></i> Las actividades de esta materia ya suman 100%. No puedes agregar más.
                                            </small>
                                        <?php else: ?>
                                            <small class="form-text" style="color:#10b981;">
                                                Puedes asignar hasta <strong><?= number_format($ponderacion_disponible, 1) ?>%</strong> a esta actividad.
                                            </small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <small class="form-text text-muted">
                                            Valor porcentual de la actividad sobre la nota total del período (0–100%).
                                            Aún no hay actividades creadas para esta materia.
                                        </small>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="">Fecha de entrega*</label>
                                    <input type="date" class="form-control" name="fecha_entrega" required tabindex="4">
                                </div>

                                 <div class="mb-3">
                                    <label for="">Archivo adjunto <small class="text-muted">(opcional · PDF, JPG, PNG, DOC, DOCX · máx. 10 MB)</small></label>
                                    <input type="file" class="form-control" name="archivo_actividad" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" tabindex="6">
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
                        <p>Revisa los datos ingresados antes de agregar el acudiente.</p>

                        <div class="botones mt-3">
                            <button type="button" class="btn btn-secondary" onclick="prevStep()">Anterior</button>
                            <button type="submit" class="btn btn-success">Agregar Actividad</button>
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