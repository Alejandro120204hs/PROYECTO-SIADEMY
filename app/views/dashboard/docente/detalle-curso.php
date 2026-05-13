<?php
    require_once BASE_PATH . '/app/helpers/session_docente.php';
    require_once BASE_PATH . '/app/controllers/docente/view_data.php';

    $idCurso = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $dataVistaDocenteDetalleCurso = obtenerDataVistaDocenteDetalleCurso($idCurso);
    extract($dataVistaDocenteDetalleCurso, EXTR_SKIP);
?>

<!doctype html>
<html lang="es">
    

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • Detalle del Curso</title>
    <?php include_once __DIR__ . '/../../layouts/header_coordinador.php' ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-admin.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/docente/detalle-curso.css?v=<?= $detalleCursoCssVersion ?>">
</head>

<body>
    <div class="app" id="appGrid"
         data-base-url="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>"
         data-id-curso="<?= (int) $id_curso ?>"
         data-detalle-actividades='<?= docenteJsonParaHtml($detalleActividadesPorAsignatura) ?>'
         data-resumen-calificaciones='<?= docenteJsonParaHtml($resumenCalificacionesPorAsignatura) ?>'
         data-perfil-estudiantes='<?= docenteJsonParaHtml($perfilAcademicoPorEstudiante) ?>'
         data-calificaciones-estudiantes='<?= docenteJsonParaHtml($calificacionesPorEstudiante) ?>'>
        <!-- LEFT SIDEBAR -->
        <?php include_once __DIR__ . '/../../layouts/sidebar_docente.php' ?>

        <!-- MAIN -->
        <main class="main">
            <div class="topbar">
                <div class="topbar-left">
                    <div class="title">Detalle del Curso</div>
                </div>
                <?php include_once BASE_PATH . '/app/views/layouts/boton_perfil_solo.php'; ?>
            </div>

            <!-- COURSE PROFILE HEADER -->
            <div class="student-profile-header">
                <div class="profile-main">
                    <div class="profile-avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="ri-book-open-line" style="font-size: 48px;"></i>
                    </div>
                    <div class="profile-info">
                        <h2>
                            <?= $curso['grado'] ?>° - <?= $curso['curso'] ?> • <?= $curso['jornada'] ?>
                            <span class="period-indicator">
                                <i class="ri-calendar-check-line"></i>
                                Periodo 1 - 2026
                            </span>
                        </h2>
                        <p class="profile-subtitle">
                            <i class="ri-user-star-line"></i> Director: Prof. <?= $curso['nombres_docente'] . ' ' . $curso['apellidos_docente'] ?>
                        </p>
                        <div class="profile-badges">
                            <span class="badge-item <?= $curso['estado'] == 'Activo' ? 'badge-active' : '' ?>">
                                <i class="ri-checkbox-circle-fill"></i> <?= $curso['estado'] ?>
                            </span>
                            <span class="badge-item badge-info">
                                <i class="ri-calendar-line"></i> Año: <?= $anioActual ?>
                            </span>
                            <span class="badge-item badge-info">
                                <i class="ri-book-2-line"></i> <?= $totalMisAsignaturas ?> asignatura<?= $totalMisAsignaturas != 1 ? 's' : '' ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="profile-actions">
                    <a href="<?= BASE_URL ?>/docente-cursos" class="btn-profile-action btn-secondary-action">
                        <i class="ri-arrow-left-line"></i> Volver a Cursos
                    </a>
                    <button class="btn-profile-action btn-icon-action" onclick="window.print()">
                        <i class="ri-printer-line"></i>
                    </button>
                </div>
            </div>

            <!-- QUICK STATS - MEJORADAS Y CLICKEABLES -->
            <div class="quick-stats">
                <!-- Card 1: Actividades Pendientes por Calificar -->
                <div class="stat-card <?= $actividadesPendientesCalificar > 10 ? 'alert-high' : ($actividadesPendientesCalificar > 5 ? 'alert-medium' : '') ?>" 
                     onclick="window.location.href='<?= BASE_URL ?>/docente-actividades-pendientes?curso=<?= $id_curso ?>'"
                     title="Ver actividades pendientes de calificación">
                    <?php if ($actividadesPendientesCalificar > 0): ?>
                        <div class="quick-actions-badge"><?= $actividadesPendientesCalificar ?></div>
                    <?php endif; ?>
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <i class="ri-file-list-3-line"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-label">
                            Pendientes por Calificar
                            <i class="ri-information-line info-tooltip" data-tooltip="Actividades que han entregado tus estudiantes"></i>
                        </span>
                        <strong class="stat-value"><?= $actividadesPendientesCalificar ?></strong>
                    </div>
                </div>

                <!-- Card 2: Estudiantes en Riesgo Académico -->
                <div class="stat-card <?= $estudiantesEnRiesgo > 5 ? 'alert-high' : ($estudiantesEnRiesgo > 0 ? 'alert-medium' : '') ?>" 
                     onclick="window.location.href='<?= BASE_URL ?>/docente-estudiantes-riesgo?curso=<?= $id_curso ?>'"
                     title="Ver estudiantes con promedio inferior a 3.0">
                    <?php if ($estudiantesEnRiesgo > 0): ?>
                        <div class="quick-actions-badge"><?= $estudiantesEnRiesgo ?></div>
                    <?php endif; ?>
                    <div class="stat-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                        <i class="ri-alert-line"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-label">
                            Estudiantes en Riesgo
                            <i class="ri-information-line info-tooltip" data-tooltip="Estudiantes con promedio < 3.0 en tus asignaturas"></i>
                        </span>
                        <strong class="stat-value"><?= $estudiantesEnRiesgo ?></strong>
                    </div>
                </div>

                <!-- Card 3: Próximas Actividades -->
                <div class="stat-card" 
                     onclick="window.location.href='<?= BASE_URL ?>/docente-proximas-actividades?curso=<?= $id_curso ?>'"
                     title="Ver actividades con fecha límite próxima">
                    <?php if ($proximasActividades > 0): ?>
                        <div class="quick-actions-badge" style="background: #f59e0b;"><?= $proximasActividades ?></div>
                    <?php endif; ?>
                    <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <i class="ri-calendar-event-line"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-label">
                            Próximas Actividades
                            <i class="ri-information-line info-tooltip" data-tooltip="Actividades con vencimiento en los próximos 7 días"></i>
                        </span>
                        <strong class="stat-value"><?= $proximasActividades ?></strong>
                    </div>
                </div>

                <!-- Card 4: Promedio General del Curso -->
                <div class="stat-card" 
                     onclick="window.location.href='<?= BASE_URL ?>/docente-rendimiento-curso?curso=<?= $id_curso ?>'"
                     title="Ver rendimiento académico del curso">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                        <i class="ri-bar-chart-box-line"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-label">
                            Promedio General
                            <i class="ri-information-line info-tooltip" data-tooltip="Promedio de tus asignaturas en este curso"></i>
                        </span>
                        <strong class="stat-value" style="color: <?= $promedioGeneral >= 4.0 ? '#10b981' : ($promedioGeneral >= 3.0 ? '#f59e0b' : '#ef4444') ?>">
                            <?= number_format($promedioGeneral, 1) ?>
                        </strong>
                    </div>
                </div>
            </div>

            <!-- ACCIONES RÁPIDAS -->
            <?php if ($totalMisAsignaturas > 0): ?>
            <div class="quick-actions-section">
                <h3 style="color: #e6e9f4; font-size: 16px; margin: 0 0 16px 0; font-weight: 600;">
                    <i class="ri-flashlight-line" style="color: #6366f1;"></i>
                    Acciones Rápidas
                </h3>
                <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                    <a href="<?= BASE_URL ?>/docente/agregar-actividad<?= isset($id_curso) ? '?id_curso='.$id_curso : '' ?>" class="quick-action-btn">
                        <i class="ri-add-circle-line"></i>
                        Nueva Actividad
                    </a>
                    <a href="<?= BASE_URL ?>/docente-calificaciones?curso=<?= $id_curso ?>" class="quick-action-btn">
                        <i class="ri-file-edit-line"></i>
                        Registrar Calificaciones
                    </a>
                    <a href="<?= BASE_URL ?>/docente-asistencia?curso=<?= $id_curso ?>" class="quick-action-btn">
                        <i class="ri-user-follow-line"></i>
                        Tomar Asistencia
                    </a>
                    <a href="<?= BASE_URL ?>/docente-reportes?curso=<?= $id_curso ?>" class="quick-action-btn">
                        <i class="ri-file-chart-line"></i>
                        Generar Reporte
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- SECCIÓN DE ASIGNATURAS - SOLO LAS DEL DOCENTE -->
            <div class="academic-section" style="margin-bottom: 32px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 0 28px;">
                    <h3 class="section-title">
                        <i class="ri-book-line" style="color: #6366f1; margin-right: 8px;"></i>
                        Mis Asignaturas en este Curso (<?= $totalMisAsignaturas ?>)
                    </h3>
                </div>

                <?php if (!empty($mis_asignaturas)): ?>
                    <div class="subjects-table">
                        <table class="table table-dark table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 60px; padding-left: 28px;">#</th>
                                    <th style="width: 30%;">Asignatura</th>
                                    <th style="width: 35%;">Descripción</th>
                                    <th style="width: 15%; text-align: center;">Estudiantes</th>
                                    <th style="width: 20%; text-align: center; padding-right: 28px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $index = 0;
                                foreach ($mis_asignaturas as $asignatura): 
                                    $index++;
                                ?>
                                    <tr>
                                        <td style="padding-left: 28px;">
                                            <div style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, rgba(79, 70, 229, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); display: flex; align-items: center; justify-content: center; font-weight: 600; color: #6366f1; font-size: 14px;">
                                                <?= $index ?>
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
                                        <td style="text-align: center;">
                                            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: rgba(99, 102, 241, 0.15); color: #6366f1; border-radius: 8px; font-weight: 600; font-size: 14px;">
                                                <i class="ri-group-line"></i>
                                                <?= $totalEstudiantes ?>
                                            </span>
                                        </td>
                                        <td style="text-align: center; padding-right: 28px;">
                                            <button type="button"
                                               class="btn-open-actividades-modal"
                                               data-asignatura-id="<?= (int)$asignatura['id_asignatura'] ?>"
                                               data-asignatura-nombre="<?= htmlspecialchars($asignatura['asignatura'], ENT_QUOTES, 'UTF-8') ?>"
                                               style="padding: 10px 14px; border-radius: 8px; text-decoration: none; transition: all 0.2s ease; display: inline-flex; align-items: center; justify-content: center; font-size: 16px; background: rgba(79, 70, 229, 0.1); color: #6366f1; margin-right: 8px; border: none;"
                                               onmouseover="this.style.background='rgba(79, 70, 229, 0.2)'; this.style.transform='scale(1.1)'"
                                               onmouseout="this.style.background='rgba(79, 70, 229, 0.1)'; this.style.transform='scale(1)'"
                                               title="Ver actividades">
                                                <i class="ri-file-list-3-line"></i>
                                            </button>
                                            <button type="button"
                                               class="btn-open-calificaciones-modal"
                                               data-asignatura-id="<?= (int)$asignatura['id_asignatura'] ?>"
                                               data-asignatura-nombre="<?= htmlspecialchars($asignatura['asignatura'], ENT_QUOTES, 'UTF-8') ?>"
                                               style="padding: 10px 14px; border-radius: 8px; text-decoration: none; transition: all 0.2s ease; display: inline-flex; align-items: center; justify-content: center; font-size: 16px; background: rgba(16, 185, 129, 0.1); color: #10b981; border: none;"
                                               onmouseover="this.style.background='rgba(16, 185, 129, 0.2)'; this.style.transform='scale(1.1)'"
                                               onmouseout="this.style.background='rgba(16, 185, 129, 0.1)'; this.style.transform='scale(1)'"
                                               title="Ver calificaciones">
                                                <i class="ri-file-edit-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="ri-book-line empty-state-icon"></i>
                        <h3 class="empty-state-title">No tienes asignaturas asignadas en este curso</h3>
                        <p class="empty-state-description">
                            Actualmente no estás dictando ninguna asignatura en <?= $curso['grado'] ?>° - <?= $curso['curso'] ?>.
                            <br>Contacta al coordinador académico si crees que esto es un error.
                        </p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- TABLA DE ESTUDIANTES -->
            <div class="academic-section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 0 28px;">
                    <h3 class="section-title">
                        <i class="ri-team-line" style="color: #6366f1; margin-right: 8px;"></i>
                        Estudiantes del Curso (<?= $totalEstudiantes ?>)
                    </h3>
                    <?php if ($totalEstudiantes > 0): ?>
                        <div style="display: flex; gap: 12px;">
                            <input type="text" 
                                   id="searchStudent" 
                                   placeholder="Buscar estudiante..." 
                                   style="padding: 10px 16px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #e6e9f4; font-size: 14px; width: 250px;"
                                   onkeyup="filtrarEstudiantes()">
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($estudiantes)): ?>
                    <div class="subjects-table">
                        <table class="table table-dark table-hover" id="tablaEstudiantes">
                            <thead>
                                <tr>
                                    <th style="width: 80px; padding-left: 28px;">#</th>
                                    <th style="width: 35%;">Estudiante</th>
                                    <th style="width: 20%;">Documento</th>
                                    <th style="width: 20%;">Fecha de Matrícula</th>
                                    <th style="width: 25%; text-align: center; padding-right: 28px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($estudiantes as $index => $estudiante): ?>
                                    <tr class="student-row">
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
                                                    <div class="student-name" style="font-weight: 600; color: #e6e9f4; font-size: 15px;">
                                                        <?= htmlspecialchars($estudiante['estudiante_nombres'] . ' ' . $estudiante['estudiante_apellidos']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="student-document"><?= htmlspecialchars($estudiante['estudiante_documento']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($estudiante['fecha'])) ?></td>
                                        <td style="text-align: center; padding-right: 28px;">
                                            <button type="button"
                                               class="btn-open-perfil-estudiante-modal"
                                               data-estudiante-id="<?= (int)$estudiante['id_estudiante'] ?>"
                                               data-estudiante-nombre="<?= htmlspecialchars($estudiante['estudiante_nombres'] . ' ' . $estudiante['estudiante_apellidos'], ENT_QUOTES, 'UTF-8') ?>"
                                               style="padding: 10px 14px; border-radius: 8px; text-decoration: none; transition: all 0.2s ease; display: inline-flex; align-items: center; justify-content: center; font-size: 16px; background: rgba(79, 70, 229, 0.1); color: #6366f1; margin-right: 8px; border: none;"
                                               onmouseover="this.style.background='rgba(79, 70, 229, 0.2)'; this.style.transform='scale(1.1)'"
                                               onmouseout="this.style.background='rgba(79, 70, 229, 0.1)'; this.style.transform='scale(1)'"
                                               title="Ver perfil académico">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                            <button type="button"
                                               class="btn-open-calificaciones-estudiante-modal"
                                               data-estudiante-id="<?= (int)$estudiante['id_estudiante'] ?>"
                                               data-estudiante-nombre="<?= htmlspecialchars($estudiante['estudiante_nombres'] . ' ' . $estudiante['estudiante_apellidos'], ENT_QUOTES, 'UTF-8') ?>"
                                               style="padding: 10px 14px; border-radius: 8px; text-decoration: none; transition: all 0.2s ease; display: inline-flex; align-items: center; justify-content: center; font-size: 16px; background: rgba(16, 185, 129, 0.1); color: #10b981; border: none;"
                                               onmouseover="this.style.background='rgba(16, 185, 129, 0.2)'; this.style.transform='scale(1.1)'"
                                               onmouseout="this.style.background='rgba(16, 185, 129, 0.1)'; this.style.transform='scale(1)'"
                                               title="Ver/editar calificaciones">
                                                <i class="ri-file-edit-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="ri-user-unfollow-line empty-state-icon"></i>
                        <h3 class="empty-state-title">No hay estudiantes matriculados</h3>
                        <p class="empty-state-description">
                            Este curso aún no tiene estudiantes matriculados para el año <?= $anioActual ?>.
                        </p>
                    </div>
                <?php endif; ?>
            </div>

        </main>
    </div>

    <!-- MODAL: ACTIVIDADES DE LA ASIGNATURA -->
    <div class="modal fade" id="modalActividadesAsignatura" tabindex="-1" aria-labelledby="modalActividadesAsignaturaLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content" style="background: #0f172a; color: #e6e9f4; border: 1px solid rgba(255,255,255,0.08); border-radius: 16px;">
                <div class="modal-header" style="border-bottom: 1px solid rgba(255,255,255,0.08);">
                    <h5 class="modal-title" id="modalActividadesAsignaturaLabel">
                        <i class="ri-file-list-3-line" style="color: #6366f1; margin-right: 8px;"></i>
                        Actividades de la asignatura
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div id="actividadesAsignaturaResumen" style="display:flex; gap: 12px; flex-wrap: wrap; margin-bottom: 16px;"></div>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle" style="margin-bottom: 0;">
                            <thead>
                                <tr>
                                    <th>Actividad</th>
                                    <th>Tipo</th>
                                    <th>Entrega</th>
                                    <th>Estado</th>
                                    <th style="text-align:center;">Entregas</th>
                                    <th style="text-align:center;">Calificadas</th>
                                    <th style="text-align:center;">Acción</th>
                                </tr>
                            </thead>
                            <tbody id="tablaActividadesAsignaturaBody"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.08);">
                    <a id="btnModalNuevaActividad" href="<?= BASE_URL ?>/docente/agregar-actividad?id_curso=<?= (int)$id_curso ?>" class="btn btn-primary" style="background:#6366f1; border-color:#6366f1;">
                        <i class="ri-add-line"></i> Nueva actividad
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: RESUMEN DE CALIFICACIONES -->
    <div class="modal fade" id="modalCalificacionesAsignatura" tabindex="-1" aria-labelledby="modalCalificacionesAsignaturaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content" style="background: #0f172a; color: #e6e9f4; border: 1px solid rgba(255,255,255,0.08); border-radius: 16px;">
                <div class="modal-header" style="border-bottom: 1px solid rgba(255,255,255,0.08);">
                    <h5 class="modal-title" id="modalCalificacionesAsignaturaLabel">
                        <i class="ri-bar-chart-box-line" style="color: #10b981; margin-right: 8px;"></i>
                        Resumen de calificaciones
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div id="calificacionesAsignaturaResumen" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 12px;"></div>
                    <div id="calificacionesAsignaturaDetalle" style="margin-top: 18px;"></div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.08);">
                    <a id="btnModalVerActividades" href="<?= BASE_URL ?>/docente/actividades?id_curso=<?= (int)$id_curso ?>" class="btn btn-success" style="background:#10b981; border-color:#10b981;">
                        <i class="ri-arrow-right-line"></i> Ir a actividades
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: PERFIL ACADEMICO DEL ESTUDIANTE -->
    <div class="modal fade" id="modalPerfilAcademicoEstudiante" tabindex="-1" aria-labelledby="modalPerfilAcademicoEstudianteLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content" style="background: #0f172a; color: #e6e9f4; border: 1px solid rgba(255,255,255,0.08); border-radius: 16px;">
                <div class="modal-header" style="border-bottom: 1px solid rgba(255,255,255,0.08);">
                    <h5 class="modal-title" id="modalPerfilAcademicoEstudianteLabel">
                        <i class="ri-user-star-line" style="color:#6366f1; margin-right:8px;"></i>
                        Perfil academico
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div id="perfilAcademicoEstudianteContenido"></div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.08);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: CALIFICACIONES DEL ESTUDIANTE -->
    <div class="modal fade" id="modalCalificacionesEstudiante" tabindex="-1" aria-labelledby="modalCalificacionesEstudianteLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content" style="background: #0f172a; color: #e6e9f4; border: 1px solid rgba(255,255,255,0.08); border-radius: 16px;">
                <div class="modal-header" style="border-bottom: 1px solid rgba(255,255,255,0.08);">
                    <h5 class="modal-title" id="modalCalificacionesEstudianteLabel">
                        <i class="ri-file-edit-line" style="color:#10b981; margin-right:8px;"></i>
                        Calificaciones del estudiante
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div id="calificacionesEstudianteResumen" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 12px; margin-bottom: 16px;"></div>
                    <div id="calificacionesEstudianteDetalle"></div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.08);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>/public/assets/dashboard/js/docente/detalle-curso.js?v=<?= $detalleCursoJsVersion ?>"></script>
    
</body>   
</html>