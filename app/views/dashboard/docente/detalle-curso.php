<?php 
    // 1. INICIAR SESIÓN
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // 2. VERIFICAR QUE ESTÉ LOGUEADO
    if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'Docente') {
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
    
    // 3. CARGAR DEPENDENCIAS
    require_once BASE_PATH . '/app/controllers/administrador/curso.php';
    require_once BASE_PATH . '/app/controllers/perfil.php';
    require_once BASE_PATH . '/app/models/administradores/matricula.php';
    require_once BASE_PATH . '/app/models/administradores/docente_asignatura.php';
    
    // 4. OBTENER ID DEL CURSO
    $id_curso = $_GET['id'] ?? 0;
    
    if (!$id_curso) {
        header('Location: ' . BASE_URL . '/docente-cursos');
        exit;
    }
    
    // 5. OBTENER DATOS DEL CURSO
    $curso = mostrarCursoId($id_curso);
    
    if (!$curso) {
        header('Location: ' . BASE_URL . '/docente-cursos');
        exit;
    }
    
    // 6. OBTENER ID DOCENTE desde la tabla docentes
    $id_usuario_sesion = $_SESSION['user']['id'];
    
    // Obtener conexión PDO (ajusta según tu configuración)
    require_once BASE_PATH . '/config/database.php';
    $db = new Conexion();
    $pdo = $db->getConexion();
    
    $stmt = $pdo->prepare("SELECT id, nombres, apellidos FROM docente WHERE id_usuario = ?");
    $stmt->execute([$id_usuario_sesion]);
    $docente_info = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$docente_info) {
        // Si no existe el docente, redirigir
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    $usuario = mostrarPerfil($id_usuario_sesion);
    
    $id_docente = $docente_info['id'];
    $nombre_docente = $docente_info['nombres'] . ' ' . $docente_info['apellidos'];
    
    // 7. OBTENER DATOS
    $matriculaObj = new Matricula();
    $anioActual = date('Y');
    $estudiantes = $matriculaObj->listarPorCurso($id_curso, $anioActual) ?: [];
    
    $docenteAsignaturaObj = new DocenteAsignatura();
    $asignaturas = $docenteAsignaturaObj->obtenerAsignaturasPorCurso($id_curso) ?: [];
    
    // 8. FILTRAR SOLO ASIGNATURAS DEL DOCENTE
    $mis_asignaturas = [];
    foreach ($asignaturas as $asignatura) {
        if (!empty($asignatura['docentes'])) {
            foreach ($asignatura['docentes'] as $docente) {
                // Ahora SÍ existe id_docente
                if ($docente['id_docente'] == $id_docente && $docente['estado'] === 'activo') {
                    $mis_asignaturas[] = $asignatura;
                    break; // Solo agregar una vez por asignatura
                }
            }
        }
    }
    
    // 9. CALCULAR ESTADÍSTICAS
    $totalEstudiantes = count($estudiantes);
    $totalMisAsignaturas = count($mis_asignaturas);
    
    // Métricas placeholder - TODO: Implementar queries reales
    $actividadesPendientesCalificar = 0;
    $estudiantesEnRiesgo = 0;
    $proximasActividades = 0;
    $promedioGeneral = 0;

    // 9.1 DATOS REALES PARA ACCIONES DE ESTUDIANTES (PERFIL Y CALIFICACIONES)
    $perfilAcademicoPorEstudiante = [];
    $calificacionesPorEstudiante = [];

    foreach ($estudiantes as $estudianteBase) {
        $idEstudianteBase = (int)($estudianteBase['id_estudiante'] ?? 0);
        if ($idEstudianteBase <= 0) {
            continue;
        }

        $perfilAcademicoPorEstudiante[$idEstudianteBase] = [
            'nombre' => trim((string)($estudianteBase['estudiante_nombres'] ?? '') . ' ' . (string)($estudianteBase['estudiante_apellidos'] ?? '')),
            'documento' => (string)($estudianteBase['estudiante_documento'] ?? ''),
            'foto' => (string)($estudianteBase['foto'] ?? 'default.png'),
            'fecha_matricula' => (string)($estudianteBase['fecha'] ?? ''),
            'total_actividades' => 0,
            'total_entregadas' => 0,
            'total_calificadas' => 0,
            'promedio_general' => null,
            'ultima_calificacion' => null
        ];

        $calificacionesPorEstudiante[$idEstudianteBase] = [];
    }

    try {
        $sqlPerfilAcademico = "SELECT
                                m.id_estudiante,
                                COUNT(DISTINCT a.id) AS total_actividades,
                                COUNT(DISTINCT ea.id) AS total_entregadas,
                                COUNT(DISTINCT c.id) AS total_calificadas,
                                AVG(c.nota) AS promedio_general,
                                MAX(c.fecha_calificacion) AS ultima_calificacion
                            FROM matricula m
                            LEFT JOIN asignatura_curso ac
                                ON ac.id_curso = m.id_curso
                               AND ac.estado = 'activo'
                            LEFT JOIN docente_asignatura_curso dac
                                ON dac.id_asignatura_curso = ac.id
                               AND dac.id_docente = :id_docente
                               AND dac.estado = 'activo'
                            LEFT JOIN actividad a
                                ON a.id_asignatura_curso = ac.id
                               AND a.id_docente = :id_docente_actividad
                               AND a.id_institucion = :id_institucion_actividad
                            LEFT JOIN entrega_actividad ea
                                ON ea.id_actividad = a.id
                               AND ea.id_estudiante = m.id_estudiante
                            LEFT JOIN calificacion c
                                ON c.id_entrega = ea.id
                            WHERE m.id_curso = :id_curso
                              AND m.id_institucion = :id_institucion
                              AND m.anio = :anio
                              AND dac.id IS NOT NULL
                            GROUP BY m.id_estudiante";

        $stmtPerfilAcademico = $pdo->prepare($sqlPerfilAcademico);
        $stmtPerfilAcademico->bindValue(':id_docente', (int)$id_docente, PDO::PARAM_INT);
        $stmtPerfilAcademico->bindValue(':id_docente_actividad', (int)$id_docente, PDO::PARAM_INT);
        $stmtPerfilAcademico->bindValue(':id_institucion_actividad', (int)$_SESSION['user']['id_institucion'], PDO::PARAM_INT);
        $stmtPerfilAcademico->bindValue(':id_curso', (int)$id_curso, PDO::PARAM_INT);
        $stmtPerfilAcademico->bindValue(':id_institucion', (int)$_SESSION['user']['id_institucion'], PDO::PARAM_INT);
        $stmtPerfilAcademico->bindValue(':anio', (int)$anioActual, PDO::PARAM_INT);
        $stmtPerfilAcademico->execute();

        $perfilesDb = $stmtPerfilAcademico->fetchAll(PDO::FETCH_ASSOC) ?: [];

        foreach ($perfilesDb as $perfilDb) {
            $idEstudiantePerfil = (int)($perfilDb['id_estudiante'] ?? 0);
            if ($idEstudiantePerfil <= 0 || !isset($perfilAcademicoPorEstudiante[$idEstudiantePerfil])) {
                continue;
            }

            $perfilAcademicoPorEstudiante[$idEstudiantePerfil]['total_actividades'] = (int)($perfilDb['total_actividades'] ?? 0);
            $perfilAcademicoPorEstudiante[$idEstudiantePerfil]['total_entregadas'] = (int)($perfilDb['total_entregadas'] ?? 0);
            $perfilAcademicoPorEstudiante[$idEstudiantePerfil]['total_calificadas'] = (int)($perfilDb['total_calificadas'] ?? 0);
            $perfilAcademicoPorEstudiante[$idEstudiantePerfil]['promedio_general'] = $perfilDb['promedio_general'] !== null
                ? round((float)$perfilDb['promedio_general'], 2)
                : null;
            $perfilAcademicoPorEstudiante[$idEstudiantePerfil]['ultima_calificacion'] = $perfilDb['ultima_calificacion'] ?: null;
        }

        $sqlCalificaciones = "SELECT
                                m.id_estudiante,
                                a.id AS id_actividad,
                                a.titulo,
                                a.tipo,
                                a.fecha_entrega AS fecha_limite,
                                asig.nombre AS asignatura,
                                ea.id AS id_entrega,
                                ea.estado AS estado_entrega,
                                c.nota,
                                c.observacion
                            FROM matricula m
                            LEFT JOIN asignatura_curso ac
                                ON ac.id_curso = m.id_curso
                               AND ac.estado = 'activo'
                            LEFT JOIN docente_asignatura_curso dac
                                ON dac.id_asignatura_curso = ac.id
                               AND dac.id_docente = :id_docente_det
                               AND dac.estado = 'activo'
                            LEFT JOIN actividad a
                                ON a.id_asignatura_curso = ac.id
                               AND a.id_docente = :id_docente_act_det
                               AND a.id_institucion = :id_inst_act_det
                            LEFT JOIN asignatura asig
                                ON asig.id = a.id_asignatura
                            LEFT JOIN entrega_actividad ea
                                ON ea.id_actividad = a.id
                               AND ea.id_estudiante = m.id_estudiante
                            LEFT JOIN calificacion c
                                ON c.id_entrega = ea.id
                            WHERE m.id_curso = :id_curso_det
                              AND m.id_institucion = :id_inst_det
                              AND m.anio = :anio_det
                              AND dac.id IS NOT NULL
                              AND a.id IS NOT NULL
                            ORDER BY m.id_estudiante, a.fecha_entrega DESC, a.id DESC";

        $stmtCalificaciones = $pdo->prepare($sqlCalificaciones);
        $stmtCalificaciones->bindValue(':id_docente_det', (int)$id_docente, PDO::PARAM_INT);
        $stmtCalificaciones->bindValue(':id_docente_act_det', (int)$id_docente, PDO::PARAM_INT);
        $stmtCalificaciones->bindValue(':id_inst_act_det', (int)$_SESSION['user']['id_institucion'], PDO::PARAM_INT);
        $stmtCalificaciones->bindValue(':id_curso_det', (int)$id_curso, PDO::PARAM_INT);
        $stmtCalificaciones->bindValue(':id_inst_det', (int)$_SESSION['user']['id_institucion'], PDO::PARAM_INT);
        $stmtCalificaciones->bindValue(':anio_det', (int)$anioActual, PDO::PARAM_INT);
        $stmtCalificaciones->execute();

        $calificacionesDb = $stmtCalificaciones->fetchAll(PDO::FETCH_ASSOC) ?: [];

        foreach ($calificacionesDb as $calificacionDb) {
            $idEstudianteCal = (int)($calificacionDb['id_estudiante'] ?? 0);
            if ($idEstudianteCal <= 0 || !isset($calificacionesPorEstudiante[$idEstudianteCal])) {
                continue;
            }

            $idActividad = (int)($calificacionDb['id_actividad'] ?? 0);

            $calificacionesPorEstudiante[$idEstudianteCal][] = [
                'id_actividad' => $idActividad,
                'id_entrega' => isset($calificacionDb['id_entrega']) ? (int)$calificacionDb['id_entrega'] : null,
                'titulo' => (string)($calificacionDb['titulo'] ?? 'Actividad sin titulo'),
                'tipo' => (string)($calificacionDb['tipo'] ?? 'Sin tipo'),
                'asignatura' => (string)($calificacionDb['asignatura'] ?? 'Sin asignatura'),
                'fecha_limite' => (string)($calificacionDb['fecha_limite'] ?? ''),
                'estado_entrega' => (string)($calificacionDb['estado_entrega'] ?? 'Pendiente'),
                'nota' => $calificacionDb['nota'] !== null ? round((float)$calificacionDb['nota'], 2) : null,
                'observacion' => (string)($calificacionDb['observacion'] ?? ''),
                'url_entregas' => BASE_URL . '/docente/ver-entregas?id_actividad=' . $idActividad
            ];
        }
    } catch (Throwable $e) {
        error_log('Error preparando datos de estudiantes en detalle curso docente: ' . $e->getMessage());
    }

    // 10. DATOS REALES PARA MODALES DE ACCIONES POR ASIGNATURA
    $detalleActividadesPorAsignatura = [];
    $resumenCalificacionesPorAsignatura = [];

    foreach ($mis_asignaturas as $asignatura) {
        $idAsignatura = (int)($asignatura['id_asignatura'] ?? 0);
        if ($idAsignatura > 0) {
            $detalleActividadesPorAsignatura[$idAsignatura] = [];
            $resumenCalificacionesPorAsignatura[$idAsignatura] = [
                'total_actividades' => 0,
                'total_entregas' => 0,
                'total_calificadas' => 0,
                'promedio_general' => null,
                'actividades' => []
            ];
        }
    }

    try {
        $sqlActividades = "SELECT
                            a.id,
                            a.id_asignatura,
                            a.titulo,
                            a.tipo,
                            a.estado,
                            a.fecha_entrega,
                            a.ponderacion,
                            COUNT(DISTINCT ea.id) AS total_entregas,
                            COUNT(DISTINCT c.id) AS total_calificadas,
                            AVG(c.nota) AS promedio_notas
                        FROM actividad a
                        INNER JOIN asignatura_curso ac ON a.id_asignatura_curso = ac.id
                        LEFT JOIN entrega_actividad ea ON ea.id_actividad = a.id
                        LEFT JOIN calificacion c ON c.id_entrega = ea.id
                        WHERE ac.id_curso = :id_curso
                          AND a.id_docente = :id_docente
                          AND a.id_institucion = :id_institucion
                        GROUP BY a.id, a.id_asignatura, a.titulo, a.tipo, a.estado, a.fecha_entrega, a.ponderacion
                        ORDER BY a.fecha_entrega DESC, a.id DESC";

        $stmtActividades = $pdo->prepare($sqlActividades);
        $stmtActividades->bindValue(':id_curso', (int)$id_curso, PDO::PARAM_INT);
        $stmtActividades->bindValue(':id_docente', (int)$id_docente, PDO::PARAM_INT);
        $stmtActividades->bindValue(':id_institucion', (int)$_SESSION['user']['id_institucion'], PDO::PARAM_INT);
        $stmtActividades->execute();

        $actividadesCurso = $stmtActividades->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $acumuladoresPromedio = [];

        foreach ($actividadesCurso as $actividad) {
            $idAsignaturaActividad = (int)($actividad['id_asignatura'] ?? 0);

            if ($idAsignaturaActividad <= 0 || !isset($detalleActividadesPorAsignatura[$idAsignaturaActividad])) {
                continue;
            }

            $promedioActividad = $actividad['promedio_notas'] !== null
                ? round((float)$actividad['promedio_notas'], 2)
                : null;

            $actividadDetalle = [
                'id' => (int)$actividad['id'],
                'titulo' => (string)($actividad['titulo'] ?? 'Actividad sin titulo'),
                'tipo' => (string)($actividad['tipo'] ?? 'Sin tipo'),
                'estado' => (string)($actividad['estado'] ?? 'activa'),
                'fecha_entrega' => (string)($actividad['fecha_entrega'] ?? ''),
                'ponderacion' => isset($actividad['ponderacion']) ? (float)$actividad['ponderacion'] : 0,
                'total_entregas' => (int)($actividad['total_entregas'] ?? 0),
                'total_calificadas' => (int)($actividad['total_calificadas'] ?? 0),
                'promedio_notas' => $promedioActividad,
                'url_entregas' => BASE_URL . '/docente/ver-entregas?id_actividad=' . (int)$actividad['id']
            ];

            $detalleActividadesPorAsignatura[$idAsignaturaActividad][] = $actividadDetalle;

            $resumenCalificacionesPorAsignatura[$idAsignaturaActividad]['total_actividades']++;
            $resumenCalificacionesPorAsignatura[$idAsignaturaActividad]['total_entregas'] += $actividadDetalle['total_entregas'];
            $resumenCalificacionesPorAsignatura[$idAsignaturaActividad]['total_calificadas'] += $actividadDetalle['total_calificadas'];
            $resumenCalificacionesPorAsignatura[$idAsignaturaActividad]['actividades'][] = $actividadDetalle;

            if ($promedioActividad !== null) {
                if (!isset($acumuladoresPromedio[$idAsignaturaActividad])) {
                    $acumuladoresPromedio[$idAsignaturaActividad] = [];
                }
                $acumuladoresPromedio[$idAsignaturaActividad][] = $promedioActividad;
            }
        }

        foreach ($acumuladoresPromedio as $idAsignaturaPromedio => $promedios) {
            if (!empty($promedios) && isset($resumenCalificacionesPorAsignatura[$idAsignaturaPromedio])) {
                $resumenCalificacionesPorAsignatura[$idAsignaturaPromedio]['promedio_general'] = round(array_sum($promedios) / count($promedios), 2);
            }
        }
    } catch (Throwable $e) {
        error_log('Error preparando modales de detalle curso docente: ' . $e->getMessage());
    }
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
    <script>
        const detalleActividadesPorAsignatura = <?= json_encode($detalleActividadesPorAsignatura, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
        const resumenCalificacionesPorAsignatura = <?= json_encode($resumenCalificacionesPorAsignatura, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
        const perfilAcademicoPorEstudiante = <?= json_encode($perfilAcademicoPorEstudiante, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
        const calificacionesPorEstudiante = <?= json_encode($calificacionesPorEstudiante, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

        function formatoFecha(fechaIso) {
            if (!fechaIso) return 'Sin fecha';
            const fecha = new Date(fechaIso + 'T00:00:00');
            if (Number.isNaN(fecha.getTime())) return 'Sin fecha';
            return fecha.toLocaleDateString('es-CO', { year: 'numeric', month: 'short', day: '2-digit' });
        }

        function badgeEstado(estado) {
            const valor = (estado || '').toString().toLowerCase();
            if (valor === 'vencida') return { clase: 'bg-danger-subtle text-danger', texto: 'Vencida' };
            if (valor === 'cerrada' || valor === 'inactiva') return { clase: 'bg-secondary-subtle text-secondary', texto: 'Cerrada' };
            return { clase: 'bg-success-subtle text-success', texto: 'Activa' };
        }

        function formatearFechaLarga(fechaIso) {
            if (!fechaIso) return 'Sin registro';
            const fecha = new Date(fechaIso);
            if (Number.isNaN(fecha.getTime())) return 'Sin registro';
            return fecha.toLocaleDateString('es-CO', {
                year: 'numeric',
                month: 'long',
                day: '2-digit'
            });
        }

        function mostrarModalSeguro(modalId) {
            const modalEl = document.getElementById(modalId);
            if (!modalEl) {
                console.error('Modal no encontrado:', modalId);
                return;
            }

            if (window.bootstrap && window.bootstrap.Modal) {
                const instancia = window.bootstrap.Modal.getOrCreateInstance(modalEl);
                instancia.show();
                return;
            }

            if (window.jQuery && typeof window.jQuery(modalEl).modal === 'function') {
                window.jQuery(modalEl).modal('show');
                return;
            }

            alert('No se pudo abrir el modal. Recarga la pagina e intentalo de nuevo.');
        }

        function abrirModalActividades(idAsignatura, nombreAsignatura) {
            const actividades = detalleActividadesPorAsignatura[idAsignatura] || [];
            const titulo = document.getElementById('modalActividadesAsignaturaLabel');
            const resumen = document.getElementById('actividadesAsignaturaResumen');
            const body = document.getElementById('tablaActividadesAsignaturaBody');
            const btnNuevaActividad = document.getElementById('btnModalNuevaActividad');

            titulo.innerHTML = '<i class="ri-file-list-3-line" style="color: #6366f1; margin-right: 8px;"></i> Actividades - ' + nombreAsignatura;
            btnNuevaActividad.href = '<?= BASE_URL ?>/docente/agregar-actividad?id_curso=<?= (int)$id_curso ?>&id_asignatura=' + encodeURIComponent(idAsignatura);

            const totalEntregas = actividades.reduce((acc, item) => acc + (parseInt(item.total_entregas, 10) || 0), 0);
            const totalCalificadas = actividades.reduce((acc, item) => acc + (parseInt(item.total_calificadas, 10) || 0), 0);

            resumen.innerHTML = [
                '<span style="padding:8px 12px; border-radius:10px; background:rgba(99,102,241,.16); color:#818cf8; font-weight:600;">' + actividades.length + ' actividades</span>',
                '<span style="padding:8px 12px; border-radius:10px; background:rgba(14,165,233,.16); color:#38bdf8; font-weight:600;">' + totalEntregas + ' entregas</span>',
                '<span style="padding:8px 12px; border-radius:10px; background:rgba(16,185,129,.16); color:#34d399; font-weight:600;">' + totalCalificadas + ' calificadas</span>'
            ].join('');

            if (!actividades.length) {
                body.innerHTML = '<tr><td colspan="7" class="text-center" style="padding:24px; color:#94a3b8;">No hay actividades registradas para esta asignatura.</td></tr>';
            } else {
                body.innerHTML = actividades.map((item) => {
                    const badge = badgeEstado(item.estado);
                    const promedio = item.promedio_notas !== null ? parseFloat(item.promedio_notas).toFixed(2) : 'N/A';
                    return '<tr>' +
                        '<td><div style="font-weight:600; color:#e2e8f0;">' + (item.titulo || 'Actividad') + '</div><div style="font-size:12px; color:#94a3b8;">Promedio: ' + promedio + '</div></td>' +
                        '<td>' + (item.tipo || 'Sin tipo') + '</td>' +
                        '<td>' + formatoFecha(item.fecha_entrega) + '</td>' +
                        '<td><span class="badge ' + badge.clase + '">' + badge.texto + '</span></td>' +
                        '<td style="text-align:center;">' + (parseInt(item.total_entregas, 10) || 0) + '</td>' +
                        '<td style="text-align:center;">' + (parseInt(item.total_calificadas, 10) || 0) + '</td>' +
                        '<td style="text-align:center;"><a class="btn btn-sm btn-outline-info" href="' + item.url_entregas + '"><i class="ri-eye-line"></i></a></td>' +
                    '</tr>';
                }).join('');
            }

            mostrarModalSeguro('modalActividadesAsignatura');
        }

        function abrirModalCalificaciones(idAsignatura, nombreAsignatura) {
            const resumen = resumenCalificacionesPorAsignatura[idAsignatura] || {
                total_actividades: 0,
                total_entregas: 0,
                total_calificadas: 0,
                promedio_general: null,
                actividades: []
            };

            const titulo = document.getElementById('modalCalificacionesAsignaturaLabel');
            const resumenContenedor = document.getElementById('calificacionesAsignaturaResumen');
            const detalleContenedor = document.getElementById('calificacionesAsignaturaDetalle');
            const btnVerActividades = document.getElementById('btnModalVerActividades');

            titulo.innerHTML = '<i class="ri-bar-chart-box-line" style="color: #10b981; margin-right: 8px;"></i> Calificaciones - ' + nombreAsignatura;
            btnVerActividades.href = '<?= BASE_URL ?>/docente/actividades?id_curso=<?= (int)$id_curso ?>';

            const promedioGeneral = resumen.promedio_general !== null ? parseFloat(resumen.promedio_general).toFixed(2) : 'N/A';

            resumenContenedor.innerHTML = [
                '<div style="background:rgba(99,102,241,.15); border:1px solid rgba(99,102,241,.28); padding:14px; border-radius:12px;"><div style="font-size:12px; color:#a5b4fc;">Actividades</div><div style="font-size:22px; font-weight:700;">' + (parseInt(resumen.total_actividades, 10) || 0) + '</div></div>',
                '<div style="background:rgba(14,165,233,.15); border:1px solid rgba(14,165,233,.28); padding:14px; border-radius:12px;"><div style="font-size:12px; color:#7dd3fc;">Entregas recibidas</div><div style="font-size:22px; font-weight:700;">' + (parseInt(resumen.total_entregas, 10) || 0) + '</div></div>',
                '<div style="background:rgba(16,185,129,.15); border:1px solid rgba(16,185,129,.28); padding:14px; border-radius:12px;"><div style="font-size:12px; color:#6ee7b7;">Calificadas</div><div style="font-size:22px; font-weight:700;">' + (parseInt(resumen.total_calificadas, 10) || 0) + '</div></div>',
                '<div style="background:rgba(245,158,11,.15); border:1px solid rgba(245,158,11,.28); padding:14px; border-radius:12px;"><div style="font-size:12px; color:#fcd34d;">Promedio general</div><div style="font-size:22px; font-weight:700;">' + promedioGeneral + '</div></div>'
            ].join('');

            if (!resumen.actividades || !resumen.actividades.length) {
                detalleContenedor.innerHTML = '<div class="text-center" style="padding: 20px; color:#94a3b8; background:rgba(148,163,184,.08); border-radius:12px;">Aun no hay actividades calificadas para esta asignatura.</div>';
            } else {
                detalleContenedor.innerHTML = '<div style="margin-bottom:10px; font-weight:600;">Detalle por actividad</div>' +
                    '<div class="table-responsive"><table class="table table-dark table-hover align-middle"><thead><tr><th>Actividad</th><th>Promedio</th><th style="text-align:center;">Entregas</th><th style="text-align:center;">Calificadas</th><th style="text-align:center;">Ver</th></tr></thead><tbody>' +
                    resumen.actividades.map((item) => {
                        const promedio = item.promedio_notas !== null ? parseFloat(item.promedio_notas).toFixed(2) : 'N/A';
                        return '<tr>' +
                            '<td>' + (item.titulo || 'Actividad') + '</td>' +
                            '<td>' + promedio + '</td>' +
                            '<td style="text-align:center;">' + (parseInt(item.total_entregas, 10) || 0) + '</td>' +
                            '<td style="text-align:center;">' + (parseInt(item.total_calificadas, 10) || 0) + '</td>' +
                            '<td style="text-align:center;"><a class="btn btn-sm btn-outline-success" href="' + item.url_entregas + '"><i class="ri-external-link-line"></i></a></td>' +
                        '</tr>';
                    }).join('') +
                    '</tbody></table></div>';
            }

            mostrarModalSeguro('modalCalificacionesAsignatura');
        }

        function abrirModalPerfilAcademicoEstudiante(idEstudiante, nombreEstudiante) {
            const perfil = perfilAcademicoPorEstudiante[idEstudiante] || null;
            const titulo = document.getElementById('modalPerfilAcademicoEstudianteLabel');
            const contenido = document.getElementById('perfilAcademicoEstudianteContenido');

            titulo.innerHTML = '<i class="ri-user-star-line" style="color:#6366f1; margin-right:8px;"></i> Perfil academico - ' + nombreEstudiante;

            if (!perfil) {
                contenido.innerHTML = '<div class="text-center" style="padding:22px; color:#94a3b8;">No hay datos academicos disponibles para este estudiante.</div>';
                mostrarModalSeguro('modalPerfilAcademicoEstudiante');
                return;
            }

            const promedio = perfil.promedio_general !== null ? parseFloat(perfil.promedio_general).toFixed(2) : 'N/A';
            const progreso = perfil.total_actividades > 0
                ? Math.round((perfil.total_entregadas / perfil.total_actividades) * 100)
                : 0;
            const foto = perfil.foto ? perfil.foto : 'default.png';

            contenido.innerHTML =
                '<div style="display:flex; flex-wrap:wrap; gap:16px; align-items:center; margin-bottom:16px;">' +
                    '<img src="<?= BASE_URL ?>/public/uploads/estudiantes/' + foto + '" onerror="this.onerror=null; this.src=\'<?= BASE_URL ?>/public/uploads/estudiantes/default.png\'" alt="' + (perfil.nombre || nombreEstudiante) + '" style="width:72px; height:72px; border-radius:16px; object-fit:cover; border:2px solid rgba(99,102,241,.3);">' +
                    '<div><div style="font-size:20px; font-weight:700;">' + (perfil.nombre || nombreEstudiante) + '</div><div style="color:#94a3b8;">Documento: ' + (perfil.documento || 'N/A') + '</div><div style="color:#94a3b8;">Matricula: ' + formatearFechaLarga(perfil.fecha_matricula) + '</div></div>' +
                '</div>' +
                '<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 12px;">' +
                    '<div style="background:rgba(99,102,241,.15); border:1px solid rgba(99,102,241,.28); padding:14px; border-radius:12px;"><div style="font-size:12px; color:#a5b4fc;">Actividades</div><div style="font-size:22px; font-weight:700;">' + (parseInt(perfil.total_actividades, 10) || 0) + '</div></div>' +
                    '<div style="background:rgba(14,165,233,.15); border:1px solid rgba(14,165,233,.28); padding:14px; border-radius:12px;"><div style="font-size:12px; color:#7dd3fc;">Entregadas</div><div style="font-size:22px; font-weight:700;">' + (parseInt(perfil.total_entregadas, 10) || 0) + '</div></div>' +
                    '<div style="background:rgba(16,185,129,.15); border:1px solid rgba(16,185,129,.28); padding:14px; border-radius:12px;"><div style="font-size:12px; color:#6ee7b7;">Calificadas</div><div style="font-size:22px; font-weight:700;">' + (parseInt(perfil.total_calificadas, 10) || 0) + '</div></div>' +
                    '<div style="background:rgba(245,158,11,.15); border:1px solid rgba(245,158,11,.28); padding:14px; border-radius:12px;"><div style="font-size:12px; color:#fcd34d;">Promedio</div><div style="font-size:22px; font-weight:700;">' + promedio + '</div></div>' +
                '</div>' +
                '<div style="margin-top:16px; background:rgba(148,163,184,.08); border:1px solid rgba(148,163,184,.2); border-radius:12px; padding:14px;">' +
                    '<div style="display:flex; justify-content:space-between; margin-bottom:8px;"><span>Progreso de entregas</span><strong>' + progreso + '%</strong></div>' +
                    '<div style="height:8px; border-radius:999px; background:rgba(148,163,184,.2); overflow:hidden;"><div style="height:100%; width:' + Math.max(0, Math.min(100, progreso)) + '%; background:linear-gradient(90deg, #6366f1 0%, #10b981 100%);"></div></div>' +
                    '<div style="margin-top:10px; color:#94a3b8; font-size:12px;">Ultima calificacion: ' + formatearFechaLarga(perfil.ultima_calificacion) + '</div>' +
                '</div>';

            mostrarModalSeguro('modalPerfilAcademicoEstudiante');
        }

        function abrirModalCalificacionesEstudiante(idEstudiante, nombreEstudiante) {
            const perfil = perfilAcademicoPorEstudiante[idEstudiante] || null;
            const calificaciones = calificacionesPorEstudiante[idEstudiante] || [];
            const titulo = document.getElementById('modalCalificacionesEstudianteLabel');
            const resumen = document.getElementById('calificacionesEstudianteResumen');
            const detalle = document.getElementById('calificacionesEstudianteDetalle');

            titulo.innerHTML = '<i class="ri-file-edit-line" style="color:#10b981; margin-right:8px;"></i> Calificaciones - ' + nombreEstudiante;

            const totalActividades = perfil ? (parseInt(perfil.total_actividades, 10) || 0) : calificaciones.length;
            const totalEntregadas = perfil ? (parseInt(perfil.total_entregadas, 10) || 0) : calificaciones.filter(item => item.id_entrega).length;
            const totalCalificadas = perfil ? (parseInt(perfil.total_calificadas, 10) || 0) : calificaciones.filter(item => item.nota !== null).length;
            const promedio = perfil && perfil.promedio_general !== null ? parseFloat(perfil.promedio_general).toFixed(2) : 'N/A';

            resumen.innerHTML = [
                '<div style="background:rgba(99,102,241,.15); border:1px solid rgba(99,102,241,.28); padding:14px; border-radius:12px;"><div style="font-size:12px; color:#a5b4fc;">Actividades</div><div style="font-size:22px; font-weight:700;">' + totalActividades + '</div></div>',
                '<div style="background:rgba(14,165,233,.15); border:1px solid rgba(14,165,233,.28); padding:14px; border-radius:12px;"><div style="font-size:12px; color:#7dd3fc;">Entregadas</div><div style="font-size:22px; font-weight:700;">' + totalEntregadas + '</div></div>',
                '<div style="background:rgba(16,185,129,.15); border:1px solid rgba(16,185,129,.28); padding:14px; border-radius:12px;"><div style="font-size:12px; color:#6ee7b7;">Calificadas</div><div style="font-size:22px; font-weight:700;">' + totalCalificadas + '</div></div>',
                '<div style="background:rgba(245,158,11,.15); border:1px solid rgba(245,158,11,.28); padding:14px; border-radius:12px;"><div style="font-size:12px; color:#fcd34d;">Promedio</div><div style="font-size:22px; font-weight:700;">' + promedio + '</div></div>'
            ].join('');

            if (!calificaciones.length) {
                detalle.innerHTML = '<div class="text-center" style="padding: 20px; color:#94a3b8; background:rgba(148,163,184,.08); border-radius:12px;">No hay actividades registradas para mostrar calificaciones.</div>';
                mostrarModalSeguro('modalCalificacionesEstudiante');
                return;
            }

            detalle.innerHTML =
                '<div class="table-responsive"><table class="table table-dark table-hover align-middle">' +
                    '<thead><tr><th>Actividad</th><th>Asignatura</th><th>Entrega</th><th>Nota</th><th>Observacion</th><th style="text-align:center;">Editar</th></tr></thead><tbody>' +
                    calificaciones.map((item) => {
                        const estadoEntrega = item.id_entrega ? (item.estado_entrega || 'Entregado') : 'Sin entregar';
                        const nota = item.nota !== null ? parseFloat(item.nota).toFixed(2) : 'Sin nota';
                        const observacion = item.observacion ? item.observacion : '-';
                        const botonEditar = item.id_entrega
                            ? '<a href="' + item.url_entregas + '" class="btn btn-sm btn-outline-success" title="Ir a calificar"><i class="ri-external-link-line"></i></a>'
                            : '<span style="color:#64748b; font-size:12px;">No disponible</span>';

                        return '<tr>' +
                            '<td><div style="font-weight:600;">' + (item.titulo || 'Actividad') + '</div><div style="font-size:12px; color:#94a3b8;">' + formatoFecha(item.fecha_limite) + '</div></td>' +
                            '<td>' + (item.asignatura || 'Sin asignatura') + '</td>' +
                            '<td>' + estadoEntrega + '</td>' +
                            '<td>' + nota + '</td>' +
                            '<td style="max-width:260px; white-space:normal; color:#94a3b8;">' + observacion + '</td>' +
                            '<td style="text-align:center;">' + botonEditar + '</td>' +
                        '</tr>';
                    }).join('') +
                    '</tbody></table></div>';

            mostrarModalSeguro('modalCalificacionesEstudiante');
        }

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

        document.addEventListener('click', function(e) {
            const btnActividades = e.target.closest('.btn-open-actividades-modal');
            if (btnActividades) {
                abrirModalActividades(btnActividades.dataset.asignaturaId, btnActividades.dataset.asignaturaNombre || 'Asignatura');
                return;
            }

            const btnCalificaciones = e.target.closest('.btn-open-calificaciones-modal');
            if (btnCalificaciones) {
                abrirModalCalificaciones(btnCalificaciones.dataset.asignaturaId, btnCalificaciones.dataset.asignaturaNombre || 'Asignatura');
                return;
            }

            const btnPerfilEstudiante = e.target.closest('.btn-open-perfil-estudiante-modal');
            if (btnPerfilEstudiante) {
                abrirModalPerfilAcademicoEstudiante(btnPerfilEstudiante.dataset.estudianteId, btnPerfilEstudiante.dataset.estudianteNombre || 'Estudiante');
                return;
            }

            const btnCalificacionesEstudiante = e.target.closest('.btn-open-calificaciones-estudiante-modal');
            if (btnCalificacionesEstudiante) {
                abrirModalCalificacionesEstudiante(btnCalificacionesEstudiante.dataset.estudianteId, btnCalificacionesEstudiante.dataset.estudianteNombre || 'Estudiante');
            }
        });

        // Confirmación antes de imprimir
        const printBtn = document.querySelector('.btn-icon-action');
        if (printBtn) {
            printBtn.addEventListener('click', function(e) {
                if (!confirm('¿Deseas imprimir la información de este curso?')) {
                    e.preventDefault();
                }
            });
        }

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