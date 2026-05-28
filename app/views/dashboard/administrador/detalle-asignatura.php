<?php
    require_once BASE_PATH . '/app/helpers/session_administrador.php';
    require_once BASE_PATH . '/app/controllers/administrador/view_data.php';

    $idAsignatura = (int)($_GET['id'] ?? 0);
    $data = obtenerDataVistaAdminDetalleAsignatura($idAsignatura);

    if ($data === null) {
        header('Location: ' . BASE_URL . '/administrador-panel-asignaturas');
        exit;
    }

    extract($data, EXTR_SKIP);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • <?= htmlspecialchars($asignatura['nombre']) ?></title>
    <?php include_once __DIR__ . '/../../layouts/header_coordinador.php' ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-admin.css?v=<?= $adminCssVersion ?>">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/administrador/detalle-curso.css">
    <style>
        /* Paleta oscura del sistema */
        .det-grid              { display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-top:20px; padding:0 28px 32px; }
        .det-card              { background:#11193a; border:1px solid rgba(255,255,255,.08); border-radius:12px; padding:22px; }
        .det-card h4           { margin:0 0 16px; font-size:13px; font-weight:600; color:#8b91a3; text-transform:uppercase; letter-spacing:.07em; display:flex; align-items:center; gap:6px; }
        /* Tabla */
        .det-table             { width:100%; border-collapse:collapse; font-size:13.5px; }
        .det-table th          { text-align:left; padding:8px 12px; font-size:11.5px; color:#8b91a3; border-bottom:1px solid rgba(255,255,255,.07); font-weight:600; letter-spacing:.04em; text-transform:uppercase; }
        .det-table td          { padding:10px 12px; border-bottom:1px solid rgba(255,255,255,.04); color:#cbd5e1; }
        .det-table tr:last-child td { border-bottom:none; }
        .det-table .av         { width:32px; height:32px; border-radius:50%; background:linear-gradient(135deg,#667eea,#764ba2); display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; color:#fff; flex-shrink:0; }
        .det-table .av img     { width:32px; height:32px; border-radius:50%; object-fit:cover; }
        .det-table .flex-cell  { display:flex; align-items:center; gap:10px; }
        /* Badges */
        .badge-estado          { display:inline-block; padding:2px 10px; border-radius:20px; font-size:11px; font-weight:600; }
        .badge-estado.Activo   { background:rgba(16,185,129,.18); color:#10b981; }
        .badge-estado.Inactivo { background:rgba(239,68,68,.15);  color:#ef4444; }
        /* Barras de distribución */
        .dist-bar              { display:flex; align-items:center; gap:10px; margin-bottom:12px; font-size:13px; }
        .dist-bar .label       { width:70px; color:#8b91a3; font-weight:500; }
        .dist-bar .track       { flex:1; background:rgba(255,255,255,.07); border-radius:6px; height:9px; overflow:hidden; }
        .dist-bar .fill        { height:100%; border-radius:6px; transition:width .5s ease; min-width:2px; }
        .dist-bar .count       { width:26px; text-align:right; color:#cbd5e1; font-size:12px; font-weight:600; }
        .fill.superior         { background:#10b981; }
        .fill.alto             { background:#3b82f6; }
        .fill.basico           { background:#f59e0b; }
        .fill.bajo             { background:#ef4444; }
        /* Promedio grande */
        .promedio-grande       { font-size:56px; font-weight:700; line-height:1; font-family:'Montserrat',sans-serif; }
        .promedio-grande.sin   { color:#8b91a3; font-size:32px; }
        /* Pills */
        .stat-pill-det         { display:inline-flex; align-items:center; gap:6px; border-radius:20px; padding:4px 14px; font-size:12px; font-weight:600; margin-top:10px; }
        /* Estado vacío */
        .empty-state           { text-align:center; padding:32px 16px; color:#8b91a3; }
        .empty-state i         { font-size:34px; display:block; margin-bottom:10px; opacity:.6; }
        .empty-state p         { margin:0; font-size:13px; }
        @media(max-width:900px) { .det-grid { grid-template-columns:1fr; padding:0 20px 28px; } }
    </style>
</head>
<body>
<div class="app hide-right" id="appGrid">
    <?php include_once __DIR__ . '/../../layouts/sidebar_coordinador.php' ?>

    <main class="main">
        <!-- TOPBAR -->
        <div class="topbar">
            <div class="topbar-left">
                <button class="toggle-btn" id="toggleLeft"><i class="ri-menu-2-line"></i></button>
                <button class="btn-back" onclick="history.back()"><i class="ri-arrow-left-line"></i> Volver</button>
                <div class="title">Detalle de Asignatura</div>
            </div>
            <?php include_once BASE_PATH . '/app/views/layouts/boton_perfil_solo.php' ?>
        </div>

        <!-- HEADER DE LA ASIGNATURA -->
        <div class="student-profile-header">
            <div class="profile-main">
                <div class="profile-avatar" style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);">
                    <i class="ri-book-open-line" style="font-size:48px"></i>
                </div>
                <div class="profile-info">
                    <h2><?= htmlspecialchars($asignatura['nombre']) ?></h2>
                    <p class="profile-subtitle"><?= htmlspecialchars($asignatura['descripcion'] ?: 'Sin descripción') ?></p>
                    <div class="profile-badges">
                        <span class="badge-item <?= $asignatura['estado'] === 'Activo' ? 'badge-active' : '' ?>">
                            <i class="ri-checkbox-circle-fill"></i> <?= htmlspecialchars($asignatura['estado']) ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="profile-actions">
                <a href="<?= BASE_URL ?>/administrador/editar-asignatura?id=<?= (int)$asignatura['id'] ?>" class="btn-profile-action">
                    <i class="ri-edit-line"></i> Editar
                </a>
                <a href="<?= BASE_URL ?>/administrador-panel-asignaturas" class="btn-profile-action btn-secondary-action">
                    <i class="ri-arrow-left-line"></i> Asignaturas
                </a>
            </div>
        </div>

        <!-- STATS RÁPIDAS -->
        <div class="quick-stats">
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#667eea,#764ba2)"><i class="ri-user-star-line"></i></div>
                <div class="stat-content">
                    <span class="stat-label">Docentes</span>
                    <span class="stat-value"><?= $totalDocentes ?></span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#f093fb,#f5576c)"><i class="ri-book-3-line"></i></div>
                <div class="stat-content">
                    <span class="stat-label">Cursos</span>
                    <span class="stat-value"><?= $totalCursos ?></span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#4facfe,#00f2fe)"><i class="ri-group-line"></i></div>
                <div class="stat-content">
                    <span class="stat-label">Estudiantes</span>
                    <span class="stat-value"><?= $totalEstudiantes ?></span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#43e97b,#38f9d7)"><i class="ri-line-chart-line"></i></div>
                <div class="stat-content">
                    <span class="stat-label">Promedio</span>
                    <span class="stat-value"><?= $stats['promedio'] !== null ? number_format((float)$stats['promedio'], 1, '.', '') : '—' ?></span>
                </div>
            </div>
        </div>

        <!-- GRILLA PRINCIPAL -->
        <div class="det-grid">

            <!-- DOCENTES ASIGNADOS -->
            <div class="det-card">
                <h4><i class="ri-user-star-line"></i> Docentes asignados</h4>
                <?php if (!empty($docentes)): ?>
                <table class="det-table">
                    <thead>
                        <tr>
                            <th>Docente</th>
                            <th>Curso</th>
                            <th>Jornada</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($docentes as $d): ?>
                        <tr>
                            <td>
                                <div class="flex-cell">
                                    <?php if (!empty($d['foto'])): ?>
                                        <div class="av"><img src="<?= BASE_URL ?>/public/assets/extras/uploads/<?= htmlspecialchars($d['foto']) ?>" alt=""></div>
                                    <?php else: ?>
                                        <div class="av"><?= strtoupper(substr($d['nombres'], 0, 1)) ?></div>
                                    <?php endif; ?>
                                    <?= htmlspecialchars($d['nombres'] . ' ' . $d['apellidos']) ?>
                                </div>
                            </td>
                            <td><?= (int)$d['grado'] ?>° <?= htmlspecialchars($d['curso']) ?></td>
                            <td><?= htmlspecialchars($d['jornada']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <div class="empty-state"><i class="ri-user-unfollow-line"></i> No hay docentes asignados</div>
                <?php endif; ?>
            </div>

            <!-- CURSOS DONDE SE IMPARTE -->
            <div class="det-card">
                <h4><i class="ri-book-3-line"></i> Cursos donde se imparte</h4>
                <?php if (!empty($cursos)): ?>
                <table class="det-table">
                    <thead>
                        <tr>
                            <th>Curso</th>
                            <th>Jornada</th>
                            <th>Estudiantes</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($cursos as $c): ?>
                        <tr>
                            <td><?= (int)$c['grado'] ?>° <?= htmlspecialchars($c['curso']) ?></td>
                            <td><?= htmlspecialchars($c['jornada']) ?></td>
                            <td><?= (int)$c['total_estudiantes'] ?></td>
                            <td><span class="badge-estado <?= htmlspecialchars($c['estado']) ?>"><?= htmlspecialchars($c['estado']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <div class="empty-state"><i class="ri-book-line"></i> Sin cursos asignados</div>
                <?php endif; ?>
            </div>

            <!-- DISTRIBUCIÓN DE CALIFICACIONES -->
            <div class="det-card">
                <h4><i class="ri-pie-chart-line"></i> Distribución de calificaciones</h4>
                <?php
                    $total = (int)$stats['total_calificaciones'];
                    $niveles = [
                        'superior' => ['label' => 'Superior', 'count' => (int)$stats['nivel_superior']],
                        'alto'     => ['label' => 'Alto',     'count' => (int)$stats['nivel_alto']],
                        'basico'   => ['label' => 'Básico',   'count' => (int)$stats['nivel_basico']],
                        'bajo'     => ['label' => 'Bajo',     'count' => (int)$stats['nivel_bajo']],
                    ];
                ?>
                <?php if ($total > 0): ?>
                    <?php foreach ($niveles as $key => $n): ?>
                    <div class="dist-bar">
                        <span class="label"><?= $n['label'] ?></span>
                        <div class="track">
                            <div class="fill <?= $key ?>" style="width:<?= round($n['count'] / $total * 100) ?>%"></div>
                        </div>
                        <span class="count"><?= $n['count'] ?></span>
                    </div>
                    <?php endforeach; ?>
                    <div class="stat-pill-det"><i class="ri-file-list-3-line"></i> <?= $total ?> calificaciones registradas</div>
                <?php else: ?>
                    <div class="empty-state"><i class="ri-file-unknow-line"></i> Sin calificaciones registradas</div>
                <?php endif; ?>
            </div>

            <!-- PROMEDIO GENERAL -->
            <div class="det-card" style="display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;gap:14px;background:#0e1632;">
                <h4 style="margin:0;justify-content:center"><i class="ri-line-chart-line"></i> Promedio general</h4>
                <?php if ($stats['promedio'] !== null): ?>
                    <?php
                        $prom  = (float)$stats['promedio'];
                        $color = $prom >= 4.5 ? '#10b981' : ($prom >= 4.0 ? '#3b82f6' : ($prom > 3.0 ? '#f59e0b' : '#ef4444'));
                        $nivel = $prom >= 4.5 ? 'Superior' : ($prom >= 4.0 ? 'Alto' : ($prom > 3.0 ? 'Básico' : 'Bajo'));
                    ?>
                    <div class="promedio-grande" style="color:<?= $color ?>"><?= number_format($prom, 1) ?></div>
                    <span class="stat-pill-det" style="background:<?= $color ?>22;color:<?= $color ?>"><?= $nivel ?></span>
                    <p style="color:#8b91a3;font-size:13px;margin:0">Sobre <?= (int)$stats['total_calificaciones'] ?> notas registradas</p>
                <?php else: ?>
                    <div class="promedio-grande sin">—</div>
                    <p style="color:#8b91a3;font-size:13px;margin:0">Sin notas registradas aún</p>
                <?php endif; ?>
            </div>

        </div><!-- /det-grid -->
    </main>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-admin.js?v=<?= $mainAdminJsVersion ?>"></script>
</body>
</html>
