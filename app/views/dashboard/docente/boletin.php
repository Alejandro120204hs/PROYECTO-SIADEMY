<?php
/**
 * Vista: Boletines de Mis Estudiantes — Rol Docente
 * Variables del controlador:
 *   $modo                — 'lista' | 'boletin'
 *   $anio, $idInstitucion, $idDocente
 *
 * Modo lista:  $cursos, $estudiantes, $stats, $idCursoFiltro, $busqueda
 * Modo boletin: $idEstudiante, $boletin_estudiante, $boletin_periodos,
 *               $boletin_por_periodo, $boletin_sin_datos, $periodoActivoDefault
 */

require_once BASE_PATH . '/app/controllers/perfil.php';
$id_user = $_SESSION['user']['id'] ?? 0;
$usuario = mostrarPerfil($id_user);

function doc_bol_notaBadge(string $estado): string {
    return match ($estado) {
        'superior' => 'nota-superior', 'alto'  => 'nota-alto',
        'basico'   => 'nota-basico',   'bajo'  => 'nota-bajo',
        default    => 'nota-sin',
    };
}
function doc_bol_promClase(?float $p): string {
    if ($p === null) return 'prom-sinnota';
    if ($p >= 4.5)  return 'prom-superior';
    if ($p >= 4.0)  return 'prom-alto';
    if ($p >  3.0)  return 'prom-basico';
    return 'prom-bajo';
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • Boletines <?= (int)$anio ?></title>
    <?php include_once __DIR__ . '/../../layouts/header_coordinador.php' ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-boletines-admin.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/modo-claro-docente.css">
</head>
<body>
<div class="app hide-right" id="appGrid">

    <!-- SIDEBAR -->
    <?php include_once __DIR__ . '/../../layouts/sidebar_docente.php'; ?>

    <!-- MAIN -->
    <main class="main">

        <!-- TOPBAR -->
        <div class="topbar">
            <div class="topbar-left">
                <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú">
                    <i class="ri-menu-2-line"></i>
                </button>
                <?php if ($modo === 'boletin'): ?>
                    <a href="<?= BASE_URL ?>/docente/boletines" class="btn-back">
                        <i class="ri-arrow-left-line"></i>
                    </a>
                <?php endif; ?>
                <div class="title">
                    <?= $modo === 'lista' ? 'Boletines de Mis Estudiantes' : 'Boletín del Estudiante' ?>
                </div>
            </div>
            <?php include_once BASE_PATH . '/app/views/layouts/boton_perfil_solo.php'; ?>
        </div>

        <?php if ($modo === 'lista'): ?>
        <!-- ══ MODO LISTA ══════════════════════════════════════════════════════ -->

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue"><i class="ri-user-3-line"></i></div>
                <div class="stat-content">
                    <h3><?= (int)($stats['total_estudiantes'] ?? 0) ?></h3>
                    <p>Mis estudiantes</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="ri-book-2-line"></i></div>
                <div class="stat-content">
                    <h3><?= (int)($stats['total_cursos'] ?? 0) ?></h3>
                    <p>Cursos que dicto</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple"><i class="ri-calendar-line"></i></div>
                <div class="stat-content">
                    <h3><?= (int)$anio ?></h3>
                    <p>Año lectivo</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange"><i class="ri-file-paper-2-line"></i></div>
                <div class="stat-content">
                    <h3><?= count($estudiantes ?? []) ?></h3>
                    <p>Resultados mostrados</p>
                </div>
            </div>
        </div>

        <!-- FILTROS -->
        <form method="GET" action="<?= BASE_URL ?>/docente/boletines" class="filtros-bar">
            <div class="search-wrap">
                <i class="ri-search-line"></i>
                <input type="text" name="q"
                       placeholder="Buscar por nombre o documento…"
                       value="<?= htmlspecialchars($busqueda ?? '') ?>">
            </div>
            <select name="curso" class="select-curso">
                <option value="">Todos mis cursos</option>
                <?php foreach ($cursos as $c): ?>
                    <option value="<?= (int)$c['id'] ?>"
                        <?= (int)($idCursoFiltro ?? 0) === (int)$c['id'] ? 'selected' : '' ?>>
                        Grado <?= htmlspecialchars($c['grado']) ?> – <?= htmlspecialchars($c['curso']) ?>
                        <?= !empty($c['jornada']) ? '(' . htmlspecialchars($c['jornada']) . ')' : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn-filtrar">
                <i class="ri-filter-3-line"></i> Filtrar
            </button>
            <?php if (!empty($busqueda) || !empty($idCursoFiltro)): ?>
                <a href="<?= BASE_URL ?>/docente/boletines" class="btn-limpiar">
                    <i class="ri-close-line"></i> Limpiar
                </a>
            <?php endif; ?>
        </form>

        <?php if (empty($estudiantes)): ?>
            <div class="empty-state">
                <i class="ri-file-paper-2-line"></i>
                <h3>Sin estudiantes</h3>
                <p>
                    <?php if (empty($cursos)): ?>
                        No tienes cursos asignados para el año <?= (int)$anio ?>.
                    <?php else: ?>
                        No se encontraron estudiantes con los filtros aplicados.
                    <?php endif; ?>
                </p>
            </div>
        <?php else: ?>
            <div class="table-card">
                <table class="estudiantes-table">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Documento</th>
                            <th>Grado / Curso</th>
                            <th>Jornada</th>
                            <th class="text-center">Boletín</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($estudiantes as $est): ?>
                            <tr onclick="window.location='<?= BASE_URL ?>/docente/boletines?id=<?= (int)$est['id'] ?>'" style="cursor:pointer">
                                <td>
                                    <div class="est-cell">
                                        <div class="est-avatar">
                                            <?php if (!empty($est['foto'])): ?>
                                                <img src="<?= BASE_URL . '/public/uploads/fotos/' . htmlspecialchars($est['foto']) ?>" alt="">
                                            <?php else: ?>
                                                <i class="ri-user-3-fill"></i>
                                            <?php endif; ?>
                                        </div>
                                        <span><?= htmlspecialchars($est['apellidos'] . ', ' . $est['nombres']) ?></span>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($est['documento'] ?? '—') ?></td>
                                <td>Grado <?= htmlspecialchars($est['grado']) ?> – <?= htmlspecialchars($est['curso']) ?></td>
                                <td><?= htmlspecialchars($est['jornada'] ?? '—') ?></td>
                                <td class="text-center">
                                    <a href="<?= BASE_URL ?>/docente/boletines?id=<?= (int)$est['id'] ?>"
                                       class="btn-ver-boletin"
                                       onclick="event.stopPropagation()">
                                        <i class="ri-file-paper-2-line"></i> Ver
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <?php elseif ($modo === 'boletin'): ?>
        <!-- ══ MODO BOLETÍN ════════════════════════════════════════════════════ -->

        <?php if ($boletin_sin_datos): ?>
            <div class="empty-state">
                <i class="ri-file-paper-2-line"></i>
                <h3>Boletín no disponible</h3>
                <p>
                    <?php if ($boletin_estudiante === null): ?>
                        No se encontró la matrícula activa del estudiante para <?= (int)$anio ?>.
                    <?php else: ?>
                        La institución aún no tiene períodos configurados para <?= (int)$anio ?>.
                    <?php endif; ?>
                </p>
            </div>
        <?php else: ?>

            <!-- CABECERA -->
            <div class="student-header">
                <div class="student-avatar">
                    <?php if (!empty($boletin_estudiante['foto'])): ?>
                        <img src="<?= BASE_URL . '/public/uploads/fotos/' . htmlspecialchars($boletin_estudiante['foto']) ?>" alt="Foto">
                    <?php else: ?>
                        <i class="ri-user-3-fill"></i>
                    <?php endif; ?>
                </div>
                <div class="student-info">
                    <h2><?= htmlspecialchars(trim($boletin_estudiante['nombres'] . ' ' . $boletin_estudiante['apellidos'])) ?></h2>
                    <div class="student-meta">
                        <span><i class="ri-graduation-cap-line"></i> Grado <?= htmlspecialchars($boletin_estudiante['grado']) ?></span>
                        <span><i class="ri-group-line"></i> <?= htmlspecialchars($boletin_estudiante['curso']) ?></span>
                        <?php if (!empty($boletin_estudiante['jornada'])): ?>
                            <span><i class="ri-sun-line"></i> Jornada <?= htmlspecialchars($boletin_estudiante['jornada']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($boletin_estudiante['documento'])): ?>
                            <span><i class="ri-id-card-line"></i> <?= htmlspecialchars($boletin_estudiante['documento']) ?></span>
                        <?php endif; ?>
                        <span><i class="ri-calendar-line"></i> Año <?= (int)$anio ?></span>
                    </div>
                </div>
                <button class="btn-print" onclick="imprimirBoletin()">
                    <i class="ri-printer-line"></i> Imprimir
                </button>
            </div>

            <!-- TABS -->
            <div class="period-tabs">
                <?php foreach ($boletin_por_periodo as $bloque): ?>
                    <?php
                        $numP     = (int)$bloque['periodo']['numero_periodo'];
                        $promP    = $bloque['promedio_periodo'] !== null ? (float)$bloque['promedio_periodo'] : null;
                        $isActive = ($numP === $periodoActivoDefault);
                    ?>
                    <button class="period-tab <?= $isActive ? 'active' : '' ?>"
                            data-periodo="<?= $numP ?>"
                            onclick="cambiarPeriodo(<?= $numP ?>)">
                        <i class="ri-calendar-2-line"></i>
                        <?= htmlspecialchars($bloque['periodo']['nombre'] ?: 'Período ' . $numP) ?>
                        <?php if ($promP !== null): ?>
                            <span class="tab-avg"><?= number_format($promP, 1) ?></span>
                        <?php endif; ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- CONTENIDO POR PERÍODO -->
            <?php foreach ($boletin_por_periodo as $bloque): ?>
                <?php
                    $numP       = (int)$bloque['periodo']['numero_periodo'];
                    $periodo    = $bloque['periodo'];
                    $materias   = $bloque['materias'];
                    $asistencia = $bloque['asistencia'];
                    $promP      = $bloque['promedio_periodo'] !== null ? (float)$bloque['promedio_periodo'] : null;
                    $isActive   = ($numP === $periodoActivoDefault);
                ?>
                <div class="periodo-content <?= $isActive ? 'active' : '' ?>" id="periodo-<?= $numP ?>">
                    <div class="periodo-stats">
                        <div class="stat-pill">
                            <i class="ri-bar-chart-2-line"></i>
                            <div>
                                <span class="stat-label">Promedio del período</span>
                                <strong class="stat-val <?= doc_bol_promClase($promP) ?>">
                                    <?= $promP !== null ? number_format($promP, 1) : 'Sin datos' ?>
                                </strong>
                            </div>
                        </div>
                        <div class="stat-pill">
                            <i class="ri-user-follow-line"></i>
                            <div>
                                <span class="stat-label">Asistencia</span>
                                <strong class="stat-val">
                                    <?php if ($asistencia['porcentaje_asistencia'] !== null): ?>
                                        <?= number_format($asistencia['porcentaje_asistencia'], 1) ?>%
                                    <?php else: ?>
                                        Sin registro
                                    <?php endif; ?>
                                </strong>
                            </div>
                        </div>
                        <div class="stat-pill">
                            <i class="ri-calendar-check-line"></i>
                            <div>
                                <span class="stat-label">Clases / Presentes</span>
                                <strong class="stat-val">
                                    <?= (int)$asistencia['presentes'] ?> / <?= (int)$asistencia['total_registros'] ?>
                                </strong>
                            </div>
                        </div>
                        <div class="stat-pill">
                            <i class="ri-calendar-event-line"></i>
                            <div>
                                <span class="stat-label">Fechas del período</span>
                                <strong class="stat-val period-dates">
                                    <?= date('d M', strtotime($periodo['fecha_inicio'])) ?>
                                    — <?= date('d M Y', strtotime($periodo['fecha_fin'])) ?>
                                </strong>
                            </div>
                        </div>
                    </div>

                    <?php if (empty($materias)): ?>
                        <div class="empty-period">
                            <i class="ri-inbox-line"></i>
                            <p>No hay actividades en este período.</p>
                        </div>
                    <?php else: ?>
                        <div class="materias-table-wrap">
                            <table class="materias-table">
                                <thead>
                                    <tr>
                                        <th>Materia</th>
                                        <th>Docente</th>
                                        <th class="text-center">Actividades</th>
                                        <th class="text-center">Promedio</th>
                                        <th class="text-center">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($materias as $m): ?>
                                        <?php
                                            $prom        = $m['promedio'] !== null ? (float)$m['promedio'] : null;
                                            $estadoNota  = $m['estado_nota']  ?? 'sin-nota';
                                            $estadoLabel = $m['estado_label'] ?? 'Sin Nota';
                                        ?>
                                        <tr>
                                            <td class="td-materia"><?= htmlspecialchars($m['materia']) ?></td>
                                            <td class="td-docente"><?= htmlspecialchars($m['docente_nombre'] ?: '—') ?></td>
                                            <td class="text-center">
                                                <div class="eval-counts">
                                                    <span class="ec-ok"      title="Calificadas"><i class="ri-check-line"></i><?= (int)$m['actividades_calificadas'] ?></span>
                                                    <span class="ec-fail"    title="Vencidas"><i class="ri-time-line"></i><?= (int)$m['actividades_vencidas'] ?></span>
                                                    <span class="ec-pending" title="Pendientes"><i class="ri-hourglass-line"></i><?= (int)$m['actividades_pendientes'] ?></span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($prom !== null): ?>
                                                    <span class="nota-badge <?= doc_bol_notaBadge($estadoNota) ?>"><?= number_format($prom, 1) ?></span>
                                                <?php else: ?>
                                                    <span class="nota-badge nota-sin">S/N</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="estado-badge <?= htmlspecialchars($estadoNota) ?>">
                                                    <?= htmlspecialchars($estadoLabel) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

        <?php endif; /* boletin_sin_datos */ ?>
        <?php endif; /* modo */ ?>

    </main>
</div>


<?php if ($modo === 'boletin' && !$boletin_sin_datos): ?>
<!-- SECCIÓN DE IMPRESIÓN -->
<div class="print-only">
    <div class="print-header">
        <img src="<?= BASE_URL ?>/public/assets/extras/img/LOGO-NEGATIVO 1 (1).png"
             class="print-logo" alt="Logo"
             style="filter:invert(1) brightness(.3) sepia(1) hue-rotate(190deg);">
        <div class="print-inst-info">
            <div class="print-inst-name">SIADEMY — Sistema Académico</div>
            <div class="print-doc-title">Boletín de Calificaciones</div>
        </div>
        <div class="print-year-badge">Año <?= (int)$anio ?></div>
    </div>

    <table class="print-student-table">
        <tr>
            <th>Estudiante</th>
            <td><?= htmlspecialchars(trim($boletin_estudiante['nombres'] . ' ' . $boletin_estudiante['apellidos'])) ?></td>
            <th>Documento</th>
            <td><?= htmlspecialchars($boletin_estudiante['documento'] ?? '—') ?></td>
        </tr>
        <tr>
            <th>Grado</th>
            <td><?= htmlspecialchars($boletin_estudiante['grado']) ?></td>
            <th>Curso</th>
            <td><?= htmlspecialchars($boletin_estudiante['curso']) ?></td>
        </tr>
        <?php if (!empty($boletin_estudiante['jornada'])): ?>
        <tr>
            <th>Jornada</th>
            <td><?= htmlspecialchars($boletin_estudiante['jornada']) ?></td>
            <th>Año lectivo</th>
            <td><?= (int)$anio ?></td>
        </tr>
        <?php endif; ?>
    </table>

    <?php foreach ($boletin_por_periodo as $bloque): ?>
        <?php
            $numP       = (int)$bloque['periodo']['numero_periodo'];
            $periodo    = $bloque['periodo'];
            $materias   = $bloque['materias'];
            $asistencia = $bloque['asistencia'];
            $promP      = $bloque['promedio_periodo'] !== null ? (float)$bloque['promedio_periodo'] : null;
            $pc = match(true) {
                $promP !== null && $promP >= 4.5 => 'superior',
                $promP !== null && $promP >= 4.0 => 'alto',
                $promP !== null && $promP >  3.0 => 'basico',
                $promP !== null                  => 'bajo',
                default                          => 'sin-nota',
            };
        ?>
        <div class="print-period-block" data-periodo="<?= $numP ?>">
            <div class="print-period-title">
                <span><?= htmlspecialchars($periodo['nombre'] ?: 'Período ' . $numP) ?></span>
                <span>
                    <?= date('d/m/Y', strtotime($periodo['fecha_inicio'])) ?>
                    al <?= date('d/m/Y', strtotime($periodo['fecha_fin'])) ?>
                    &nbsp;|&nbsp; Promedio: <strong><?= $promP !== null ? number_format($promP, 1) : 'N/A' ?></strong>
                </span>
            </div>
            <?php if (empty($materias)): ?>
                <p style="font-size:11px;color:#6b7280;margin-bottom:10px;">Sin actividades en este período.</p>
            <?php else: ?>
                <table class="print-grades-table">
                    <thead>
                        <tr>
                            <th style="width:35%">Materia</th>
                            <th style="width:25%">Docente</th>
                            <th class="tc" style="width:10%">Calif.</th>
                            <th class="tc" style="width:10%">Venc.</th>
                            <th class="tc" style="width:10%">Promedio</th>
                            <th class="tc" style="width:10%">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($materias as $m): ?>
                            <?php
                                $prom = $m['promedio'] !== null ? (float)$m['promedio'] : null;
                                $estN = $m['estado_nota'] ?? 'sin-nota';
                                $estL = $m['estado_label'] ?? 'Sin Nota';
                                $mpc  = match($estN) {
                                    'superior' => 'superior', 'alto'   => 'alto',
                                    'basico'   => 'basico',   'bajo'   => 'bajo',
                                    default    => 'sin-nota',
                                };
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($m['materia']) ?></td>
                                <td><?= htmlspecialchars($m['docente_nombre'] ?: '—') ?></td>
                                <td class="tc"><?= (int)$m['actividades_calificadas'] ?></td>
                                <td class="tc"><?= (int)$m['actividades_vencidas'] ?></td>
                                <td class="tc">
                                    <span class="print-prom-badge <?= $mpc ?>">
                                        <?= $prom !== null ? number_format($prom, 1) : 'S/N' ?>
                                    </span>
                                </td>
                                <td class="tc"><?= htmlspecialchars($estL) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="print-attendance">
                    Asistencia: <?= (int)$asistencia['presentes'] ?> presentes •
                    <?= (int)$asistencia['ausentes'] ?> ausentes •
                    <?= (int)$asistencia['justificados'] ?> justificados •
                    <?= (int)$asistencia['tardes'] ?> tardes •
                    Total: <?= (int)$asistencia['total_registros'] ?>
                    <?php if ($asistencia['porcentaje_asistencia'] !== null): ?>
                        | <strong><?= number_format($asistencia['porcentaje_asistencia'], 1) ?>%</strong>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <div class="print-footer">
        <div class="print-signature"><div class="print-sig-line">&nbsp;</div><div class="print-sig-label">Director(a) de Grupo</div></div>
        <div class="print-signature"><div class="print-sig-line">&nbsp;</div><div class="print-sig-label">Acudiente / Padre de Familia</div></div>
        <div class="print-signature"><div class="print-sig-line">&nbsp;</div><div class="print-sig-label">Coordinador(a) Académico</div></div>
    </div>
</div>
<?php endif; ?>


<script>
(function () {
    const sidebar   = document.getElementById('leftSidebar');
    const appGrid   = document.getElementById('appGrid');
    const toggleBtn = document.getElementById('toggleLeft');

    const overlay = document.querySelector('.sidebar-overlay') || document.createElement('div');
    if (!overlay.parentElement) {
        overlay.className = 'sidebar-overlay';
        document.body.appendChild(overlay);
    }

    let visible = localStorage.getItem('leftSidebarVisible') !== 'false';
    function isMobile() { return window.innerWidth <= 768; }

    function openMobile() {
        if (!sidebar) return;
        sidebar.classList.add('mobile-open');
        sidebar.classList.remove('hidden');
        overlay.classList.add('active');
    }
    function closeMobile() {
        if (!sidebar) return;
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
    }
    function applyDesktop() {
        if (appGrid) appGrid.classList.toggle('hide-left', !visible);
        if (sidebar) sidebar.classList.toggle('hidden', !visible);
    }

    overlay.onclick = closeMobile;
    window.addEventListener('resize', function() { if (!isMobile()) closeMobile(); });

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            if (isMobile()) {
                sidebar.classList.contains('mobile-open') ? closeMobile() : openMobile();
            } else {
                visible = !visible;
                localStorage.setItem('leftSidebarVisible', visible);
                applyDesktop();
            }
        });
    }
    if (!isMobile()) applyDesktop();
})();

