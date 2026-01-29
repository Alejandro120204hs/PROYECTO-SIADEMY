<?php 
    
    require_once BASE_PATH . '/app/controllers/administrador/curso.php';
    require_once BASE_PATH . '/app/models/administradores/matricula.php';
    require_once BASE_PATH . '/app/models/administradores/docente_asignatura.php';
    
    // Obtener el ID del curso y validar
    $id_curso = $_GET['id'] ?? 0;
    
    if (!$id_curso) {
        header('Location: ' . BASE_URL . '/docente-cursos');
        exit;
    }
    
    // Obtener información del curso
    $curso = mostrarCursoId($id_curso);
    
    if (!$curso) {
        header('Location: ' . BASE_URL . '/docente-cursos');
        exit;
    }
    
    // Obtener ID del docente actual (asumiendo que está en sesión)
    session_start();

    $id_docente = $_SESSION['usuario']['id_docente'] ?? 0;

    
    
    // Obtener estudiantes matriculados en el curso
    $matriculaObj = new Matricula();
    $anioActual = date('Y');
    $estudiantes = $matriculaObj->listarPorCurso($id_curso, $anioActual);
    
    // Obtener asignaturas del curso con sus docentes
    $docenteAsignaturaObj = new DocenteAsignatura();
    $asignaturas = $docenteAsignaturaObj->obtenerAsignaturasPorCurso($id_curso);
    
    // FILTRAR solo las asignaturas que imparte este docente
    $mis_asignaturas = array_filter($asignaturas, function($asignatura) use ($id_docente) {
        if (empty($asignatura['docentes'])) return false;
        
        foreach ($asignatura['docentes'] as $docente) {
            if ($docente['id_docente'] == $id_docente && $docente['estado'] === 'activo') {
                return true;
            }
        }
        return false;
    });
    
    
    // Calcular estadísticas
    $totalEstudiantes = count($estudiantes);
    $totalMisAsignaturas = count($mis_asignaturas);
    
    // TODO: Estas métricas deben venir del controlador con queries específicas
    // Por ahora son placeholder - REEMPLAZAR con datos reales
    $actividadesPendientesCalificar = 0; // Query: actividades del docente en este curso sin calificar
    $estudiantesEnRiesgo = 0; // Query: estudiantes con promedio < 3.0 en asignaturas del docente
    $proximasActividades = 0; // Query: actividades con fecha límite próxima (próximos 7 días)
    $promedioGeneral = 0; // Query: promedio de las asignaturas del docente en este curso

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
        /* RESET Y ESTRUCTURA BASE */
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

        .main {
            padding: 28px 0 40px 0 !important;
        }

        .student-profile-header {
            padding-left: 28px !important;
            padding-right: 28px !important;
        }

        .quick-stats {
            padding-left: 28px !important;
            padding-right: 28px !important;
        }

        .topbar {
            padding-left: 28px !important;
            padding-right: 28px !important;
        }

        /* MEJORAS EN CARDS - Hacerlas clickeables */
        .stat-card {
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3);
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-card:active {
            transform: translateY(-2px);
        }

        /* Indicador de clickeable */
        .stat-card::after {
            content: '\ea6e'; /* Remix icon arrow-right */
            font-family: 'remixicon';
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%) translateX(10px);
            opacity: 0;
            transition: all 0.3s ease;
            color: rgba(255, 255, 255, 0.6);
            font-size: 20px;
        }

        .stat-card:hover::after {
            opacity: 1;
            transform: translateY(-50%) translateX(0);
        }

        /* Estado de alerta en cards */
        .stat-card.alert-high .stat-value {
            color: #ef4444;
            animation: pulse-red 2s infinite;
        }

        .stat-card.alert-medium .stat-value {
            color: #f59e0b;
        }

        @keyframes pulse-red {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        /* Mejora visual del badge de acciones rápidas */
        .quick-actions-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ef4444;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.4);
            animation: bounce-in 0.5s ease;
        }

        @keyframes bounce-in {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        /* Empty states mejorados */
        .empty-state {
            text-align: center;
            padding: 80px 40px;
            background: rgba(79, 70, 229, 0.05);
            border-radius: 16px;
            margin: 0 28px;
        }

        .empty-state-icon {
            font-size: 64px;
            color: rgba(79, 70, 229, 0.3);
            margin-bottom: 16px;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .empty-state-title {
            color: #e6e9f4;
            margin: 0 0 8px 0;
            font-size: 20px;
            font-weight: 600;
        }

        .empty-state-description {
            color: #97a1b6;
            font-size: 14px;
            margin: 0 0 20px 0;
        }

        .empty-state-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .empty-state-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
        }

        /* Sección de acciones rápidas */
        .quick-actions-section {
            margin: 24px 28px;
            padding: 24px;
            background: rgba(79, 70, 229, 0.08);
            border-radius: 16px;
            border: 1px solid rgba(79, 70, 229, 0.15);
        }

        .quick-action-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            background: rgba(255, 255, 255, 0.05);
            color: #e6e9f4;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .quick-action-btn:hover {
            background: rgba(79, 70, 229, 0.2);
            border-color: rgba(79, 70, 229, 0.3);
            transform: translateX(4px);
        }

        .quick-action-btn i {
            font-size: 20px;
        }

        /* Tooltip para información adicional */
        .info-tooltip {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            background: rgba(99, 102, 241, 0.2);
            color: #6366f1;
            border-radius: 50%;
            font-size: 12px;
            cursor: help;
            margin-left: 6px;
        }

        .info-tooltip:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            padding: 8px 12px;
            background: #1f2937;
            color: #e6e9f4;
            border-radius: 8px;
            font-size: 12px;
            white-space: nowrap;
            margin-bottom: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            z-index: 1000;
        }

        /* Indicador de periodo activo */
        .period-indicator {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 12px;
        }

        .period-indicator i {
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="app" id="appGrid">
        <!-- LEFT SIDEBAR -->
        <?php include_once __DIR__ . '/../../layouts/sidebar_docente.php' ?>

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
                    <a href="<?= BASE_URL ?>/docente-crear-actividad?curso=<?= $id_curso ?>" class="quick-action-btn">
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
                                            <a href="<?= BASE_URL ?>/docente-actividades?asignatura=<?= $asignatura['id_asignatura'] ?>&curso=<?= $id_curso ?>" 
                                               style="padding: 10px 14px; border-radius: 8px; text-decoration: none; transition: all 0.2s ease; display: inline-flex; align-items: center; justify-content: center; font-size: 16px; background: rgba(79, 70, 229, 0.1); color: #6366f1; margin-right: 8px;"
                                               onmouseover="this.style.background='rgba(79, 70, 229, 0.2)'; this.style.transform='scale(1.1)'"
                                               onmouseout="this.style.background='rgba(79, 70, 229, 0.1)'; this.style.transform='scale(1)'"
                                               title="Ver actividades">
                                                <i class="ri-file-list-3-line"></i>
                                            </a>
                                            <a href="<?= BASE_URL ?>/docente-calificaciones?asignatura=<?= $asignatura['id_asignatura'] ?>&curso=<?= $id_curso ?>" 
                                               style="padding: 10px 14px; border-radius: 8px; text-decoration: none; transition: all 0.2s ease; display: inline-flex; align-items: center; justify-content: center; font-size: 16px; background: rgba(16, 185, 129, 0.1); color: #10b981;"
                                               onmouseover="this.style.background='rgba(16, 185, 129, 0.2)'; this.style.transform='scale(1.1)'"
                                               onmouseout="this.style.background='rgba(16, 185, 129, 0.1)'; this.style.transform='scale(1)'"
                                               title="Ver calificaciones">
                                                <i class="ri-file-edit-line"></i>
                                            </a>
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
                                            <a href="<?= BASE_URL ?>/docente-perfil-estudiante?id=<?= $estudiante['id_estudiante'] ?>&curso=<?= $id_curso ?>" 
                                               style="padding: 10px 14px; border-radius: 8px; text-decoration: none; transition: all 0.2s ease; display: inline-flex; align-items: center; justify-content: center; font-size: 16px; background: rgba(79, 70, 229, 0.1); color: #6366f1; margin-right: 8px;"
                                               onmouseover="this.style.background='rgba(79, 70, 229, 0.2)'; this.style.transform='scale(1.1)'"
                                               onmouseout="this.style.background='rgba(79, 70, 229, 0.1)'; this.style.transform='scale(1)'"
                                               title="Ver perfil académico">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                            <a href="<?= BASE_URL ?>/docente-calificaciones-estudiante?id=<?= $estudiante['id_estudiante'] ?>&curso=<?= $id_curso ?>" 
                                               style="padding: 10px 14px; border-radius: 8px; text-decoration: none; transition: all 0.2s ease; display: inline-flex; align-items: center; justify-content: center; font-size: 16px; background: rgba(16, 185, 129, 0.1); color: #10b981;"
                                               onmouseover="this.style.background='rgba(16, 185, 129, 0.2)'; this.style.transform='scale(1.1)'"
                                               onmouseout="this.style.background='rgba(16, 185, 129, 0.1)'; this.style.transform='scale(1)'"
                                               title="Ver/editar calificaciones">
                                                <i class="ri-file-edit-line"></i>
                                            </a>
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

    <script>
        // Filtrar estudiantes en tiempo real
        function filtrarEstudiantes() {
            const input = document.getElementById('searchStudent');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('tablaEstudiantes');
            const rows = table.getElementsByClassName('student-row');

            for (let i = 0; i < rows.length; i++) {
                const nameCell = rows[i].querySelector('.student-name');
                const docCell = rows[i].querySelector('.student-document');
                
                if (nameCell || docCell) {
                    const nameText = nameCell ? nameCell.textContent.toLowerCase() : '';
                    const docText = docCell ? docCell.textContent.toLowerCase() : '';
                    
                    if (nameText.indexOf(filter) > -1 || docText.indexOf(filter) > -1) {
                        rows[i].style.display = '';
                    } else {
                        rows[i].style.display = 'none';
                    }
                }
            }
        }

        // Confirmación antes de imprimir
        document.querySelector('.btn-icon-action').addEventListener('click', function(e) {
            if (!confirm('¿Deseas imprimir la información de este curso?')) {
                e.preventDefault();
            }
        });

        // Animación de entrada para las cards
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.stat-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
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