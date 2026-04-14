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
        <button class="toggle-btn" id="toggleRight" title="Mostrar/Ocultar panel derecho">
          <i class="ri-layout-right-2-line"></i>
        </button>
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
    <aside class="rightbar" id="rightSidebar">
      <div class="user">
        <button class="btn" title="Notificaciones"><i class="ri-notification-3-line"></i></button>
        <button class="btn" title="Configuración"><i class="ri-settings-3-line"></i></button>
        <a href="<?= BASE_URL ?>/dashboard-perfil" class="avatar" title="Ir al perfil" style="text-decoration:none;color:inherit;">DA</a>
      </div>

      <div class="panel-title">Cursos Recientes</div>
      <p class="muted">Tienes 12 cursos</p>
      <div class="course-list">
        <div class="course">
          <div class="dot">A</div>
          <div><strong>Curso 1</strong><small>Clase VII A</small></div><i class="ri-information-line" style="margin-left:auto;color:#94a3b8"></i>
        </div>
        <div class="course">
          <div class="dot">B</div>
          <div><strong>Curso 2</strong><small>Clase VII A</small></div><i class="ri-information-line" style="margin-left:auto;color:#94a3b8"></i>
        </div>
        <div class="course">
          <div class="dot">C</div>
          <div><strong>Curso 3</strong><small>Clase VII A</small></div><i class="ri-information-line" style="margin-left:auto;color:#94a3b8"></i>
        </div>
        <div class="course">
          <div class="dot">D</div>
          <div><strong>Curso 4</strong><small>Clase VII B</small></div><i class="ri-information-line" style="margin-left:auto;color:#94a3b8"></i>
        </div>
        <div class="course">
          <div class="dot">E</div>
          <div><strong>Curso 5</strong><small>Clase VII B</small></div><i class="ri-information-line" style="margin-left:auto;color:#94a3b8"></i>
        </div>
      </div>
      <a href="#" class="btn-primary">Ver más</a>

      <div class="panel-title" style="margin-top:18px">Mensajes</div>

      <div class="msg">
        <div class="avatar">S</div>
        <div>
          <strong>Samantha William</strong>
          <div class="muted">Profesora • &nbsp;Nuevo material para la clase de mañana...</div>
        </div>
        <span class="time">12:45 PM</span>
      </div>
      <div class="msg">
        <div class="avatar">J</div>
        <div>
          <strong>Juan Pérez</strong>
          <div class="muted">Estudiante • &nbsp;Profe, ¿puede revisar mi actividad?</div>
        </div>
        <span class="time">12:10 PM</span>
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

        <div class="event-item">
          <div class="event-date">
            <span class="day">05</span>
            <span class="month">Nov</span>
          </div>
          <div class="event-content">
            <h4>Día del Deporte</h4>
            <p>Competencias deportivas inter-cursos</p>
            <div class="event-time">⚽ 7:00 AM - 3:00 PM</div>
          </div>
        </div>

        <div class="event-item">
          <div class="event-date">
            <span class="day">10</span>
            <span class="month">Nov</span>
          </div>
          <div class="event-content">
            <h4>Feria de Ciencias</h4>
            <p>Exposición de proyectos científicos estudiantiles</p>
            <div class="event-time">🔬 1:00 PM - 5:00 PM</div>
          </div>
        </div>

        <a href="#" class="btn-primary">Ver todos los eventos</a>
      </div>
    </aside>
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