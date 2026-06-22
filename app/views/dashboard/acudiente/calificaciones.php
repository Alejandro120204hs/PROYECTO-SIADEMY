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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-acudiente.css?v=<?= @filemtime(BASE_PATH . '/public/assets/dashboard/css/styles-acudiente.css') ?: 1 ?>">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/acudiente-calificaciones.css?v=<?= @filemtime(BASE_PATH . '/public/assets/dashboard/css/acudiente-calificaciones.css') ?: 1 ?>">
</head>

<body>
  <div class="app hide-right" id="appGrid">
    <?php include_once __DIR__ . '/../../layouts/sidebar_acudiente.php' ?>

    <!-- MAIN -->
    <main class="main">
      <div class="topbar">
        <div class="topbar-left">
          <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
            <i class="ri-menu-2-line"></i>
          </button>
          <div class="title">Calificaciones</div>
        </div>
        <?php include_once BASE_PATH . '/app/views/layouts/boton_perfil_solo.php'; ?>
      </div>

      <?php include __DIR__ . '/../../layouts/mis_estudiantes_acudiente.php'; ?>

      <?php if (!$estudianteSeleccionado): ?>
        <section class="card">
          <div class="empty-state">
            <i class="ri-user-search-line"></i>
            <h3>No tienes estudiantes asociados</h3>
            <p>Si crees que esto es un error, comunícate con la institución para verificar la vinculación.</p>
          </div>
        </section>
      <?php else: ?>

        <?php
          $nombreEstudiante = trim($estudianteSeleccionado['nombres'] . ' ' . $estudianteSeleccionado['apellidos']);
          $iniciales = mb_strtoupper(
              mb_substr($estudianteSeleccionado['nombres'], 0, 1) . mb_substr($estudianteSeleccionado['apellidos'], 0, 1)
          );
          $cursoActual = $estudianteSeleccionado['id_curso']
              ? $estudianteSeleccionado['grado'] . '° - ' . $estudianteSeleccionado['nombre_curso']
              : 'Sin matrícula activa';
        ?>

        <!-- STUDENT INFO BAR -->
        <div class="student-bar">
          <div class="student-quick-info">
            <div class="student-avatar-small">
              <img src="<?= BASE_URL ?>/public/uploads/estudiantes/<?= htmlspecialchars($estudianteSeleccionado['foto'] ?: 'default.png') ?>" alt="" onerror="this.onerror=null; this.src='<?= BASE_URL ?>/public/uploads/estudiantes/default.png'">
            </div>
            <div>
              <strong><?= htmlspecialchars($nombreEstudiante) ?></strong>
              <small><?= htmlspecialchars($cursoActual) ?> • Periodo activo: <?= (int)$periodoActual ?></small>
            </div>
          </div>
          <form class="period-selector" method="get" action="<?= BASE_URL ?>/acudiente/calificaciones">
            <label for="periodSelect">Periodo:</label>
            <select id="periodSelect" name="periodo" onchange="this.form.submit()">
              <?php for ($p = 1; $p <= 4; $p++): ?>
                <option value="<?= $p ?>" <?= $p === $periodoSeleccionado ? 'selected' : '' ?>>Periodo <?= $p ?></option>
              <?php endfor; ?>
            </select>
          </form>
        </div>

        <!-- SUMMARY CARDS -->
        <section class="summary-cards">
          <div class="summary-card">
            <div class="summary-icon" style="background: rgba(79, 70, 229, 0.2);">
              <i class="ri-bar-chart-box-line" style="color: #6366f1;"></i>
            </div>
            <div class="summary-content">
              <small>Promedio General</small>
              <strong><?= $resumenCalificaciones['promedio_general'] > 0 ? number_format((float)$resumenCalificaciones['promedio_general'], 1) : '—' ?></strong>
              <span class="trend neutral">Periodo <?= (int)$periodoActual ?> activo</span>
            </div>
          </div>

          <div class="summary-card">
            <div class="summary-icon" style="background: rgba(34, 197, 94, 0.2);">
              <i class="ri-checkbox-circle-line" style="color: #4ade80;"></i>
            </div>
            <div class="summary-content">
              <small>Materias Aprobadas</small>
              <strong><?= $totalAprobadas ?>/<?= (int)$resumenCalificaciones['total_materias'] ?></strong>
              <span class="trend neutral"><?= $resumenCalificaciones['total_materias'] > 0 ? round($totalAprobadas / $resumenCalificaciones['total_materias'] * 100) : 0 ?>% del total</span>
            </div>
          </div>

          <div class="summary-card">
            <div class="summary-icon" style="background: rgba(251, 191, 36, 0.2);">
              <i class="ri-alert-line" style="color: #fbbf24;"></i>
            </div>
            <div class="summary-content">
              <small>En Observación</small>
              <strong><?= $totalEnObservacion ?></strong>
              <span class="trend neutral"><?= $peorMateria ? htmlspecialchars($peorMateria['nombre']) : 'Ninguna' ?></span>
            </div>
          </div>

          <div class="summary-card">
            <div class="summary-icon" style="background: rgba(59, 130, 246, 0.2);">
              <i class="ri-trophy-line" style="color: #60a5fa;"></i>
            </div>
            <div class="summary-content">
              <small>Mejor Materia</small>
              <strong><?= $mejorMateria ? number_format($mejorMateria['promedio'], 1) : '—' ?></strong>
              <span class="trend neutral"><?= $mejorMateria ? htmlspecialchars($mejorMateria['nombre']) : 'Sin datos' ?></span>
            </div>
          </div>
        </section>

        <!-- GRADES BY SUBJECT -->
        <section class="grades-section">
          <div class="grades-section-header">
            <h3>Calificaciones por Asignatura</h3>
            <div class="view-toggle-group">
              <button class="btn-view-toggle active" data-view="cards" title="Vista de tarjetas">
                <i class="ri-layout-grid-line"></i>
              </button>
              <button class="btn-view-toggle" data-view="table" title="Vista de tabla">
                <i class="ri-table-line"></i>
              </button>
            </div>
          </div>

          <?php if (empty($calificacionesMaterias)): ?>
            <div class="card">
              <p class="muted" style="margin: 0;">Este estudiante no tiene materias registradas.</p>
            </div>
          <?php else: ?>

            <!-- ── Vista: Cards ─────────────────────────────────────────── -->
            <div id="viewCards">
              <?php foreach ($calificacionesMaterias as $materia): ?>
                <?php
                  $clase = match ($materia['estado_general']) {
                      'superior' => 'excellent',
                      'alto' => 'good',
                      'basico' => 'average',
                      'bajo' => 'low',
                      default => '',
                  };
                  $progreso = $materia['promedio_general'] !== null
                      ? max(0, min(100, ($materia['promedio_general'] / 5) * 100))
                      : 0;
                  $actividadesPeriodo = $materia['periodos'][$periodoSeleccionado]['evaluaciones'];
                ?>
                <div class="subject-card <?= $clase ?>">
                  <div class="subject-header">
                    <div class="subject-icon" style="background: <?= htmlspecialchars($materia['color_icono']) ?>;">
                      <i class="<?= htmlspecialchars($materia['icono']) ?>"></i>
                    </div>
                    <div class="subject-info">
                      <h4><?= htmlspecialchars($materia['nombre']) ?></h4>
                      <small><?= $materia['profesor'] !== '' ? 'Prof. ' . htmlspecialchars($materia['profesor']) : 'Sin docente asignado' ?></small>
                    </div>
                    <div class="subject-grade">
                      <div class="grade-big"><?= $materia['promedio_general'] !== null ? number_format($materia['promedio_general'], 1) : '—' ?></div>
                      <small>Promedio</small>
                    </div>
                  </div>
                  <div class="subject-details">
                    <?php foreach ($materia['periodos'] as $numPeriodo => $datosPeriodo): ?>
                      <div class="detail-row<?= $numPeriodo === $periodoSeleccionado ? ' detail-row-activo' : '' ?>">
                        <span>Periodo <?= $numPeriodo ?><?= $numPeriodo === $periodoActual ? ' (actual)' : '' ?></span>
                        <span class="grade-value"><?= $datosPeriodo['notaFinal'] !== null ? number_format($datosPeriodo['notaFinal'], 1) : '—' ?></span>
                      </div>
                    <?php endforeach; ?>

                    <div class="progress-bar">
                      <div class="progress-fill <?= $clase === 'low' ? 'warning' : '' ?>" style="width: <?= $progreso ?>%;"></div>
                    </div>

                    <?php if ($clase === 'low'): ?>
                      <div class="alert-box">
                        <i class="ri-alert-line"></i>
                        <span>Esta materia requiere atención especial</span>
                      </div>
                    <?php endif; ?>

                    <div class="subject-actions">
                      <details class="subject-activities">
                        <summary class="btn-action"><i class="ri-file-list-3-line"></i> Ver actividades del Periodo <?= $periodoSeleccionado ?></summary>
                        <div class="activities-list">
                          <?php if (empty($actividadesPeriodo)): ?>
                            <p class="muted" style="margin: 0;">No hay actividades registradas en este periodo.</p>
                          <?php else: ?>
                            <?php foreach ($actividadesPeriodo as $actividad): ?>
                              <div class="activity-row">
                                <div class="activity-info">
                                  <strong><?= htmlspecialchars($actividad['nombre']) ?></strong>
                                  <small><?= htmlspecialchars($actividad['fecha']) ?> • Ponderación <?= htmlspecialchars($actividad['peso']) ?></small>
                                </div>
                                <?php if ($actividad['nota'] !== null): ?>
                                  <span class="grade-value"><?= number_format($actividad['nota'], 1) ?></span>
                                <?php else: ?>
                                  <span class="grade-value nota-pendiente">Sin calificar</span>
                                <?php endif; ?>
                              </div>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </div>
                      </details>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>

            <!-- ── Vista: Tabla ─────────────────────────────────────────── -->
            <div id="viewTable" style="display:none;">
              <div class="card" style="padding:0; overflow:hidden;">
                <table class="calif-table">
                  <thead>
                    <tr>
                      <th>Materia</th>
                      <th>Docente</th>
                      <th>P1</th>
                      <th>P2</th>
                      <th>P3</th>
                      <th>P4</th>
                      <th>Promedio</th>
                      <th>Estado</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($calificacionesMaterias as $materia):
                      $claseT = match ($materia['estado_general']) {
                          'superior' => 'excellent',
                          'alto'     => 'good',
                          'basico'   => 'average',
                          'bajo'     => 'low',
                          default    => '',
                      };
                      $estadoLabel = match ($materia['estado_general']) {
                          'superior' => 'Superior',
                          'alto'     => 'Alto',
                          'basico'   => 'Básico',
                          'bajo'     => 'Bajo',
                          default    => '—',
                      };
                    ?>
                      <tr>
                        <td>
                          <div style="display:flex;align-items:center;gap:10px;">
                            <div class="tbl-icon" style="background:<?= htmlspecialchars($materia['color_icono']) ?>;"><i class="<?= htmlspecialchars($materia['icono']) ?>"></i></div>
                            <strong><?= htmlspecialchars($materia['nombre']) ?></strong>
                          </div>
                        </td>
                        <td><?= $materia['profesor'] !== '' ? htmlspecialchars($materia['profesor']) : '—' ?></td>
                        <?php for ($p = 1; $p <= 4; $p++):
                          $nota = $materia['periodos'][$p]['notaFinal'] ?? null;
                          $esActual = ($p === $periodoActual);
                          $esSelec  = ($p === $periodoSeleccionado);
                          $cellClass = '';
                          if ($nota !== null) {
                              if ($nota >= 4.5) $cellClass = 'cell-excellent';
                              elseif ($nota >= 4.0) $cellClass = 'cell-good';
                              elseif ($nota >= 3.1) $cellClass = 'cell-average';
                              else $cellClass = 'cell-low';
                          }
                        ?>
                          <td class="grade-cell <?= $cellClass ?><?= $esSelec ? ' cell-selected' : '' ?>">
                            <?= $nota !== null ? number_format($nota, 1) : '—' ?>
                            <?php if ($esActual): ?><span class="cell-badge">actual</span><?php endif; ?>
                          </td>
                        <?php endfor; ?>
                        <td class="grade-cell <?= $claseT !== '' ? 'cell-' . $claseT : '' ?>">
                          <strong><?= $materia['promedio_general'] !== null ? number_format($materia['promedio_general'], 1) : '—' ?></strong>
                        </td>
                        <td><span class="estado-badge <?= $claseT ?>"><?= $estadoLabel ?></span></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>

          <?php endif; ?>
        </section>

        <!-- ESCALA DE CALIFICACIÓN -->
        <section class="card">
          <h3>Escala de Calificación</h3>
          <div class="scale-info">
            <div class="scale-item">
              <div class="scale-color excellent"></div>
              <div>
                <strong>Superior</strong>
                <small>4.5 - 5.0</small>
              </div>
            </div>
            <div class="scale-item">
              <div class="scale-color good"></div>
              <div>
                <strong>Alto</strong>
                <small>4.0 - 4.4</small>
              </div>
            </div>
            <div class="scale-item">
              <div class="scale-color average"></div>
              <div>
                <strong>Básico</strong>
                <small>3.1 - 3.9</small>
              </div>
            </div>
            <div class="scale-item">
              <div class="scale-color low"></div>
              <div>
                <strong>Bajo</strong>
                <small>0.0 - 3.0</small>
              </div>
            </div>
          </div>
        </section>

        <!-- ESTADÍSTICAS -->
        <section class="card">
          <h3>Estadísticas del Año</h3>
          <div class="stat-item">
            <span>Total de materias</span>
            <strong><?= (int)$resumenCalificaciones['total_materias'] ?></strong>
          </div>
          <div class="stat-item">
            <span>Total de actividades evaluables</span>
            <strong><?= (int)$resumenCalificaciones['total_evaluaciones'] ?></strong>
          </div>
          <div class="stat-item">
            <span>Actividades pendientes por entregar</span>
            <strong><?= (int)$resumenCalificaciones['pendientes'] ?></strong>
          </div>
        </section>

      <?php endif; ?>
    </main>
  </div>

  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-acudiente.js"></script>
  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/acudiente-calificaciones.js?v=<?= @filemtime(BASE_PATH . '/public/assets/dashboard/js/acudiente-calificaciones.js') ?: 1 ?>"></script>
</body>

</html>
