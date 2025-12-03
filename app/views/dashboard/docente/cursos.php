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
  <link rel="stylesheet" href="../../assets/dashboard/css/styles-docente.css">
</head>

<body>
  <div class="app" id="appGrid">
    <!-- LEFT SIDEBAR -->
    <aside class="sidebar" id="leftSidebar">
      <a class="brand" href="#">
        <span class="logo"><i class="ri-shield-star-line"></i></span>
        <span>Siademy</span>
      </a>
      <nav class="nav">
        <a href="docente.html"><i class="ri-home-5-line"></i> Panel</a>
        <a class="active" href="cursos.html"><i class="ri-team-line"></i> Cursos</a>
        <a href="eventos.html"><i class="ri-calendar-event-line"></i> Eventos</a>
      
      
      </nav>
    </aside>

    <!-- MAIN -->
    <main class="main">
      <div class="topbar">
        <div class="topbar-left">
          <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
            <i class="ri-menu-2-line"></i>
          </button>
          <div class="title">Mis Cursos</div>
        </div>
        <div class="topbar-actions">
          
        </div>
        <button class="toggle-btn" id="toggleRight" title="Mostrar/Ocultar panel derecho">
          <i class="ri-layout-right-2-line"></i>
        </button>
      </div>

      <!-- TEACHER INFO BAR -->
      <div class="teacher-info-bar">
        <div class="teacher-profile">
          <div class="teacher-avatar">DA</div>
          <div>
            <strong>Diego Alvarado</strong>
            <small>Profesor de Matemáticas y Física</small>
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
              <strong>181</strong>
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

      <!-- FILTER TABS -->
      <div class="filter-section">
        <div class="filter-tabs-courses">
          <button class="filter-tab-course active" data-filter="all">
            <i class="ri-list-check"></i>
            Todos los cursos
            <span class="badge-count">6</span>
          </button>
          <button class="filter-tab-course" data-filter="mathematics">
            <i class="ri-calculator-line"></i>
            Matemáticas
            <span class="badge-count">3</span>
          </button>
          <button class="filter-tab-course" data-filter="physics">
            <i class="ri-test-tube-line"></i>
            Física
            <span class="badge-count">2</span>
          </button>
          <button class="filter-tab-course" data-filter="other">
            <i class="ri-book-2-line"></i>
            Otros
            <span class="badge-count">1</span>
          </button>
        </div>
        <div class="filter-search">
          <i class="ri-search-line"></i>
          <input type="text" placeholder="Buscar curso...">
        </div>
      </div>

      <!-- COURSES GRID -->
      <section class="courses-grid">
        <!-- Course Card 1 -->
        <div class="course-card" data-category="mathematics">
          <div class="course-header">
            <div class="course-icon" style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
              <i class="ri-calculator-line"></i>
            </div>
            <div class="course-actions">
              <button class="btn-course-action" title="Editar"><i class="ri-edit-line"></i></button>
              <button class="btn-course-action" title="Más opciones"><i class="ri-more-2-fill"></i></button>
            </div>
          </div>
          
          <div class="course-body">
            <h3>Matemáticas Avanzadas</h3>
            <p class="course-description">Álgebra y Trigonometría</p>
            
            <div class="course-meta">
              <span class="meta-item">
                <i class="ri-group-line"></i>
                <strong>10° A</strong>
              </span>
              <span class="meta-item">
                <i class="ri-user-line"></i>
                32 estudiantes
              </span>
            </div>

            <div class="course-schedule">
              <div class="schedule-item">
                <i class="ri-calendar-line"></i>
                <span>Lun - Mié - Vie</span>
              </div>
              <div class="schedule-item">
                <i class="ri-time-line"></i>
                <span>8:00 AM - 9:30 AM</span>
              </div>
            </div>

            <div class="course-progress">
              <div class="progress-info">
                <small>Progreso del período</small>
                <strong>68%</strong>
              </div>
              <div class="progress-bar-course">
                <div class="progress-fill-course" style="width: 68%;"></div>
              </div>
            </div>
          </div>

          <div class="course-footer">
            <button class="btn-course-primary">
              <i class="ri-eye-line"></i>
              Ver detalles
            </button>
            <button class="btn-course-secondary">
              <i class="ri-file-list-line"></i>
              Calificaciones
            </button>
          </div>
        </div>

        <!-- Course Card 2 -->
        <div class="course-card" data-category="physics">
          <div class="course-header">
            <div class="course-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
              <i class="ri-test-tube-line"></i>
            </div>
            <div class="course-actions">
              <button class="btn-course-action" title="Editar"><i class="ri-edit-line"></i></button>
              <button class="btn-course-action" title="Más opciones"><i class="ri-more-2-fill"></i></button>
            </div>
          </div>
          
          <div class="course-body">
            <h3>Física I</h3>
            <p class="course-description">Mecánica Clásica</p>
            
            <div class="course-meta">
              <span class="meta-item">
                <i class="ri-group-line"></i>
                <strong>11° B</strong>
              </span>
              <span class="meta-item">
                <i class="ri-user-line"></i>
                28 estudiantes
              </span>
            </div>

            <div class="course-schedule">
              <div class="schedule-item">
                <i class="ri-calendar-line"></i>
                <span>Mar - Jue</span>
              </div>
              <div class="schedule-item">
                <i class="ri-time-line"></i>
                <span>10:00 AM - 11:30 AM</span>
              </div>
            </div>

            <div class="course-progress">
              <div class="progress-info">
                <small>Progreso del período</small>
                <strong>75%</strong>
              </div>
              <div class="progress-bar-course">
                <div class="progress-fill-course" style="width: 75%;"></div>
              </div>
            </div>
          </div>

          <div class="course-footer">
            <button class="btn-course-primary">
              <i class="ri-eye-line"></i>
              Ver detalles
            </button>
            <button class="btn-course-secondary">
              <i class="ri-file-list-line"></i>
              Calificaciones
            </button>
          </div>
        </div>

        <!-- Course Card 3 -->
        <div class="course-card" data-category="mathematics">
          <div class="course-header">
            <div class="course-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
              <i class="ri-function-line"></i>
            </div>
            <div class="course-actions">
              <button class="btn-course-action" title="Editar"><i class="ri-edit-line"></i></button>
              <button class="btn-course-action" title="Más opciones"><i class="ri-more-2-fill"></i></button>
            </div>
          </div>
          
          <div class="course-body">
            <h3>Cálculo Diferencial</h3>
            <p class="course-description">Límites y Derivadas</p>
            
            <div class="course-meta">
              <span class="meta-item">
                <i class="ri-group-line"></i>
                <strong>11° A</strong>
              </span>
              <span class="meta-item">
                <i class="ri-user-line"></i>
                30 estudiantes
              </span>
            </div>

            <div class="course-schedule">
              <div class="schedule-item">
                <i class="ri-calendar-line"></i>
                <span>Lun - Mié - Vie</span>
              </div>
              <div class="schedule-item">
                <i class="ri-time-line"></i>
                <span>2:00 PM - 3:30 PM</span>
              </div>
            </div>

            <div class="course-progress">
              <div class="progress-info">
                <small>Progreso del período</small>
                <strong>52%</strong>
              </div>
              <div class="progress-bar-course">
                <div class="progress-fill-course" style="width: 52%;"></div>
              </div>
            </div>
          </div>

          <div class="course-footer">
            <button class="btn-course-primary">
              <i class="ri-eye-line"></i>
              Ver detalles
            </button>
            <button class="btn-course-secondary">
              <i class="ri-file-list-line"></i>
              Calificaciones
            </button>
          </div>
        </div>

        <!-- Course Card 4 -->
        <div class="course-card" data-category="mathematics">
          <div class="course-header">
            <div class="course-icon" style="background: linear-gradient(135deg, #ec4899, #db2777);">
              <i class="ri-pie-chart-line"></i>
            </div>
            <div class="course-actions">
              <button class="btn-course-action" title="Editar"><i class="ri-edit-line"></i></button>
              <button class="btn-course-action" title="Más opciones"><i class="ri-more-2-fill"></i></button>
            </div>
          </div>
          
          <div class="course-body">
            <h3>Estadística</h3>
            <p class="course-description">Probabilidad y Análisis</p>
            
            <div class="course-meta">
              <span class="meta-item">
                <i class="ri-group-line"></i>
                <strong>10° B</strong>
              </span>
              <span class="meta-item">
                <i class="ri-user-line"></i>
                29 estudiantes
              </span>
            </div>

            <div class="course-schedule">
              <div class="schedule-item">
                <i class="ri-calendar-line"></i>
                <span>Mar - Jue</span>
              </div>
              <div class="schedule-item">
                <i class="ri-time-line"></i>
                <span>8:00 AM - 9:30 AM</span>
              </div>
            </div>

            <div class="course-progress">
              <div class="progress-info">
                <small>Progreso del período</small>
                <strong>60%</strong>
              </div>
              <div class="progress-bar-course">
                <div class="progress-fill-course" style="width: 60%;"></div>
              </div>
            </div>
          </div>

          <div class="course-footer">
            <button class="btn-course-primary">
              <i class="ri-eye-line"></i>
              Ver detalles
            </button>
            <button class="btn-course-secondary">
              <i class="ri-file-list-line"></i>
              Calificaciones
            </button>
          </div>
        </div>

        <!-- Course Card 5 -->
        <div class="course-card" data-category="other">
          <div class="course-header">
            <div class="course-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
              <i class="ri-shapes-line"></i>
            </div>
            <div class="course-actions">
              <button class="btn-course-action" title="Editar"><i class="ri-edit-line"></i></button>
              <button class="btn-course-action" title="Más opciones"><i class="ri-more-2-fill"></i></button>
            </div>
          </div>
          
          <div class="course-body">
            <h3>Geometría Analítica</h3>
            <p class="course-description">Vectores y Plano Cartesiano</p>
            
            <div class="course-meta">
              <span class="meta-item">
                <i class="ri-group-line"></i>
                <strong>9° A</strong>
              </span>
              <span class="meta-item">
                <i class="ri-user-line"></i>
                35 estudiantes
              </span>
            </div>

            <div class="course-schedule">
              <div class="schedule-item">
                <i class="ri-calendar-line"></i>
                <span>Lun - Vie</span>
              </div>
              <div class="schedule-item">
                <i class="ri-time-line"></i>
                <span>10:00 AM - 11:00 AM</span>
              </div>
            </div>

            <div class="course-progress">
              <div class="progress-info">
                <small>Progreso del período</small>
                <strong>82%</strong>
              </div>
              <div class="progress-bar-course">
                <div class="progress-fill-course" style="width: 82%;"></div>
              </div>
            </div>
          </div>

          <div class="course-footer">
            <button class="btn-course-primary">
              <i class="ri-eye-line"></i>
              Ver detalles
            </button>
            <button class="btn-course-secondary">
              <i class="ri-file-list-line"></i>
              Calificaciones
            </button>
          </div>
        </div>

        <!-- Course Card 6 -->
        <div class="course-card" data-category="physics">
          <div class="course-header">
            <div class="course-icon" style="background: linear-gradient(135deg, #06b6d4, #0891b2);">
              <i class="ri-flashlight-line"></i>
            </div>
            <div class="course-actions">
              <button class="btn-course-action" title="Editar"><i class="ri-edit-line"></i></button>
              <button class="btn-course-action" title="Más opciones"><i class="ri-more-2-fill"></i></button>
            </div>
          </div>
          
          <div class="course-body">
            <h3>Física II</h3>
            <p class="course-description">Electromagnetismo</p>
            
            <div class="course-meta">
              <span class="meta-item">
                <i class="ri-group-line"></i>
                <strong>11° C</strong>
              </span>
              <span class="meta-item">
                <i class="ri-user-line"></i>
                27 estudiantes
              </span>
            </div>

            <div class="course-schedule">
              <div class="schedule-item">
                <i class="ri-calendar-line"></i>
                <span>Mié - Vie</span>
              </div>
              <div class="schedule-item">
                <i class="ri-time-line"></i>
                <span>1:00 PM - 2:30 PM</span>
              </div>
            </div>

            <div class="course-progress">
              <div class="progress-info">
                <small>Progreso del período</small>
                <strong>45%</strong>
              </div>
              <div class="progress-bar-course">
                <div class="progress-fill-course warning" style="width: 45%;"></div>
              </div>
            </div>
          </div>

          <div class="course-footer">
            <button class="btn-course-primary">
              <i class="ri-eye-line"></i>
              Ver detalles
            </button>
            <button class="btn-course-secondary">
              <i class="ri-file-list-line"></i>
              Calificaciones
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
  <script>
    // Toggle sidebars
    const app = document.getElementById('appGrid');
    const toggleLeft = document.getElementById('toggleLeft');
    const toggleRight = document.getElementById('toggleRight');
    const leftSidebar = document.getElementById('leftSidebar');
    const rightSidebar = document.getElementById('rightSidebar');

    toggleLeft.addEventListener('click', () => {
      leftSidebar.classList.toggle('hidden');
      app.classList.toggle('hide-left');
    });

    toggleRight.addEventListener('click', () => {
      rightSidebar.classList.toggle('hidden');
      app.classList.toggle('hide-right');
    });

    // Filter courses
    const filterTabs = document.querySelectorAll('.filter-tab-course');
    const courseCards = document.querySelectorAll('.course-card');

    filterTabs.forEach(tab => {
      tab.addEventListener('click', function() {
        // Remove active class from all tabs
        filterTabs.forEach(t => t.classList.remove('active'));
        this.classList.add('active');

        const filter = this.getAttribute('data-filter');

        courseCards.forEach(card => {
          if (filter === 'all') {
            card.style.display = 'block';
          } else {
            if (card.getAttribute('data-category') === filter) {
              card.style.display = 'block';
            } else {
              card.style.display = 'none';
            }
          }
        });
      });
    });

    // Search filter
    const searchInput = document.querySelector('.filter-search input');
    searchInput.addEventListener('input', function() {
      const searchTerm = this.value.toLowerCase();
      
      courseCards.forEach(card => {
        const courseName = card.querySelector('h3').textContent.toLowerCase();
        const courseDesc = card.querySelector('.course-description').textContent.toLowerCase();
        
        if (courseName.includes(searchTerm) || courseDesc.includes(searchTerm)) {
          card.style.display = 'block';
        } else {
          card.style.display = 'none';
        }
      });
    });
  </script>
</body>

</html>