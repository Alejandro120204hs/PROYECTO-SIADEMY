<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once BASE_PATH . '/app/controllers/perfil.php';
$id = $_SESSION['user']['id'] ?? 0;
$usuario = mostrarPerfil($id);
$mainDocenteJsVersion = @filemtime(BASE_PATH . '/public/assets/dashboard/js/main-docente.js') ?: time();
?>

<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Eventos Académicos</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-docente.css">
</head>

<body>
  <div class="app hide-right" id="appGrid">
    <?php include_once __DIR__ . '/../../layouts/sidebar_docente.php' ?>

    <main class="main">
      <div class="topbar">
        <div class="topbar-left">
          <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
            <i class="ri-menu-2-line"></i>
          </button>
          <div class="title">Eventos Académicos</div>
        </div>
        <?php include_once BASE_PATH . '/app/views/layouts/boton_perfil_solo.php'; ?>
      </div>

      <section class="filter-section">
        <div class="filter-tabs-events">
          <button class="filter-tab-event active" data-filter="all">
            <i class="ri-calendar-line"></i>
            <span>Todos</span>
            <span class="badge-count"><?= (int)($statsEventos['all'] ?? 0) ?></span>
          </button>
          <button class="filter-tab-event" data-filter="upcoming">
            <i class="ri-time-line"></i>
            <span>Próximos</span>
            <span class="badge-count"><?= (int)($statsEventos['upcoming'] ?? 0) ?></span>
          </button>
          <button class="filter-tab-event" data-filter="meetings">
            <i class="ri-user-voice-line"></i>
            <span>Reuniones</span>
            <span class="badge-count"><?= (int)($statsEventos['meetings'] ?? 0) ?></span>
          </button>
          <button class="filter-tab-event" data-filter="exams">
            <i class="ri-file-edit-line"></i>
            <span>Exámenes</span>
            <span class="badge-count"><?= (int)($statsEventos['exams'] ?? 0) ?></span>
          </button>
          <button class="filter-tab-event" data-filter="activities">
            <i class="ri-basketball-line"></i>
            <span>Actividades</span>
            <span class="badge-count"><?= (int)($statsEventos['activities'] ?? 0) ?></span>
          </button>
        </div>
        <div class="filter-search">
          <i class="ri-search-2-line"></i>
          <input type="text" placeholder="Buscar eventos..." id="searchEvents">
        </div>
      </section>

      <section class="events-calendar-section">
        <div class="calendar-view-header">
          <h3>
            <i class="ri-calendar-2-line"></i>
            <span id="calendarMonthYear"></span>
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
        </div>
      </section>

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
          <?php if (empty($eventosDocente)): ?>
            <div class="event-card" data-category="none" data-date="">
              <div class="event-card-body">
                <h4>Sin eventos registrados</h4>
                <p>Aún no hay eventos institucionales ni actividades con fecha programada.</p>
              </div>
            </div>
          <?php else: ?>
            <?php foreach ($eventosDocente as $evento): ?>
              <div class="event-card"
                   data-category="<?= htmlspecialchars($evento['category'], ENT_QUOTES, 'UTF-8') ?>"
                   data-upcoming="<?= !empty($evento['is_upcoming']) ? '1' : '0' ?>"
                   data-date="<?= htmlspecialchars($evento['fecha_evento'], ENT_QUOTES, 'UTF-8') ?>">
                <div class="event-card-header">
                  <div class="event-type-badge <?= htmlspecialchars($evento['category'], ENT_QUOTES, 'UTF-8') ?>">
                    <i class="<?= htmlspecialchars($evento['icon'], ENT_QUOTES, 'UTF-8') ?>"></i>
                    <span><?= htmlspecialchars($evento['category_name'], ENT_QUOTES, 'UTF-8') ?></span>
                  </div>
                </div>
                <div class="event-card-body">
                  <h4><?= htmlspecialchars($evento['nombre_evento'], ENT_QUOTES, 'UTF-8') ?></h4>
                  <p><?= htmlspecialchars($evento['descripcion'], ENT_QUOTES, 'UTF-8') ?></p>

                  <div class="event-meta">
                    <div class="meta-item">
                      <i class="ri-calendar-line"></i>
                      <span><strong><?= date('d M, Y', strtotime($evento['fecha_evento'])) ?></strong></span>
                    </div>
                    <div class="meta-item">
                      <i class="ri-time-line"></i>
                      <span><?= !empty($evento['hora_inicio']) ? htmlspecialchars(substr($evento['hora_inicio'], 0, 5), ENT_QUOTES, 'UTF-8') : 'Sin hora definida' ?></span>
                    </div>
                    <div class="meta-item">
                      <i class="ri-information-line"></i>
                      <span><?= $evento['fuente'] === 'actividad' ? 'Actividad docente' : 'Evento institucional' ?></span>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </section>
    </main>
  </div>

  <div class="modal fade" id="dayEventsModal" tabindex="-1" aria-labelledby="dayEventsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content calendar-day-modal-content">
        <div class="modal-header calendar-day-modal-header">
          <h5 class="modal-title" id="dayEventsModalLabel">Eventos del día</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body" id="dayEventsModalBody"></div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    window.docenteEventosData = <?= json_encode($eventosDocente, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
  </script>
  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-docente.js?v=<?= $mainDocenteJsVersion ?>"></script>
</body>

</html>
