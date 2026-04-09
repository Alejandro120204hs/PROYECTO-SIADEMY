<?php 
  require_once BASE_PATH . '/app/helpers/session_administrador.php';
   //ENLAZAMOS LA DEPENDENCIA DEL CONTROLADOR QUE TIENE LA FUNCION PARA MOSTRAR LOS DATOS
    require_once BASE_PATH . '/app/controllers/perfil.php';
    require_once BASE_PATH . '/app/controllers/administrador/eventos.php';
    
    // LLAMAMOS EL ID QUE VIENE ATRAVEZ DEL METODO GET
    $id = $_SESSION['user']['id'];
    // LLAMAMOS LA FUNCION ESPECIFICA DEL CONTROLADOR
    $usuario = mostrarPerfil($id);
    
    // OBTENEMOS LOS EVENTOS DE LA INSTITUCIÓN
    $eventos = mostrarEventos();
    
    // FUNCIÓN PARA OBTENER EL DÍA DE LA SEMANA EN ESPAÑOL
    function getDiaSemana($timestamp) {
        $dias = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
        return $dias[date('w', $timestamp)];
    }
    
    // FUNCIÓN PARA MAPEAR TIPO DE EVENTO A CATEGORIA PARA FILTROS
    function getTipoCategoria($tipo) {
        $map = [
            'reuniones' => 'meetings',
            'examen' => 'exams',
            'actividad' => 'activities',
            'taller' => 'workshops',
            'conferencia' => 'conferences'
        ];
        return $map[strtolower($tipo)] ?? 'all';
    }
    
    // FUNCIÓN PARA OBTENER ICONO DEL TIPO DE EVENTO
    function getIconoTipo($tipo) {
        $iconos = [
            'reuniones' => 'ri-user-voice-line',
            'examen' => 'ri-file-edit-line',
            'actividad' => 'ri-basketball-line',
            'taller' => 'ri-briefcase-line',
            'conferencia' => 'ri-presentation-line'
        ];
        return $iconos[strtolower($tipo)] ?? 'ri-calendar-line';
    }
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Eventos Académicos</title>
  <?php 
    include_once __DIR__ . '/../../layouts/header_coordinador.php'
  ?>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-docente.css">
</head>

