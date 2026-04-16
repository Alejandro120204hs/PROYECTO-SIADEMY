<?php
    require_once BASE_PATH . '/app/helpers/session_docente.php';
    require_once BASE_PATH . '/app/controllers/docente/view_data.php';

    extract(obtenerDataVistaDocenteAsistencia(), EXTR_SKIP);
?>

<!doctype html>
<html lang="es">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • Gestión de Asistencia</title>
    <?php include_once __DIR__ . '/../../layouts/header_coordinador.php' ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-docente.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/docente/asistencia.css?v=<?= $asistenciaCssVersion ?>">

</head>

<body style="margin:0;padding:0;overflow-x:hidden;">
    <div class="app hide-right" id="appGrid" style="margin:0;padding:0;"
        data-base-url="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>"
        data-cursos='<?= docenteJsonParaHtml($mis_cursos_asignaturas) ?>'
        data-curso-id="<?= (int) ($curso_seleccionado ?? 0) ?>"
        data-asignatura-id="<?= (int) ($asignatura_seleccionada ?? 0) ?>"
        data-fecha="<?= htmlspecialchars($fecha_seleccionada, ENT_QUOTES, 'UTF-8') ?>">
        <?php include_once __DIR__ . '/../../layouts/sidebar_docente.php'; ?>
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
                                            <?= $curso_seleccionado == $curso['id_curso'] ? 'selected' : '' ?>>
                                        <?= $curso['curso_nombre'] ?> - <?= $curso['jornada'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- FILTRO 2: ASIGNATURA (se actualiza din├ímicamente) -->
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

                        <!-- BOT├ôN APLICAR FILTROS -->
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

                <!-- ESTAD├ìSTICAS -->
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
                        <span class="history-panel-sub">├Ültimos 20 registros</span>
                    </div>
                    <?php if (empty($historial_asistencia)): ?>
                        <div class="history-empty">No hay historial para este curso y asignatura todav├¡a.</div>
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

    <!-- BOT├ôN GUARDAR FLOTANTE -->
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

    <script src="<?= BASE_URL ?>/public/assets/dashboard/js/docente/asistencia.js?v=<?= $asistenciaJsVersion ?>"></script>

</body>   
</html>


