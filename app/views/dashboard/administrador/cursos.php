<?php 
  require_once BASE_PATH . '/app/helpers/session_administrador.php';
  require_once BASE_PATH . '/app/controllers/administrador/curso.php';

  // LLAMAMOS LA FUNCION
  $datos = mostrarCursos();
  
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
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-admin.css">
 
</head>
<body>
  <div class="app" id="appGrid">
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

        <div class="div"></div>

        <button class="btn-agregar-estudiante" onclick="window.location.href='administrador/registrar-curso'">
        Agregar Curso
        </button>
        
        <div class="search">
          <i class="ri-search-2-line"></i>
          <input type="text" placeholder="Buscar curso, profesor o materia...">
        </div>
        <button class="toggle-btn" id="toggleRight" title="Mostrar/Ocultar panel derecho">
          <i class="ri-layout-right-2-line"></i>
        </button>
      </div>

      <!-- KPI CARDS -->
      <div class="kpis">
        <div class="kpi">
          <div class="icon"><i class="ri-book-3-line"></i></div>
          <div>
            <small>Total Cursos</small>
            <strong>24</strong>
          </div>
        </div>
        <div class="kpi">
          <div class="icon"><i class="ri-group-line"></i></div>
          <div>
            <small>Total Estudiantes</small>
            <strong>842</strong>
          </div>
        </div>
        <div class="kpi">
          <div class="icon"><i class="ri-user-star-line"></i></div>
          <div>
            <small>Profesores Activos</small>
            <strong>38</strong>
          </div>
        </div>
        <div class="kpi">
          <div class="icon"><i class="ri-alarm-warning-line"></i></div>
          <div>
            <small>Alertas Activas</small>
            <strong>12</strong>
          </div>
        </div>
      </div>

      <!-- FILTER SECTION -->
     

      <!-- COURSES GRID -->
      <section class="courses-section">
        <div class="courses-header">
          <h3>Cursos Activos (24)</h3>
          <div class="view-toggle">
            <button class="view-btn active" data-view="grid"><i class="ri-grid-line"></i></button>
            <button class="view-btn" data-view="list"><i class="ri-list-check"></i></button>
          </div>
        </div>

        <div class="courses-grid">
          <!-- Course Card 1 -->
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
                <span>35 estudiantes</span> <span class="cupo">Cupo Maximo: <?= $curso['cupo_maximo'] ?> <i class="ri-group-line"></i></span>
              </div>
              <div class="stat">
                <i class="ri-user-line"></i>
                <span>Prof. <?= $curso['nombres_docente']. ' ' .$curso['apellidos_docente'] ?></span>
              </div>
            </div>

            

            <div class="course-alerts">
              <div class="alert-item">
                <i class="ri-error-warning-line"></i>
                <span>3 estudiantes en riesgo</span>
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
      </section>

    </main>

    <!-- RIGHT SIDEBAR -->
    <!-- AQUI VA EL INCLUDE DEL SIDEBAR RIGHT -->
     <?php 
        include_once __DIR__ . '/../../layouts/sidebar_right_coordinador.php'
     ?>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-estudiante.js"></script>
  <script>
    // Toggle view grid/list
    document.querySelectorAll('.view-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        const view = this.dataset.view;
        const grid = document.querySelector('.courses-grid');
        if (view === 'list') {
          grid.style.gridTemplateColumns = '1fr';
        } else {
          grid.style.gridTemplateColumns = 'repeat(auto-fill, minmax(340px, 1fr))';
        }
      });
    });
  </script>
</body>

</html>