<?php 
  require_once BASE_PATH . '/app/helpers/session_administrador.php';
  require_once BASE_PATH . '/app/controllers/administrador/view_data.php';

  extract(obtenerDataVistaAdminDashboard(), EXTR_SKIP);
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
  <div class="app hide-right" id="appGrid" data-dashboard='<?= htmlspecialchars(json_encode($dashboardData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, "UTF-8") ?>'>
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

  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-admin.js?v=<?= $mainAdminJsVersion ?>"></script>
</body>


</html>

 