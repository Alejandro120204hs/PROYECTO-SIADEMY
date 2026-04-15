<?php 
  require_once BASE_PATH . '/app/helpers/session_administrador.php';

   //ENLAZAMOS LA DEPENDENCIA DEL CONTROLADOR QUE TIENE LA FUNCION PARA MOSTRAR LOS DATOS
    require_once BASE_PATH . '/app/controllers/perfil.php';
    
    // IMPORTAMOS LOS MODELOS NECESARIOS
    require_once BASE_PATH . '/app/models/administradores/estudiante.php';
    require_once BASE_PATH . '/app/models/administradores/acudiente.php';
    require_once BASE_PATH . '/app/models/administradores/docente.php';
    require_once BASE_PATH . '/app/models/administradores/eventos.php';
    require_once BASE_PATH . '/app/models/administradores/cursos.php';
    require_once BASE_PATH . '/app/models/administradores/asignatura.php';
    
    // LLAMAMOS EL ID QUE VIENE ATRAVEZ DEL METODO GET
    $id = $_SESSION['user']['id'];
    // LLAMAMOS LA FUNCION ESPECIFICA DEL CONTROLADOR
    $usuario = mostrarPerfil($id);

    // OBTENEMOS LA INSTITUCIÓN DEL ADMIN
    $id_institucion = $_SESSION['user']['id_institucion'];

    // INSTANCIAMOS LOS MODELOS
    $objEstudiante = new Estudiante();
    $objAcudiente = new Acudiente();
    $objDocente = new Docente();
    $objEvento = new Evento();
    $objCurso = new Curso();
    $objAsignatura = new Asignatura();

    // CONTAMOS LOS REGISTROS POR INSTITUCIÓN
    $totalEstudiantes = $objEstudiante->contar($id_institucion);
    $totalAcudientes = $objAcudiente->contar($id_institucion);
    $totalProfesores = $objDocente->contar($id_institucion);
    $totalEventos = $objEvento->contar($id_institucion);
    $totalCursos = $objCurso->contar($id_institucion);
    $totalAsignaturas = $objAsignatura->contar($id_institucion);

    // DATOS REALES PARA GRÁFICO Y CALENDARIO DEL DASHBOARD
    $eventosInstitucion = $objEvento->listar($id_institucion);
    $mesesAbreviados = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    $anioActual = (int)date('Y');
    $anioAnterior = $anioActual - 1;
    $serieAnioActual = array_fill(0, 12, 0);
    $serieAnioAnterior = array_fill(0, 12, 0);

    $hoy = new DateTimeImmutable('today');
    $inicioSemanaActual = $hoy->modify('monday this week');
    $finSemanaActual = $hoy->modify('sunday this week');
    $inicioSemanaAnterior = $inicioSemanaActual->modify('-7 days');
    $finSemanaAnterior = $inicioSemanaActual->modify('-1 day');
    $totalSemanaActual = 0;
    $totalSemanaAnterior = 0;
    $eventosCalendario = [];

    foreach ($eventosInstitucion as $evento) {
      $fechaCruda = (string)($evento['fecha_evento'] ?? '');
      if ($fechaCruda === '') {
        continue;
      }

      $fechaEvento = DateTimeImmutable::createFromFormat('Y-m-d', substr($fechaCruda, 0, 10));
      if (!$fechaEvento) {
        continue;
      }

      $anioEvento = (int)$fechaEvento->format('Y');
      $mesEvento = (int)$fechaEvento->format('n') - 1;

      if ($anioEvento === $anioActual && isset($serieAnioActual[$mesEvento])) {
        $serieAnioActual[$mesEvento]++;
      }

      if ($anioEvento === $anioAnterior && isset($serieAnioAnterior[$mesEvento])) {
        $serieAnioAnterior[$mesEvento]++;
      }

      if ($fechaEvento >= $inicioSemanaActual && $fechaEvento <= $finSemanaActual) {
        $totalSemanaActual++;
      }

      if ($fechaEvento >= $inicioSemanaAnterior && $fechaEvento <= $finSemanaAnterior) {
        $totalSemanaAnterior++;
      }

      $eventosCalendario[] = [
        'id' => (int)($evento['id'] ?? 0),
        'title' => (string)($evento['nombre_evento'] ?? 'Evento académico'),
        'date' => $fechaEvento->format('Y-m-d'),
        'timeStart' => !empty($evento['hora_inicio']) ? substr((string)$evento['hora_inicio'], 0, 5) : '',
        'timeEnd' => !empty($evento['hora_fin']) ? substr((string)$evento['hora_fin'], 0, 5) : '',
        'type' => (string)($evento['tipo_evento'] ?? 'evento'),
        'location' => (string)($evento['ubicacion'] ?? '')
      ];
    }

    usort($eventosCalendario, function ($a, $b) {
      $claveA = ($a['date'] ?? '') . ' ' . ($a['timeStart'] ?? '');
      $claveB = ($b['date'] ?? '') . ' ' . ($b['timeStart'] ?? '');
      return strcmp($claveA, $claveB);
    });

    $dashboardData = [
      'chart' => [
        'labels' => $mesesAbreviados,
        'currentYear' => $anioActual,
        'previousYear' => $anioAnterior,
        'currentSeries' => $serieAnioActual,
        'previousSeries' => $serieAnioAnterior
      ],
      'totals' => [
        'currentWeek' => $totalSemanaActual,
        'previousWeek' => $totalSemanaAnterior
      ],
      'calendar' => [
        'events' => $eventosCalendario
      ]
    ];

    $adminCssVersion = @filemtime(BASE_PATH . '/public/assets/dashboard/css/styles-admin.css') ?: time();
    $mainAdminJsVersion = @filemtime(BASE_PATH . '/public/assets/dashboard/js/main-admin.js') ?: time();
