<?php 
  require_once BASE_PATH . '/app/helpers/session_administrador.php';
  // ENLAZAMOS LA DEPENDENCIA, EN ESTE CASO EL CONTROLADOR QUE TIENE LA FUNCION DE COSULTAR LOS DATOS
  require_once BASE_PATH . '/app/controllers/administrador/asignatura.php';

  // LLAMAMOS LA FUNCION ESPECIFICA QUE EXISTE EN DICHO CONTROLADOR
  $asignaturas = mostrarAsignaturas();
?>





<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Gestión de Asignaturas</title>
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
          <div class="title">Gestión de Asignaturas</div>
        </div>
        <button class="btn-agregar-estudiante" onclick="window.location.href='administrador/registrar-asignatura'">
        Agregar Asignatura
        </button>
        <div class="search">
          <i class="ri-search-2-line"></i>
          <input type="text" placeholder="Buscar asignatura o profesor...">
        </div>
        
        <button class="toggle-btn" id="toggleRight" title="Mostrar/Ocultar panel derecho">
          <i class="ri-layout-right-2-line"></i>
        </button>
      </div>

      <!-- KPI CARDS -->
      <div class="kpis">
        <div class="kpi">
          <div class="icon"><i class="ri-booklet-line"></i></div>
          <div>
            <small>Total Asignaturas</small>
            <strong>18</strong>
          </div>
        </div>
        <div class="kpi">
          <div class="icon"><i class="ri-user-star-line"></i></div>
          <div>
            <small>Profesores</small>
            <strong>38</strong>
          </div>
        </div>
        <div class="kpi">
          <div class="icon"><i class="ri-line-chart-line"></i></div>
          <div>
            <small>Promedio General</small>
            <strong>3.8</strong>
          </div>
        </div>
      </div>

   

      <!-- SUBJECTS GRID -->
      <section class="subjects-section">
        <div class="subjects-header">
          <h3>Asignaturas Activas (18)</h3>
          <div class="view-toggle">
            <button class="view-btn active" data-view="grid"><i class="ri-grid-line"></i></button>
            <button class="view-btn" data-view="list"><i class="ri-list-check"></i></button>
          </div>
        </div>

        <div class="subjects-grid">
          <!-- Subject Card 1 -->

          <?php if(!empty($asignaturas)): ?>
          <?php foreach($asignaturas as $asignaturas): ?>

          <div class="subject-card">
            <div class="subject-header">
              <div class="subject-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <i class="ri-calculator-line"></i>
              </div>
              <div class="subject-status status-active"><?= $asignaturas['estado'] ?></div>
            </div>
            
            <h4><?= $asignaturas['nombre'] ?></h4>
            <p class="subject-area"><?= $asignaturas['descripcion'] ?></p>
            
            <div class="subject-info">
              <div class="info-item">
                <i class="ri-user-line"></i>
                <div>
                  <span class="info-label">Profesores</span>
                  <strong>4</strong>
                </div>
              </div>
              <div class="info-item">
                <i class="ri-time-line"></i>
                <div>
                  <span class="info-label">Horas/Semana</span>
                  <strong>5</strong>
                </div>
              </div>
            </div>

            <div class="subject-stats">
              <div class="stat-box">
                <span class="stat-label">Promedio</span>
                <strong class="stat-value grade-good">4.1</strong>
              </div>
              <div class="stat-box">
                <span class="stat-label">Estudiantes</span>
                <strong class="stat-value">842</strong>
              </div>
            </div>

            <div class="subject-actions">
              <button class="btn-secondary"><i class="bi bi-eye"></i></button>
              <button class="btn-secondary"><a href="<?= BASE_URL ?>/administrador/editar-asignatura?id=<?= $asignaturas['id'] ?>"><i class="bi bi-pencil-square"></i></a></button>
              <button class="btn-secondary"><i class="bi bi-trash3-fill"></i></button>

            </div>
          </div>
              <?php endforeach; ?>
              <?php else: ?>

                  <h3>No hay asignaturas registrados</h3>
                
              <?php endif; ?>


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

      <div class="panel-title">Áreas Académicas</div>
      <p class="muted">Distribución por área</p>
      
      <div class="area-list">
        <div class="area-item">
          <div class="area-icon" style="background: #667eea;">
            <i class="ri-calculator-line"></i>
          </div>
          <div>
            <strong>Ciencias Exactas</strong>
            <small>2 asignaturas</small>
          </div>
        </div>
        <div class="area-item">
          <div class="area-icon" style="background: #f093fb;">
            <i class="ri-flask-line"></i>
          </div>
          <div>
            <strong>Ciencias Naturales</strong>
            <small>3 asignaturas</small>
          </div>
        </div>
        <div class="area-item">
          <div class="area-icon" style="background: #fa709a;">
            <i class="ri-book-open-line"></i>
          </div>
          <div>
            <strong>Humanidades</strong>
            <small>4 asignaturas</small>
          </div>
        </div>
        <div class="area-item">
          <div class="area-icon" style="background: #a8edea;">
            <i class="ri-global-line"></i>
          </div>
          <div>
            <strong>Idiomas</strong>
            <small>2 asignaturas</small>
          </div>
        </div>
      </div>

      <div class="panel-title" style="margin-top:18px">Top Profesores</div>
      <p class="muted">Mejor desempeño académico</p>

      <div class="msg">
        <div class="avatar" style="background: #10b981;">MG</div>
        <div>
          <strong>María González</strong>
          <div class="muted">Literatura • 4.5 promedio</div>
        </div>
        <i class="ri-star-fill" style="color: #fbbf24; margin-left: auto;"></i>
      </div>
      <div class="msg">
        <div class="avatar" style="background: #6366f1;">CM</div>
        <div>
          <strong>Carlos Méndez</strong>
          <div class="muted">Matemáticas • 4.1 promedio</div>
        </div>
        <i class="ri-star-fill" style="color: #fbbf24; margin-left: auto;"></i>
      </div>

      <!-- EVENTS SECTION -->
      <div class="events-section">
        <div class="panel-title">Próximos Eventos</div>
        <p class="muted">Eventos académicos programados</p>

        <div class="event-item">
          <div class="event-date">
            <span class="day">28</span>
            <span class="month">Oct</span>
          </div>
          <div class="event-content">
            <h4>Reunión de Padres</h4>
            <p>Reunión general para padres de familia del grado 7°</p>
            <div class="event-time">📅 2:00 PM - 4:00 PM</div>
          </div>
        </div>

        <div class="event-item">
          <div class="event-date">
            <span class="day">30</span>
            <span class="month">Oct</span>
          </div>
          <div class="event-content">
            <h4>Examen de Matemáticas</h4>
            <p>Evaluación final del segundo período académico</p>
            <div class="event-time">📚 8:00 AM - 10:00 AM</div>
          </div>
        </div>

        <div class="event-item">
          <div class="event-date">
            <span class="day">02</span>
            <span class="month">Nov</span>
          </div>
          <div class="event-content">
            <h4>Festival Cultural</h4>
            <p>Presentación de obras teatrales y danzas típicas</p>
            <div class="event-time">🎭 9:00 AM - 12:00 PM</div>
          </div>
        </div>

        <a href="#" class="btn-primary">Ver todos los eventos</a>
      </div>
    </aside>
  </div>

  <!-- FOOTER -->
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-admin.js"></script>
 
</body>

</html>