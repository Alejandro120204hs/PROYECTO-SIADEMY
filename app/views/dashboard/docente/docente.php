<?php 
  // require_once BASE_PATH . '/app/helpers/session_administrador.php';
   // ENLAZAMOS LA DEPENDENCIA, EN ESTE CASO EL CONTROLADOR QUE TIENE LA FUNCION DE COSULTAR LOS DATOS
  require_once BASE_PATH . '/app/controllers/docente/curso.php';
  require_once BASE_PATH . '/app/controllers/perfil.php';

  // LLAMAMOS LA FUNCION ESPECIFICA QUE EXISTE EN DICHO CONTROLADOR
  $datos = mostrarCursos();
  $estadisticas = obtenerEstadisticasDocenteDashboard();
  $estudiantesBajoRendimiento = listarEstudiantesBajoRendimientoDocente(20);
  $eventosCalendarioDocente = obtenerEventosCalendarioDocente();
  $id = $_SESSION['user']['id'] ?? 0;
  $usuario = mostrarPerfil($id);
  $mainDocenteJsVersion = @filemtime(BASE_PATH . '/public/assets/dashboard/js/main-docente.js') ?: time();
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
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- DataTables CSS - VERSIÓN COMPATIBLE -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
  
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-docente.css">
</head>

<body>
  <div class="app hide-right" id="appGrid">
    <!-- LEFT SIDEBAR -->
    
    <?php 
      include_once __DIR__ . '/../../layouts/sidebar_docente.php'
    ?>


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
        <?php
          include_once BASE_PATH . '/app/views/layouts/boton_perfil_solo.php';
        ?>
      
      </div>

      <section class="kpis">
        <div class="kpi">
          <div class="icon"><i class="ri-user-3-line"></i></div>
          <div>
            <small>Estudiantes</small>
            <strong><?= (int)($estadisticas['total_estudiantes'] ?? 0) ?></strong>
          </div>
        </div>
        <div class="kpi">
          <div class="icon"><i class="ri-user-2-line"></i></div>
          <div>
            <small>Acudientes</small>
            <strong><?= (int)($estadisticas['total_acudientes'] ?? 0) ?></strong>
          </div>
        </div>
        <div class="kpi">
          <div class="icon"><i class="ri-user-star-line"></i></div>
          <div>
            <small>Cursos</small>
            <strong><?= (int)($estadisticas['total_cursos'] ?? 0) ?></strong>
          </div>
        </div>
        <div class="kpi">
          <div class="icon"><i class="ri-calendar-2-line"></i></div>
          <div>
            <small>Eventos</small>
            <strong><?= (int)($estadisticas['total_eventos'] ?? 0) ?></strong>
          </div>
        </div>
      </section>

      <!-- DATATABLE: Cursos Asignados -->
      <section class="datatable-card">
        <h3>Mis Cursos Asignados</h3>

        <div class="table-responsive">
          <table id="coursesTable" class="table table-dark table-hover align-middle" style="width:100%">
            <thead>
              <tr>
                <th>Curso</th>
                <th>Grado</th>
                <th>N° Estudiantes</th>
                <th>Horario</th>
                <th class="text-center" style="width:60px">Ver</th>
              </tr>
            </thead>
         <tbody>
  <?php if(!empty($datos)): ?>
    <?php foreach($datos as $curso): ?>
      <tr>
        <td>
          <strong><?= htmlspecialchars($curso['curso'], ENT_QUOTES, 'UTF-8') ?></strong>
        </td>
        <td>
          <strong><?= htmlspecialchars($curso['grado'], ENT_QUOTES, 'UTF-8') ?></strong>
        </td>
        <td>
          <span class="badge bg-info"><?= $curso['total_estudiantes'] ?> estudiantes</span>
        </td>
        <td>
          <small class="d-block"><?= htmlspecialchars($curso['jornada'] ?? 'Sin jornada', ENT_QUOTES, 'UTF-8') ?></small>
          <small class="text-muted">Año académico actual</small>
        </td>
        <td class="text-center">
          <a href="<?= BASE_URL ?>/docente/detalle-curso?id=<?= urlencode((string)$curso['id']) ?>" class="btn btn-sm btn-outline-light" title="Ver detalles">
            <i class="ri-eye-line"></i>
          </a>
        </td>
      </tr>
    <?php endforeach; ?>
  <?php else: ?>
    <tr>
      <td colspan="5" class="text-center text-muted">No hay cursos registrados</td>
    </tr>
  <?php endif; ?>
