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
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-acudiente.css">
  <style>
    .student-avatar-small img {
      width: 100%;
      height: 100%;
      border-radius: 50%;
      object-fit: cover;
    }

    .detail-row.detail-row-activo {
      background: rgba(79, 70, 229, .12);
      border-radius: 8px;
      padding-left: 10px;
      padding-right: 10px;
      margin: 0 -10px;
    }

    .subject-activities {
      flex: 1;
    }

    .subject-activities summary {
      list-style: none;
    }

    .subject-activities summary::-webkit-details-marker,
    .subject-activities summary::marker {
      display: none;
      content: '';
    }

    .activities-list {
      margin-top: 12px;
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .activity-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
      background: #0e142e;
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 10px 14px;
    }

    .activity-info strong {
      display: block;
      font-size: 14px;
    }

    .activity-info small {
      color: #97a1b6;
      font-size: 12px;
    }

    .grade-value.nota-pendiente {
      color: #97a1b6;
      font-weight: 500;
      font-size: 13px;
    }

    .empty-state {
      text-align: center;
      padding: 40px 20px;
      color: #c7cbe1;
    }

    .empty-state i {
      font-size: 48px;
      color: #4f46e5;
      margin-bottom: 12px;
      display: block;
    }
  </style>
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
          <h3>Calificaciones por Asignatura</h3>

          <?php if (empty($calificacionesMaterias)): ?>
            <div class="card">
              <p class="muted" style="margin: 0;">Este estudiante no tiene materias registradas.</p>
            </div>
          <?php else: ?>
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
</body>

</html>
