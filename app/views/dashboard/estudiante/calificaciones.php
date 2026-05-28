<?php
    require_once BASE_PATH . '/app/controllers/perfil.php';
    $id      = $_SESSION['user']['id'] ?? 0;
    $usuario = mostrarPerfil($id);

    $resumen = isset($resumen_calificaciones) && is_array($resumen_calificaciones)
        ? $resumen_calificaciones
        : ['promedio_general' => 0, 'total_materias' => 0, 'total_evaluaciones' => 0, 'pendientes' => 0];

    $calificaciones_materias = isset($calificaciones_materias) && is_array($calificaciones_materias)
        ? $calificaciones_materias : [];

    $periodo_actual = isset($periodo_actual) ? (int)$periodo_actual : 1;

    function notaBadgeClass(float $n): string {
        if ($n >= 4.5) return 'nb-excelente';
        if ($n >= 4.0) return 'nb-bueno';
        if ($n >= 3.0) return 'nb-regular';
        return 'nb-bajo';
    }
    function notaGradient(float $n): string {
        if ($n >= 4.5) return 'linear-gradient(135deg,#059669,#10b981)';
        if ($n >= 4.0) return 'linear-gradient(135deg,#1d4ed8,#3b82f6)';
        if ($n >= 3.0) return 'linear-gradient(135deg,#b45309,#f59e0b)';
        return 'linear-gradient(135deg,#b91c1c,#ef4444)';
    }
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • Calificaciones</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-calificaciones.css">
</head>
<body>
<div class="app hide-right" id="appGrid">
    <?php include_once __DIR__ . '/../../layouts/sidebar_estudiante.php' ?>

    <main class="main">
        <!-- TOPBAR -->
        <div class="topbar">
            <div class="topbar-left">
                <button class="toggle-btn" id="toggleLeft"><i class="ri-menu-2-line"></i></button>
                <div class="title">Calificaciones</div>
            </div>
            <div class="search">
                <i class="ri-search-2-line"></i>
                <input type="text" id="searchInput" placeholder="Buscar materia o evaluación...">
            </div>
            <?php include_once BASE_PATH . '/app/views/layouts/boton_perfil_solo.php'; ?>
        </div>

        <!-- STATS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue"><i class="ri-award-line"></i></div>
                <div class="stat-content">
                    <h3><?= number_format($resumen['promedio_general'] ?? 0, 1) ?></h3>
                    <p>Promedio General</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="ri-book-open-line"></i></div>
                <div class="stat-content">
                    <h3><?= (int)($resumen['total_materias'] ?? 0) ?></h3>
                    <p>Materias Cursando</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple"><i class="ri-file-list-3-line"></i></div>
                <div class="stat-content">
                    <h3><?= (int)($resumen['total_evaluaciones'] ?? 0) ?></h3>
                    <p>Evaluaciones</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange"><i class="ri-time-line"></i></div>
                <div class="stat-content">
                    <h3><?= (int)($resumen['pendientes'] ?? 0) ?></h3>
                    <p>Pendientes</p>
                </div>
            </div>
        </div>

        <!-- TABLA DE CALIFICACIONES -->
        <?php if (empty($calificaciones_materias)): ?>
        <div class="empty-state">
            <i class="ri-survey-line"></i>
            <h3>Sin calificaciones registradas</h3>
            <p>Aún no hay evaluaciones para mostrar en este período.</p>
        </div>
        <?php else: ?>

        <div class="grades-panel">
            <!-- CABECERA DE LA TABLA -->
            <div class="grades-header">
                <div class="gh-materia">Materia</div>
                <div class="gh-periodos">
                    <span>Período 1</span>
                    <span>Período 2</span>
                    <span>Período 3</span>
                    <span>Período 4</span>
                </div>
                <div class="gh-promedio">Promedio</div>
                <div class="gh-accion"></div>
            </div>

            <!-- FILAS -->
            <div class="grades-list" id="gradesList">
            <?php foreach ($calificaciones_materias as $idAC => $materia):
                // Calcular promedio general de los períodos con nota
                $notas = array_filter(
                    array_map(fn($p) => $p['notaFinal'], $materia['periodos']),
                    fn($n) => $n !== null
                );
                $promedioGeneral = count($notas) > 0
                    ? round(array_sum($notas) / count($notas), 1)
                    : null;
            ?>
            <div class="grade-row" data-materia-id="<?= (int)$idAC ?>">

                <!-- FILA PRINCIPAL -->
                <div class="gr-main">
                    <div class="gr-materia">
                        <div class="gr-icon" style="background:<?= htmlspecialchars($materia['color_icono']) ?>">
                            <i class="<?= htmlspecialchars($materia['icono']) ?>"></i>
                        </div>
                        <div class="gr-info">
                            <span class="gr-nombre"><?= htmlspecialchars($materia['nombre']) ?></span>
                            <span class="gr-prof"><i class="ri-user-line"></i><?= htmlspecialchars($materia['profesor']) ?></span>
                        </div>
                    </div>

                    <div class="gr-periodos">
                        <?php for ($p = 1; $p <= 4; $p++):
                            $nota = $materia['periodos'][$p]['notaFinal'] ?? null;
                        ?>
                        <div class="gr-periodo <?= $p === $periodo_actual ? 'current' : '' ?>">
                            <?php if ($nota !== null): ?>
                                <span class="nota-badge <?= notaBadgeClass((float)$nota) ?>">
                                    <?= number_format((float)$nota, 1) ?>
                                </span>
                            <?php else: ?>
                                <span class="nota-empty">—</span>
                            <?php endif; ?>
                        </div>
                        <?php endfor; ?>
                    </div>

                    <div class="gr-promedio">
                        <?php if ($promedioGeneral !== null): ?>
                            <div class="promedio-circle" style="background:<?= notaGradient((float)$promedioGeneral) ?>">
                                <?= number_format((float)$promedioGeneral, 1) ?>
                            </div>
                        <?php else: ?>
                            <span class="nota-empty">—</span>
                        <?php endif; ?>
                    </div>

                    <div class="gr-accion">
                        <button class="btn-detalle" aria-label="Ver detalle">
                            <i class="ri-arrow-down-s-line"></i>
                        </button>
                    </div>
                </div>

                <!-- DETALLE EXPANDIBLE -->
                <div class="gr-detalle">
                    <div class="detalle-inner">
                        <!-- Tabs de período -->
                        <div class="detalle-tabs">
                            <?php for ($p = 1; $p <= 4; $p++):
                                $nota = $materia['periodos'][$p]['notaFinal'] ?? null;
                            ?>
                            <button class="dtab <?= $p === $periodo_actual ? 'active' : '' ?>" data-periodo="<?= $p ?>">
                                <span class="dtab-label">Período <?= $p ?></span>
                                <?php if ($nota !== null): ?>
                                    <span class="dtab-nota <?= notaBadgeClass((float)$nota) ?>">
                                        <?= number_format((float)$nota, 1) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="dtab-nota empty">—</span>
                                <?php endif; ?>
                            </button>
                            <?php endfor; ?>
                        </div>
                        <!-- Evaluaciones (JS las rellena) -->
                        <div class="evaluaciones-container"></div>
                    </div>
                </div>

            </div>
            <?php endforeach; ?>
            </div>
        </div>

        <?php endif; ?>
    </main>
</div>

<script>
window.calificacionesData = <?= json_encode([
    'materias'      => $calificaciones_materias,
    'periodoActual' => (int)$periodo_actual,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?>;
</script>
<script src="<?= BASE_URL ?>/public/assets/dashboard/js/estudiante/calificaciones.js"></script>
</body>
</html>