?>

<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Panel Principal</title>
  <?php
    include_once __DIR__ . '/../../layouts/header_coordinador.php'
  ?>
<link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-admin.css?v=<?= $adminCssVersion ?>">

</head>

<body>
  <div class="app hide-right" id="appGrid">
    <!-- LEFT SIDEBAR -->
    <!-- AQUI VA EL INCLUDE DEL SIDEBAR LEFT -->
     <?php
      include_once __DIR__ . '/../../layouts/sidebar_coordinador.php';
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
          include_once BASE_PATH . '/app/views/layouts/boton_perfil_solo.php'
        ?>
      </div>

      <section class="kpis">
        <div class="kpi">
          <div class="icon"><i class="ri-user-3-line"></i></div>
          <div>
            <small>Estudiantes</small>
            <strong><?php echo $totalEstudiantes; ?></strong>
          </div>
        </div>
        <div class="kpi">
          <div class="icon"><i class="ri-user-2-line"></i></div>
          <div>
            <small>Acudientes</small>
            <strong><?php echo $totalAcudientes; ?></strong>
          </div>
        </div>
        <div class="kpi">
          <div class="icon"><i class="ri-user-star-line"></i></div>
          <div>
            <small>Profesores</small>
            <strong><?php echo $totalProfesores; ?></strong>
          </div>
        </div>
        <div class="kpi">
          <div class="icon"><i class="ri-calendar-2-line"></i></div>
          <div>
            <small>Eventos</small>
            <strong><?php echo $totalEventos; ?></strong>
          </div>
        </div>
        <div class="kpi">
          <div class="icon"><i class="ri-book-3-line"></i></div>
          <div>
            <small>Cursos</small>
            <strong><?php echo $totalCursos; ?></strong>
          </div>
        </div>
        <div class="kpi">
          <div class="icon"><i class="ri-article-line"></i></div>
          <div>
            <small>Asignaturas</small>
            <strong><?php echo $totalAsignaturas; ?></strong>
          </div>
        </div>
      </section>

      <!-- LINE CHART SECTION -->
      <section class="card">
        <div style="display:flex; align-items:center; gap:12px">
          <h3>Rendimiento escolar</h3>
          <div class="legend">
            <span class="pill"><span class="dot week"></span> Esta semana <b id="weekTotal"><?= number_format((int)$totalSemanaActual, 0, ',', '.') ?></b></span>
            <span class="pill"><span class="dot last"></span> La semana pasada <b id="lastWeekTotal"><?= number_format((int)$totalSemanaAnterior, 0, ',', '.') ?></b></span>
          </div>
        </div>
        <canvas id="lineChart"></canvas>
      </section>

  

      <!-- DATATABLE SECTION -->
      <!-- DATATABLE: Estudiantes con bajo rendimiento -->
    


    </main>

    
    
  </div>

   <!-- Bootstrap and DataTables Scripts -->
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

  <script>
    window.adminDashboardData = <?= json_encode($dashboardData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  </script>


  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-admin.js?v=<?= $mainAdminJsVersion ?>"></script>
</body>


</html>

 