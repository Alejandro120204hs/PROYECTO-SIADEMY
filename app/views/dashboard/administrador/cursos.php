<?php 
  require_once BASE_PATH . '/app/helpers/session_administrador.php';
  require_once BASE_PATH . '/app/controllers/administrador/view_data.php';

  extract(obtenerDataVistaAdminCursos(), EXTR_SKIP);
  
?>

<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Gestión de Cursos</title>
 <?php 
    include_once __DIR__ . '/../../layouts/header_coordinador.php'
 ?>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-admin.css?v=<?= $adminCssVersion ?>">
 
</head>
<body class="admin-cursos-page">
  <div class="app hide-right" id="appGrid">
    <!-- LEFT SIDEBAR -->
    <?php 
      include_once __DIR__ . '/../../layouts/sidebar_coordinador.php'
    ?>

    <!-- MAIN -->
    <main class="main">
      <div class="topbar">
        <div class="topbar-left">
          <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
            <i class="ri-menu-2-line"></i>
          </button>
          <div class="title cursos">Gestión de Cursos</div>
        </div>
        
        <div class="search">
          <i class="ri-search-2-line"></i>
          <input type="text" placeholder="Buscar curso, profesor o materia...">
        </div>

        <div class="topbar-actions">
          <button class="btn-agregar-estudiante" onclick="window.location.href='administrador/registrar-curso'">
            <i class="ri-add-line"></i> Agregar Curso
          </button>
        </div>

        <?php
          include_once BASE_PATH . '/app/views/layouts/boton_perfil_solo.php'
        ?>
      </div>

      <!-- KPI CARDS -->
      <div class="kpis">
        <div class="kpi">
          <div class="icon"><i class="ri-book-3-line"></i></div>
          <div>
            <small>Total Cursos</small>
            <strong><?php echo $totalCursos; ?></strong>
          </div>
        </div>
        <div class="kpi">
          <div class="icon"><i class="ri-group-line"></i></div>
          <div>
            <small>Total Estudiantes</small>
            <strong><?php echo $totalEstudiantes; ?></strong>
          </div>
        </div>
        <div class="kpi">
          <div class="icon"><i class="ri-user-star-line"></i></div>
          <div>
            <small>Profesores Activos</small>
            <strong><?php echo $totalProfesores; ?></strong>
          </div>
        </div>
        <div class="kpi">
          <div class="icon"><i class="ri-alarm-warning-line"></i></div>
          <div>
            <small>Alertas Activas</small>
            <strong>0</strong>
          </div>
        </div>
      </div>

      <!-- FILTER SECTION -->
     

      <!-- COURSES GRID -->
      <section class="courses-section">
        <div class="courses-header">
          <h3>Cursos Activos (<?= $totalCursos ?>)</h3>
          <div class="view-toggle">
            <button class="view-btn active" data-view="grid"><i class="ri-grid-line"></i></button>
            <button class="view-btn" data-view="list"><i class="ri-list-check"></i></button>
          </div>
        </div>

        <div class="courses-grid" id="cursosGrid">
           <?php if(!empty($datos)): ?>
            <?php foreach($datos as $curso): ?>
          <div class="course-card">
            
            <div class="course-header">
              <div class="course-badge" style="background: #4f46e5;"><?= $curso['grado'] ?>°</div>
              <div class="course-status status-success"><?= $curso['estado'] ?></div>
            </div>
            <h4>Curso - <?= $curso['curso'] ?></h4>
            <p class="course-subtitle"><?= $curso['nivel_academico'] ?></p>
            
            <div class="course-stats">
              <div class="stat">
                <i class="ri-group-line"></i>
                <span><?= (int)$curso['total_estudiantes'] ?> estudiantes</span> <span class="cupo">Cupo Maximo: <?= $curso['cupo_maximo'] ?> <i class="ri-group-line"></i></span>
              </div>
              <div class="stat">
                <i class="ri-user-line"></i>
                <span>Prof. <?= $curso['nombres_docente']. ' ' .$curso['apellidos_docente'] ?></span>
              </div>
            </div>

            <div class="course-actions">
              <button class="btn-secondary" onclick="window.location.href='<?= BASE_URL ?>/administrador/detalle-curso?id=<?= $curso['id'] ?>'"><i class="bi bi-eye"></i></button>
              <button class="btn-secondary"><a href="<?= BASE_URL ?>/administrador/editar-curso?id=<?= $curso['id'] ?>"><i class="bi bi-pencil-square"></i></a></button>
              <button class="btn-secondary"><a href="<?= BASE_URL ?>/administrador/eliminar-curso?accion=eliminar&id=<?= $curso['id'] ?>"><i class="bi bi-trash3-fill"></i></a></button>
            </div>
          </div>
          <?php endforeach; ?>
              <?php else: ?>
                <h2>No hay cursos registrados</h2>
              <?php endif; ?>
        </div>

        <!-- TABLE VIEW -->
        <div class="datatable-card table-scroll-x" id="cursosTabla" style="display:none;">
          <table id="tablaCursos" class="table table-dark table-hover table-scroll-content">
            <thead>
              <tr>
                <th>Grado</th>
                <th>Curso</th>
                <th>Nivel Académico</th>
                <th>Docente</th>
                <th>Estudiantes</th>
                <th>Cupo Máximo</th>
                <th>Estado</th>
                <th width="130">Acción</th>
              </tr>
            </thead>
            <tbody>
              <?php if(!empty($datos)): ?>
              <?php foreach($datos as $curso): ?>
              <tr>
                <td><?= $curso['grado'] ?>°</td>
                <td><?= $curso['curso'] ?></td>
                <td><?= $curso['nivel_academico'] ?></td>
                <td><?= $curso['nombres_docente'] . ' ' . $curso['apellidos_docente'] ?></td>
                <td><?= (int)$curso['total_estudiantes'] ?></td>
                <td><?= $curso['cupo_maximo'] ?></td>
                <td><?= $curso['estado'] ?></td>
                <td class="acciones">
                  <a class="btn-action" href="<?= BASE_URL ?>/administrador/detalle-curso?id=<?= $curso['id'] ?>">Ver</a>
                  <a class="btn-action" href="<?= BASE_URL ?>/administrador/editar-curso?id=<?= $curso['id'] ?>">Editar</a>
                  <a class="btn-action" href="<?= BASE_URL ?>/administrador/eliminar-curso?accion=eliminar&id=<?= $curso['id'] ?>"><i class="bi bi-trash3-fill"></i></a>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php else: ?>
              <tr><td colspan="8">No hay cursos registrados</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

      </section>

    </main>

    
  </div>

  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-admin.js?v=<?= $mainAdminJsVersion ?>"></script>
  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-estudiante.js"></script>
  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/administrador/cursos.js"></script>
</body>

</html>