<body class="eventos-admin-page">
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
          <div class="title">Eventos Académicos</div>
        </div>
        <div class="topbar-actions">
          <button class="btn-action" onclick="window.location.href='<?= BASE_URL ?>/administrador/registrar-evento'">
            <i class="ri-add-line"></i>
            <span>Nuevo Evento</span>
          </button>
        </div>
        <div class="user">
       <?php
          include_once BASE_PATH . '/app/views/layouts/boton_perfil_solo.php'
        ?>
        </div>
      </div>

      <!-- FILTER SECTION -->
      <section class="filter-section">
        <div class="filter-tabs-events">
          <button class="filter-tab-event active" data-filter="all">
            <i class="ri-calendar-line"></i>
            <span>Todos</span>
            <span class="badge-count">24</span>
          </button>
          <button class="filter-tab-event" data-filter="upcoming">
            <i class="ri-time-line"></i>
            <span>Próximos</span>
            <span class="badge-count">8</span>
          </button>
          <button class="filter-tab-event" data-filter="meetings">
            <i class="ri-user-voice-line"></i>
            <span>Reuniones</span>
            <span class="badge-count">5</span>
          </button>
          <button class="filter-tab-event" data-filter="exams">
            <i class="ri-file-edit-line"></i>
            <span>Exámenes</span>
            <span class="badge-count">6</span>
          </button>
          <button class="filter-tab-event" data-filter="activities">
            <i class="ri-basketball-line"></i>
            <span>Actividades</span>
            <span class="badge-count">5</span>
          </button>
        </div>
        <div class="filter-search">
          <i class="ri-search-2-line"></i>
          <input type="text" placeholder="Buscar eventos..." id="searchEvents">
        </div>
      </section>

      <!-- CALENDAR VIEW SECTION -->
      <section class="events-calendar-section">
        <div class="calendar-view-header">
          <h3>
            <i class="ri-calendar-2-line"></i>
            <span id="calendarMonthYear">Octubre 2024</span>
          </h3>
          <div class="calendar-controls">
            <button class="btn-calendar-nav" id="prevMonthEvents">
              <i class="ri-arrow-left-s-line"></i>
            </button>
            <button class="btn-calendar-today" id="todayBtn">Hoy</button>
            <button class="btn-calendar-nav" id="nextMonthEvents">
              <i class="ri-arrow-right-s-line"></i>
            </button>
          </div>
        </div>

        <div class="calendar-large-grid" id="calendarLargeGrid">
          <!-- Calendar will be generated by JavaScript -->
        </div>
      </section>

      <!-- EVENTS LIST SECTION -->
      <section class="events-list-section">
        <div class="events-list-header">
          <h3>
            <i class="ri-list-check"></i>
            Próximos Eventos
          </h3>
          <div class="view-options">
            
            <button class="btn-view active" data-view="grid">
              <i class="ri-grid-line"></i>
            </button>
            <button class="btn-view " data-view="list">
              <i class="ri-list-check"></i>
            </button>
          </div>
        </div>

        <div class="events-container" id="eventsContainer">
          <?php 
          if(empty($eventos)): 
          ?>
            <div class="no-events-message" style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #999;">
              <i class="ri-calendar-blank-line" style="font-size: 48px; margin-bottom: 20px;"></i>
              <p>No hay eventos registrados aún</p>
              <a href="<?= BASE_URL ?>/administrador/registrar-evento" class="btn btn-primary mt-3">
                <i class="ri-add-line"></i> Crear Primer Evento
              </a>
            </div>
          <?php 
          else:
            foreach($eventos as $evento):
              $categoria = getTipoCategoria($evento['tipo_evento']);
              $icono = getIconoTipo($evento['tipo_evento']);
              $fecha = strtotime($evento['fecha_evento']);
              $diaSemana = getDiaSemana($fecha);
          ?>
            <!-- Evento Dinámico -->
            <div class="event-card" data-category="<?= htmlspecialchars($categoria) ?>" data-date="<?= htmlspecialchars($evento['fecha_evento']) ?>">
              <div class="event-card-header">
                <div class="event-type-badge <?= htmlspecialchars($categoria) ?>">
                  <i class="<?= htmlspecialchars($icono) ?>"></i>
                  <span><?= htmlspecialchars(ucfirst($evento['tipo_evento'])) ?></span>
                </div>
                <div class="event-actions">
                  <a href="<?= BASE_URL ?>/administrador/editar-evento?id=<?= htmlspecialchars($evento['id']) ?>" class="btn-event-action" title="Editar">
                    <i class="ri-edit-line"></i>
                  </a>
                  <button class="btn-event-action" onclick="if(confirm('¿Deseas eliminar este evento?')) window.location.href='<?= BASE_URL ?>/administrador/eliminar-evento?accion=eliminar&id=<?= htmlspecialchars($evento['id']) ?>';" title="Eliminar">
                    <i class="ri-delete-bin-line"></i>
                  </button>
                </div>
              </div>
              <div class="event-card-body">
                <h4><?= htmlspecialchars($evento['nombre_evento']) ?></h4>
                <p><?= htmlspecialchars(substr($evento['descripcion'], 0, 150)) ?><?= strlen($evento['descripcion']) > 150 ? '...' : '' ?></p>
                
                <div class="event-meta">
                  <div class="meta-item">
                    <i class="ri-calendar-line"></i>
                    <span><strong><?= date('d M', $fecha) ?></strong> - <?= ucfirst($diaSemana) ?></span>
                  </div>
                  <div class="meta-item">
                    <i class="ri-time-line"></i>
                    <span><?= htmlspecialchars($evento['hora_inicio']) ?> - <?= htmlspecialchars($evento['hora_fin']) ?></span>
                  </div>
                  <div class="meta-item">
                    <i class="ri-map-pin-line"></i>
                    <span><?= htmlspecialchars($evento['ubicacion']) ?></span>
                  </div>
                </div>

                <div class="event-participants">
                  <div class="participants-avatars">
                    <div class="participant-avatar"><?= strtoupper(substr($evento['responsable'], 0, 2)) ?></div>
                  </div>
                  <span class="participants-text">
                    <?php 
                      $partic = intval($evento['participantes_esperados']);
                      echo $partic > 0 ? "$partic participantes esperados" : "Participantes por confirmar";
                    ?>
                  </span>
                </div>
              </div>
              <div class="event-card-footer">
                <a href="<?= BASE_URL ?>/administrador/editar-evento?id=<?= htmlspecialchars($evento['id']) ?>" class="btn-event-secondary">
                  <i class="ri-information-line"></i>
                  Ver / Editar
                </a>
                <?php if($evento['requiere_confirmacion']): ?>
                  <button class="btn-event-primary">
                    <i class="ri-checkbox-circle-line"></i>
                    Confirmar
                  </button>
                <?php endif; ?>
              </div>
            </div>
          <?php 
            endforeach;
          endif; 
          ?>
        </div>
      </section>
    </main>

    
  </div>

   <!-- Bootstrap and DataTables Scripts -->
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-docente.js"></script>
  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-admin.js"></script>


</body>

</html>