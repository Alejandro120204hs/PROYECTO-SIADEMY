<?php
    require_once BASE_PATH . '/app/controllers/perfil.php';
    $id      = $_SESSION['user']['id'] ?? 0;
    $usuario = mostrarPerfil($id);

    // Paleta de avatares para cuando no hay foto
    $avatarGradients = [
        'linear-gradient(135deg,#4f46e5,#6366f1)',
        'linear-gradient(135deg,#10b981,#059669)',
        'linear-gradient(135deg,#f59e0b,#d97706)',
        'linear-gradient(135deg,#ef4444,#dc2626)',
        'linear-gradient(135deg,#8b5cf6,#7c3aed)',
        'linear-gradient(135deg,#06b6d4,#0891b2)',
        'linear-gradient(135deg,#ec4899,#db2777)',
        'linear-gradient(135deg,#14b8a6,#0d9488)',
    ];

    // Garantiza que $profesores esté definido aunque el controlador falle
    $profesores      = $profesores      ?? [];
    $totalProfesores = $totalProfesores ?? count($profesores);
    $totalMaterias   = $totalMaterias   ?? 0;
    $promedioGeneral = $promedioGeneral ?? null;

    /**
     * Genera las iniciales del nombre (máx 2 letras).
     */
    function iniciales(string $nombres, string $apellidos): string {
        $n = mb_strtoupper(mb_substr(trim($nombres),   0, 1));
        $a = mb_strtoupper(mb_substr(trim($apellidos), 0, 1));
        return $n . $a;
    }

    /**
     * Clase CSS según la nota (escala 0-5).
     */
    function notaClass(?float $nota): string {
        if ($nota === null) return 'sin-nota';
        if ($nota >= 4.0)  return 'excelente';
        if ($nota >= 3.0)  return 'bueno';
        if ($nota >= 2.0)  return 'riesgo';
        return 'critico';
    }
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • Mis Profesores</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
    <?php include_once __DIR__ . '/../../layouts/header_coordinador.php' ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-profesores.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/modo-claro-estudiante.css">
