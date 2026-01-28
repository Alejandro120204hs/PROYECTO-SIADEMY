<?php 
    require_once BASE_PATH . '/app/helpers/session_docente.php';
    require_once BASE_PATH . '/app/controllers/administrador/curso.php';
    require_once BASE_PATH . '/app/models/administradores/matricula.php';
    require_once BASE_PATH . '/app/models/administradores/docente_asignatura.php';
    
    // Obtener el ID del curso
    $id_curso = $_GET['id'] ?? 0;
    
    // Obtener información del curso
    $curso = mostrarCursoId($id_curso);
    
    // Obtener estudiantes matriculados en el curso
    $matriculaObj = new Matricula();
    $anioActual = date('Y');
    $estudiantes = $matriculaObj->listarPorCurso($id_curso, $anioActual);
    
    // Obtener asignaturas del curso con sus docentes
    $docenteAsignaturaObj = new DocenteAsignatura();
    $asignaturas = $docenteAsignaturaObj->obtenerAsignaturasPorCurso($id_curso);
    
    // Calcular estadísticas
    $totalEstudiantes = count($estudiantes);

?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • Detalle del Curso</title>
    <?php include_once __DIR__ . '/../../layouts/header_coordinador.php' ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-admin.css">
    
    <style>
        /* Forzar que las secciones ocupen el 100% sin espacios */
        .academic-section {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        
        .subjects-table {
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: auto;
        }
        
        .subjects-table table {
            width: 100% !important;
            min-width: 100% !important;
        }

        /* Ajustar el main para usar todo el ancho */
        .main {
            padding: 28px 0 40px 0 !important;
        }

        /* Ajustar el header del curso */
        .student-profile-header {
            padding-left: 28px !important;
            padding-right: 28px !important;
        }

        /* Ajustar las quick stats */
        .quick-stats {
            padding-left: 28px !important;
            padding-right: 28px !important;
        }

        /* Ajustar el topbar */
        .topbar {
            padding-left: 28px !important;
            padding-right: 28px !important;
        }
    </style>
</head>

<body>
    <div class="app" id="appGrid">
        <!-- LEFT SIDEBAR -->
        <?php include_once __DIR__ . '/../../layouts/sidebar_coordinador.php' ?>

        <!-- MAIN -->
        <main class="main">
            <div class="topbar">
                <div class="topbar-left">
                    <div class="title">Detalle del Curso</div>
                </div>

                <div class="user">
                    <button class="btn" title="Notificaciones"><i class="ri-notification-3-line"></i></button>
                    <button class="btn" title="Configuración"><i class="ri-settings-3-line"></i></button>
                    <div class="avatar" title="Admin">AD</div>
                </div>
            </div>

            <!-- COURSE PROFILE HEADER -->
            <div class="student-profile-header">
                <div class="profile-main">
                    <div class="profile-avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="ri-book-open-line" style="font-size: 48px;"></i>
                    </div>
                    <div class="profile-info">
                        <h2><?= $curso['grado'] ?>° - <?= $curso['curso'] ?> • <?= $curso['jornada'] ?></h2>
                        <p class="profile-subtitle"><i class="ri-user-star-line"></i> Director: Prof. <?= $curso['nombres_docente'] . ' ' . $curso['apellidos_docente'] ?></p>
                        <div class="profile-badges">
                            <span class="badge-item <?= $curso['estado'] == 'Activo' ? 'badge-active' : '' ?>">
                                <i class="ri-checkbox-circle-fill"></i> <?= $curso['estado'] ?>
                            </span>
                            <span class="badge-item badge-info">
                                <i class="ri-calendar-line"></i> Año: <?= $anioActual ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="profile-actions">
                    <a href="<?= BASE_URL ?>/administrador-panel-cursos" class="btn-profile-action btn-secondary-action">
                        <i class="ri-arrow-left-line"></i> Volver a Cursos
                    </a>
                    <button class="btn-profile-action btn-icon-action">
                        <i class="ri-more-2-fill"></i>
                    </button>
                </div>
            </div>

            <!-- QUICK STATS -->
            <div class="quick-stats">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="ri-group-line"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-label">Estudiantes Matriculados</span>
                        <strong class="stat-value"><?= $totalEstudiantes ?></strong>
                    </div>
                </div>

            </div>

            <!-- SECCIÓN DE ASIGNATURAS -->
            <div class="academic-section" style="margin-bottom: 32px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 0 28px;">
                    <h3 class="section-title">
                        <i class="ri-book-line" style="color: #6366f1; margin-right: 8px;"></i>
                        Asignaturas del Curso (<?= count($asignaturas) ?>)
                    </h3>
                </div>

                <?php if (!empty($asignaturas)): ?>
                    <div class="subjects-table">
                        <table class="table table-dark table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 60px; padding-left: 28px;">#</th>
                                    <th style="width: 25%;">Asignatura</th>
                                    <th style="width: 35%;">Descripción</th>
                                    <th style="width: 40%; padding-right: 28px;">Docentes Asignados</th>
                                    <th style="width: 10%;">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($asignaturas as $index => $asignatura): ?>
                                    <tr>
                                        <td style="padding-left: 28px;">
                                            <div style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, rgba(79, 70, 229, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); display: flex; align-items: center; justify-content: center; font-weight: 600; color: #6366f1; font-size: 14px;">
                                                <?= $index + 1 ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 12px;">
                                                <div style="width: 42px; height: 42px; border-radius: 10px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                                                    <i class="ri-book-open-line"></i>
                                                </div>
                                                <div>
                                                    <div style="font-weight: 600; color: #e6e9f4; font-size: 15px;">
                                                        <?= htmlspecialchars($asignatura['asignatura']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span style="color: #97a1b6; font-size: 14px;">
                                                <?= htmlspecialchars($asignatura['descripcion'] ?: 'Sin descripción') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($asignatura['docentes'])): ?>
                                                <div style="display: flex; flex-direction: column; gap: 8px;">
                                                    <?php foreach($asignatura['docentes'] as $docente): ?>
                                                        <div style="display: flex; align-items: center; gap: 10px;">
                                                            <div style="width: 32px; height: 32px; border-radius: 8px; background: <?= $docente['estado'] === 'activo' ? 'rgba(16, 185, 129, 0.15)' : 'rgba(148, 163, 184, 0.15)' ?>; display: flex; align-items: center; justify-content: center;">
                                                                <i class="ri-user-line" style="color: <?= $docente['estado'] === 'activo' ? '#10b981' : '#94a3b8' ?>; font-size: 16px;"></i>
                                                            </div>
                                                            <span style="color: #e6e9f4; font-size: 14px; font-weight: 500;">
                                                                <?= htmlspecialchars($docente['nombre']) ?>
                                                            </span>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <span style="color: #ef4444; font-size: 13px; display: flex; align-items: center; gap: 6px;">
                                                    <i class="ri-alert-line"></i>
                                                    Sin docente asignado
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="padding-right: 28px;">
                                            <?php if (!empty($asignatura['docentes'])): ?>
                                                <div style="display: flex; flex-direction: column; gap: 8px;">
                                                    <?php foreach($asignatura['docentes'] as $docente): ?>
                                                        <?php if ($docente['estado'] === 'activo'): ?>
                                                            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: rgba(16, 185, 129, 0.15); color: #10b981; border-radius: 6px; font-size: 11px; font-weight: 600; letter-spacing: 0.5px;">
                                                                <i class="ri-checkbox-circle-fill" style="font-size: 12px;"></i>
                                                                ACTIVO
                                                            </span>
                                                        <?php else: ?>
                                                            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: rgba(148, 163, 184, 0.15); color: #94a3b8; border-radius: 6px; font-size: 11px; font-weight: 600; letter-spacing: 0.5px;">
                                                                <i class="ri-close-circle-fill" style="font-size: 12px;"></i>
                                                                INACTIVO
                                                            </span>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <span style="color: #97a1b6; font-size: 13px;">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 60px 40px; background: rgba(79, 70, 229, 0.05); border-radius: 16px; margin: 0 28px;">
                        <i class="ri-book-line" style="font-size: 64px; color: rgba(79, 70, 229, 0.3); margin-bottom: 16px;"></i>
                        <h3 style="color: #e6e9f4; margin: 0 0 8px 0; font-size: 20px;">No hay asignaturas asignadas</h3>
                        <p style="color: #97a1b6; font-size: 14px; margin: 0 0 20px 0;">Este curso aún no tiene asignaturas configuradas.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- TABLA DE ESTUDIANTES -->
            <div class="academic-section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 0 28px;">
                    <h3 class="section-title">
                        <i class="ri-team-line" style="color: #6366f1; margin-right: 8px;"></i>
                        Estudiantes Matriculados (<?= $totalEstudiantes ?>)
                    </h3>
                </div>

                <?php if (!empty($estudiantes)): ?>
                    <div class="subjects-table">
                        <table class="table table-dark table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 80px; padding-left: 28px;">#</th>
                                    <th style="width: 35%;">Estudiante</th>
                                    <th style="width: 20%;">Documento</th>
                                    <th style="width: 25%;">Fecha de Matrícula</th>
                                    <th style="width: 20%; text-align: center; padding-right: 28px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($estudiantes as $index => $estudiante): ?>
                                    <tr>
                                        <td style="padding-left: 28px;">
                                            <div style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, rgba(79, 70, 229, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); display: flex; align-items: center; justify-content: center; font-weight: 600; color: #6366f1; font-size: 14px;">
                                                <?= $index + 1 ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 14px;">
                                                <img src="<?= BASE_URL ?>/public/uploads/estudiantes/<?= $estudiante['foto'] ?>" 
                                                     alt="<?= $estudiante['estudiante_nombres'] ?>" 
                                                     style="width: 48px; height: 48px; border-radius: 12px; object-fit: cover; border: 2px solid rgba(79, 70, 229, 0.2); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);"
                                                     onerror="this.onerror=null; this.src='<?= BASE_URL ?>/public/uploads/estudiantes/default.png'">
                                                <div>
                                                    <div style="font-weight: 600; color: #e6e9f4; font-size: 15px;">
                                                        <?= htmlspecialchars($estudiante['estudiante_nombres'] . ' ' . $estudiante['estudiante_apellidos']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($estudiante['estudiante_documento']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($estudiante['fecha'])) ?></td>
                                        <td style="text-align: center; padding-right: 28px;">
                                            <a href="<?= BASE_URL ?>/administrador/detalle-estudiante?id=<?= $estudiante['id_estudiante'] ?>" 
                                               style="padding: 10px 14px; border-radius: 8px; text-decoration: none; transition: all 0.2s ease; display: inline-flex; align-items: center; justify-content: center; font-size: 16px; background: rgba(79, 70, 229, 0.1); color: #6366f1;"
                                               onmouseover="this.style.background='rgba(79, 70, 229, 0.2)'; this.style.transform='scale(1.1)'"
                                               onmouseout="this.style.background='rgba(79, 70, 229, 0.1)'; this.style.transform='scale(1)'">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                            <a href="<?= BASE_URL ?>/administrador/editar-matricula?id=<?= $estudiante['id'] ?>" 
                                               style="padding: 10px 14px; border-radius: 8px; text-decoration: none; transition: all 0.2s ease; display: inline-flex; align-items: center; justify-content: center; font-size: 16px; background: rgba(59, 130, 246, 0.1); color: #3b82f6; margin-left: 8px;"
                                               onmouseover="this.style.background='rgba(59, 130, 246, 0.2)'; this.style.transform='scale(1.1)'"
                                               onmouseout="this.style.background='rgba(59, 130, 246, 0.1)'; this.style.transform='scale(1)'">
                                                <i class="ri-edit-line"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 80px 40px;">
                        <i class="ri-user-unfollow-line" style="font-size: 80px; color: rgba(79, 70, 229, 0.2); margin-bottom: 24px;"></i>
                        <h3 style="color: #e6e9f4; margin: 0 0 12px 0; font-size: 22px;">No hay estudiantes matriculados</h3>
                        <p style="color: #97a1b6; font-size: 15px; margin: 0 0 24px 0;">Este curso aún no tiene estudiantes matriculados para el año <?= $anioActual ?>.</p>
                        <a href="<?= BASE_URL ?>/administrador/registrar-matricula?curso=<?= $id_curso ?>" class="btn-profile-action btn-primary-action" style="display: inline-flex;">
                            <i class="ri-user-add-line"></i>
                            Matricular Primer Estudiante
                        </a>
                    </div>
                <?php endif; ?>
            </div>

        </main>
    </div>

    <script>
        // Script vacío - funcionalidad futura
    </script>
        
    <style>
        .appGrid,
        .app {
            display: grid !important;
            grid-template-columns: 260px 1fr !important;
        }
        
        .main {
            padding: 1rem !important;
        }
    </style>
</body>

</html>