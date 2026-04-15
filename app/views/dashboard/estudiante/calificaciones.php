<?php
    require_once BASE_PATH . '/app/controllers/perfil.php';
    $id = $_SESSION['user']['id'] ?? 0;
    $usuario = mostrarPerfil($id);
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • Calificaciones</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-calificaciones.css">
</head>

<body>
    <div class="app hide-right" id="appGrid">
        <!-- LEFT SIDEBAR -->
        <?php
        include_once __DIR__ . '/../../layouts/sidebar_estudiante.php'
        ?>

        <!-- MAIN CONTENT -->
        <main class="main">
            <div class="topbar">
                <div class="topbar-left">
                    <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
                        <i class="ri-menu-2-line"></i>
                    </button>
                    <div class="title">Calificaciones</div>
                </div>

                <div class="search">
                    <i class="ri-search-2-line"></i>
                    <input type="text" id="searchInput" placeholder="Buscar materia o evaluación...">
                </div>

                 <?php
          include_once BASE_PATH . '/app/views/layouts/boton_perfil_solo.php';
        ?>
            </div>

            <!-- ACCIONES -->
            <div class="actiones">
                <div class="actions-bar">
                    <button class="btn-progreso" id="progreso">
                        <i class="ri-line-chart-line"></i>
                        <span>Progreso</span>
                    </button>
                </div>

                <div class="actions-bar">
                    <button class="btn-download" id="downloadBoletin">
                        <i class="ri-survey-line"></i>
                        <span>Generar Boletín</span>
                    </button>
                </div>
            </div>

            <!-- STATS CARDS -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="ri-award-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= number_format($resumen_calificaciones['promedio_general'] ?? 0, 1) ?></h3>
                        <p>Promedio General</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="ri-book-open-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= (int)($resumen_calificaciones['total_materias'] ?? 0) ?></h3>
                        <p>Materias Cursando</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon purple">
                        <i class="ri-file-list-3-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= (int)($resumen_calificaciones['total_evaluaciones'] ?? 0) ?></h3>
                        <p>Evaluaciones</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="ri-time-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= (int)($resumen_calificaciones['pendientes'] ?? 0) ?></h3>
                        <p>Pendientes</p>
                    </div>
                </div>
            </div>

            <!-- CALIFICACIONES GRID -->
            <div class="calificaciones-grid" id="calificacionesGrid">
                <?php if (empty($calificaciones_materias)): ?>
                    <div style="text-align:center; padding: 48px 24px; color:#9aa5bd; background: rgba(17, 29, 80, 0.45); border: 1px solid rgba(255,255,255,0.08); border-radius: 18px;">
                        <i class="ri-survey-line" style="font-size: 42px;"></i>
                        <h3 style="margin-top: 12px; color: #fff;">Sin calificaciones registradas</h3>
                        <p style="margin: 6px 0 0;">Aun no hay evaluaciones para mostrar en este periodo.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($calificaciones_materias as $materia): ?>
                        <div class="calificacion-card" data-materia-id="<?= (int)$materia['id'] ?>">
                            <div class="card-header">
                                <div class="materia-info">
                                    <div class="materia-icon" style="background: <?= htmlspecialchars($materia['color_icono']) ?>;">
                                        <i class="<?= htmlspecialchars($materia['icono']) ?>"></i>
                                    </div>
                                    <div class="materia-details">
                                        <h3><?= htmlspecialchars($materia['nombre']) ?></h3>
                                        <p><?= htmlspecialchars($materia['profesor']) ?></p>
                                    </div>
                                </div>
                                <div class="expand-icon">
                                    <i class="ri-arrow-down-s-line"></i>
                                </div>
                            </div>
                            <div class="periodos-section">
                                <div class="periodo-buttons">
                                    <?php for ($periodo = 1; $periodo <= 4; $periodo++): ?>
                                        <button class="periodo-btn <?= ((int)$periodo_actual === $periodo) ? 'current' : '' ?>" data-periodo="<?= $periodo ?>">
                                            <i class="ri-calendar-line"></i>
                                            <span>Periodo <?= $periodo ?></span>
                                        </button>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div class="evaluaciones-section"></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>
        </main>

        <!-- RIGHT SIDEBAR -->
        
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.calificacionesData = <?= json_encode([
            'materias' => $calificaciones_materias,
            'periodoActual' => (int)$periodo_actual,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    </script>
    <script src="<?= BASE_URL ?>/public/assets/dashboard/js/estudiante/calificaciones.js"></script>
</body>

</html>