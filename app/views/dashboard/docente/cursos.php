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
  <div class="app" id="appGrid">
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
       
        <button class="toggle-btn" id="toggleRight" title="Mostrar/Ocultar panel derecho">
          <i class="ri-layout-right-2-line"></i>
        </button>
      </div>

      <!-- TEACHER INFO BAR -->
      <div class="teacher-info-bar">
        <div class="teacher-profile">
          <div class="teacher-avatar">WM</div>
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
            <option value="all" selected>Todos los cursos (6)</option>
            <option value="mathematics">Matemáticas (3)</option>
            <option value="physics">Física (2)</option>
            <option value="other">Otros (1)</option>
          </select>
        </div>
        <div class="cursos-filter-search">
          <i class="ri-search-line"></i>
          <input type="text" id="searchInput" placeholder="Buscar por grado o curso...">
        </div>
      </div>

      <!-- COURSES GRID -->
      <section class="cursos-grid">
        
        <!-- Course Card 1 - Matemáticas -->
        <div class="curso-card" data-category="mathematics">
          <div class="curso-card-header">
            <div class="curso-icon" style="background: linear-gradient(135deg, #4f46e5, #6366f1)">
              <i class="ri-calculator-line"></i>
            </div>
            <div class="curso-badge-jornada jornada-manana">
              <i class="ri-sun-line"></i>
              Mañana
            </div>
          </div>
          
          <div class="curso-card-body">
            <div class="curso-info-principal">
              <div class="curso-grado">Grado 10°</div>
              <h3 class="curso-nombre">Matemáticas Avanzadas</h3>
              <div class="curso-codigo">Curso 101-A</div>
            </div>

            <div class="curso-meta-grid">
              <div class="curso-meta-item">
                <i class="ri-user-line"></i>
                <div>
                  <strong>32</strong>
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
              <i class="ri-map-pin-line"></i>
              <span>Salón 203 - Edificio A</span>
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
            <button class="btn-curso-primary">
              <i class="ri-eye-line"></i>
              Ver Detalles
            </button>
            <button class="btn-curso-secondary">
              <i class="ri-clipboard-line"></i>
              Actividades
            </button>
          </div>
        </div>

       

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

    <!-- RIGHT SIDEBAR -->
    <aside class="rightbar" id="rightSidebar">
      <div class="user">
        <button class="btn" title="Notificaciones"><i class="ri-notification-3-line"></i></button>
        <button class="btn" title="Configuración"><i class="ri-settings-3-line"></i></button>
        <div class="avatar" title="Diego A.">DA</div>
      </div>

      <div class="panel-title">Resumen Semanal</div>
      <p class="muted">31 Oct - 04 Nov 2025</p>

      <div class="weekly-summary">
        <div class="summary-item">
          <i class="ri-calendar-check-line"></i>
          <div>
            <strong>18 clases</strong>
            <small>Esta semana</small>
          </div>
        </div>
        <div class="summary-item">
          <i class="ri-file-list-line"></i>
          <div>
            <strong>12 tareas</strong>
            <small>Por calificar</small>
          </div>
        </div>
        <div class="summary-item">
          <i class="ri-alarm-warning-line"></i>
          <div>
            <strong>3 exámenes</strong>
            <small>Programados</small>
          </div>
        </div>
      </div>

      <div class="panel-title" style="margin-top:20px">Estudiantes Destacados</div>
      <p class="muted">Top 3 del mes</p>

      <div class="top-students">
        <div class="student-item">
          <div class="student-rank first">1</div>
          <div class="student-avatar">MA</div>
          <div class="student-info">
            <strong>María Álvarez</strong>
            <small>10° A • Promedio: 4.9</small>
          </div>
        </div>

        <div class="student-item">
          <div class="student-rank second">2</div>
          <div class="student-avatar">CP</div>
          <div class="student-info">
            <strong>Carlos Pérez</strong>
            <small>11° A • Promedio: 4.8</small>
          </div>
        </div>

        <div class="student-item">
          <div class="student-rank third">3</div>
          <div class="student-avatar">LG</div>
          <div class="student-info">
            <strong>Laura Gómez</strong>
            <small>11° B • Promedio: 4.7</small>
          </div>
        </div>
      </div>

      <div class="panel-title" style="margin-top:20px">Acciones Rápidas</div>

      <button class="quick-action">
        <i class="ri-add-circle-line"></i>
        Crear Actividad
      </button>

      <button class="quick-action">
        <i class="ri-file-upload-line"></i>
        Subir Material
      </button>

      <button class="quick-action">
        <i class="ri-calendar-todo-line"></i>
        Programar Examen
      </button>

      <button class="quick-action">
        <i class="ri-message-3-line"></i>
        Enviar Mensaje
      </button>

      <div class="panel-title" style="margin-top:20px">Recordatorios</div>

      <div class="reminder-item">
        <i class="ri-alarm-line"></i>
        <div>
          <strong>Entrega de notas</strong>
          <small>Viernes 01 Nov • 5:00 PM</small>
        </div>
      </div>

      <div class="reminder-item">
        <i class="ri-team-line"></i>
        <div>
          <strong>Reunión de docentes</strong>
          <small>Lunes 04 Nov • 3:00 PM</small>
        </div>
      </div>
    </aside>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/docente/cursos.js"></script>
</body>

</html>