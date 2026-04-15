<?php
  require_once BASE_PATH . '/app/controllers/perfil.php';
  $id = $_SESSION['user']['id'] ?? 0;
  $usuario = mostrarPerfil($id);
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
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-estudiante.css">
</head>

<body>
  <div class="app" id="appGrid">
    <!-- LEFT SIDEBAR -->
    <?php
    include_once __DIR__ . '/../../layouts/sidebar_estudiante.php'
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
      <!-- DATATABLE SECTION -->
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
                <th>Periodo</th>
                <th class="text-center" style="width:100px">Estado</th>
                <th class="text-center" style="width:60px">Ver</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  <strong>Matemáticas</strong>
                  <small class="d-block text-muted">Álgebra y Geometría</small>
                </td>
                <td>Prof. Carlos Méndez</td>
                <td>
                  <span class="badge bg-danger">2.8</span>
                </td>
                <td>Segundo Periodo</td>
                <td class="text-center">
                  <span class="badge bg-warning">En Riesgo</span>
                </td>
                <td class="text-center">
                  <button class="btn btn-sm btn-outline-light" title="Ver detalles">
                    <i class="ri-eye-line"></i>
                  </button>
                </td>
              </tr>

              <tr>
                <td>
                  <strong>Física</strong>
                  <small class="d-block text-muted">Mecánica Clásica</small>
                </td>
                <td>Prof. Ana Rodríguez</td>
                <td>
                  <span class="badge bg-danger">2.5</span>
                </td>
                <td>Segundo Periodo</td>
                <td class="text-center">
                  <span class="badge bg-danger">Crítico</span>
                </td>
                <td class="text-center">
                  <button class="btn btn-sm btn-outline-light" title="Ver detalles">
                    <i class="ri-eye-line"></i>
                  </button>
                </td>
              </tr>

              <tr>
                <td>
                  <strong>Química</strong>
                  <small class="d-block text-muted">Química Orgánica</small>
                </td>
                <td>Prof. Luis Torres</td>
                <td>
                  <span class="badge bg-warning text-dark">3.0</span>
                </td>
                <td>Segundo Periodo</td>
                <td class="text-center">
                  <span class="badge bg-warning">En Riesgo</span>
                </td>
                <td class="text-center">
                  <button class="btn btn-sm btn-outline-light" title="Ver detalles">
                    <i class="ri-eye-line"></i>
                  </button>
                </td>
              </tr>

              <tr>
                <td>
                  <strong>Inglés</strong>
                  <small class="d-block text-muted">Nivel Intermedio</small>
                </td>
                <td>Prof. Patricia Gómez</td>
                <td>
                  <span class="badge bg-warning text-dark">2.9</span>
                </td>
                <td>Segundo Periodo</td>
                <td class="text-center">
                  <span class="badge bg-warning">En Riesgo</span>
                </td>
                <td class="text-center">
                  <button class="btn btn-sm btn-outline-light" title="Ver detalles">
                    <i class="ri-eye-line"></i>
                  </button>
                </td>
              </tr>

              <tr>
                <td>
                  <strong>Programación</strong>
                  <small class="d-block text-muted">Java Avanzado</small>
                </td>
                <td>Prof. Diego Álvarez</td>
                <td>
                  <span class="badge bg-danger">2.7</span>
                </td>
                <td>Segundo Periodo</td>
                <td class="text-center">
                  <span class="badge bg-danger">Crítico</span>
                </td>
                <td class="text-center">
                  <button class="btn btn-sm btn-outline-light" title="Ver detalles">
                    <i class="ri-eye-line"></i>
                  </button>
                </td>
              </tr>
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
            <!-- Calendar will be generated by JavaScript -->
          </div>
        </div>
      </section>

    </main>

    <!-- RIGHT SIDEBAR -->

  </div>

  <!-- Bootstrap and DataTables Scripts -->

  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-estudiante.js"></script>
</body>

</html>