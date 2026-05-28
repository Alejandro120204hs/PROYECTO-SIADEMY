<?php
    require_once BASE_PATH . '/app/helpers/session_estudiante.php';
    require_once BASE_PATH . '/app/controllers/perfil.php';
    $id      = $_SESSION['user']['id'] ?? 0;
    $usuario = mostrarPerfil($id);

    $horariosPorDia       = $horariosPorDia       ?? array_fill(1, 6, []);
    $coloresPorAsignatura = $coloresPorAsignatura ?? [];
    $totalBloques         = $totalBloques         ?? 0;
    $cursoNombre          = $cursoNombre          ?? '';

    $dias = HorarioModel::$dias;
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • Mi Horario</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-horarios.css">
</head>
<body>
<div class="app hide-right" id="appGrid">

    <!-- LEFT SIDEBAR -->
    <?php include_once __DIR__ . '/../../layouts/sidebar_estudiante.php'; ?>

    <!-- MAIN -->
    <main class="main">

        <!-- TOPBAR -->
        <div class="topbar">
            <div class="topbar-left">
                <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú">
                    <i class="ri-menu-2-line"></i>
                </button>
                <div class="title">
                    Mi Horario
                    <?php if ($cursoNombre): ?>
                        <span style="font-size:16px; font-weight:500; color:#818cf8; font-family:'Inter',sans-serif;">
                            — Curso <?= htmlspecialchars($cursoNombre) ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <?php include_once BASE_PATH . '/app/views/layouts/boton_perfil_solo.php'; ?>
        </div>

        <!-- STATS -->
        <div class="stats-grid" style="grid-template-columns: repeat(2,1fr); max-width: 500px; margin-bottom:28px;">
            <div class="stat-card">
                <div class="stat-icon blue"><i class="ri-calendar-2-line"></i></div>
                <div class="stat-content">
                    <h3><?= $totalBloques ?></h3>
                    <p>Clases por semana</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="ri-book-2-line"></i></div>
                <div class="stat-content">
                    <h3><?= count($coloresPorAsignatura) ?></h3>
                    <p>Asignaturas</p>
                </div>
            </div>
        </div>

        <!-- WEEKLY GRID HEADER -->
        <div class="week-header">
            <span class="week-title">
                <i class="ri-calendar-line"></i>
                Horario semanal
                <?php if ($totalBloques > 0): ?>
                <span class="week-badge"><?= $totalBloques ?> bloques</span>
                <?php endif; ?>
            </span>
        </div>

        <?php if ($totalBloques === 0): ?>
        <div class="empty-state">
            <i class="ri-calendar-close-line"></i>
            <h3>Sin horario disponible</h3>
            <p>Tu curso aún no tiene horario configurado. Consulta con tu institución.</p>
        </div>

        <?php else: ?>
        <div class="schedule-grid">
            <?php foreach ($dias as $numDia => $nombreDia):
                $bloquesDia = $horariosPorDia[$numDia] ?? [];
            ?>
            <div class="day-column">
                <div class="day-header">
                    <span class="day-name"><?= $nombreDia ?></span>
                    <span class="day-blocks-count">
                        <?= count($bloquesDia) ?> <?= count($bloquesDia) === 1 ? 'clase' : 'clases' ?>
                    </span>
                </div>
                <div class="day-body">
                    <?php if (empty($bloquesDia)): ?>
                    <div class="day-empty">
                        <i class="ri-moon-line"></i>
                        <span>Día libre</span>
                    </div>
                    <?php else: ?>
                    <?php foreach ($bloquesDia as $bloque):
                        $color = $coloresPorAsignatura[$bloque['asignatura_nombre']] ?? '#4f46e5';
                        $horaI = substr($bloque['hora_inicio'], 0, 5);
                        $horaF = substr($bloque['hora_fin'],    0, 5);
                    ?>
                    <div class="block-card" style="--block-color: <?= $color ?>;">
                        <div class="block-time">
                            <i class="ri-time-line"></i>
                            <?= $horaI ?> – <?= $horaF ?>
                        </div>
                        <div class="block-subject"><?= htmlspecialchars($bloque['asignatura_nombre']) ?></div>
                        <div class="block-teacher">
                            <i class="ri-user-3-line"></i>
                            <?= htmlspecialchars($bloque['docente_nombre']) ?>
                        </div>
                        <?php if (!empty($bloque['aula'])): ?>
                        <div class="block-aula">
                            <i class="ri-map-pin-line"></i>
                            <?= htmlspecialchars($bloque['aula']) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const app     = document.getElementById('appGrid');
    const sidebar = document.getElementById('leftSidebar');
    const btnLeft = document.getElementById('toggleLeft');
    if (btnLeft && sidebar && app) {
        btnLeft.addEventListener('click', () => {
            sidebar.classList.toggle('hidden');
            app.classList.toggle('hide-left', sidebar.classList.contains('hidden'));
        });
    }

    document.querySelectorAll('.block-card').forEach((card, i) => {
        card.style.opacity   = '0';
        card.style.transform = 'translateY(10px)';
        setTimeout(() => {
            card.style.transition = 'opacity .25s ease, transform .25s ease';
            card.style.opacity    = '1';
            card.style.transform  = 'translateY(0)';
        }, i * 50);
    });
});
</script>
</body>
</html>