</head>
<body>
<div class="app hide-right" id="appGrid">

    <!-- LEFT SIDEBAR -->
    <?php include_once __DIR__ . '/../../layouts/sidebar_estudiante.php'; ?>

    <!-- MAIN CONTENT -->
    <main class="main">

        <!-- TOPBAR -->
        <div class="topbar">
            <div class="topbar-left">
                <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú">
                    <i class="ri-menu-2-line"></i>
                </button>
                <div class="title">Mis Profesores</div>
            </div>
            <div class="search">
                <i class="ri-search-2-line"></i>
                <input type="text" id="searchInput" placeholder="Buscar profesor o materia...">
            </div>
            <?php include_once BASE_PATH . '/app/views/layouts/boton_perfil_solo.php'; ?>
        </div>

        <!-- STATS CARDS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue"><i class="ri-user-3-line"></i></div>
                <div class="stat-content">
                    <h3><?= $totalProfesores ?></h3>
                    <p>Total Profesores</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="ri-book-2-line"></i></div>
                <div class="stat-content">
                    <h3><?= $totalMaterias ?></h3>
                    <p>Materias Activas</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple"><i class="ri-award-line"></i></div>
                <div class="stat-content">
                    <h3><?= $promedioGeneral !== null ? number_format($promedioGeneral, 1) : '—' ?></h3>
                    <p>Tu Promedio</p>
                </div>
            </div>
        </div>

        <!-- SECTION HEADER -->
        <div class="section-header">
            <span class="section-title">
                Docentes
                <?php if ($totalProfesores > 0): ?>
                    <span class="count-badge"><?= $totalProfesores ?></span>
                <?php endif; ?>
            </span>
        </div>

        <!-- PROFESORES GRID -->
        <div class="profesores-grid" id="profesoresGrid">

            <?php if (empty($profesores)): ?>
                <div class="empty-state">
                    <i class="ri-user-unfollow-line"></i>
                    <h3>Sin profesores registrados</h3>
                    <p>Aún no hay docentes asignados a tus materias para este año.</p>
                </div>

            <?php else: ?>
                <?php foreach ($profesores as $i => $prof):
                    $nombres   = htmlspecialchars($prof['nombres']   ?? '');
                    $apellidos = htmlspecialchars($prof['apellidos'] ?? '');
                    $correo    = htmlspecialchars($prof['correo']    ?? '');
                    $materia   = htmlspecialchars($prof['nombre_asignatura'] ?? '');
                    $foto      = $prof['foto'] ?? '';
                    $promedio  = is_numeric($prof['promedio_estudiante']) ? (float)$prof['promedio_estudiante'] : null;
                    $asistencia= is_numeric($prof['porcentaje_asistencia']) ? (int)$prof['porcentaje_asistencia'] : null;
                    $totalClases = (int)($prof['total_clases'] ?? 0);
                    $gradient  = $avatarGradients[$i % count($avatarGradients)];
                    $iniciales = iniciales($prof['nombres'] ?? '', $prof['apellidos'] ?? '');
                    $claseNota = notaClass($promedio);
                ?>
                <div class="profesor-card" data-nombre="<?= strtolower($nombres . ' ' . $apellidos) ?>" data-materia="<?= strtolower($materia) ?>">

                    <!-- AVATAR + MATERIA -->
                    <div class="profesor-header">
                        <div class="profesor-avatar" style="background: <?= $gradient ?>;">
                            <?php if ($foto && file_exists(BASE_PATH . '/public/uploads/docentes/' . $foto)): ?>
                                <img src="<?= BASE_URL ?>/public/uploads/docentes/<?= htmlspecialchars($foto) ?>" alt="<?= $nombres ?>">
                            <?php else: ?>
                                <span><?= $iniciales ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="profesor-materia-badge">
                            <i class="ri-book-2-line"></i>
                            <span><?= $materia ?></span>
                        </div>
                    </div>

                    <!-- NOMBRE Y CORREO -->
                    <div class="profesor-info">
                        <h3><?= $nombres . ' ' . $apellidos ?></h3>
                        <?php if ($correo): ?>
                            <a class="profesor-correo" href="mailto:<?= $correo ?>">
                                <i class="ri-mail-line"></i><?= $correo ?>
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- ESTADÍSTICAS -->
                    <div class="profesor-stats">
                        <!-- Promedio -->
                        <div class="pstat-item">
                            <span class="pstat-label">Tu nota</span>
                            <span class="pstat-value nota-badge <?= $claseNota ?>">
                                <?= $promedio !== null ? number_format($promedio, 1) : '—' ?>
                            </span>
                        </div>

                        <!-- Asistencia -->
                        <div class="pstat-item">
                            <span class="pstat-label">Asistencia</span>
                            <?php if ($asistencia !== null && $totalClases > 0): ?>
                                <span class="pstat-value asist-badge <?= $asistencia >= 80 ? 'alta' : ($asistencia >= 60 ? 'media' : 'baja') ?>">
                                    <?= $asistencia ?>%
                                </span>
                            <?php else: ?>
                                <span class="pstat-value sin-nota">—</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- BARRA DE PROMEDIO -->
                    <?php if ($promedio !== null): ?>
                    <div class="progreso-wrap">
                        <div class="progreso-bar <?= $claseNota ?>" style="width: <?= min(100, $promedio * 20) ?>%"></div>
                    </div>
                    <?php endif; ?>

                    <!-- ACCIÓN -->
                    <?php if ($correo): ?>
                    <div class="profesor-actions">
                        <a href="mailto:<?= $correo ?>" class="btn-contactar">
                            <i class="ri-mail-send-line"></i> Contactar
                        </a>
                    </div>
                    <?php endif; ?>

                </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </main>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Toggle sidebar ────────────────────────────────────────
    const app      = document.getElementById('appGrid');
    const sidebar  = document.getElementById('leftSidebar');
    const btnLeft  = document.getElementById('toggleLeft');

    if (btnLeft && sidebar && app) {
        btnLeft.addEventListener('click', () => {
            sidebar.classList.toggle('hidden');
            app.classList.toggle('hide-left', sidebar.classList.contains('hidden'));
        });
    }

    // ── Búsqueda en tiempo real ───────────────────────────────
    const input = document.getElementById('searchInput');
    const cards = document.querySelectorAll('.profesor-card');

    if (input) {
        input.addEventListener('input', () => {
            const q = input.value.toLowerCase().trim();
            cards.forEach(card => {
                const nombre  = card.dataset.nombre  || '';
                const materia = card.dataset.materia || '';
                card.style.display = (!q || nombre.includes(q) || materia.includes(q)) ? '' : 'none';
            });
        });
    }

    // ── Animación de entrada ──────────────────────────────────
    cards.forEach((card, i) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(16px)';
        setTimeout(() => {
            card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, i * 60);
    });
});
</script>
</body>
</html>
