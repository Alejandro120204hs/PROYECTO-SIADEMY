<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Asistencia</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-acudiente.css?v=<?= @filemtime(BASE_PATH . '/public/assets/dashboard/css/styles-acudiente.css') ?: 1 ?>">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/acudiente-asistencia.css?v=<?= @filemtime(BASE_PATH . '/public/assets/dashboard/css/acudiente-asistencia.css') ?: 1 ?>">
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
          <div class="title">Asistencia</div>
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
        $pctGlobal = (float)($totalesGlobales['porcentaje_asistencia'] ?? 0);
        $colorPct  = $pctGlobal >= 80 ? '#4ade80' : ($pctGlobal >= 60 ? '#fbbf24' : '#f87171');
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
              <i class="ri-percent-line" style="color:#6366f1;"></i>
            </div>
            <div class="summary-content">
              <small>Asistencia Global</small>
              <strong style="color:<?= $colorPct ?>;"><?= $pctGlobal ?>%</strong>
              <span class="trend neutral"><?= (int)($totalesGlobales['total_clases'] ?? 0) ?> clases registradas</span>
            </div>
          </div>
          <div class="summary-card">
            <div class="summary-icon" style="background:rgba(74,222,128,.2);">
              <i class="ri-checkbox-circle-line" style="color:#4ade80;"></i>
            </div>
            <div class="summary-content">
              <small>Presente</small>
              <strong><?= (int)($totalesGlobales['presentes'] ?? 0) ?></strong>
              <span class="trend positive">clases asistidas</span>
            </div>
          </div>
          <div class="summary-card">
            <div class="summary-icon" style="background:rgba(248,113,113,.2);">
              <i class="ri-close-circle-line" style="color:#f87171;"></i>
            </div>
            <div class="summary-content">
              <small>Ausente</small>
              <strong><?= (int)($totalesGlobales['ausentes'] ?? 0) ?></strong>
              <span class="trend negative">inasistencias</span>
            </div>
          </div>
          <div class="summary-card">
            <div class="summary-icon" style="background:rgba(251,191,36,.2);">
              <i class="ri-time-line" style="color:#fbbf24;"></i>
            </div>
            <div class="summary-content">
              <small>Tarde / Justificado</small>
              <strong><?= (int)($totalesGlobales['tardes'] ?? 0) + (int)($totalesGlobales['justificados'] ?? 0) ?></strong>
              <span class="trend neutral"><?= (int)($totalesGlobales['tardes'] ?? 0) ?> tarde · <?= (int)($totalesGlobales['justificados'] ?? 0) ?> justif.</span>
            </div>
          </div>
        </section>

        <!-- POR ASIGNATURA -->
        <section class="card">
          <h3>Por Asignatura</h3>
          <?php if (empty($resumenPorAsignatura)): ?>
            <div class="empty-state" style="padding:24px;">
              <i class="ri-calendar-close-line"></i>
              <p>Aún no hay registros de asistencia.</p>
            </div>
          <?php else: ?>
            <div style="overflow-x:auto;">
              <table class="asist-table">
                <thead>
                  <tr>
                    <th>Asignatura</th>
                    <th>Total</th>
                    <th>Presente</th>
                    <th>Ausente</th>
                    <th>Tarde</th>
                    <th>Justif.</th>
                    <th>% Asistencia</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($resumenPorAsignatura as $r):
                    $pct = (float)$r['porcentaje_asistencia'];
                    $col = $pct >= 80 ? '#4ade80' : ($pct >= 60 ? '#fbbf24' : '#f87171');
                  ?>
                  <tr>
                    <td><strong style="color:#e2e5f0;"><?= htmlspecialchars($r['nombre_asignatura']) ?></strong></td>
                    <td><?= (int)$r['total_clases'] ?></td>
                    <td style="color:#4ade80;"><?= (int)$r['presentes'] ?></td>
                    <td style="color:#f87171;"><?= (int)$r['ausentes'] ?></td>
                    <td style="color:#fbbf24;"><?= (int)$r['tardes'] ?></td>
                    <td style="color:#a5b4fc;"><?= (int)$r['justificados'] ?></td>
                    <td>
                      <div style="display:flex;align-items:center;gap:10px;">
                        <div class="pct-bar" style="flex:1;">
                          <div class="pct-fill" style="width:<?= min($pct,100) ?>%;background:<?= $col ?>;"></div>
                        </div>
                        <span style="color:<?= $col ?>;font-weight:600;min-width:38px;"><?= $pct ?>%</span>
                      </div>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </section>

        <!-- HISTORIAL RECIENTE -->
        <section class="card">
          <h3>Historial Reciente <span style="color:#9aa3b2;font-size:13px;font-weight:400;">(últimas 50 entradas)</span></h3>
          <?php if (empty($historial)): ?>
            <div class="empty-state" style="padding:24px;">
              <i class="ri-calendar-close-line"></i>
              <p>Sin registros disponibles.</p>
            </div>
          <?php else: ?>
            <div style="overflow-x:auto;">
              <table class="asist-table">
                <thead>
                  <tr>
                    <th>Fecha</th>
                    <th>Asignatura</th>
                    <th>Estado</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($historial as $h): ?>
                  <tr>
                    <td><?= date('d/m/Y', strtotime($h['fecha'])) ?></td>
                    <td><?= htmlspecialchars($h['nombre_asignatura']) ?></td>
                    <td><span class="badge-estado badge-<?= htmlspecialchars($h['estado']) ?>"><?= htmlspecialchars($h['estado']) ?></span></td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </section>

      <?php endif; ?>
    </main>
  </div>

  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-acudiente.js"></script>
</body>
</html>
