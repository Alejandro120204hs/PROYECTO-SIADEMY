<?php 
  require_once BASE_PATH . '/app/helpers/session_administrador.php';
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
          <div class="title">Gestión de Cursos</div>
        </div>
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
          <div class="course-card">
            <div class="course-header">
              <div class="course-badge" style="background: #4f46e5;">7A</div>
              <div class="course-status status-success">Activo</div>
            </div>
            <h4>Matemáticas Avanzadas</h4>
            <p class="course-subtitle">Álgebra y Geometría</p>
            
            <div class="course-stats">
              <div class="stat">
                <i class="ri-group-line"></i>
                <span>35 estudiantes</span>
              </div>
              <div class="stat">
                <i class="ri-user-line"></i>
                <span>Prof. Carlos Méndez</span>
              </div>
            </div>

            <div class="course-performance">
              <div class="performance-label">
                <span>Promedio General</span>
                <strong class="grade-good">4.2</strong>
              </div>
              <div class="progress-bar">
                <div class="progress-fill" style="width: 84%; background: #10b981;"></div>
              </div>
            </div>

            <div class="course-alerts">
              <div class="alert-item">
                <i class="ri-error-warning-line"></i>
                <span>3 estudiantes en riesgo</span>
              </div>
            </div>

            <div class="course-actions">
              <button class="btn-secondary"><i class="ri-eye-line"></i> Ver detalles</button>
              <button class="btn-icon" title="Más opciones"><i class="ri-more-2-line"></i></button>
            </div>
          </div>

          <!-- Course Card 2 -->
          <div class="course-card">
            <div class="course-header">
              <div class="course-badge" style="background: #7c3aed;">7B</div>
              <div class="course-status status-warning">En Riesgo</div>
            </div>
            <h4>Física Mecánica</h4>
            <p class="course-subtitle">Cinemática y Dinámica</p>
            
            <div class="course-stats">
              <div class="stat">
                <i class="ri-group-line"></i>
                <span>32 estudiantes</span>
              </div>
              <div class="stat">
                <i class="ri-user-line"></i>
                <span>Prof. Ana Rodríguez</span>
              </div>
            </div>

            <div class="course-performance">
              <div class="performance-label">
                <span>Promedio General</span>
                <strong class="grade-warning">3.1</strong>
              </div>
              <div class="progress-bar">
                <div class="progress-fill" style="width: 62%; background: #f59e0b;"></div>
              </div>
            </div>

            <div class="course-alerts">
              <div class="alert-item alert-critical">
                <i class="ri-alert-line"></i>
                <span>8 estudiantes en riesgo</span>
              </div>
            </div>

            <div class="course-actions">
              <button class="btn-secondary"><i class="ri-eye-line"></i> Ver detalles</button>
              <button class="btn-icon" title="Más opciones"><i class="ri-more-2-line"></i></button>
            </div>
          </div>

          <!-- Course Card 3 -->
          <div class="course-card">
            <div class="course-header">
              <div class="course-badge" style="background: #06b6d4;">8A</div>
              <div class="course-status status-success">Activo</div>
            </div>
            <h4>Química Orgánica</h4>
            <p class="course-subtitle">Compuestos y Reacciones</p>
            
            <div class="course-stats">
              <div class="stat">
                <i class="ri-group-line"></i>
                <span>38 estudiantes</span>
              </div>
              <div class="stat">
                <i class="ri-user-line"></i>
                <span>Prof. Luis Torres</span>
              </div>
            </div>

            <div class="course-performance">
              <div class="performance-label">
                <span>Promedio General</span>
                <strong class="grade-good">3.9</strong>
              </div>
              <div class="progress-bar">
                <div class="progress-fill" style="width: 78%; background: #10b981;"></div>
              </div>
            </div>

            <div class="course-alerts">
              <div class="alert-item">
                <i class="ri-error-warning-line"></i>
                <span>2 estudiantes en riesgo</span>
              </div>
            </div>

            <div class="course-actions">
              <button class="btn-secondary"><i class="ri-eye-line"></i> Ver detalles</button>
              <button class="btn-icon" title="Más opciones"><i class="ri-more-2-line"></i></button>
            </div>
          </div>

          <!-- Course Card 4 -->
          <div class="course-card">
            <div class="course-header">
              <div class="course-badge" style="background: #f59e0b;">9A</div>
              <div class="course-status status-danger">Crítico</div>
            </div>
            <h4>Programación Java</h4>
            <p class="course-subtitle">POO y Estructuras de Datos</p>
            
            <div class="course-stats">
              <div class="stat">
                <i class="ri-group-line"></i>
                <span>28 estudiantes</span>
              </div>
              <div class="stat">
                <i class="ri-user-line"></i>
                <span>Prof. Diego Álvarez</span>
              </div>
            </div>

            <div class="course-performance">
              <div class="performance-label">
                <span>Promedio General</span>
                <strong class="grade-danger">2.7</strong>
              </div>
              <div class="progress-bar">
                <div class="progress-fill" style="width: 54%; background: #ef4444;"></div>
              </div>
            </div>

            <div class="course-alerts">
              <div class="alert-item alert-critical">
                <i class="ri-alert-line"></i>
                <span>12 estudiantes en riesgo</span>
              </div>
            </div>

            <div class="course-actions">
              <button class="btn-secondary"><i class="ri-eye-line"></i> Ver detalles</button>
              <button class="btn-icon" title="Más opciones"><i class="ri-more-2-line"></i></button>
            </div>
          </div>

          <!-- Course Card 5 -->
          <div class="course-card">
            <div class="course-header">
              <div class="course-badge" style="background: #ec4899;">10A</div>
              <div class="course-status status-success">Activo</div>
            </div>
            <h4>Literatura Universal</h4>
            <p class="course-subtitle">Siglo XX y Contemporánea</p>
            
            <div class="course-stats">
              <div class="stat">
                <i class="ri-group-line"></i>
                <span>30 estudiantes</span>
              </div>
              <div class="stat">
                <i class="ri-user-line"></i>
                <span>Prof. María González</span>
              </div>
            </div>

            <div class="course-performance">
              <div class="performance-label">
                <span>Promedio General</span>
                <strong class="grade-excellent">4.5</strong>
              </div>
              <div class="progress-bar">
                <div class="progress-fill" style="width: 90%; background: #10b981;"></div>
              </div>
            </div>

            <div class="course-alerts">
              <div class="alert-item alert-success">
                <i class="ri-checkbox-circle-line"></i>
                <span>Sin alertas</span>
              </div>
            </div>

            <div class="course-actions">
              <button class="btn-secondary"><i class="ri-eye-line"></i> Ver detalles</button>
              <button class="btn-icon" title="Más opciones"><i class="ri-more-2-line"></i></button>
            </div>
          </div>

          <!-- Course Card 6 -->
          <div class="course-card">
            <div class="course-header">
              <div class="course-badge" style="background: #14b8a6;">11A</div>
              <div class="course-status status-success">Activo</div>
            </div>
            <h4>Inglés Avanzado</h4>
            <p class="course-subtitle">Nivel B2 - Upper Intermediate</p>
            
            <div class="course-stats">
              <div class="stat">
                <i class="ri-group-line"></i>
                <span>26 estudiantes</span>
              </div>
              <div class="stat">
                <i class="ri-user-line"></i>
                <span>Prof. Patricia Gómez</span>
              </div>
            </div>

            <div class="course-performance">
              <div class="performance-label">
                <span>Promedio General</span>
                <strong class="grade-good">4.1</strong>
              </div>
              <div class="progress-bar">
                <div class="progress-fill" style="width: 82%; background: #10b981;"></div>
              </div>
            </div>

            <div class="course-alerts">
              <div class="alert-item">
                <i class="ri-error-warning-line"></i>
                <span>1 estudiante en riesgo</span>
              </div>
            </div>

            <div class="course-actions">
              <button class="btn-secondary"><i class="ri-eye-line"></i> Ver detalles</button>
              <button class="btn-icon" title="Más opciones"><i class="ri-more-2-line"></i></button>
            </div>
          </div>
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