</tbody>
          </table class="table datatable">
        </div>
      </section>

      <!-- DATATABLE SECTION -->
      <!-- DATATABLE: Estudiantes con bajo rendimiento -->
      <section class="datatable-card">
        <h3>Estudiantes con bajo rendimiento</h3>

        <div class="table-responsive">
          <table id="studentsTable" class="table table-dark table-hover align-middle" style="width:100%">
            <thead>
              <tr>
                <th>Nombres</th>
                <th>N° Documento</th>
                <th style="min-width:140px">Curso</th>
                <th>Asignaturas</th>
                <th class="text-center" style="width:60px">Imprimir</th>
                <th class="text-center" style="width:60px">Opc.</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($estudiantesBajoRendimiento)): ?>
                <?php foreach ($estudiantesBajoRendimiento as $estudiante): ?>
                <tr>
                  <td><?= htmlspecialchars(trim(($estudiante['nombres'] ?? '') . ' ' . ($estudiante['apellidos'] ?? '')), ENT_QUOTES, 'UTF-8') ?></td>
                  <td><a href="#">ID <?= htmlspecialchars((string)($estudiante['documento'] ?? 'N/A'), ENT_QUOTES, 'UTF-8') ?></a></td>
                  <td>
                    <small class="d-block text-muted">Clase</small>
                    <strong><?= htmlspecialchars((string)($estudiante['grado'] ?? ''), ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars((string)($estudiante['curso'] ?? ''), ENT_QUOTES, 'UTF-8') ?></strong>
                  </td>
                  <td>
                    <?= htmlspecialchars((string)($estudiante['asignatura'] ?? 'Sin asignatura'), ENT_QUOTES, 'UTF-8') ?>
                    <?php if (isset($estudiante['promedio'])): ?>
                      <small class="d-block text-danger">Promedio: <?= number_format((float)$estudiante['promedio'], 2) ?></small>
                    <?php endif; ?>
                  </td>
                  <td class="text-center">
                    <button class="btn btn-sm btn-outline-light" title="Imprimir">
                      <i class="ri-printer-line"></i>
                    </button>
                  </td>
                  <td class="text-center">
                    <button class="btn btn-sm btn-outline-light" title="Más opciones">
                      <i class="ri-more-2-fill"></i>
                    </button>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="text-center text-muted">No hay estudiantes en bajo rendimiento para tus cursos.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>

      <!-- CALENDAR SECTION -->
      <section class="calendar-card">
        <div class="calendar-header">
          <h3 id="calendarCurrentMonth">Calendario Académico</h3>
          <div class="calendar-nav">
            <button id="prevMonth"><i class="ri-arrow-left-s-line"></i></button>
            <button id="nextMonth"><i class="ri-arrow-right-s-line"></i></button>
          </div>
        </div>
        <div id="calendarContainer">
          <div class="calendar-grid" id="calendarGrid">
            <!-- Calendar will be generated by JavaScript -->
          </div>
        </div>
      </section>

    </main>

    <!-- MODAL EVENTOS DEL DIA -->
    <div class="modal fade" id="dayEventsModal" tabindex="-1" aria-labelledby="dayEventsModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content calendar-day-modal-content">
          <div class="modal-header calendar-day-modal-header">
            <h5 class="modal-title" id="dayEventsModalLabel">Eventos del día</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body" id="dayEventsModalBody">
          </div>
        </div>
      </div>
    </div>

  
  </div>

  <!-- Bootstrap and DataTables Scripts -->


<!-- Bootstrap and DataTables Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
  window.docenteCalendarEvents = <?= json_encode($eventosCalendarioDocente, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>
<script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-docente.js?v=<?= $mainDocenteJsVersion ?>"></script>

</body>

</html>