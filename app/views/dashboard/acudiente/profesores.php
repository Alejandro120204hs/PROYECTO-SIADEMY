<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Profesores</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <?php include_once __DIR__ . '/../../layouts/header_coordinador.php' ?>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-acudiente.css?v=<?= @filemtime(BASE_PATH . '/public/assets/dashboard/css/styles-acudiente.css') ?: 1 ?>">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/acudiente-profesores.css?v=<?= @filemtime(BASE_PATH . '/public/assets/dashboard/css/acudiente-profesores.css') ?: 1 ?>">
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
          <div class="title">Profesores</div>
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
          <div style="color:#9aa3b2;font-size:13px;">
            <?= $totalProfesores ?> profesor<?= $totalProfesores !== 1 ? 'es' : '' ?> · <?= $totalMaterias ?> materia<?= $totalMaterias !== 1 ? 's' : '' ?>
          </div>
        </div>

        <?php if (empty($profesores)): ?>
          <section class="card">
            <div class="empty-state">
              <i class="ri-user-3-line"></i>
              <h3>Sin profesores registrados</h3>
              <p>No hay docentes vinculados al curso de este estudiante aún.</p>
            </div>
          </section>
        <?php else: ?>

          <section class="card">
            <h3>Docentes del Estudiante</h3>
            <div class="prof-grid">
              <?php foreach ($profesores as $prof):
                $iniciales = mb_strtoupper(mb_substr($prof['nombres'], 0, 1) . mb_substr($prof['apellidos'], 0, 1));
                $promedio  = $prof['promedio_estudiante'];
                $pctAsist  = $prof['porcentaje_asistencia'];
                $colorProm = ($promedio !== null && $promedio < 3.0) ? '#f87171' : ($promedio !== null ? '#4ade80' : '#9aa3b2');
              ?>
                <div class="prof-card">
                  <div class="prof-header">
                    <div class="prof-avatar">
                      <?php if ($prof['foto']): ?>
                        <img src="<?= BASE_URL ?>/public/uploads/docentes/<?= htmlspecialchars($prof['foto']) ?>"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex';" alt="">
                        <div class="prof-avatar-initials" style="display:none;"><?= $iniciales ?></div>
                      <?php else: ?>
                        <div class="prof-avatar-initials"><?= $iniciales ?></div>
                      <?php endif; ?>
                    </div>
                    <div>
                      <div class="prof-name"><?= htmlspecialchars($prof['nombres'] . ' ' . $prof['apellidos']) ?></div>
                      <div class="prof-subject"><?= htmlspecialchars($prof['nombre_asignatura']) ?></div>
                    </div>
                  </div>

                  <?php if ($prof['correo']): ?>
                    <div class="prof-contact">
                      <i class="ri-mail-line"></i>
                      <a href="mailto:<?= htmlspecialchars($prof['correo']) ?>" style="color:inherit;"><?= htmlspecialchars($prof['correo']) ?></a>
                    </div>
                  <?php endif; ?>

                  <div class="prof-stats">
                    <div class="prof-stat">
                      <small>Promedio</small>
                      <strong style="color:<?= $colorProm ?>;"><?= $promedio !== null ? number_format((float)$promedio, 1) : '—' ?></strong>
                    </div>
                    <div class="prof-stat">
                      <small>Asistencia</small>
                      <strong><?= $pctAsist !== null ? (int)$pctAsist . '%' : '—' ?></strong>
                    </div>
                  </div>

                  <?php if ($prof['correo']): ?>
                    <div class="prof-card-footer">
                      <a href="mailto:<?= htmlspecialchars($prof['correo']) ?>" class="btn-contactar">
                        <i class="ri-mail-send-line"></i> Contactar docente
                      </a>
                    </div>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          </section>

        <?php endif; ?>
      <?php endif; ?>
    </main>
  </div>

  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-acudiente.js?v=<?= @filemtime(BASE_PATH . '/public/assets/dashboard/js/main-acudiente.js') ?: 1 ?>"></script>
</body>
</html>