// Profile dropdown
(function () {
    function init() {
        const btn      = document.getElementById('userMenuBtn');
        const dropdown = document.getElementById('userDropdown');
        if (!btn || !dropdown || btn.dataset.dropdownInit === '1') return;
        btn.dataset.dropdownInit = '1';
        const overlay = document.createElement('div');
        overlay.style.cssText = 'position:fixed;inset:0;z-index:999;display:none;';
        document.body.appendChild(overlay);
        btn.addEventListener('click', e => {
            e.stopPropagation();
            const open = dropdown.classList.toggle('show');
            overlay.style.display = open ? 'block' : 'none';
        });
        overlay.addEventListener('click', () => {
            dropdown.classList.remove('show');
            overlay.style.display = 'none';
        });
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                dropdown.classList.remove('show');
                overlay.style.display = 'none';
            }
        });
    }
    document.readyState === 'loading'
        ? document.addEventListener('DOMContentLoaded', init) : init();
})();

<?php if ($modo === 'boletin'): ?>
function cambiarPeriodo(n) {
    document.querySelectorAll('.period-tab').forEach(b =>
        b.classList.toggle('active', parseInt(b.dataset.periodo) === n));
    document.querySelectorAll('.periodo-content').forEach(d =>
        d.classList.toggle('active', d.id === 'periodo-' + n));
}
function imprimirBoletin() {
    const activeTab    = document.querySelector('.period-tab.active');
    const activePeriodo = activeTab ? parseInt(activeTab.dataset.periodo) : null;
    document.querySelectorAll('.print-period-block').forEach(b => {
        b.style.display = (!activePeriodo || parseInt(b.dataset.periodo) === activePeriodo) ? '' : 'none';
    });
    window.print();
    document.querySelectorAll('.print-period-block').forEach(b => { b.style.display = ''; });
}
<?php endif; ?>
</script>

</body>
</html>
