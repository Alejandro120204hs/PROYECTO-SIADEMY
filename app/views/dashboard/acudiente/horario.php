<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Horario</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <?php include_once __DIR__ . '/../../layouts/header_coordinador.php' ?>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-acudiente.css?v=<?= @filemtime(BASE_PATH . '/public/assets/dashboard/css/styles-acudiente.css') ?: 1 ?>">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/acudiente-horario.css?v=<?= @filemtime(BASE_PATH . '/public/assets/dashboard/css/acudiente-horario.css') ?: 1 ?>">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/modo-claro-acudiente.css">
</head>
<body>
  <div class="app hide-right" id="appGrid">
    <?php include_once __DIR__ . '/../../layouts/sidebar_acudiente.php' ?>

    <main class="main">
      <div class="topbar">
        <div class="topbar-left">
          <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
            <i class="ri-menu-2-line"></i>
          </button>
          <div class="title">Horario</div>
        </div>
        <?php include_once BASE_PATH . '/app/views/layouts/boton_perfil_solo.php'; ?>
      </div>

      <?php include __DIR__ . '/../../layouts/mis_estudiantes_acudiente.php'; ?>

      <?php if (!$estudianteSeleccionado): ?>
        <section class="card">
          <div class="empty-state">
            <i class="ri-user-search-line"></i>
            <h3>No tienes estudiantes asociados</h3>
            <p>Comunícate con la institución para verificar la vinculación.</p>
          </div>
        </section>
      <?php else:
        $nombreEstudiante = trim($estudianteSeleccionado['nombres'] . ' ' . $estudianteSeleccionado['apellidos']);
        $cursoActual = $estudianteSeleccionado['id_curso']
            ? $estudianteSeleccionado['grado'] . '° - ' . $estudianteSeleccionado['nombre_curso']
            : 'Sin matrícula activa';
        $dias = HorarioModel::$dias;
      ?>

        <div class="student-bar">
          <div class="student-quick-info">
            <div class="student-avatar-small">
              <img src="<?= BASE_URL ?>/public/uploads/estudiantes/<?= htmlspecialchars($estudianteSeleccionado['foto'] ?: 'default.png') ?>"
                   onerror="this.onerror=null;this.src='<?= BASE_URL ?>/public/uploads/estudiantes/default.png'" alt="">
            </div>
            <div>
              <strong><?= htmlspecialchars($nombreEstudiante) ?></strong>
              <small><?= htmlspecialchars($cursoActual) ?></small>
            </div>
          </div>
          <div style="color:#9aa3b2;font-size:13px;"><?= $totalBloques ?> bloque<?= $totalBloques !== 1 ? 's' : '' ?> en total</div>
        </div>

        <?php if (empty($horarios)): ?>
          <section class="card">
            <div class="empty-state">
              <i class="ri-calendar-close-line"></i>
              <h3>Sin horario registrado</h3>
              <p>El horario aún no ha sido configurado para este curso.</p>
            </div>
          </section>
        <?php else: ?>

          <!-- LEYENDA -->
          <section class="card" style="padding:16px 20px;">
            <div class="legend-list">
              <?php foreach ($coloresPorAsignatura as $nombreAsig => $color): ?>
                <div class="legend-item">
                  <div class="legend-dot" style="background:<?= htmlspecialchars($color) ?>;"></div>
                  <?= htmlspecialchars($nombreAsig) ?>
                </div>
              <?php endforeach; ?>
            </div>
          </section>

          <!-- HORARIO POR DÍA -->
          <section class="card">
            <h3>Horario Semanal</h3>
            <div class="horario-grid">
              <?php foreach ($dias as $numDia => $nombreDia):
                $bloquesDia = $horariosPorDia[$numDia] ?? [];
                if (empty($bloquesDia)) continue;
              ?>
                <div class="dia-card">
                  <div class="dia-header"><i class="ri-calendar-line" style="margin-right:6px;"></i><?= $nombreDia ?></div>
                  <?php foreach ($bloquesDia as $bloque):
                    $color = $coloresPorAsignatura[$bloque['asignatura_nombre']] ?? '#6366f1';
                    $horaI = substr($bloque['hora_inicio'], 0, 5);
                    $horaF = substr($bloque['hora_fin'],    0, 5);
                  ?>
                    <div class="bloque">
                      <div class="bloque-color-bar" style="background:<?= $color ?>;"></div>
                      <div class="bloque-info">
                        <strong><?= htmlspecialchars($bloque['asignatura_nombre']) ?></strong>
                        <small><?= htmlspecialchars($bloque['docente_nombre']) ?></small>
                        <div class="bloque-hora">
                          <i class="ri-time-line"></i>
                          <?= $horaI ?> – <?= $horaF ?>
                          <?php if (!empty($bloque['aula'])): ?>
                            &nbsp;·&nbsp;<i class="ri-map-pin-line"></i><?= htmlspecialchars($bloque['aula']) ?>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endforeach; ?>
            </div>
          </section>

        <?php endif; ?>
      <?php endif; ?>
    </main>
  </div>

  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-acudiente.js"></script>
</body>
</html>
