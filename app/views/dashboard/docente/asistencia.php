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
    
    // 3. OBTENER ID DEL DOCENTE desde la tabla docentes
    $id_usuario_sesion = $_SESSION['user']['id'];
    
    // TODO: Reemplazar con tu lógica real de BD
    // require_once BASE_PATH . '/config/database.php';
    // $db = new Conexion();
    // $pdo = $db->getConexion();
    
    // Por ahora, datos FAKE pero con estructura real
    $id_docente = 1;
    $nombre_docente = 'Carlos Méndez';
    
    // 4. OBTENER PARÁMETROS DE FILTRO
    $curso_seleccionado = $_GET['curso'] ?? null;
    $asignatura_seleccionada = $_GET['asignatura'] ?? null;
    $fecha_seleccionada = $_GET['fecha'] ?? date('Y-m-d');
    
    // 5. DATOS FAKE - CURSOS DEL DOCENTE
    // TODO: Reemplazar con query real a docente_asignatura
    $mis_cursos_asignaturas = [
        [
            'id_curso' => 1,
            'curso_nombre' => '10° - A',
            'grado' => 10,
            'jornada' => 'Mañana',
            'asignaturas' => [
                ['id' => 1, 'nombre' => 'Matemáticas', 'horario' => 'Lunes 8:00 AM'],
                ['id' => 2, 'nombre' => 'Física', 'horario' => 'Miércoles 10:00 AM']
            ]
        ],
        [
            'id_curso' => 2,
            'curso_nombre' => '10° - B',
            'grado' => 10,
            'jornada' => 'Tarde',
            'asignaturas' => [
                ['id' => 3, 'nombre' => 'Matemáticas', 'horario' => 'Martes 2:00 PM']
            ]
        ],
        [
            'id_curso' => 3,
            'curso_nombre' => '11° - A',
            'grado' => 11,
            'jornada' => 'Mañana',
            'asignaturas' => [
                ['id' => 4, 'nombre' => 'Cálculo', 'horario' => 'Jueves 9:00 AM'],
                ['id' => 5, 'nombre' => 'Trigonometría', 'horario' => 'Viernes 8:00 AM']
            ]
        ]
    ];
    
    // 6. OBTENER DATOS DEL CURSO Y ASIGNATURA SELECCIONADOS
    $curso_actual = null;
    $asignatura_actual = null;
    $asignaturas_del_curso = [];
    
    if ($curso_seleccionado) {
        foreach ($mis_cursos_asignaturas as $curso) {
            if ($curso['id_curso'] == $curso_seleccionado) {
                $curso_actual = $curso;
                $asignaturas_del_curso = $curso['asignaturas'];
                
                // Si hay asignatura seleccionada, buscarla
                if ($asignatura_seleccionada) {
                    foreach ($curso['asignaturas'] as $asig) {
                        if ($asig['id'] == $asignatura_seleccionada) {
                            $asignatura_actual = $asig;
                            break;
                        }
                    }
                }
                break;
            }
        }
    }
    
    // 7. ESTUDIANTES DEL CURSO (solo si hay curso seleccionado)
    $estudiantes = [];
    if ($curso_seleccionado) {
        // TODO: Query real a matricula + estudiante
        $estudiantes = [
            [
                'id' => 1,
                'nombres' => 'María Camila',
                'apellidos' => 'Rodríguez López',
                'documento' => '1005234567',
                'foto' => 'default.png',
                'asistencia_hoy' => null
            ],
            [
                'id' => 2,
                'nombres' => 'Juan Sebastián',
                'apellidos' => 'Martínez García',
                'documento' => '1005234568',
                'foto' => 'default.png',
                'asistencia_hoy' => 'P'
            ],
            [
                'id' => 3,
                'nombres' => 'Ana Sofía',
                'apellidos' => 'Hernández Morales',
                'documento' => '1005234569',
                'foto' => 'default.png',
                'asistencia_hoy' => 'P'
            ],
            [
                'id' => 4,
                'nombres' => 'Carlos Andrés',
                'apellidos' => 'Gómez Ruiz',
                'documento' => '1005234570',
                'foto' => 'default.png',
                'asistencia_hoy' => 'A'
            ],
            [
                'id' => 5,
                'nombres' => 'Laura Valentina',
                'apellidos' => 'Castro Pérez',
                'documento' => '1005234571',
                'foto' => 'default.png',
                'asistencia_hoy' => 'T'
            ],
            [
                'id' => 6,
                'nombres' => 'Diego Alejandro',
                'apellidos' => 'Vargas Sánchez',
                'documento' => '1005234572',
                'foto' => 'default.png',
                'asistencia_hoy' => 'P'
            ],
            [
                'id' => 7,
                'nombres' => 'Daniela',
                'apellidos' => 'Torres Jiménez',
                'documento' => '1005234573',
                'foto' => 'default.png',
                'asistencia_hoy' => null
            ],
            [
                'id' => 8,
                'nombres' => 'Santiago',
                'apellidos' => 'Ramírez Ortiz',
                'documento' => '1005234574',
                'foto' => 'default.png',
                'asistencia_hoy' => 'E'
            ]
        ];
    }
    
    // 8. CALCULAR ESTADÍSTICAS
    $totalEstudiantes = count($estudiantes);
    $presentes = count(array_filter($estudiantes, fn($e) => $e['asistencia_hoy'] === 'P'));
    $ausentes = count(array_filter($estudiantes, fn($e) => $e['asistencia_hoy'] === 'A'));
    $tardanzas = count(array_filter($estudiantes, fn($e) => $e['asistencia_hoy'] === 'T'));
    $excusas = count(array_filter($estudiantes, fn($e) => $e['asistencia_hoy'] === 'E'));
    $sinMarcar = count(array_filter($estudiantes, fn($e) => $e['asistencia_hoy'] === null));
    
    $porcentajeAsistencia = $totalEstudiantes > 0 ? round(($presentes / $totalEstudiantes) * 100, 1) : 0;
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • Gestión de Asistencia</title>
    <?php include_once __DIR__ . '/../../layouts/header_coordinador.php' ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-admin.css">
    
    <style>
        /* ============================================
           ESTILOS BASE Y ESTRUCTURA
        ============================================ */
        .main {
            padding: 28px 0 40px 0 !important;
        }

        .topbar {
            padding-left: 28px !important;
            padding-right: 28px !important;
        }

        /* ============================================
           SECCIÓN DE FILTROS - LO MÁS IMPORTANTE
        ============================================ */
        .filters-section {
            padding: 24px 28px;
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.1) 0%, rgba(118, 75, 162, 0.05) 100%);
            border-bottom: 2px solid rgba(79, 70, 229, 0.2);
            margin-bottom: 24px;
        }

        .filters-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .filters-header h3 {
            color: #e6e9f4;
            font-size: 18px;
            font-weight: 700;
            margin: 0;
        }

        .filters-header .badge {
            padding: 4px 10px;
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr auto;
            gap: 16px;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-label {
            color: #97a1b6;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .filter-label i {
            color: #6366f1;
            font-size: 16px;
        }

        .filter-select,
        .filter-input {
            width: 100%;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: #e6e9f4;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .filter-select:focus,
        .filter-input:focus {
            outline: none;
            border-color: rgba(79, 70, 229, 0.5);
            background: rgba(79, 70, 229, 0.1);
        }

        .filter-select option {
            background: #1e293b;
            color: #e6e9f4;
            padding: 12px;
        }

        .filter-btn {
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
        }

        .filter-btn i {
            font-size: 18px;
        }

        /* Mensaje cuando no hay selección */
        .no-selection-message {
            background: rgba(245, 158, 11, 0.1);
            border: 2px dashed rgba(245, 158, 11, 0.3);
            border-radius: 12px;
            padding: 20px;
            margin: 0 28px 24px 28px;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .no-selection-message i {
            font-size: 32px;
            color: #f59e0b;
        }

        .no-selection-message-content h4 {
            color: #e6e9f4;
            margin: 0 0 6px 0;
            font-size: 16px;
            font-weight: 600;
        }

        .no-selection-message-content p {
            color: #97a1b6;
            margin: 0;
            font-size: 14px;
        }

        /* Información del contexto actual */
        .context-info {
            padding: 16px 28px;
            background: rgba(79, 70, 229, 0.05);
            border-left: 4px solid #6366f1;
            margin: 0 28px 24px 28px;
            border-radius: 0 8px 8px 0;
        }

        .context-info-content {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .context-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #e6e9f4;
            font-size: 14px;
            font-weight: 500;
        }

        .context-item i {
            color: #6366f1;
            font-size: 18px;
        }

        .context-item strong {
            color: #6366f1;
        }

        /* ============================================
           RESTO DE ESTILOS (HEREDADOS DEL ANTERIOR)
        ============================================ */
        .attendance-stats {
            padding: 24px 28px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(79, 70, 229, 0.3);
            transform: translateY(-2px);
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            flex-shrink: 0;
        }

        .stat-content {
            flex: 1;
        }

        .stat-label {
            display: block;
            color: #97a1b6;
            font-size: 13px;
            margin-bottom: 4px;
            font-weight: 500;
        }

        .stat-value {
            display: block;
            color: #e6e9f4;
            font-size: 28px;
            font-weight: 700;
            line-height: 1;
        }

        .stat-percentage {
            font-size: 14px;
            margin-left: 8px;
            opacity: 0.7;
        }

        .stat-card.presentes .stat-icon {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .stat-card.ausentes .stat-icon {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .stat-card.tardanzas .stat-icon {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .stat-card.excusas .stat-icon {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        .stat-card.sin-marcar .stat-icon {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        }

        .attendance-controls {
            padding: 24px 28px;
            background: rgba(79, 70, 229, 0.05);
            border-bottom: 1px solid rgba(79, 70, 229, 0.1);
        }

        .controls-grid {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
        }

        .controls-left {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .controls-right {
            display: flex;
            gap: 12px;
        }

        .quick-action-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            background: rgba(79, 70, 229, 0.1);
            color: #6366f1;
            border: 1px solid rgba(79, 70, 229, 0.2);
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .quick-action-btn:hover {
            background: rgba(79, 70, 229, 0.2);
            border-color: rgba(79, 70, 229, 0.4);
            transform: translateY(-2px);
        }

        .quick-action-btn i {
            font-size: 18px;
        }

        .quick-action-btn.success {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            border-color: rgba(16, 185, 129, 0.2);
        }

        .quick-action-btn.success:hover {
            background: rgba(16, 185, 129, 0.2);
            border-color: rgba(16, 185, 129, 0.4);
        }

        .quick-action-btn.danger {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border-color: rgba(239, 68, 68, 0.2);
        }

        .quick-action-btn.danger:hover {
            background: rgba(239, 68, 68, 0.2);
            border-color: rgba(239, 68, 68, 0.4);
        }

        .search-box {
            position: relative;
            flex: 1;
            max-width: 300px;
        }

        .search-box input {
            width: 100%;
            padding: 12px 16px 12px 44px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: #e6e9f4;
            font-size: 14px;
        }

        .search-box i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #6366f1;
            font-size: 18px;
        }

        .attendance-table-wrapper {
            padding: 24px 28px;
        }

        .attendance-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 12px;
            overflow: hidden;
        }

        .attendance-table thead {
            background: rgba(79, 70, 229, 0.1);
        }

        .attendance-table th {
            padding: 16px;
            text-align: left;
            color: #e6e9f4;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid rgba(79, 70, 229, 0.2);
        }

        .attendance-table tbody tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.2s ease;
        }

        .attendance-table tbody tr:hover {
            background: rgba(79, 70, 229, 0.05);
        }

        .attendance-table td {
            padding: 16px;
            color: #97a1b6;
            font-size: 14px;
        }

        .student-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .student-avatar {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            object-fit: cover;
            border: 2px solid rgba(79, 70, 229, 0.2);
        }

        .student-name {
            font-weight: 600;
            color: #e6e9f4;
            font-size: 15px;
            margin-bottom: 2px;
        }

        .student-document {
            color: #6b7280;
            font-size: 12px;
        }

        .attendance-actions {
            display: flex;
            gap: 8px;
        }

        .attendance-btn {
            width: 40px;
            height: 40px;
            border: 2px solid transparent;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 18px;
            position: relative;
        }

        .attendance-btn:hover {
            transform: scale(1.1);
        }

        .attendance-btn.active {
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
        }

        .attendance-btn.presente {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
        }

        .attendance-btn.presente:hover,
        .attendance-btn.presente.active {
            background: #10b981;
            color: white;
            border-color: #10b981;
        }

        .attendance-btn.ausente {
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
        }

        .attendance-btn.ausente:hover,
        .attendance-btn.ausente.active {
            background: #ef4444;
            color: white;
            border-color: #ef4444;
        }

        .attendance-btn.tardanza {
            background: rgba(245, 158, 11, 0.15);
            color: #f59e0b;
        }

        .attendance-btn.tardanza:hover,
        .attendance-btn.tardanza.active {
            background: #f59e0b;
            color: white;
            border-color: #f59e0b;
        }

        .attendance-btn.excusa {
            background: rgba(59, 130, 246, 0.15);
            color: #3b82f6;
        }

        .attendance-btn.excusa:hover,
        .attendance-btn.excusa.active {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }

        .attendance-btn::before {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%) translateY(-8px);
            padding: 6px 10px;
            background: #1f2937;
            color: #e6e9f4;
            border-radius: 6px;
            font-size: 11px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
            z-index: 10;
        }

        .attendance-btn:hover::before {
            opacity: 1;
        }

        .save-button-floating {
            position: fixed;
            bottom: 32px;
            right: 32px;
            padding: 16px 32px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.4);
            display: none;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            z-index: 100;
        }

        .save-button-floating.visible {
            display: flex;
        }

        .save-button-floating:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(16, 185, 129, 0.5);
        }

        .save-button-floating i {
            font-size: 20px;
        }

        .changes-count {
            padding: 4px 10px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
        }

        .attendance-legend {
            padding: 20px 28px;
            background: rgba(79, 70, 229, 0.05);
            border-top: 1px solid rgba(79, 70, 229, 0.1);
        }

        .legend-title {
            color: #e6e9f4;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .legend-items {
            display: flex;
            gap: 24px;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #97a1b6;
        }

        .legend-icon {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        @media (max-width: 1024px) {
            .filters-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 768px) {
            .filters-grid {
                grid-template-columns: 1fr;
            }

            .attendance-stats {
                grid-template-columns: 1fr;
            }

            .controls-grid {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box {
                max-width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="app" id="appGrid">
        <!-- LEFT SIDEBAR -->
        <?php include_once __DIR__ . '/../../layouts/sidebar_docente.php' ?>

        <!-- MAIN -->
        <main class="main">
            <!-- TOPBAR -->
            <div class="topbar">
                <div class="topbar-left">
                    <div class="title">Gestión de Asistencia</div>
                </div>

                <div class="user">
                    <button class="btn" title="Notificaciones"><i class="ri-notification-3-line"></i></button>
                    <button class="btn" title="Configuración"><i class="ri-settings-3-line"></i></button>
                    <div class="avatar" title="Docente">DC</div>
                </div>
            </div>

            <!-- FILTROS - SECCIÓN PRINCIPAL -->
            <div class="filters-section">
                <div class="filters-header">
                    <i class="ri-filter-3-line" style="color: #6366f1; font-size: 24px;"></i>
                    <h3>Selecciona el Contexto</h3>
                    <span class="badge">
                        <i class="ri-calendar-line"></i>
                        Periodo 1 - 2026
                    </span>
                </div>

                <form method="GET" action="<?= BASE_URL ?>/docente/asistencia" id="filterForm">
                    <div class="filters-grid">
                        <!-- FILTRO 1: CURSO -->
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="ri-book-open-line"></i>
                                Curso
                            </label>
                            <select name="curso" class="filter-select" id="selectCurso" onchange="actualizarAsignaturas()">
                                <option value="">-- Selecciona un curso --</option>
                                <?php foreach ($mis_cursos_asignaturas as $curso): ?>
                                    <option value="<?= $curso['id_curso'] ?>" 
                                            <?= $curso_seleccionado == $curso['id_curso'] ? 'selected' : '' ?>
                                            data-asignaturas='<?= json_encode($curso['asignaturas']) ?>'>
                                        <?= $curso['curso_nombre'] ?> - <?= $curso['jornada'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- FILTRO 2: ASIGNATURA (se actualiza dinámicamente) -->
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="ri-book-2-line"></i>
                                Asignatura
                            </label>
                            <select name="asignatura" class="filter-select" id="selectAsignatura" <?= !$curso_seleccionado ? 'disabled' : '' ?>>
                                <option value="">-- Todas las asignaturas --</option>
                                <?php if ($curso_seleccionado && !empty($asignaturas_del_curso)): ?>
                                    <?php foreach ($asignaturas_del_curso as $asig): ?>
                                        <option value="<?= $asig['id'] ?>" 
                                                <?= $asignatura_seleccionada == $asig['id'] ? 'selected' : '' ?>>
                                            <?= $asig['nombre'] ?> - <?= $asig['horario'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- FILTRO 3: FECHA -->
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="ri-calendar-event-line"></i>
                                Fecha
                            </label>
                            <input type="date" 
                                   name="fecha" 
                                   class="filter-input" 
                                   value="<?= $fecha_seleccionada ?>"
                                   max="<?= date('Y-m-d') ?>">
                        </div>

                        <!-- BOTÓN APLICAR FILTROS -->
                        <div class="filter-group">
                            <button type="submit" class="filter-btn">
                                <i class="ri-search-line"></i>
                                Cargar
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <?php if (!$curso_seleccionado): ?>
                <!-- MENSAJE: DEBE SELECCIONAR UN CURSO -->
                <div class="no-selection-message">
                    <i class="ri-information-line"></i>
                    <div class="no-selection-message-content">
                        <h4>Selecciona un curso para comenzar</h4>
                        <p>Utiliza los filtros de arriba para elegir el curso y la fecha en la que deseas tomar asistencia.</p>
                    </div>
                </div>
            <?php else: ?>
                <!-- CONTEXTO ACTUAL -->
                <div class="context-info">
                    <div class="context-info-content">
                        <div class="context-item">
                            <i class="ri-book-open-line"></i>
                            Curso: <strong><?= $curso_actual['curso_nombre'] ?></strong>
                        </div>
                        <?php if ($asignatura_actual): ?>
                            <div class="context-item">
                                <i class="ri-book-2-line"></i>
                                Asignatura: <strong><?= $asignatura_actual['nombre'] ?></strong>
                            </div>
                        <?php endif; ?>
                        <div class="context-item">
                            <i class="ri-calendar-line"></i>
                            Fecha: <strong><?= date('d/m/Y', strtotime($fecha_seleccionada)) ?></strong>
                        </div>
                        <div class="context-item">
                            <i class="ri-group-line"></i>
                            Estudiantes: <strong><?= $totalEstudiantes ?></strong>
                        </div>
                    </div>
                </div>

                <!-- ESTADÍSTICAS -->
                <div class="attendance-stats">
                    <div class="stat-card presentes">
                        <div class="stat-icon">
                            <i class="ri-checkbox-circle-line"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-label">Presentes</span>
                            <strong class="stat-value">
                                <?= $presentes ?>
                                <span class="stat-percentage">(<?= $porcentajeAsistencia ?>%)</span>
                            </strong>
                        </div>
                    </div>

                    <div class="stat-card ausentes">
                        <div class="stat-icon">
                            <i class="ri-close-circle-line"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-label">Ausentes</span>
                            <strong class="stat-value"><?= $ausentes ?></strong>
                        </div>
                    </div>

                    <div class="stat-card tardanzas">
                        <div class="stat-icon">
                            <i class="ri-time-line"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-label">Tardanzas</span>
                            <strong class="stat-value"><?= $tardanzas ?></strong>
                        </div>
                    </div>

                    <div class="stat-card excusas">
                        <div class="stat-icon">
                            <i class="ri-file-text-line"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-label">Excusas</span>
                            <strong class="stat-value"><?= $excusas ?></strong>
                        </div>
                    </div>

                    <div class="stat-card sin-marcar">
                        <div class="stat-icon">
                            <i class="ri-question-line"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-label">Sin Marcar</span>
                            <strong class="stat-value"><?= $sinMarcar ?></strong>
                        </div>
                    </div>
                </div>

                <!-- CONTROLES -->
                <div class="attendance-controls">
                    <div class="controls-grid">
                        <div class="controls-left">
                            <button class="quick-action-btn success" onclick="marcarTodosPresentes()">
                                <i class="ri-checkbox-multiple-line"></i>
                                Marcar Todos Presentes
                            </button>
                            <button class="quick-action-btn danger" onclick="limpiarAsistencia()">
                                <i class="ri-eraser-line"></i>
                                Limpiar Todo
                            </button>
                            <button class="quick-action-btn" onclick="verHistorial()">
                                <i class="ri-history-line"></i>
                                Ver Historial
                            </button>
                        </div>
                        
                        <div class="controls-right">
                            <div class="search-box">
                                <i class="ri-search-line"></i>
                                <input type="text" id="searchStudent" placeholder="Buscar estudiante..." onkeyup="filtrarEstudiantes()">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TABLA DE ASISTENCIA -->
                <div class="attendance-table-wrapper">
                    <?php if (!empty($estudiantes)): ?>
                        <table class="attendance-table" id="tablaAsistencia">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">#</th>
                                    <th style="width: 35%;">Estudiante</th>
                                    <th style="width: 15%;">Documento</th>
                                    <th style="width: 20%; text-align: center;">Estado Actual</th>
                                    <th style="width: 30%; text-align: center;">Marcar Asistencia</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($estudiantes as $index => $estudiante): ?>
                                    <tr class="student-row" data-student-id="<?= $estudiante['id'] ?>">
                                        <td>
                                            <div style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, rgba(79, 70, 229, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); display: flex; align-items: center; justify-content: center; font-weight: 600; color: #6366f1; font-size: 14px;">
                                                <?= $index + 1 ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="student-info">
                                                <img src="<?= BASE_URL ?>/public/uploads/estudiantes/<?= $estudiante['foto'] ?>" 
                                                     alt="<?= $estudiante['nombres'] ?>" 
                                                     class="student-avatar"
                                                     onerror="this.onerror=null; this.src='<?= BASE_URL ?>/public/uploads/estudiantes/default.png'">
                                                <div>
                                                    <div class="student-name">
                                                        <?= htmlspecialchars($estudiante['nombres'] . ' ' . $estudiante['apellidos']) ?>
                                                    </div>
                                                    <div class="student-document"><?= $estudiante['documento'] ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="student-doc"><?= htmlspecialchars($estudiante['documento']) ?></td>
                                        <td style="text-align: center;">
                                            <span class="current-status" data-status="<?= $estudiante['asistencia_hoy'] ?>">
                                                <?php
                                                    switch($estudiante['asistencia_hoy']) {
                                                        case 'P':
                                                            echo '<span style="color: #10b981; font-weight: 600;"><i class="ri-checkbox-circle-fill"></i> Presente</span>';
                                                            break;
                                                        case 'A':
                                                            echo '<span style="color: #ef4444; font-weight: 600;"><i class="ri-close-circle-fill"></i> Ausente</span>';
                                                            break;
                                                        case 'T':
                                                            echo '<span style="color: #f59e0b; font-weight: 600;"><i class="ri-time-fill"></i> Tardanza</span>';
                                                            break;
                                                        case 'E':
                                                            echo '<span style="color: #3b82f6; font-weight: 600;"><i class="ri-file-text-fill"></i> Excusa</span>';
                                                            break;
                                                        default:
                                                            echo '<span style="color: #6b7280; font-weight: 600;"><i class="ri-question-fill"></i> Sin marcar</span>';
                                                    }
                                                ?>
                                            </span>
                                        </td>
                                        <td style="text-align: center;">
                                            <div class="attendance-actions">
                                                <button class="attendance-btn presente <?= $estudiante['asistencia_hoy'] === 'P' ? 'active' : '' ?>" 
                                                        data-tooltip="Presente"
                                                        data-type="P"
                                                        onclick="marcarAsistencia(<?= $estudiante['id'] ?>, 'P', this)">
                                                    <i class="ri-checkbox-circle-line"></i>
                                                </button>
                                                <button class="attendance-btn ausente <?= $estudiante['asistencia_hoy'] === 'A' ? 'active' : '' ?>" 
                                                        data-tooltip="Ausente"
                                                        data-type="A"
                                                        onclick="marcarAsistencia(<?= $estudiante['id'] ?>, 'A', this)">
                                                    <i class="ri-close-circle-line"></i>
                                                </button>
                                                <button class="attendance-btn tardanza <?= $estudiante['asistencia_hoy'] === 'T' ? 'active' : '' ?>" 
                                                        data-tooltip="Tardanza"
                                                        data-type="T"
                                                        onclick="marcarAsistencia(<?= $estudiante['id'] ?>, 'T', this)">
                                                    <i class="ri-time-line"></i>
                                                </button>
                                                <button class="attendance-btn excusa <?= $estudiante['asistencia_hoy'] === 'E' ? 'active' : '' ?>" 
                                                        data-tooltip="Excusa"
                                                        data-type="E"
                                                        onclick="marcarAsistencia(<?= $estudiante['id'] ?>, 'E', this)">
                                                    <i class="ri-file-text-line"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

                <!-- LEYENDA -->
                <div class="attendance-legend">
                    <div class="legend-title">
                        <i class="ri-information-line" style="color: #6366f1;"></i>
                        Leyenda de Estados
                    </div>
                    <div class="legend-items">
                        <div class="legend-item">
                            <div class="legend-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
                                <i class="ri-checkbox-circle-fill"></i>
                            </div>
                            <span><strong>Presente (P)</strong> - El estudiante asistió a clase</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-icon" style="background: rgba(239, 68, 68, 0.15); color: #ef4444;">
                                <i class="ri-close-circle-fill"></i>
                            </div>
                            <span><strong>Ausente (A)</strong> - El estudiante no asistió</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-icon" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">
                                <i class="ri-time-fill"></i>
                            </div>
                            <span><strong>Tardanza (T)</strong> - Llegó tarde a clase</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-icon" style="background: rgba(59, 130, 246, 0.15); color: #3b82f6;">
                                <i class="ri-file-text-fill"></i>
                            </div>
                            <span><strong>Excusa (E)</strong> - Falta justificada</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </main>
    </div>

    <!-- BOTÓN GUARDAR FLOTANTE -->
    <?php if ($curso_seleccionado): ?>
        <button class="save-button-floating" id="saveButton">
            <i class="ri-save-line"></i>
            Guardar Cambios
            <span class="changes-count" id="changesCount">0</span>
        </button>
    <?php endif; ?>

    <script>
        // ============================================
        // VARIABLES GLOBALES
        // ============================================
        let cambiosPendientes = {};
        let estadoOriginal = {};

        // Datos de asignaturas por curso (para actualización dinámica)
        const cursosData = <?= json_encode($mis_cursos_asignaturas) ?>;

        // ============================================
        // ACTUALIZAR SELECT DE ASIGNATURAS
        // ============================================
        function actualizarAsignaturas() {
            const selectCurso = document.getElementById('selectCurso');
            const selectAsignatura = document.getElementById('selectAsignatura');
            const cursoId = selectCurso.value;

            // Limpiar select de asignaturas
            selectAsignatura.innerHTML = '<option value="">-- Todas las asignaturas --</option>';

            if (cursoId) {
                // Buscar las asignaturas del curso seleccionado
                const cursoData = cursosData.find(c => c.id_curso == cursoId);
                
                if (cursoData && cursoData.asignaturas) {
                    cursoData.asignaturas.forEach(asig => {
                        const option = document.createElement('option');
                        option.value = asig.id;
                        option.textContent = `${asig.nombre} - ${asig.horario}`;
                        selectAsignatura.appendChild(option);
                    });
                }
                
                selectAsignatura.disabled = false;
            } else {
                selectAsignatura.disabled = true;
            }
        }

        // Guardar estado original al cargar
        document.addEventListener('DOMContentLoaded', function() {
            const filas = document.querySelectorAll('.student-row');
            filas.forEach(fila => {
                const studentId = fila.getAttribute('data-student-id');
                const statusElement = fila.querySelector('.current-status');
                const status = statusElement.getAttribute('data-status');
                estadoOriginal[studentId] = status;
            });
        });

        // ============================================
        // MARCAR ASISTENCIA INDIVIDUAL
        // ============================================
        function marcarAsistencia(studentId, tipo, boton) {
            const fila = boton.closest('.student-row');
            const statusElement = fila.querySelector('.current-status');
            const botonesAsistencia = fila.querySelectorAll('.attendance-btn');
            
            botonesAsistencia.forEach(btn => btn.classList.remove('active'));
            boton.classList.add('active');
            
            let nuevoHTML = '';
            switch(tipo) {
                case 'P':
                    nuevoHTML = '<span style="color: #10b981; font-weight: 600;"><i class="ri-checkbox-circle-fill"></i> Presente</span>';
                    break;
                case 'A':
                    nuevoHTML = '<span style="color: #ef4444; font-weight: 600;"><i class="ri-close-circle-fill"></i> Ausente</span>';
                    break;
                case 'T':
                    nuevoHTML = '<span style="color: #f59e0b; font-weight: 600;"><i class="ri-time-fill"></i> Tardanza</span>';
                    break;
                case 'E':
                    nuevoHTML = '<span style="color: #3b82f6; font-weight: 600;"><i class="ri-file-text-fill"></i> Excusa</span>';
                    break;
            }
            
            statusElement.innerHTML = nuevoHTML;
            statusElement.setAttribute('data-status', tipo);
            
            if (estadoOriginal[studentId] !== tipo) {
                cambiosPendientes[studentId] = tipo;
            } else {
                delete cambiosPendientes[studentId];
            }
            
            actualizarEstadisticas();
            mostrarBotonGuardar();
        }

        // ============================================
        // MARCAR TODOS PRESENTES
        // ============================================
        function marcarTodosPresentes() {
            if (!confirm('¿Estás seguro de marcar a TODOS los estudiantes como presentes?')) {
                return;
            }
            
            const filas = document.querySelectorAll('.student-row');
            filas.forEach(fila => {
                const studentId = fila.getAttribute('data-student-id');
                const botonPresente = fila.querySelector('.attendance-btn.presente');
                marcarAsistencia(studentId, 'P', botonPresente);
            });
        }

        // ============================================
        // LIMPIAR ASISTENCIA
        // ============================================
        function limpiarAsistencia() {
            if (!confirm('¿Estás seguro de limpiar toda la asistencia? Esta acción no se puede deshacer.')) {
                return;
            }
            
            const filas = document.querySelectorAll('.student-row');
            filas.forEach(fila => {
                const studentId = fila.getAttribute('data-student-id');
                const statusElement = fila.querySelector('.current-status');
                const botonesAsistencia = fila.querySelectorAll('.attendance-btn');
                
                botonesAsistencia.forEach(btn => btn.classList.remove('active'));
                
                statusElement.innerHTML = '<span style="color: #6b7280; font-weight: 600;"><i class="ri-question-fill"></i> Sin marcar</span>';
                statusElement.setAttribute('data-status', '');
                
                if (estadoOriginal[studentId] !== null && estadoOriginal[studentId] !== '') {
                    cambiosPendientes[studentId] = null;
                } else {
                    delete cambiosPendientes[studentId];
                }
            });
            
            actualizarEstadisticas();
            mostrarBotonGuardar();
        }

        // ============================================
        // ACTUALIZAR ESTADÍSTICAS EN TIEMPO REAL
        // ============================================
        function actualizarEstadisticas() {
            const filas = document.querySelectorAll('.student-row');
            let presentes = 0, ausentes = 0, tardanzas = 0, excusas = 0, sinMarcar = 0;
            
            filas.forEach(fila => {
                const status = fila.querySelector('.current-status').getAttribute('data-status');
                switch(status) {
                    case 'P': presentes++; break;
                    case 'A': ausentes++; break;
                    case 'T': tardanzas++; break;
                    case 'E': excusas++; break;
                    default: sinMarcar++; break;
                }
            });
            
            const total = filas.length;
            const porcentaje = total > 0 ? Math.round((presentes / total) * 100 * 10) / 10 : 0;
            
            document.querySelector('.stat-card.presentes .stat-value').innerHTML = 
                `${presentes} <span class="stat-percentage">(${porcentaje}%)</span>`;
            document.querySelector('.stat-card.ausentes .stat-value').textContent = ausentes;
            document.querySelector('.stat-card.tardanzas .stat-value').textContent = tardanzas;
            document.querySelector('.stat-card.excusas .stat-value').textContent = excusas;
            document.querySelector('.stat-card.sin-marcar .stat-value').textContent = sinMarcar;
        }

        // ============================================
        // MOSTRAR/OCULTAR BOTÓN GUARDAR
        // ============================================
        function mostrarBotonGuardar() {
            const saveButton = document.getElementById('saveButton');
            const changesCount = document.getElementById('changesCount');
            const numCambios = Object.keys(cambiosPendientes).length;
            
            if (numCambios > 0) {
                saveButton.classList.add('visible');
                changesCount.textContent = numCambios;
            } else {
                saveButton.classList.remove('visible');
            }
        }

        // ============================================
        // GUARDAR ASISTENCIA
        // ============================================
        const saveBtn = document.getElementById('saveButton');
        if (saveBtn) {
            saveBtn.addEventListener('click', function() {
                if (Object.keys(cambiosPendientes).length === 0) {
                    alert('No hay cambios pendientes por guardar.');
                    return;
                }
                
                console.log('Guardando asistencia:', cambiosPendientes);
                
                // TODO: Implementar AJAX real
                /*
                fetch('<?= BASE_URL ?>/api/guardar-asistencia', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        curso_id: <?= $curso_seleccionado ?>,
                        asignatura_id: <?= $asignatura_seleccionada ?? 'null' ?>,
                        fecha: '<?= $fecha_seleccionada ?>',
                        asistencias: cambiosPendientes
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('✅ Asistencia guardada exitosamente');
                        cambiosPendientes = {};
                        estadoOriginal = {...estadoOriginal, ...cambiosPendientes};
                        mostrarBotonGuardar();
                    }
                });
                */
                
                alert('✅ Asistencia guardada exitosamente!\n\n' + 
                      'Cambios realizados: ' + Object.keys(cambiosPendientes).length);
                
                cambiosPendientes = {};
                mostrarBotonGuardar();
            });
        }

        // ============================================
        // FILTRAR ESTUDIANTES
        // ============================================
        function filtrarEstudiantes() {
            const input = document.getElementById('searchStudent');
            const filter = input.value.toLowerCase();
            const filas = document.querySelectorAll('.student-row');
            
            filas.forEach(fila => {
                const nombre = fila.querySelector('.student-name').textContent.toLowerCase();
                const documento = fila.querySelector('.student-doc').textContent.toLowerCase();
                
                if (nombre.includes(filter) || documento.includes(filter)) {
                    fila.style.display = '';
                } else {
                    fila.style.display = 'none';
                }
            });
        }

        // ============================================
        // VER HISTORIAL
        // ============================================
        function verHistorial() {
            alert('Función para ver historial de asistencia.\n\nEsto abriría una vista con el calendario mensual completo.');
        }

        // ============================================
        // ATAJOS DE TECLADO
        // ============================================
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                const saveBtn = document.getElementById('saveButton');
                if (saveBtn) saveBtn.click();
            }
            
            if ((e.ctrlKey || e.metaKey) && e.key === 'a' && e.shiftKey) {
                e.preventDefault();
                marcarTodosPresentes();
            }
        });

        // ============================================
        // ADVERTENCIA AL SALIR
        // ============================================
        window.addEventListener('beforeunload', function(e) {
            if (Object.keys(cambiosPendientes).length > 0) {
                e.preventDefault();
                e.returnValue = '¿Estás seguro? Tienes cambios sin guardar.';
                return e.returnValue;
            }
        });
    </script>
    
    <style>
        .appGrid,
        .app {
            display: grid !important;
            grid-template-columns: 260px 1fr !important;
        }
    </style>
    
</body>   
</html>