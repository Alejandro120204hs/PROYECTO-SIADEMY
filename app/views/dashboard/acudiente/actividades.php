<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Actividades</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <?php include_once __DIR__ . '/../../layouts/header_coordinador.php' ?>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-acudiente.css?v=<?= @filemtime(BASE_PATH . '/public/assets/dashboard/css/styles-acudiente.css') ?: 1 ?>">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/acudiente-actividades.css?v=<?= @filemtime(BASE_PATH . '/public/assets/dashboard/css/acudiente-actividades.css') ?: 1 ?>">
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
          <div class="title">Actividades</div>
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
        </div>

        <!-- KPI CARDS -->
        <section class="summary-cards">
          <div class="summary-card">
            <div class="summary-icon" style="background:rgba(99,102,241,.2);">
              <i class="ri-task-line" style="color:#6366f1;"></i>
            </div>
            <div class="summary-content">
              <small>Total</small>
              <strong><?= $statActividades['total'] ?></strong>
              <span class="trend neutral">actividades</span>
            </div>
          </div>
          <div class="summary-card">
            <div class="summary-icon" style="background:rgba(251,191,36,.2);">
              <i class="ri-time-line" style="color:#fbbf24;"></i>
            </div>
            <div class="summary-content">
              <small>Pendientes</small>
              <strong><?= $statActividades['pendientes'] ?></strong>
              <span class="trend neutral">por entregar</span>
            </div>
          </div>
          <div class="summary-card">
            <div class="summary-icon" style="background:rgba(74,222,128,.2);">
              <i class="ri-checkbox-circle-line" style="color:#4ade80;"></i>
            </div>
            <div class="summary-content">
              <small>Calificadas</small>
              <strong><?= $statActividades['calificadas'] ?></strong>
              <span class="trend positive">con nota</span>
            </div>
          </div>
          <div class="summary-card">
            <div class="summary-icon" style="background:rgba(248,113,113,.2);">
              <i class="ri-error-warning-line" style="color:#f87171;"></i>
            </div>
            <div class="summary-content">
              <small>Vencidas</small>
              <strong><?= $statActividades['vencidas'] ?></strong>
              <span class="trend negative">sin entregar</span>
            </div>
          </div>
        </section>

        <!-- LISTA DE ACTIVIDADES -->
        <section class="card">
          <h3>Actividades</h3>

          <?php if (empty($actividades)): ?>
            <div class="empty-state">
              <i class="ri-task-line"></i>
              <p>No hay actividades registradas.</p>
            </div>
          <?php else: ?>

            <div class="filter-tabs">
              <button class="filter-tab active" data-filter="todas">Todas (<?= $statActividades['total'] ?>)</button>
              <button class="filter-tab" data-filter="Pendiente">Pendientes (<?= $statActividades['pendientes'] ?>)</button>
              <button class="filter-tab" data-filter="Entregada">Entregadas (<?= $statActividades['entregadas'] ?>)</button>
              <button class="filter-tab" data-filter="Calificada">Calificadas (<?= $statActividades['calificadas'] ?>)</button>
              <button class="filter-tab" data-filter="Vencida">Vencidas (<?= $statActividades['vencidas'] ?>)</button>
            </div>

            <div class="act-list" id="actList">
              <?php foreach ($actividades as $act):
                $fecha = date('d/m/Y', strtotime($act['fecha_entrega']));
                $diasR = (int)((strtotime($act['fecha_entrega']) - time()) / 86400);
              ?>
                <div class="act-item" data-estado="<?= htmlspecialchars($act['estado_entrega']) ?>">
                  <div class="act-left">
                    <div class="act-title"><?= htmlspecialchars($act['titulo']) ?></div>
                    <div class="act-meta">
                      <span><i class="ri-book-line"></i> <?= htmlspecialchars($act['materia']) ?></span>
                      <span><i class="ri-user-line"></i> <?= htmlspecialchars($act['docente']) ?></span>
                      <span><i class="ri-calendar-line"></i> <?= $fecha ?></span>
                      <?php if ($act['ponderacion']): ?>
                        <span><i class="ri-scales-line"></i> <?= (float)$act['ponderacion'] ?>%</span>
                      <?php endif; ?>
                    </div>
                  </div>
                  <div class="act-right">
                    <span class="badge-estado badge-<?= htmlspecialchars($act['estado_entrega']) ?>"><?= htmlspecialchars($act['estado_entrega']) ?></span>
                    <?php if ($act['nota'] !== null): ?>
                      <span class="nota-value"><?= number_format((float)$act['nota'], 1) ?></span>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>

          <?php endif; ?>
        </section>

      <?php endif; ?>
    </main>
  </div>

  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-acudiente.js"></script>
  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/acudiente-actividades.js?v=<?= @filemtime(BASE_PATH . '/public/assets/dashboard/js/acudiente-actividades.js') ?: 1 ?>"></script>
</body>
</html>
