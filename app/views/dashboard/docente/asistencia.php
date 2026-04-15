<?php
    // 1. VERIFICAR SESIÓN
    if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'Docente') {
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    // 2. CARGAR DEPENDENCIAS
    require_once BASE_PATH . '/app/controllers/perfil.php';
    require_once BASE_PATH . '/app/models/docente/asistencia.php';

    $id_usuario_sesion = (int) $_SESSION['user']['id'];
    $id_institucion    = (int) ($_SESSION['user']['id_institucion'] ?? 0);
    $usuario           = mostrarPerfil($id_usuario_sesion);

    // 3. PARÁMETROS DE FILTRO (GET)
    $curso_seleccionado      = isset($_GET['curso'])      ? (int) $_GET['curso']      : null;
    $asignatura_seleccionada = isset($_GET['asignatura']) ? (int) $_GET['asignatura'] : null;
    $fecha_seleccionada      = !empty($_GET['fecha'])     ? $_GET['fecha']             : date('Y-m-d');

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_seleccionada)) {
        $fecha_seleccionada = date('Y-m-d');
    }

    // 4. DATOS REALES DE BD
    $objAsistencia = new AsistenciaDocente();

    // Cursos + asignaturas del docente
    $mis_cursos_asignaturas = $objAsistencia->obtenerCursosConAsignaturas($id_usuario_sesion, $id_institucion);

    // 5. RESOLVER CURSO Y ASIGNATURA ACTUALES
    $curso_actual          = null;
    $asignatura_actual     = null;
    $asignaturas_del_curso = [];

    if ($curso_seleccionado) {
        foreach ($mis_cursos_asignaturas as $curso) {
            if ($curso['id_curso'] === $curso_seleccionado) {
                $curso_actual          = $curso;
                $asignaturas_del_curso = $curso['asignaturas'];
                if ($asignatura_seleccionada) {
                    foreach ($curso['asignaturas'] as $asig) {
                        if ($asig['id'] === $asignatura_seleccionada) {
                            $asignatura_actual = $asig;
                            break;
                        }
                    }
                }
                break;
            }
        }
    }

    // 6. ESTUDIANTES + ASISTENCIA REAL
    // Mapeo de estado BD → código de vista
    $mapaEstadoVista = ['Presente' => 'P', 'Ausente' => 'A', 'Justificado' => 'E'];

    $estudiantes = [];
    $historial_asistencia = [];
    if ($curso_seleccionado && $asignatura_seleccionada) {
        $rawEstudiantes = $objAsistencia->obtenerEstudiantesConAsistencia(
            $curso_seleccionado,
            $asignatura_seleccionada,
            $fecha_seleccionada,
            $id_institucion
        );

        $id_docente_actual = $objAsistencia->obtenerIdDocente($id_usuario_sesion, $id_institucion);
        if ($id_docente_actual > 0) {
            $historial_asistencia = $objAsistencia->obtenerHistorialAsistencia(
                $curso_seleccionado,
                $asignatura_seleccionada,
                $id_docente_actual,
                $id_institucion,
                20
            );
        }

        foreach ($rawEstudiantes as $e) {
            $estadoDB = $e['asistencia_estado'] ?? null;
            $estudiantes[] = [
                'id'             => (int) $e['id'],
                'nombres'        => (string) $e['nombres'],
                'apellidos'      => (string) $e['apellidos'],
                'documento'      => (string) $e['documento'],
                'foto'           => !empty($e['foto']) ? $e['foto'] : 'default.png',
                'asistencia_hoy' => $estadoDB !== null ? ($mapaEstadoVista[$estadoDB] ?? null) : null,
            ];
        }
    }

    // 7. CALCULAR ESTADÍSTICAS
    $totalEstudiantes     = count($estudiantes);
    $presentes            = count(array_filter($estudiantes, fn($e) => $e['asistencia_hoy'] === 'P'));
    $ausentes             = count(array_filter($estudiantes, fn($e) => $e['asistencia_hoy'] === 'A'));
    $tardanzas            = count(array_filter($estudiantes, fn($e) => $e['asistencia_hoy'] === 'T'));
    $excusas              = count(array_filter($estudiantes, fn($e) => $e['asistencia_hoy'] === 'E'));
    $sinMarcar            = count(array_filter($estudiantes, fn($e) => $e['asistencia_hoy'] === null));
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
        /* ── Layout base ────────────────────────────────── */
        .app { grid-template-columns: 260px 1fr !important; }
        .main { padding: 0 0 48px 0 !important; }
        .topbar { padding: 28px 32px 0 32px !important; margin-bottom: 24px; }

        /* ── Page header ───────────────────────────────── */
        .page-header {
            padding: 0 32px 24px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }
        .page-header-left { display: flex; align-items: center; gap: 16px; }
        .page-header-icon {
            width: 48px; height: 48px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--brand) 0%, #7c3aed 100%);
            display: grid; place-items: center;
            font-size: 22px; color: #fff;
            box-shadow: 0 8px 20px rgba(79,70,229,.35);
        }
        .page-header-text h2 {
            margin: 0; font-size: 22px; font-weight: 700;
            font-family: 'Montserrat', sans-serif; color: #fff;
        }
        .page-header-text p {
            margin: 2px 0 0 0; font-size: 13px; color: var(--muted);
        }
        .badge-periodo {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 6px 14px;
            background: rgba(16,185,129,.12);
            color: #10b981;
            border: 1px solid rgba(16,185,129,.2);
            border-radius: 20px;
            font-size: 12px; font-weight: 600;
        }

        /* ── Filtros card ──────────────────────────────── */
        .filters-card {
            margin: 0 32px 24px 32px;
            background: #11193a;
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 8px 30px rgba(0,0,0,.2);
        }
        .filters-card-header {
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border);
        }
        .filters-card-header i {
            font-size: 20px; color: var(--brand);
        }
        .filters-card-header h3 {
            margin: 0; font-size: 15px; font-weight: 700;
            font-family: 'Montserrat', sans-serif; color: #e6e9f4;
        }
        .filters-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 180px auto;
            gap: 16px; align-items: end;
        }
        .filter-group { display: flex; flex-direction: column; gap: 6px; }
        .filter-label {
            font-size: 12px; font-weight: 600;
            color: var(--muted); letter-spacing: .04em;
            display: flex; align-items: center; gap: 5px;
            text-transform: uppercase;
        }
        .filter-label i { font-size: 14px; color: var(--brand); }
        .filter-select,
        .filter-input {
            width: 100%;
            padding: 11px 14px;
            background: #0e142e;
            border: 1px solid var(--border);
            border-radius: 10px;
            color: #e6e9f4;
            font-size: 14px; font-weight: 500;
            transition: border-color .2s, background .2s;
            cursor: pointer;
        }
        .filter-select:focus,
        .filter-input:focus {
            outline: none;
            border-color: rgba(79,70,229,.6);
            background: rgba(79,70,229,.08);
        }
        .filter-select option { background: #0e142e; color: #e6e9f4; }
        .filter-select:disabled { opacity: .45; cursor: not-allowed; }
        .filter-btn {
            padding: 11px 22px;
            background: var(--brand);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-weight: 700; font-size: 14px;
            cursor: pointer;
            display: flex; align-items: center; gap: 8px;
            white-space: nowrap;
            transition: transform .2s, box-shadow .2s;
            box-shadow: 0 4px 14px rgba(79,70,229,.35);
        }
        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(79,70,229,.45);
        }
        .filter-btn i { font-size: 17px; }

        /* ── Aviso sin selección ───────────────────────── */
        .no-selection-message {
            margin: 0 32px 24px 32px;
            background: #11193a;
            border: 1px dashed rgba(255,176,32,.3);
            border-radius: 18px;
            padding: 28px;
            display: flex; align-items: center; gap: 20px;
        }
        .no-selection-icon {
            width: 56px; height: 56px; flex-shrink: 0;
            border-radius: 14px;
            background: rgba(255,176,32,.12);
            display: grid; place-items: center;
            font-size: 26px; color: var(--accent);
        }
        .no-selection-message-content h4 {
            margin: 0 0 6px 0; font-size: 16px; font-weight: 700;
            font-family: 'Montserrat', sans-serif; color: #e6e9f4;
        }
        .no-selection-message-content p {
            margin: 0; font-size: 13px; color: var(--muted); line-height: 1.6;
        }

        /* ── Barra de contexto ─────────────────────────── */
        .context-bar {
            margin: 0 32px 20px 32px;
            background: #11193a;
            border: 1px solid var(--border);
            border-left: 4px solid var(--brand);
            border-radius: 0 12px 12px 0;
            padding: 14px 20px;
        }
        .context-bar-inner {
            display: flex; align-items: center; gap: 24px; flex-wrap: wrap;
        }
        .context-chip {
            display: flex; align-items: center; gap: 7px;
            font-size: 13px; font-weight: 600; color: #c8d0e7;
        }
        .context-chip i { font-size: 16px; color: var(--brand); }
        .context-chip strong { color: #fff; }

        /* ── KPI de asistencia ─────────────────────────── */
        .attendance-stats {
            padding: 0 32px 20px 32px;
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 14px;
        }
        .kpi-att {
            background: #11193a;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 18px 18px 16px 18px;
            display: flex; align-items: center; gap: 14px;
            transition: transform .25s, box-shadow .25s, border-color .25s;
            box-shadow: 0 4px 18px rgba(0,0,0,.18);
        }
        .kpi-att:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 28px rgba(0,0,0,.28);
        }
        .kpi-att-icon {
            width: 50px; height: 50px; flex-shrink: 0;
            border-radius: 12px;
            display: grid; place-items: center;
            font-size: 24px; color: #fff;
        }
        .kpi-att.presentes  { border-top: 2px solid #10b981; }
        .kpi-att.ausentes   { border-top: 2px solid var(--accent-2); }
        .kpi-att.tardanzas  { border-top: 2px solid var(--accent); }
        .kpi-att.excusas    { border-top: 2px solid #3b82f6; }
        .kpi-att.sin-marcar { border-top: 2px solid #6b7280; }
        .kpi-att.presentes  .kpi-att-icon { background: linear-gradient(135deg,#10b981,#059669); box-shadow:0 4px 12px rgba(16,185,129,.3); }
        .kpi-att.ausentes   .kpi-att-icon { background: linear-gradient(135deg,#ef4444,#dc2626); box-shadow:0 4px 12px rgba(239,68,68,.3); }
        .kpi-att.tardanzas  .kpi-att-icon { background: linear-gradient(135deg,#f59e0b,#d97706); box-shadow:0 4px 12px rgba(245,158,11,.3); }
        .kpi-att.excusas    .kpi-att-icon { background: linear-gradient(135deg,#3b82f6,#2563eb); box-shadow:0 4px 12px rgba(59,130,246,.3); }
        .kpi-att.sin-marcar .kpi-att-icon { background: linear-gradient(135deg,#6b7280,#4b5563); box-shadow:0 4px 12px rgba(107,114,128,.3); }
        .kpi-att-content small {
            display: block; font-size: 11px; font-weight: 600;
            text-transform: uppercase; letter-spacing: .06em;
            color: var(--muted); margin-bottom: 4px;
        }
        .kpi-att-content strong {
            display: block; font-size: 26px; font-weight: 700;
            color: #fff; line-height: 1;
        }
        .kpi-att-pct {
            font-size: 12px; color: var(--muted); font-weight: 500;
        }

        /* ── Tabla card ────────────────────────────────── */
        .table-card {
            margin: 0 32px 24px 32px;
            background: #11193a;
            border: 1px solid var(--border);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0,0,0,.2);
        }
        .table-card-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center;
            justify-content: space-between; gap: 16px; flex-wrap: wrap;
        }
        .table-card-header h3 {
            margin: 0; font-size: 16px; font-weight: 700;
            font-family: 'Montserrat', sans-serif; color: #e6e9f4;
            display: flex; align-items: center; gap: 8px;
        }
        .table-card-header h3 i { color: var(--brand); }
        .table-card-actions {
            display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
        }
        .tbl-btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 9px 16px;
            border-radius: 10px; font-size: 13px; font-weight: 600;
            cursor: pointer; border: 1px solid transparent;
            transition: all .2s;
        }
        .tbl-btn i { font-size: 16px; }
        .tbl-btn-primary {
            background: rgba(79,70,229,.12); color: #818cf8;
            border-color: rgba(79,70,229,.25);
        }
        .tbl-btn-primary:hover {
            background: rgba(79,70,229,.22); border-color: rgba(79,70,229,.45);
            transform: translateY(-1px);
        }
        .tbl-btn-success {
            background: rgba(16,185,129,.12); color: #34d399;
            border-color: rgba(16,185,129,.25);
        }
        .tbl-btn-success:hover {
            background: rgba(16,185,129,.22); border-color: rgba(16,185,129,.45);
            transform: translateY(-1px);
        }
        .tbl-btn-danger {
            background: rgba(239,68,68,.12); color: #f87171;
            border-color: rgba(239,68,68,.25);
        }
        .tbl-btn-danger:hover {
            background: rgba(239,68,68,.22); border-color: rgba(239,68,68,.45);
            transform: translateY(-1px);
        }
        .search-box {
            position: relative;
        }
        .search-box input {
            padding: 9px 14px 9px 38px;
            background: #0e142e;
            border: 1px solid var(--border);
            border-radius: 10px;
            color: #e6e9f4; font-size: 13px; width: 220px;
            transition: border-color .2s;
        }
        .search-box input:focus {
            outline: none; border-color: rgba(79,70,229,.5);
        }
        .search-box input::placeholder { color: var(--muted); }
        .search-box i {
            position: absolute; left: 11px; top: 50%;
            transform: translateY(-50%);
            color: var(--brand); font-size: 16px;
        }

        /* ── Tabla interna ─────────────────────────────── */
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
        }
        .attendance-table thead {
            background: #0e142e;
        }
        .attendance-table th {
            padding: 13px 20px;
            text-align: left; color: var(--muted);
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: .07em;
            border-bottom: 1px solid var(--border);
        }
        .attendance-table th:first-child { border-radius: 0; }
        .attendance-table tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background .15s;
        }
        .attendance-table tbody tr:last-child { border-bottom: none; }
        .attendance-table tbody tr:hover { background: rgba(79,70,229,.05); }
        .attendance-table td { padding: 14px 20px; vertical-align: middle; }

        .row-num {
            width: 28px; height: 28px;
            background: rgba(79,70,229,.12);
            color: #818cf8;
            border-radius: 7px;
            display: grid; place-items: center;
            font-size: 12px; font-weight: 700;
        }
        .student-info { display: flex; align-items: center; gap: 12px; }
        .student-avatar {
            width: 40px; height: 40px; flex-shrink: 0;
            border-radius: 10px; object-fit: cover;
            border: 2px solid rgba(79,70,229,.2);
        }
        .student-name {
            font-size: 14px; font-weight: 600; color: #e6e9f4;
            line-height: 1.3;
        }
        .student-doc { font-size: 11px; color: var(--muted); }

        /* Estado actual — pills -------------------------------- */
        .status-pill {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 12px; border-radius: 20px;
            font-size: 12px; font-weight: 600;
        }
        .status-pill.s-P { background:rgba(16,185,129,.15); color:#34d399; }
        .status-pill.s-A { background:rgba(239,68,68,.15);  color:#f87171; }
        .status-pill.s-T { background:rgba(245,158,11,.15); color:#fbbf24; }
        .status-pill.s-E { background:rgba(59,130,246,.15); color:#60a5fa; }
        .status-pill.s-null { background:rgba(107,114,128,.12); color:#9ca3af; }

        /* Botones de acción por estudiante --------------------- */
        .att-actions { display: flex; gap: 6px; }
        .att-btn {
            width: 36px; height: 36px;
            border: 1.5px solid transparent;
            border-radius: 8px;
            display: grid; place-items: center;
            cursor: pointer; font-size: 17px;
            transition: all .18s; position: relative;
        }
        .att-btn:hover { transform: scale(1.12); }
        .att-btn.presente  { background:rgba(16,185,129,.12); color:#10b981; }
        .att-btn.ausente   { background:rgba(239,68,68,.12);  color:#ef4444; }
        .att-btn.tardanza  { background:rgba(245,158,11,.12); color:#f59e0b; }
        .att-btn.excusa    { background:rgba(59,130,246,.12); color:#3b82f6; }
        .att-btn.presente.active,  .att-btn.presente:hover  { background:#10b981; color:#fff; border-color:#10b981; box-shadow:0 3px 10px rgba(16,185,129,.4); }
        .att-btn.ausente.active,   .att-btn.ausente:hover   { background:#ef4444; color:#fff; border-color:#ef4444; box-shadow:0 3px 10px rgba(239,68,68,.4); }
        .att-btn.tardanza.active,  .att-btn.tardanza:hover  { background:#f59e0b; color:#fff; border-color:#f59e0b; box-shadow:0 3px 10px rgba(245,158,11,.4); }
        .att-btn.excusa.active,    .att-btn.excusa:hover    { background:#3b82f6; color:#fff; border-color:#3b82f6; box-shadow:0 3px 10px rgba(59,130,246,.4); }
        /* tooltip */
        .att-btn::before {
            content: attr(data-tooltip);
            position: absolute; bottom: calc(100% + 6px);
            left: 50%; transform: translateX(-50%);
            padding: 4px 9px;
            background: #1e2336; color: #e6e9f4;
            border-radius: 6px; font-size: 11px;
            white-space: nowrap; opacity: 0;
            pointer-events: none; transition: opacity .15s;
        }
        .att-btn:hover::before { opacity: 1; }

        /* ── Leyenda ───────────────────────────────────── */
        .legend-bar {
            margin: 0 32px 24px 32px;
            background: #11193a;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 16px 22px;
            display: flex; align-items: center; gap: 28px; flex-wrap: wrap;
        }
        .legend-bar-title {
            font-size: 12px; font-weight: 700;
            text-transform: uppercase; letter-spacing: .06em;
            color: var(--muted); margin-right: 4px;
        }
        .legend-item {
            display: flex; align-items: center; gap: 8px;
            font-size: 12px; color: #c8d0e7;
        }
        .legend-dot {
            width: 10px; height: 10px; border-radius: 50%;
        }

        .history-panel {
            margin: 0 32px 24px 32px;
            background: #11193a;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 16px 18px;
        }
        .history-panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border);
        }
        .history-panel-title {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
            color: #e6e9f4;
            font-family: 'Montserrat', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .history-panel-sub {
            color: var(--muted);
            font-size: 12px;
            font-weight: 600;
        }

        /* ── Botón guardar flotante ────────────────────── */
        .save-button-floating {
            position: fixed; bottom: 28px; right: 28px;
            padding: 14px 28px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #fff; border: none; border-radius: 14px;
            font-weight: 700; font-size: 15px;
            cursor: pointer;
            box-shadow: 0 8px 24px rgba(16,185,129,.45);
            display: none; align-items: center; gap: 10px;
            transition: all .25s; z-index: 200;
        }
        .save-button-floating.visible { display: flex; }
        .save-button-floating:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 32px rgba(16,185,129,.55);
        }
        .save-button-floating:disabled {
            opacity: .65; cursor: not-allowed; transform: none;
        }
        .save-button-floating i { font-size: 19px; }
        .changes-count {
            padding: 3px 10px;
            background: rgba(255,255,255,.2);
            border-radius: 20px;
            font-size: 12px; font-weight: 700;
        }

        /* ── Modales (confirmación e historial) ───────── */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(3, 8, 26, .72);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1200;
            padding: 18px;
            backdrop-filter: blur(2px);
        }
        .modal-overlay.visible { display: flex; }
        .modal-card {
            width: min(760px, 100%);
            background: #11193a;
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: 0 20px 44px rgba(0,0,0,.45);
            overflow: hidden;
        }
        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 16px 18px;
            border-bottom: 1px solid var(--border);
            background: #0e142e;
        }
        .modal-title {
            margin: 0;
            font-size: 16px;
            color: #e6e9f4;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .modal-close {
            border: 1px solid var(--border);
            background: rgba(255,255,255,.04);
            color: #c8d0e7;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            cursor: pointer;
            display: grid;
            place-items: center;
            font-size: 18px;
        }
        .modal-close:hover { background: rgba(255,255,255,.1); }
        .modal-body {
            padding: 18px;
            color: #c8d0e7;
            font-size: 14px;
            line-height: 1.6;
        }
        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 14px 18px 18px;
        }
        .modal-btn {
            border: 1px solid transparent;
            border-radius: 10px;
            padding: 10px 16px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .modal-btn.secondary {
            background: rgba(255,255,255,.06);
            border-color: var(--border);
            color: #c8d0e7;
        }
        .modal-btn.secondary:hover { background: rgba(255,255,255,.1); }
        .modal-btn.primary {
            background: var(--brand);
            color: #fff;
            box-shadow: 0 8px 16px rgba(79,70,229,.35);
        }
        .modal-btn.primary:hover { background: #5b52f0; }
        .modal-btn.danger { background: #dc2626; color: #fff; }
        .modal-btn.danger:hover { background: #ef4444; }
        .history-empty {
            padding: 14px;
            border: 1px dashed rgba(255,255,255,.16);
            border-radius: 12px;
            color: var(--muted);
            text-align: center;
        }
        .history-list {
            display: grid;
            gap: 10px;
            max-height: 58vh;
            overflow: auto;
            padding-right: 4px;
        }
        .history-item {
            background: #0e142e;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 12px 14px;
            display: grid;
            grid-template-columns: 170px 1fr;
            gap: 12px;
            align-items: center;
        }
        .history-date {
            color: #fff;
            font-weight: 700;
            font-size: 13px;
        }
        .history-metrics {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }
        .metric-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid transparent;
        }
        .metric-chip.p { background: rgba(16,185,129,.13); color: #34d399; border-color: rgba(16,185,129,.25); }
        .metric-chip.a { background: rgba(239,68,68,.13); color: #f87171; border-color: rgba(239,68,68,.25); }
        .metric-chip.j { background: rgba(59,130,246,.13); color: #60a5fa; border-color: rgba(59,130,246,.25); }
        .metric-chip.t { background: rgba(245,158,11,.13); color: #fbbf24; border-color: rgba(245,158,11,.25); }

        /* ── Responsive ────────────────────────────────── */
        @media (max-width: 1100px) {
            .attendance-stats { grid-template-columns: repeat(3,1fr); }
            .filters-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 768px) {
            .filters-grid { grid-template-columns: 1fr; }
            .attendance-stats { grid-template-columns: repeat(2,1fr); }
            .table-card-header { flex-direction: column; align-items: flex-start; }
            .filters-card, .table-card, .no-selection-message,
            .context-bar, .legend-bar { margin-left: 16px; margin-right: 16px; }
            .attendance-stats { padding-left: 16px; padding-right: 16px; }
            .search-box input { width: 100%; }
            .history-item { grid-template-columns: 1fr; }
            .history-metrics { justify-content: flex-start; }
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
                <?php include_once BASE_PATH . '/app/views/layouts/boton_perfil_solo.php'; ?>
            </div>

            <!-- FILTROS -->
            <div class="filters-card">
                <div class="filters-card-header">
                    <i class="ri-filter-3-line"></i>
                    <h3>Configurar Asistencia</h3>
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
                            <select name="asignatura" class="filter-select" id="selectAsignatura" required <?= !$curso_seleccionado ? 'disabled' : '' ?>>
                                <option value="">-- Todas las asignaturas --</option>
                                <?php if ($curso_seleccionado && !empty($asignaturas_del_curso)): ?>
                                    <?php foreach ($asignaturas_del_curso as $asig): ?>
                                        <?php $asigHorario = trim((string)($asig['horario'] ?? '')); ?>
                                        <option value="<?= $asig['id'] ?>" 
                                                <?= $asignatura_seleccionada == $asig['id'] ? 'selected' : '' ?>>
                                            <?= $asig['nombre'] ?><?= $asigHorario !== '' ? ' - ' . $asigHorario : '' ?>
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

            <?php if (!$curso_seleccionado || !$asignatura_seleccionada): ?>
                <div class="no-selection-message">
                    <div class="no-selection-icon"><i class="ri-information-line"></i></div>
                    <div class="no-selection-message-content">
                        <h4>Selecciona un curso y una asignatura para comenzar</h4>
                        <p>Utiliza los filtros de arriba para elegir el curso, la asignatura y la fecha en la que deseas tomar asistencia.</p>
                    </div>
                </div>
            <?php else: ?>
                <!-- CONTEXTO ACTUAL -->
                <div class="context-bar">
                    <div class="context-bar-inner">
                        <div class="context-chip">
                            <i class="ri-book-open-line"></i>
                            Curso: <strong><?= $curso_actual['curso_nombre'] ?></strong>
                        </div>
                        <?php if ($asignatura_actual): ?>
                            <div class="context-chip">
                                <i class="ri-book-2-line"></i>
                                Asignatura: <strong><?= $asignatura_actual['nombre'] ?></strong>
                            </div>
                        <?php endif; ?>
                        <div class="context-chip">
                            <i class="ri-calendar-line"></i>
                            Fecha: <strong><?= date('d/m/Y', strtotime($fecha_seleccionada)) ?></strong>
                        </div>
                        <div class="context-chip">
                            <i class="ri-group-line"></i>
                            <strong><?= $totalEstudiantes ?></strong> estudiantes
                        </div>
                    </div>
                </div>

                <!-- ESTADÍSTICAS -->
                <div class="attendance-stats">
                    <div class="kpi-att presentes">
                        <div class="kpi-att-icon"><i class="ri-checkbox-circle-line"></i></div>
                        <div class="kpi-att-content">
                            <small>Presentes</small>
                            <strong><?= $presentes ?></strong>
                            <span class="kpi-att-pct"><?= $porcentajeAsistencia ?>% asistencia</span>
                        </div>
                    </div>
                    <div class="kpi-att ausentes">
                        <div class="kpi-att-icon"><i class="ri-close-circle-line"></i></div>
                        <div class="kpi-att-content">
                            <small>Ausentes</small>
                            <strong><?= $ausentes ?></strong>
                        </div>
                    </div>
                    <div class="kpi-att tardanzas">
                        <div class="kpi-att-icon"><i class="ri-time-line"></i></div>
                        <div class="kpi-att-content">
                            <small>Tardanzas</small>
                            <strong><?= $tardanzas ?></strong>
                        </div>
                    </div>
                    <div class="kpi-att excusas">
                        <div class="kpi-att-icon"><i class="ri-file-text-line"></i></div>
                        <div class="kpi-att-content">
                            <small>Excusas</small>
                            <strong><?= $excusas ?></strong>
                        </div>
                    </div>
                    <div class="kpi-att sin-marcar">
                        <div class="kpi-att-icon"><i class="ri-question-line"></i></div>
                        <div class="kpi-att-content">
                            <small>Sin Marcar</small>
                            <strong><?= $sinMarcar ?></strong>
                        </div>
                    </div>
                </div>

                <!-- TABLA CARD -->
                <div class="table-card">
                    <div class="table-card-header">
                        <h3><i class="ri-group-line"></i> Lista de Estudiantes</h3>
                        <div class="table-card-actions">
                            <button type="button" class="tbl-btn tbl-btn-success" onclick="marcarTodosPresentes()">
                                <i class="ri-checkbox-multiple-line"></i>
                                Todos Presentes
                            </button>
                            <div class="search-box">
                                <i class="ri-search-line"></i>
                                <input type="text" id="searchStudent" placeholder="Buscar estudiante..." onkeyup="filtrarEstudiantes()">
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($estudiantes)): ?>
                        <table class="attendance-table" id="tablaAsistencia">
                            <thead>
                                <tr>
                                     <th style="width: 50px;">#</th>
                                     <th>Estudiante</th>
                                     <th style="width: 20%; text-align: center;">Estado Actual</th>
                                     <th style="width: 28%; text-align: center;">Marcar Asistencia</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($estudiantes as $index => $estudiante): ?>
                                    <tr class="student-row" data-student-id="<?= $estudiante['id'] ?>">
                                        <td><div class="row-num"><?= $index + 1 ?></div></td>
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
                                                    <div class="student-doc"><?= $estudiante['documento'] ?></div>
                                                </div>
                                            </div>
                                        </td>

                                        <td style="text-align: center;">
                                            <span class="current-status" data-status="<?= $estudiante['asistencia_hoy'] ?>">
                                                <?php
                                                    switch($estudiante['asistencia_hoy']) {
                                                        case 'P':
                                                            echo '<span class="status-pill s-P"><i class="ri-checkbox-circle-fill"></i> Presente</span>';
                                                            break;
                                                        case 'A':
                                                            echo '<span class="status-pill s-A"><i class="ri-close-circle-fill"></i> Ausente</span>';
                                                            break;
                                                        case 'T':
                                                            echo '<span class="status-pill s-T"><i class="ri-time-fill"></i> Tardanza</span>';
                                                            break;
                                                        case 'E':
                                                            echo '<span class="status-pill s-E"><i class="ri-file-text-fill"></i> Excusa</span>';
                                                            break;
                                                        default:
                                                            echo '<span class="status-pill s-null"><i class="ri-question-fill"></i> Sin marcar</span>';
                                                    }
                                                ?>
                                            </span>
                                        </td>
                                        <td style="text-align: center;">
                                            <div class="att-actions">
                                                <button type="button" class="att-btn presente <?= $estudiante['asistencia_hoy'] === 'P' ? 'active' : '' ?>" 
                                                        data-tooltip="Presente"
                                                        data-type="P"
                                                        onclick="marcarAsistencia(<?= $estudiante['id'] ?>, 'P', this)">
                                                    <i class="ri-checkbox-circle-line"></i>
                                                </button>
                                                <button type="button" class="att-btn ausente <?= $estudiante['asistencia_hoy'] === 'A' ? 'active' : '' ?>" 
                                                        data-tooltip="Ausente"
                                                        data-type="A"
                                                        onclick="marcarAsistencia(<?= $estudiante['id'] ?>, 'A', this)">
                                                    <i class="ri-close-circle-line"></i>
                                                </button>
                                                <button type="button" class="att-btn tardanza <?= $estudiante['asistencia_hoy'] === 'T' ? 'active' : '' ?>" 
                                                        data-tooltip="Tardanza"
                                                        data-type="T"
                                                        onclick="marcarAsistencia(<?= $estudiante['id'] ?>, 'T', this)">
                                                    <i class="ri-time-line"></i>
                                                </button>
                                                <button type="button" class="att-btn excusa <?= $estudiante['asistencia_hoy'] === 'E' ? 'active' : '' ?>" 
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
                <div class="legend-bar">
                    <span class="legend-bar-title">Leyenda:</span>
                    <div class="legend-item"><span class="legend-dot" style="background:#10b981"></span> Presente</div>
                    <div class="legend-item"><span class="legend-dot" style="background:#ef4444"></span> Ausente</div>
                    <div class="legend-item"><span class="legend-dot" style="background:#f59e0b"></span> Tardanza</div>
                    <div class="legend-item"><span class="legend-dot" style="background:#3b82f6"></span> Excusa (justificada)</div>
                    <div class="legend-item"><span class="legend-dot" style="background:#6b7280"></span> Sin marcar</div>
                </div>

                <div class="history-panel">
                    <div class="history-panel-header">
                        <h3 class="history-panel-title"><i class="ri-history-line"></i> Historial de Asistencia</h3>
                        <span class="history-panel-sub">Últimos 20 registros</span>
                    </div>
                    <?php if (empty($historial_asistencia)): ?>
                        <div class="history-empty">No hay historial para este curso y asignatura todavía.</div>
                    <?php else: ?>
                        <div class="history-list">
                            <?php foreach ($historial_asistencia as $h): ?>
                                <?php
                                    $f = !empty($h['fecha']) ? date('d/m/Y', strtotime($h['fecha'])) : 'Sin fecha';
                                    $p = (int) ($h['presentes'] ?? 0);
                                    $a = (int) ($h['ausentes'] ?? 0);
                                    $j = (int) ($h['justificados'] ?? 0);
                                    $t = (int) ($h['total_registrados'] ?? 0);
                                ?>
                                <div class="history-item">
                                    <div class="history-date"><?= htmlspecialchars($f) ?></div>
                                    <div class="history-metrics">
                                        <span class="metric-chip p"><i class="ri-checkbox-circle-line"></i> <?= $p ?> Pres.</span>
                                        <span class="metric-chip a"><i class="ri-close-circle-line"></i> <?= $a ?> Aus.</span>
                                        <span class="metric-chip j"><i class="ri-file-text-line"></i> <?= $j ?> Just.</span>
                                        <span class="metric-chip t"><i class="ri-group-line"></i> <?= $t ?> Reg.</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
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

        <div class="modal-overlay" id="confirmModal" aria-hidden="true">
            <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="confirmModalTitle">
                <div class="modal-header">
                    <h3 class="modal-title" id="confirmModalTitle"><i class="ri-error-warning-line"></i> Confirmar acción</h3>
                    <button type="button" class="modal-close" id="confirmCloseBtn" aria-label="Cerrar">&times;</button>
                </div>
                <div class="modal-body" id="confirmModalMessage"></div>
                <div class="modal-actions">
                    <button type="button" class="modal-btn secondary" id="confirmCancelBtn">Cancelar</button>
                    <button type="button" class="modal-btn primary" id="confirmAcceptBtn">Aceptar</button>
                </div>
            </div>
        </div>

        <div class="modal-overlay" id="historyModal" aria-hidden="true">
            <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="historyModalTitle">
                <div class="modal-header">
                    <h3 class="modal-title" id="historyModalTitle"><i class="ri-history-line"></i> Historial de Asistencia</h3>
                    <button type="button" class="modal-close" id="historyCloseBtn" aria-label="Cerrar">&times;</button>
                </div>
                <div class="modal-body" id="historyModalBody">Cargando historial...</div>
                <div class="modal-actions">
                    <button type="button" class="modal-btn secondary" id="historyAcceptBtn">Cerrar</button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script>
        // ============================================
        // VARIABLES GLOBALES
        // ============================================
        let cambiosPendientes = {};
        let estadoOriginal = {};
        let confirmAcceptCallback = null;

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
                        const horario = (asig.horario || '').toString().trim();
                        option.textContent = horario ? `${asig.nombre} - ${horario}` : asig.nombre;
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
        function construirEstadoHTML(tipo) {
            switch (tipo) {
                case 'P': return '<span class="status-pill s-P"><i class="ri-checkbox-circle-fill"></i> Presente</span>';
                case 'A': return '<span class="status-pill s-A"><i class="ri-close-circle-fill"></i> Ausente</span>';
                case 'T': return '<span class="status-pill s-T"><i class="ri-time-fill"></i> Tardanza</span>';
                case 'E': return '<span class="status-pill s-E"><i class="ri-file-text-fill"></i> Excusa</span>';
                default:  return '<span class="status-pill s-null"><i class="ri-question-fill"></i> Sin marcar</span>';
            }
        }

        function abrirModalConfirmacion(mensaje, accionAceptar, esPeligro = false) {
            const modal = document.getElementById('confirmModal');
            const messageEl = document.getElementById('confirmModalMessage');
            const acceptBtn = document.getElementById('confirmAcceptBtn');

            if (!modal || !messageEl || !acceptBtn) {
                if (typeof accionAceptar === 'function') accionAceptar();
                return;
            }

            messageEl.textContent = mensaje;
            acceptBtn.classList.remove('primary', 'danger');
            acceptBtn.classList.add(esPeligro ? 'danger' : 'primary');
            confirmAcceptCallback = accionAceptar;
            modal.classList.add('visible');
            modal.setAttribute('aria-hidden', 'false');
        }

        function cerrarModalConfirmacion() {
            const modal = document.getElementById('confirmModal');
            if (!modal) return;
            modal.classList.remove('visible');
            modal.setAttribute('aria-hidden', 'true');
            confirmAcceptCallback = null;
        }

        function marcarAsistencia(studentId, tipo, boton) {
            const fila = boton.closest('.student-row');
            const statusElement = fila.querySelector('.current-status');
            const botonesAsistencia = fila.querySelectorAll('.att-btn');
            
            botonesAsistencia.forEach(btn => btn.classList.remove('active'));
            boton.classList.add('active');
            
            statusElement.innerHTML = construirEstadoHTML(tipo);
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
        function ejecutarMarcarTodosPresentes() {
            const filas = document.querySelectorAll('.student-row');
            filas.forEach(fila => {
                const studentId = fila.getAttribute('data-student-id');
                const botonPresente = fila.querySelector('.att-btn.presente');
                if (botonPresente) {
                    marcarAsistencia(studentId, 'P', botonPresente);
                }
            });
            mostrarToast('Todos los estudiantes quedaron en estado Presente', 'success');
        }

        function marcarTodosPresentes() {
            abrirModalConfirmacion(
                'Se marcarán todos los estudiantes como presentes. ¿Deseas continuar?',
                ejecutarMarcarTodosPresentes,
                false
            );
        }

        // ============================================
        // LIMPIAR ASISTENCIA
        // ============================================
        function ejecutarLimpiarAsistencia() {
            const filas = document.querySelectorAll('.student-row');
            filas.forEach(fila => {
                const studentId = fila.getAttribute('data-student-id');
                const statusElement = fila.querySelector('.current-status');
                const botonesAsistencia = fila.querySelectorAll('.att-btn');
                
                botonesAsistencia.forEach(btn => btn.classList.remove('active'));
                
                statusElement.innerHTML = construirEstadoHTML('');
                statusElement.setAttribute('data-status', '');
                
                if (estadoOriginal[studentId] !== null && estadoOriginal[studentId] !== '') {
                    cambiosPendientes[studentId] = null;
                } else {
                    delete cambiosPendientes[studentId];
                }
            });
            
            actualizarEstadisticas();
            mostrarBotonGuardar();
            mostrarToast('Asistencia limpiada. Recuerda guardar los cambios.', 'success');
        }

        function limpiarAsistencia() {
            abrirModalConfirmacion(
                'Se eliminarán las marcas actuales de asistencia en esta lista. ¿Deseas continuar?',
                ejecutarLimpiarAsistencia,
                true
            );
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
            
            document.querySelector('.kpi-att.presentes .kpi-att-content strong').innerHTML = 
                `${presentes} <span class="kpi-att-pct">(${porcentaje}%)</span>`;
            document.querySelector('.kpi-att.ausentes .kpi-att-content strong').textContent = ausentes;
            document.querySelector('.kpi-att.tardanzas .kpi-att-content strong').textContent = tardanzas;
            document.querySelector('.kpi-att.excusas .kpi-att-content strong').textContent = excusas;
            document.querySelector('.kpi-att.sin-marcar .kpi-att-content strong').textContent = sinMarcar;
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
                    mostrarToast('No hay cambios pendientes por guardar.', 'info');
                    return;
                }
                
                const payload = {
                    curso_id:      <?= (int)($curso_seleccionado ?? 0) ?>,
                    asignatura_id: <?= (int)($asignatura_seleccionada ?? 0) ?>,
                    fecha:         '<?= htmlspecialchars($fecha_seleccionada, ENT_QUOTES) ?>',
                    asistencias:   cambiosPendientes
                };

                saveBtn.disabled = true;
                saveBtn.innerHTML = '<i class="ri-loader-4-line"></i> Guardando...';

                fetch('<?= BASE_URL ?>/docente/guardar-asistencia', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        // Sincronizar estado original con los cambios guardados
                        Object.assign(estadoOriginal, cambiosPendientes);
                        cambiosPendientes = {};
                        mostrarBotonGuardar();
                        mostrarToast('✅ ' + (data.message || 'Asistencia guardada correctamente'), 'success');
                    } else {
                        mostrarToast('❌ ' + (data.message || 'Error al guardar'), 'error');
                    }
                })
                .catch(() => {
                    mostrarToast('❌ Error de conexión al guardar', 'error');
                })
                .finally(() => {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="ri-save-line"></i> Guardar Cambios <span class="changes-count" id="changesCount">0</span>';
                    mostrarBotonGuardar();
                });
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
        function cerrarModalHistorial() {
            const modal = document.getElementById('historyModal');
            if (!modal) return;
            modal.classList.remove('visible');
            modal.setAttribute('aria-hidden', 'true');
        }

        function formatearFecha(fechaIso) {
            const parts = (fechaIso || '').split('-');
            if (parts.length !== 3) return fechaIso;
            return `${parts[2]}/${parts[1]}/${parts[0]}`;
        }

        function construirFilaHistorial(item) {
            const presentes = Number(item.presentes || 0);
            const ausentes = Number(item.ausentes || 0);
            const justificados = Number(item.justificados || 0);
            const total = Number(item.total_registrados || 0);

            return `
                <div class="history-item">
                    <div class="history-date">${formatearFecha(item.fecha)}</div>
                    <div class="history-metrics">
                        <span class="metric-chip p"><i class="ri-checkbox-circle-line"></i> ${presentes} Pres.</span>
                        <span class="metric-chip a"><i class="ri-close-circle-line"></i> ${ausentes} Aus.</span>
                        <span class="metric-chip j"><i class="ri-file-text-line"></i> ${justificados} Just.</span>
                        <span class="metric-chip t"><i class="ri-group-line"></i> ${total} Reg.</span>
                    </div>
                </div>
            `;
        }

        function verHistorial() {
            const modal = document.getElementById('historyModal');
            const body = document.getElementById('historyModalBody');
            if (!modal || !body) {
                mostrarToast('No se pudo abrir el historial en este momento.', 'error');
                return;
            }

            modal.classList.add('visible');
            modal.setAttribute('aria-hidden', 'false');
            body.innerHTML = 'Cargando historial...';

            const curso = <?= (int)($curso_seleccionado ?? 0) ?>;
            const asignatura = <?= (int)($asignatura_seleccionada ?? 0) ?>;

            fetch(`<?= BASE_URL ?>/docente/historial-asistencia?curso=${curso}&asignatura=${asignatura}&limite=20`)
                .then(resp => resp.json())
                .then(data => {
                    if (!data.success) {
                        body.innerHTML = `<div class="history-empty">${data.message || 'No se pudo cargar el historial.'}</div>`;
                        return;
                    }

                    const historial = Array.isArray(data.historial) ? data.historial : [];
                    if (historial.length === 0) {
                        body.innerHTML = '<div class="history-empty">No hay registros históricos para esta combinación de curso y asignatura.</div>';
                        return;
                    }

                    body.innerHTML = `<div class="history-list">${historial.map(construirFilaHistorial).join('')}</div>`;
                })
                .catch(() => {
                    body.innerHTML = '<div class="history-empty">Error de conexión al consultar historial.</div>';
                });
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
        // TOAST NOTIFICATIONS
        // ============================================
        function mostrarToast(mensaje, tipo) {
            const toast = document.createElement('div');
            const bg = tipo === 'success' ? '#10b981' : (tipo === 'info' ? '#3b82f6' : '#ef4444');
            toast.style.cssText = `position:fixed;top:20px;right:20px;background:${bg};color:white;padding:14px 20px;border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,.3);font-weight:500;z-index:10000;display:flex;align-items:center;gap:10px;animation:slideInRight .3s ease`;
            toast.textContent = mensaje;
            document.body.appendChild(toast);
            setTimeout(() => { toast.style.animation = 'slideOutRight .3s ease'; setTimeout(() => toast.remove(), 300); }, 3000);
        }

        // ============================================
        // ADVERTENCIA AL SALIR
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {
            const confirmModal = document.getElementById('confirmModal');
            const confirmCloseBtn = document.getElementById('confirmCloseBtn');
            const confirmCancelBtn = document.getElementById('confirmCancelBtn');
            const confirmAcceptBtn = document.getElementById('confirmAcceptBtn');

            if (confirmCloseBtn) confirmCloseBtn.addEventListener('click', cerrarModalConfirmacion);
            if (confirmCancelBtn) confirmCancelBtn.addEventListener('click', cerrarModalConfirmacion);
            if (confirmAcceptBtn) {
                confirmAcceptBtn.addEventListener('click', function() {
                    const accion = confirmAcceptCallback;
                    cerrarModalConfirmacion();
                    if (typeof accion === 'function') accion();
                });
            }
            if (confirmModal) {
                confirmModal.addEventListener('click', function(e) {
                    if (e.target === confirmModal) cerrarModalConfirmacion();
                });
            }

            const historyModal = document.getElementById('historyModal');
            const historyCloseBtn = document.getElementById('historyCloseBtn');
            const historyAcceptBtn = document.getElementById('historyAcceptBtn');

            if (historyCloseBtn) historyCloseBtn.addEventListener('click', cerrarModalHistorial);
            if (historyAcceptBtn) historyAcceptBtn.addEventListener('click', cerrarModalHistorial);
            if (historyModal) {
                historyModal.addEventListener('click', function(e) {
                    if (e.target === historyModal) cerrarModalHistorial();
                });
            }
        });

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