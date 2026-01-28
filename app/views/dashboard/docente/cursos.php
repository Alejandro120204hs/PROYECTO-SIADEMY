<?php 
  // require_once BASE_PATH . '/app/helpers/session_administrador.php';
   // ENLAZAMOS LA DEPENDENCIA, EN ESTE CASO EL CONTROLADOR QUE TIENE LA FUNCION DE COSULTAR LOS DATOS
  require_once BASE_PATH . '/app/controllers/docente/curso.php';

  // LLAMAMOS LA FUNCION ESPECIFICA QUE EXISTE EN DICHO CONTROLADOR
  $datos = mostrarCursos();
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Mis Cursos</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-docente.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/docente/cursos.css">
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
          <div class="title">Mis Cursos</div>
        </div>
       
       
      </div>

      <!-- TEACHER INFO BAR -->
      <div class="teacher-info-bar">
        <div class="teacher-profile">
          
          <div>
            <strong>Wilson Marroquín</strong>
            <small>Profesor de Matemáticas</small>
          </div>
        </div>
        <div class="teacher-stats">
          <div class="stat-item">
            <i class="ri-book-line"></i>
            <div>
              <strong>6</strong>
              <small>Cursos activos</small>
            </div>
          </div>
          <div class="stat-item">
            <i class="ri-user-line"></i>
            <div>
              <strong>177</strong>
              <small>Estudiantes</small>
            </div>
          </div>
          <div class="stat-item">
            <i class="ri-time-line"></i>
            <div>
              <strong>24</strong>
              <small>Horas semanales</small>
            </div>
          </div>
        </div>
      </div>

      <!-- FILTER SECTION -->
      <div class="cursos-filter-section">
        <div class="cursos-filter-select-wrapper">
          <i class="ri-filter-3-line cursos-filter-icon"></i>
          <select id="courseFilter" class="cursos-filter-select">
            <option value="all" selected>Todos los grados</option>
            <?php 
            // Obtener grados únicos para evitar duplicados
            $gradosUnicos = [];
            if (!empty($datos)):
              foreach ($datos as $curso):
                if (!in_array($curso['grado'], $gradosUnicos)):
                  $gradosUnicos[] = $curso['grado'];
            ?>
            <option value="<?= $curso['grado'] ?>"><?= $curso['grado'] ?>°</option>
            <?php 
                endif;
              endforeach; 
            else: 
            ?>
            <option disabled>No hay cursos registrados</option>
            <?php endif; ?> 
           
          </select>
        </div>
        <div class="cursos-filter-search">
          <i class="ri-search-line"></i>
          <input type="text" id="searchInput" placeholder="Buscar por grado o curso...">
        </div>
      </div>

      <!-- COURSES GRID -->
      <section class="cursos-grid">
        <?php if(!empty($datos)): ?>
          <?php foreach($datos as $curso): ?>
        <!-- Course Card -->
        <div class="curso-card" data-grado="<?= $curso['grado'] ?>">
          
          <div class="curso-card-header">
           <div class="curso-icon" style="background: linear-gradient(135deg, #4f46e5, #6366f1)">
              <?= $curso['grado'] ?>°
            </div>
            <div class="curso-badge-jornada jornada-manana">
              <?= $curso['jornada'] ?>
            </div>
          </div>
          
          <div class="curso-card-body">
            <div class="curso-info-principal">
              <h3 class="curso-nombre">Curso - <?= $curso['curso'] ?></h3>
              <span class="curso-codigo" style="display:none;"><?= $curso['curso'] ?></span>
            </div>

            <div class="curso-meta-grid">
              <div class="curso-meta-item">
                <i class="ri-user-line"></i>
                <div>
                  <strong><?= $curso['total_estudiantes'] ?></strong>
                  <small>Estudiantes</small>
                </div>
              </div>
              <div class="curso-meta-item">
                <i class="ri-time-line"></i>
                <div>
                  <strong>Lun-Mié-Vie</strong>
                  <small>8:00 - 9:30 AM</small>
                </div>
              </div>
            </div>

            <div class="curso-ubicacion">
             
              <span><?= $curso['nombre_asignatura'] ?></span>
            </div>

            <div class="curso-progress-section">
              <div class="curso-progress-header">
                <small>Progreso del período</small>
                <strong class="curso-progress-percent">68%</strong>
              </div>
              <div class="curso-progress-bar">
                <div class="curso-progress-fill" style="width: 68%;"></div>
              </div>
            </div>
          </div>

          <div class="curso-card-footer">
            <a href="<?= BASE_URL ?>/docente/detalle-curso?id=<?= $curso['id'] ?>" 
              class="btn-curso-primary">
              <i class="ri-eye-line"></i>
              Ver Detalles
            </button>
        <a href="<?= BASE_URL ?>/docente/actividades?id_curso=<?= $curso['id'] ?>" 
          class="btn-curso-secondary">
          <i class="ri-clipboard-line"></i>
          Actividades
        </a>

          </div>
         
        </div>

        <?php endforeach; ?>
              <?php else: ?>

                  <h3>No hay cursos registrados</h3>
                
              <?php endif; ?>

      </section>

      <!-- UPCOMING CLASSES SECTION -->
      <section class="datatable-card">
        <h3>Próximas Clases de Hoy</h3>
        <div class="upcoming-classes">
          <div class="class-item">
            <div class="class-time">
              <i class="ri-time-line"></i>
              <div>
                <strong>8:00 AM</strong>
                <small>90 min</small>
              </div>
            </div>
            <div class="class-info">
              <h4>Matemáticas Avanzadas</h4>
              <p>Grado 10° A • Salón 203</p>
            </div>
            <div class="class-status">
              <span class="status-badge next">Próxima</span>
            </div>
            <button class="btn-class-action">
              <i class="ri-arrow-right-line"></i>
            </button>
          </div>

          <div class="class-item">
            <div class="class-time">
              <i class="ri-time-line"></i>
              <div>
                <strong>10:00 AM</strong>
                <small>60 min</small>
              </div>
            </div>
            <div class="class-info">
              <h4>Geometría Analítica</h4>
              <p>Grado 9° A • Salón 105</p>
            </div>
            <div class="class-status">
              <span class="status-badge pending">Pendiente</span>
            </div>
            <button class="btn-class-action">
              <i class="ri-arrow-right-line"></i>
            </button>
          </div>

          <div class="class-item">
            <div class="class-time">
              <i class="ri-time-line"></i>
              <div>
                <strong>2:00 PM</strong>
                <small>90 min</small>
              </div>
            </div>
            <div class="class-info">
              <h4>Cálculo Diferencial</h4>
              <p>Grado 11° A • Salón 301</p>
            </div>
            <div class="class-status">
              <span class="status-badge pending">Pendiente</span>
            </div>
            <button class="btn-class-action">
              <i class="ri-arrow-right-line"></i>
            </button>
          </div>
        </div>
      </section>

    </main>

   
  </div>

  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/docente/cursos.js"></script>
</body>

</html>