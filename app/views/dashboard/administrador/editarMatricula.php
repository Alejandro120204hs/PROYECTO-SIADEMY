<?php 
    require_once BASE_PATH . '/app/helpers/session_administrador.php';
    require_once BASE_PATH . '/app/controllers/administrador/matricula.php';
    require_once BASE_PATH . '/app/controllers/administrador/estudiante_controller.php';
    require_once BASE_PATH . '/app/controllers/administrador/curso.php';

     require_once BASE_PATH . '/app/controllers/perfil.php';
     
    
    // LLAMAMOS EL ID QUE VIENE ATRAVEZ DEL METODO GET
    $id = $_SESSION['user']['id'];
    // LLAMAMOS LA FUNCION ESPECIFICA DEL CONTROLADOR
    $usuario = mostrarPerfil($id);
    
    // Obtener datos de la matrícula a editar
    $matricula = mostrarMatriculaId($_GET['id']);
    $estudiantes = mostrarEstudiantes();
    $cursos = mostrarCursos();
?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • Editar Matrícula</title>
    <?php
        include_once __DIR__ . '/../../layouts/header_coordinador.php'
    ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-tabla-formulario.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/administrador/editar-matricula.css">
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
                    <div class="title">Editar Matrícula</div>
                </div>

                <?php
                    include_once __DIR__ . '/../../layouts/boton_perfil_solo.php'
                ?>
            </div>

            <div class="subtitulo">
                <p>Editar matrícula existente. Modifica los datos necesarios y guarda los cambios.
                <br>Ten en cuenta que cambiar el curso puede afectar la disponibilidad de cupos.</p>
            </div>

            <!-- Formulario -->
            <div class="container-fluid py-3">
                
                <!-- Información actual -->
                <div class="current-info">
                    <h6><i class="ri-file-info-line"></i> Información Actual de la Matrícula</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p style="color: black;"><strong>Estudiante:</strong> <?= htmlspecialchars($matricula['estudiante_nombres'] . ' ' . $matricula['estudiante_apellidos']) ?></p>
                            <p style="color: black;"><strong>Documento:</strong> <?= $matricula['estudiante_documento'] ?></p>
                        </div>
                        <div class="col-md-6">
                            <p style="color: black;"><strong>Curso:</strong> <?= $matricula['grado'] ?>° - <?= $matricula['nombre_curso'] ?></p>
                            <p style="color: black;"><strong>Año:</strong> <?= $matricula['anio'] ?></p>
                            <p style="color: black;"><strong>Fecha Matrícula:</strong> <?= date('d/m/Y', strtotime($matricula['fecha'])) ?></p>
                        </div>
                    </div>
                </div>

                <form id="formEditarMatricula" action="<?= BASE_URL ?>/administrador/actualizar-matricula" method="POST">
                    
                    <input type="hidden" name="id" value="<?= $matricula['id'] ?>">
                    <input type="hidden" name="accion" value="actualizar">

                    <div class="tabla-titulo mb-3">
                        <h5><i class="ri-edit-line"></i> Modificar Datos de la Matrícula</h5>
                    </div>

                    <div class="info-card">
                        <h6><i class="ri-alert-line"></i> Importante</h6>
                        <p style="margin: 0;">Al cambiar el curso, se verificará nuevamente el cupo disponible y que el estudiante no esté ya matriculado en ese curso.</p>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-1"></div>
                        
                        <div class="col-md-5">
                            <!-- Estudiante -->
                            <div class="mb-3">
                                <label for="selectEstudiante">
                                    <i class="ri-user-line"></i> Estudiante <span style="color: red;">*</span>
                                </label>
                                <select id="selectEstudiante" class="form-select" name="id_estudiante" required tabindex="1">
                                    <?php if (!empty($estudiantes)): ?>
                                        <?php foreach ($estudiantes as $estudiante): ?>
                                            <option value="<?= $estudiante['id'] ?>" 
                                                    <?= ($estudiante['id'] == $matricula['id_estudiante']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($estudiante['nombres'] . ' ' . $estudiante['apellidos']) ?> 
                                                - Doc: <?= $estudiante['documento'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Año -->
                            <div class="mb-3">
                                <label for="anio">
                                    <i class="ri-calendar-line"></i> Año Lectivo <span style="color: red;">*</span>
                                </label>
                                <select class="form-control select-similar" name="anio" id="anio" required tabindex="3">
                                    <?php 
                                        $anioActual = date('Y');
                                        for($i = $anioActual - 2; $i <= $anioActual + 1; $i++): 
                                    ?>
                                        <option value="<?= $i ?>" <?= ($i == $matricula['anio']) ? 'selected' : '' ?>>
                                            <?= $i ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                        </div>

                        <div class="col-md-5">
                            <!-- Curso -->
                            <div class="mb-3">
                                <label for="selectCurso">
                                    <i class="ri-book-line"></i> Curso <span style="color: red;">*</span>
                                </label>
                                <select id="selectCurso" class="form-select" name="id_curso" required tabindex="2">
                                    <?php if (!empty($cursos)): ?>
                                        <?php foreach ($cursos as $curso): ?>
                                            <?php if($curso['estado'] == 'Activo'): ?>
                                                <option value="<?= $curso['id'] ?>" 
                                                        <?= ($curso['id'] == $matricula['id_curso']) ? 'selected' : '' ?>>
                                                    <?= $curso['grado'] ?>° - <?= $curso['curso'] ?> 
                                                    (<?= $curso['nivel_academico'] ?> - <?= $curso['jornada'] ?>)
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Fecha de Matrícula -->
                            <div class="mb-3">
                                <label for="fecha">
                                    <i class="ri-calendar-check-line"></i> Fecha de Matrícula <span style="color: red;">*</span>
                                </label>
                                <input type="date" class="form-control" name="fecha" id="fecha" 
                                       value="<?= $matricula['fecha'] ?>" required tabindex="4">
                            </div>

                        </div>

                    </div>

                    <!-- Botones -->
                    <div class="botones mt-4">
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='<?= BASE_URL ?>/administrador-panel-matriculas'">
                            <i class="ri-arrow-left-line"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="ri-save-line"></i> Guardar Cambios
                        </button>
                    </div>

                </form>

            </div>

        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-formulario.js"></script>
    <script src="<?= BASE_URL ?>/public/assets/dashboard/js/administrador/editar-matricula.js"></script>

</body>
</html>
