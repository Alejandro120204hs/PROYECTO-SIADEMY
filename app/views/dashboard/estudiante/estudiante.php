<?php
require_once BASE_PATH . '/app/helpers/session_estudiante.php';
require_once BASE_PATH . '/app/controllers/estudiante/view_data.php';

$dataVistaEstudianteDashboard = obtenerDataVistaEstudianteDashboard();
extract($dataVistaEstudianteDashboard, EXTR_SKIP);
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Panel Principal</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <?php include_once __DIR__ . '/../../layouts/header_coordinador.php' ?>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-estudiante.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/modo-claro-estudiante.css">
</head>

<body>
  <div class="app hide-right" id="appGrid"
       data-calendar-events='<?= estudianteJsonParaHtml($eventosCalendarioEstudiante) ?>'>

    <!-- LEFT SIDEBAR -->
    <?php include_once __DIR__ . '/../../layouts/sidebar_estudiante.php' ?>

    <!-- MAIN -->
    <main class="main">
      <div class="topbar">
        <div class="topbar-left">
          <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
            <i class="ri-menu-2-line"></i>
          </button>
          <div class="title">Panel Principal</div>
        </div>
        <div class="search">
          <i class="ri-search-2-line"></i>
          <input type="text" placeholder="Buscar">
        </div>
        <?php include_once BASE_PATH . '/app/views/layouts/boton_perfil_solo.php'; ?>
      </div>

      <!-- KPIs -->
      <section class="kpis">
        <div class="kpi">
          <div class="icon"><i class="ri-book-open-line"></i></div>
          <div>
            <small>Materias</small>
            <strong><?= (int)($estadisticas['total_materias'] ?? 0) ?></strong>
          </div>
        </div>
        <div class="kpi">
          <div class="icon"><i class="ri-bar-chart-2-line"></i></div>
          <div>
            <small>Promedio General</small>
            <strong><?= number_format((float)($estadisticas['promedio_general'] ?? 0), 1) ?></strong>
          </div>
        </div>
        <div class="kpi">
          <div class="icon"><i class="ri-time-line"></i></div>
          <div>
            <small>Actividades Pendientes</small>
            <strong><?= (int)($estadisticas['actividades_pendientes'] ?? 0) ?></strong>
          </div>
        </div>
        <div class="kpi">
          <div class="icon"><i class="ri-alert-line"></i></div>
          <div>
            <small>Materias en Riesgo</small>
            <strong><?= (int)($estadisticas['en_riesgo'] ?? 0) ?></strong>
          </div>
        </div>
      </section>

      <!-- DATATABLE: Materias con bajo rendimiento -->
      <section class="datatable-card">
        <h3>Mis Materias con Bajo Rendimiento</h3>

        <div class="table-responsive">
          <table id="studentsTable" class="table table-dark table-hover align-middle" style="width:100%">
            <thead>
              <tr>
                <th>Materia</th>
                <th>Profesor</th>
                <th>Nota Actual</th>
                <th>Estado</th>
                <th class="text-center" style="width:60px">Ver</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($materiasBajoRendimiento as $materia): ?>
                <?php
                  $promedio   = $materia['promedio'];
                  $estadoNota = $materia['estado_nota'];
                  $notaTexto  = ($promedio !== null) ? number_format((float)$promedio, 1) : 'S/N';

                  // Escala: bajo ≤3.0 | basico 3.1-3.9 | alto 4.0-4.4 | superior 4.5-5.0
                  // (En esta tabla solo aparecen materias "bajo", pero se mapea por si hay otros)
                  $badgeNotaMap = [
                      'bajo'     => 'bg-danger',
                      'basico'   => 'bg-warning text-dark',
                      'alto'     => 'bg-info text-dark',
                      'superior' => 'bg-success',
                      'sin-nota' => 'bg-secondary',
                  ];
                  $badgeEstadoMap = [
                      'bajo'     => ['class' => 'bg-danger',           'label' => 'Bajo Rendimiento'],
                      'basico'   => ['class' => 'bg-warning',          'label' => 'Básico'],
                      'alto'     => ['class' => 'bg-info text-dark',   'label' => 'Alto'],
                      'superior' => ['class' => 'bg-success',          'label' => 'Superior'],
                      'sin-nota' => ['class' => 'bg-secondary',        'label' => 'Sin Nota'],
                  ];

                  $badgeClase  = $badgeNotaMap[$estadoNota]              ?? 'bg-secondary';
                  $estadoBadge = $badgeEstadoMap[$estadoNota]['class']   ?? 'bg-secondary';
                  $estadoLabel = $badgeEstadoMap[$estadoNota]['label']   ?? 'Desconocido';

                  $docente    = trim($materia['docente_nombres'] . ' ' . $materia['docente_apellidos']);
                  $urlDetalle = BASE_URL . '/estudiante-materia-detalle?id=' . (int)$materia['id_asignatura_curso'];
                ?>
                <tr>
                  <td>
                    <strong><?= htmlspecialchars($materia['materia']) ?></strong>
                    <?php if (!empty($materia['descripcion'])): ?>
                      <small class="d-block text-muted"><?= htmlspecialchars($materia['descripcion']) ?></small>
                    <?php endif; ?>
                  </td>
                  <td><?= htmlspecialchars($docente) ?></td>
                  <td>
                    <span class="badge <?= $badgeClase ?>"><?= $notaTexto ?></span>
                  </td>
                  <td>
                    <span class="badge <?= $estadoBadge ?>"><?= $estadoLabel ?></span>
                  </td>
                  <td class="text-center">
                    <a href="<?= $urlDetalle ?>" class="btn btn-sm btn-outline-light" title="Ver detalles">
                      <i class="ri-eye-line"></i>
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>

      <!-- CALENDAR SECTION -->
      <section class="calendar-card">
        <div class="calendar-header">
          <h3>Calendario Académico</h3>
          <div class="calendar-nav">
            <button id="prevMonth"><i class="ri-arrow-left-s-line"></i></button>
            <button id="nextMonth"><i class="ri-arrow-right-s-line"></i></button>
          </div>
        </div>
        <div id="calendarContainer">
          <div class="calendar-grid" id="calendarGrid">
            <!-- Generado por JavaScript -->
          </div>
        </div>
      </section>

    </main>

  </div><!-- /#appGrid -->

  <!-- Modal: eventos del día -->
  <div class="modal fade" id="estudianteCalendarDayModal" tabindex="-1" aria-labelledby="estudianteCalendarDayModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content" style="background:#11193a; border:1px solid rgba(255,255,255,.1); color:#e6e9f4;">
        <div class="modal-header" style="border-bottom:1px solid rgba(255,255,255,.1);">
          <h5 class="modal-title" id="estudianteCalendarDayModalLabel">Eventos del día</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body" id="estudianteCalendarDayModalBody">
          <!-- Contenido generado por JS -->
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-estudiante.js"></script>
</body>

</html>
