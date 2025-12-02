<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Mis Materias</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-materias.css">
</head>

<body>
  <div class="app" id="appGrid">
    <!-- LEFT SIDEBAR -->
    <?php 
      include_once __DIR__ . '/../../layouts/sidebar_estudiante.php'
    ?>

    <!-- MAIN CONTENT -->
    <main class="main">
      <div class="topbar">
        <div class="topbar-left">
          <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
            <i class="ri-menu-2-line"></i>
          </button>
          <div class="title">Mis Materias</div>
        </div>

        <div class="search">
          <i class="ri-search-2-line"></i>
          <input type="text" id="searchInput" placeholder="Buscar materias, profesores...">
        </div>

        <button class="toggle-btn" id="toggleRight" title="Mostrar/Ocultar panel derecho">
          <i class="ri-layout-right-2-line"></i>
        </button>
      </div>

      <!-- STATS CARDS -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon blue">
            <i class="ri-book-2-line"></i>
          </div>
          <div class="stat-content">
            <h3>6</h3>
            <p>Materias Activas</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon green">
            <i class="ri-medal-line"></i>
          </div>
          <div class="stat-content">
            <h3>3.8</h3>
            <p>Promedio General</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon orange">
            <i class="ri-alert-line"></i>
          </div>
          <div class="stat-content">
            <h3>2</h3>
            <p>En Riesgo</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon red">
            <i class="ri-time-line"></i>
          </div>
          <div class="stat-content">
            <h3>3</h3>
            <p>Act. Pendientes</p>
          </div>
        </div>
      </div>

      <!-- FILTERS -->
      <div class="filter-section">
        <div class="filter-group">
          <button class="filter-btn active" data-filter="todas">
            <i class="ri-apps-line"></i> Todas
          </button>
          <button class="filter-btn" data-filter="excelente">
            <i class="ri-star-line"></i> Excelentes
          </button>
          <button class="filter-btn" data-filter="riesgo">
            <i class="ri-error-warning-line"></i> En Riesgo
          </button>
          <button class="filter-btn" data-filter="critico">
            <i class="ri-alert-line"></i> Críticas
          </button>
        </div>
        <div class="view-toggle">
          <button class="view-btn active" data-view="grid" title="Vista en cuadrícula">
            <i class="ri-grid-line"></i>
          </button>
          <button class="view-btn" data-view="list" title="Vista en lista">
            <i class="ri-list-check"></i>
          </button>
        </div>
      </div>

      <!-- MATERIAS GRID -->
      <div class="materias-container grid-view" id="materiasContainer">

        <!-- MATERIA 1 -->
        <div class="materia-card" data-status="riesgo">
          <div class="materia-status riesgo"></div>
          <div class="materia-header">
            <div class="materia-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
              <i class="ri-calculator-line"></i>
            </div>
            <div class="materia-nota riesgo">2.8</div>
          </div>
          <h3 class="materia-title">Matemáticas</h3>
          <p class="materia-subtitle">Álgebra y Geometría</p>

          <div class="materia-profesor">
            <div class="profesor-avatar">CM</div>
            <div class="profesor-info">
              <strong>Prof. Carlos Méndez</strong>
              <small>carlos.mendez@colegio.edu</small>
            </div>
          </div>

          <div class="materia-stats">
            <div class="stat-item">
              <i class="ri-file-list-line"></i>
              <span>12 actividades</span>
            </div>
            <div class="stat-item warning">
              <i class="ri-time-line"></i>
              <span>3 pendientes</span>
            </div>
            <div class="stat-item">
              <i class="ri-calendar-check-line"></i>
              <span>88% asistencia</span>
            </div>
          </div>

          <div class="materia-actions">
            <button class="btn-materia primary">
              <i class="ri-eye-line"></i> Ver Detalles
            </button>
            <button class="btn-materia secondary">
              <i class="ri-folder-2-line"></i>
            </button>
          </div>
        </div>

        <!-- MATERIA 2 -->
        <div class="materia-card" data-status="critico">
          <div class="materia-status critico"></div>
          <div class="materia-header">
            <div class="materia-icon" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
              <i class="ri-flask-line"></i>
            </div>
            <div class="materia-nota critico">2.5</div>
          </div>
          <h3 class="materia-title">Física</h3>
          <p class="materia-subtitle">Mecánica Clásica</p>

          <div class="materia-profesor">
            <div class="profesor-avatar" style="background:#10b981">AR</div>
            <div class="profesor-info">
              <strong>Prof. Ana Rodríguez</strong>
              <small>ana.rodriguez@colegio.edu</small>
            </div>
          </div>

          <div class="materia-stats">
            <div class="stat-item">
              <i class="ri-file-list-line"></i>
              <span>15 actividades</span>
            </div>
            <div class="stat-item warning">
              <i class="ri-time-line"></i>
              <span>2 pendientes</span>
            </div>
            <div class="stat-item">
              <i class="ri-calendar-check-line"></i>
              <span>82% asistencia</span>
            </div>
          </div>

          <div class="materia-actions">
            <button class="btn-materia primary">
              <i class="ri-eye-line"></i> Ver Detalles
            </button>
            <button class="btn-materia secondary">
              <i class="ri-folder-2-line"></i>
            </button>
          </div>
        </div>

        <!-- MATERIA 3 -->
        <div class="materia-card" data-status="riesgo">
          <div class="materia-status riesgo"></div>
          <div class="materia-header">
            <div class="materia-icon" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);">
              <i class="ri-test-tube-line"></i>
            </div>
            <div class="materia-nota riesgo">3.0</div>
          </div>
          <h3 class="materia-title">Química</h3>
          <p class="materia-subtitle">Química Orgánica</p>

          <div class="materia-profesor">
            <div class="profesor-avatar" style="background:#f59e0b">LT</div>
            <div class="profesor-info">
              <strong>Prof. Luis Torres</strong>
              <small>luis.torres@colegio.edu</small>
            </div>
          </div>

          <div class="materia-stats">
            <div class="stat-item">
              <i class="ri-file-list-line"></i>
              <span>10 actividades</span>
            </div>
            <div class="stat-item warning">
              <i class="ri-time-line"></i>
              <span>1 pendiente</span>
            </div>
            <div class="stat-item">
              <i class="ri-calendar-check-line"></i>
              <span>90% asistencia</span>
            </div>
          </div>

          <div class="materia-actions">
            <button class="btn-materia primary">
              <i class="ri-eye-line"></i> Ver Detalles
            </button>
            <button class="btn-materia secondary">
              <i class="ri-folder-2-line"></i>
            </button>
          </div>
        </div>

        <!-- MATERIA 4 -->
        <div class="materia-card" data-status="excelente">
          <div class="materia-status excelente"></div>
          <div class="materia-header">
            <div class="materia-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
              <i class="ri-english-input"></i>
            </div>
            <div class="materia-nota excelente">4.5</div>
          </div>
          <h3 class="materia-title">Inglés</h3>
          <p class="materia-subtitle">Nivel Intermedio</p>

          <div class="materia-profesor">
            <div class="profesor-avatar" style="background:#ef4444">PG</div>
            <div class="profesor-info">
              <strong>Prof. Patricia Gómez</strong>
              <small>patricia.gomez@colegio.edu</small>
            </div>
          </div>

          <div class="materia-stats">
            <div class="stat-item">
              <i class="ri-file-list-line"></i>
              <span>14 actividades</span>
            </div>
            <div class="stat-item success">
              <i class="ri-checkbox-circle-line"></i>
              <span>0 pendientes</span>
            </div>
            <div class="stat-item">
              <i class="ri-calendar-check-line"></i>
              <span>95% asistencia</span>
            </div>
          </div>

          <div class="materia-actions">
            <button class="btn-materia primary">
              <i class="ri-eye-line"></i> Ver Detalles
            </button>
            <button class="btn-materia secondary">
              <i class="ri-folder-2-line"></i>
            </button>
          </div>
        </div>

        <!-- MATERIA 5 -->
        <div class="materia-card" data-status="critico">
          <div class="materia-status critico"></div>
          <div class="materia-header">
            <div class="materia-icon" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
              <i class="ri-microscope-line"></i>
            </div>
            <div class="materia-nota critico">2.7</div>
          </div>
          <h3 class="materia-title">Biologia</h3>
          <p class="materia-subtitle">La celula</p>

          <div class="materia-profesor">
            <div class="profesor-avatar" style="background:#8b5cf6">DA</div>
            <div class="profesor-info">
              <strong>Prof. Diego Álvarez</strong>
              <small>diego.alvarez@colegio.edu</small>
            </div>
          </div>

          <div class="materia-stats">
            <div class="stat-item">
              <i class="ri-file-list-line"></i>
              <span>18 actividades</span>
            </div>
            <div class="stat-item warning">
              <i class="ri-time-line"></i>
              <span>4 pendientes</span>
            </div>
            <div class="stat-item">
              <i class="ri-calendar-check-line"></i>
              <span>85% asistencia</span>
            </div>
          </div>

          <div class="materia-actions">
            <button class="btn-materia primary">
              <i class="ri-eye-line"></i> Ver Detalles
            </button>
            <button class="btn-materia secondary">
              <i class="ri-folder-2-line"></i>
            </button>
          </div>
        </div>

        <!-- MATERIA 6 -->
        <div class="materia-card" data-status="excelente">
          <div class="materia-status excelente"></div>
          <div class="materia-header">
            <div class="materia-icon" style="background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);">
              <i class="ri-book-open-line"></i>
            </div>
            <div class="materia-nota bien">4.2</div>
          </div>
          <h3 class="materia-title">Historia</h3>
          <p class="materia-subtitle">Historia de Colombia</p>

          <div class="materia-profesor">
            <div class="profesor-avatar" style="background:#06b6d4">MR</div>
            <div class="profesor-info">
              <strong>Prof. María Ramírez</strong>
              <small>maria.ramirez@colegio.edu</small>
            </div>
          </div>

          <div class="materia-stats">
            <div class="stat-item">
              <i class="ri-file-list-line"></i>
              <span>11 actividades</span>
            </div>
            <div class="stat-item warning">
              <i class="ri-time-line"></i>
              <span>1 pendiente</span>
            </div>
            <div class="stat-item">
              <i class="ri-calendar-check-line"></i>
              <span>97% asistencia</span>
            </div>
          </div>

          <div class="materia-actions">
            <button class="btn-materia primary">
              <i class="ri-eye-line"></i> Ver Detalles
            </button>
            <button class="btn-materia secondary">
              <i class="ri-folder-2-line"></i>
            </button>
          </div>
        </div>

      </div>
    </main>

    <!-- RIGHT SIDEBAR -->
    <aside class="rightbar" id="rightSidebar">
      <div class="user">
        <button class="btn" title="Notificaciones"><i class="ri-notification-3-line"></i></button>
        <button class="btn" title="Configuración"><i class="ri-settings-3-line"></i></button>
        <div class="avatar" title="Diego A.">DA</div>
      </div>

      <div class="panel-title">Acciones Rápidas</div>
      <p class="muted">Accesos directos importantes</p>

      <div class="quick-actions">
        <div class="quick-action">
          <div class="quick-action-icon blue">
            <i class="ri-calendar-event-line"></i>
          </div>
          <div class="quick-action-content">
            <strong>Ver Horario</strong>
            <small>Horario semanal de clases</small>
          </div>
        </div>

        <div class="quick-action">
          <div class="quick-action-icon green">
            <i class="ri-task-line"></i>
          </div>
          <div class="quick-action-content">
            <strong>Actividades</strong>
            <small>3 tareas pendientes</small>
          </div>
        </div>

        <div class="quick-action">
          <div class="quick-action-icon orange">
            <i class="ri-bar-chart-line"></i>
          </div>
          <div class="quick-action-content">
            <strong>Calificaciones</strong>
            <small>Ver todas mis notas</small>
          </div>
        </div>

        <div class="quick-action">
          <div class="quick-action-icon red">
            <i class="ri-download-line"></i>
          </div>
          <div class="quick-action-content">
            <strong>Descargar Boletín</strong>
            <small>PDF del período actual</small>
          </div>
        </div>
      </div>

      <div class="panel-title" style="margin-top:24px">Próximas Entregas</div>
      <p class="muted">Actividades por vencer</p>

      <div class="deadline-list">
        <div class="deadline-item urgent">
          <div class="deadline-date">
            <span class="day">22</span>
            <span class="month">Nov</span>
          </div>
          <div class="deadline-content">
            <strong>Taller de Física</strong>
            <small>Mecánica - Prof. Ana Rodríguez</small>
            <div class="deadline-time">
              <i class="ri-time-line"></i> Vence mañana
            </div>
          </div>
        </div>

        <div class="deadline-item">
          <div class="deadline-date">
            <span class="day">25</span>
            <span class="month">Nov</span>
          </div>
          <div class="deadline-content">
            <strong>Ensayo de Historia</strong>
            <small>Colombia - Prof. María Ramírez</small>
            <div class="deadline-time">
              <i class="ri-time-line"></i> En 4 días
            </div>
          </div>
        </div>

        <div class="deadline-item">
          <div class="deadline-date">
            <span class="day">28</span>
            <span class="month">Nov</span>
          </div>
          <div class="deadline-content">
            <strong>Proyecto Final</strong>
            <small>Java - Prof. Diego Álvarez</small>
            <div class="deadline-time">
              <i class="ri-time-line"></i> En 7 días
            </div>
          </div>
        </div>
      </div>

      <button class="btn-primary">Ver todas las actividades</button>

      <div class="tips-card">
        <h4><i class="ri-lightbulb-line"></i> Consejo Académico</h4>
        <p>Enfócate en mejorar las materias en riesgo. Contacta a tus profesores para recibir apoyo adicional y organiza
          tu tiempo de estudio.</p>
      </div>
    </aside>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/estudiante/materias.js"></script>

</body>

</html>