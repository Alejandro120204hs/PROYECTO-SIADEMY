<?php 
    require_once BASE_PATH . '/app/helpers/session_administrador.php';
    require_once BASE_PATH . '/app/controllers/administrador/estudiante_controller.php';
    require_once BASE_PATH . '/app/controllers/administrador/curso.php';
    
    $estudiantes = mostrarEstudiantes();
    $cursos = mostrarCursos();
?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • Matricular Estudiante</title>
    <?php
        include_once __DIR__ . '/../../layouts/header_coordinador.php'
    ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-admin.css">
    
    <!-- Select2 para mejorar los selects -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/administrador/add-matricula.css">
</head>

<body>
    <div class="app hide-right" id="appGrid">
        <!-- LEFT SIDEBAR -->
        <?php include_once __DIR__ . '/../../layouts/sidebar_coordinador.php'; ?>

        <!-- MAIN -->
        <main class="main">
            <div class="topbar">
                <div class="topbar-left">
                    <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
                        <i class="ri-menu-2-line"></i>
                    </button>
                    <button class="btn-back" onclick="window.history.back()">
                        <i class="ri-arrow-left-line"></i> Volver
                    </button>
                    <div class="title">Matricular Estudiante</div>
                </div>
            </div>

            <?php 
            // Mostrar alerta si existe
            if(isset($_SESSION['alerta'])){
                $alerta = $_SESSION['alerta'];
                echo '<div class="alert alert-'.$alerta['tipo'].'">';
                echo '<i class="ri-information-line"></i>';
                echo '<span>'.$alerta['mensaje'].'</span>';
                echo '<button class="btn-close" onclick="this.parentElement.remove()" style="margin-left: auto; background: transparent; border: none; color: inherit; cursor: pointer; opacity: 0.7;"><i class="ri-close-line"></i></button>';
                echo '</div>';
                unset($_SESSION['alerta']);
            }
            ?>

            <!-- FORMULARIO DE MATRÍCULA -->
            <div class="form-card">
                <h3><i class="ri-graduation-cap-line"></i> Nueva Matrícula</h3>
                <p>Completa los siguientes campos para matricular un estudiante en un curso específico</p>

                <div class="info-box">
                    <i class="ri-information-line"></i>
                    <strong>Información importante:</strong>
                    <ul>
                        <li>Verifica que el estudiante no esté ya matriculado en el curso seleccionado</li>
                        <li>El sistema validará automáticamente el cupo disponible del curso</li>
                        <li>Solo se pueden matricular estudiantes en cursos activos</li>
                    </ul>
                </div>

                <?php if(isset($_GET['curso']) && !empty($_GET['curso'])): ?>
                <div class="alert alert-success">
                    <i class="ri-checkbox-circle-line"></i>
                    <span><strong>¡Curso pre-seleccionado!</strong> El curso actual ya está seleccionado en el formulario.</span>
                </div>
                <?php endif; ?>

                <form id="formMatricula" action="<?= BASE_URL ?>/administrador/guardar-matricula" method="POST" data-curso-preseleccionado="<?= (isset($_GET['curso']) && !empty($_GET['curso'])) ? '1' : '0' ?>">
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
                        
                        <!-- SELECCIONAR ESTUDIANTE -->
                        <div class="form-group">
                            <label>
                                <i class="ri-user-line"></i> Estudiante <span style="color: #ef4444;">*</span>
                            </label>
                            <select id="selectEstudiante" class="form-select select2" name="id_estudiante" required>
                                <option value="">Seleccione un estudiante...</option>
                                <?php if (!empty($estudiantes)): ?>
                                    <?php foreach ($estudiantes as $estudiante): ?>
                                        <option value="<?= $estudiante['id'] ?>" 
                                                data-documento="<?= $estudiante['documento'] ?>">
                                            <?= htmlspecialchars($estudiante['nombres'] . ' ' . $estudiante['apellidos']) ?> 
                                            - Doc: <?= $estudiante['documento'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option disabled>No hay estudiantes registrados</option>
                                <?php endif; ?>
                            </select>
                            <small class="form-help">Busca por nombre o documento del estudiante</small>
                        </div>

                        <!-- SELECCIONAR CURSO -->
                        <div class="form-group">
                            <label>
                                <i class="ri-book-line"></i> Curso <span style="color: #ef4444;">*</span>
                            </label>
                            <select id="selectCurso" class="form-select select2" name="id_curso" required>
                                <option value="">Seleccione un curso...</option>
                                <?php 
                                // Obtener el ID del curso desde el parámetro GET
                                $curso_preseleccionado = $_GET['curso'] ?? null;
                                ?>
                                <?php if (!empty($cursos)): ?>
                                    <?php foreach ($cursos as $curso): ?>
                                        <?php if($curso['estado'] == 'Activo'): ?>
                                            <option value="<?= $curso['id'] ?>" 
                                                    <?= ($curso_preseleccionado && $curso['id'] == $curso_preseleccionado) ? 'selected' : '' ?>
                                                    data-grado="<?= $curso['grado'] ?>"
                                                    data-nombre="<?= $curso['curso'] ?>"
                                                    data-nivel="<?= $curso['nivel_academico'] ?>"
                                                    data-jornada="<?= $curso['jornada'] ?>"
                                                    data-cupo="<?= $curso['cupo_maximo'] ?>"
                                                    data-docente="<?= $curso['nombres_docente'] . ' ' . $curso['apellidos_docente'] ?>">
                                                <?= $curso['grado'] ?>° - <?= $curso['curso'] ?> 
                                                (<?= $curso['nivel_academico'] ?> - <?= $curso['jornada'] ?>)
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option disabled>No hay cursos activos</option>
                                <?php endif; ?>
                            </select>
                            <small class="form-help">Solo se muestran cursos activos</small>
                        </div>

                        <!-- AÑO LECTIVO -->
                        <div class="form-group">
                            <label>
                                <i class="ri-calendar-line"></i> Año Lectivo <span style="color: #ef4444;">*</span>
                            </label>
                            <select class="form-control" name="anio" id="anio" required>
                                <?php 
                                    $anioActual = date('Y');
                                    for($i = $anioActual - 1; $i <= $anioActual + 1; $i++): 
                                ?>
                                    <option value="<?= $i ?>" <?= ($i == $anioActual) ? 'selected' : '' ?>>
                                        <?= $i ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- FECHA DE MATRÍCULA -->
                        <div class="form-group">
                            <label>
                                <i class="ri-calendar-check-line"></i> Fecha de Matrícula <span style="color: #ef4444;">*</span>
                            </label>
                            <input type="date" class="form-control" name="fecha" id="fecha" 
                                   value="<?= date('Y-m-d') ?>" required>
                        </div>

                    </div>

                    <!-- INFORMACIÓN DEL CURSO SELECCIONADO -->
                    <div id="cursoInfo" class="curso-info">
                        <h6><i class="ri-information-line"></i> Información del Curso Seleccionado</h6>
                        <div class="row">
                            <div>
                                <p><strong>Grado:</strong> <span id="infoCursoGrado">-</span></p>
                                <p><strong>Nivel:</strong> <span id="infoCursoNivel">-</span></p>
                                <p><strong>Jornada:</strong> <span id="infoCursoJornada">-</span></p>
                            </div>
                            <div>
                                <p><strong>Director:</strong> <span id="infoCursoDocente">-</span></p>
                                <p><strong>Cupo Máximo:</strong> <span id="infoCursoCupo">-</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- BOTONES -->
                    <div class="btn-actions">
                        <a href="<?= BASE_URL ?>/administrador/panel-matriculas" class="btn-cancel">
                            <i class="ri-arrow-left-line"></i> Cancelar
                        </a>
                        <button type="submit" class="btn-submit">
                            <i class="ri-save-line"></i> Matricular Estudiante
                        </button>
                    </div>

                </form>
            </div>

        </main>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-admin.js"></script>
    <script src="<?= BASE_URL ?>/public/assets/dashboard/js/administrador/add-matricula.js"></script>

</body>
</html>
