<?php
require_once BASE_PATH . '/app/helpers/session_docente.php';
require_once BASE_PATH . '/app/controllers/docente/view_data.php';

$dataVistaDocenteEventos = obtenerDataVistaDocenteEventos();
extract($dataVistaDocenteEventos, EXTR_SKIP);
?>

<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Eventos Académicos</title>
  <?php include_once __DIR__ . '/../../layouts/header_coordinador.php' ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-docente.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/modo-claro-docente.css">
</head>

<body>
  <div class="app hide-right" id="appGrid" data-eventos='<?= docenteJsonParaHtml($eventosDocente) ?>'>
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
                   data-date="<?= htmlspecialchars($evento['fecha_evento'], ENT_QUOTES, 'UTF-8') ?>"
                   data-nombre="<?= htmlspecialchars($evento['nombre_evento'], ENT_QUOTES, 'UTF-8') ?>"
                   data-descripcion="<?= htmlspecialchars($evento['descripcion'], ENT_QUOTES, 'UTF-8') ?>"
                   data-tipo="<?= htmlspecialchars($evento['category_name'], ENT_QUOTES, 'UTF-8') ?>"
                   data-hora-inicio="<?= htmlspecialchars(substr($evento['hora_inicio'] ?? '', 0, 5), ENT_QUOTES, 'UTF-8') ?>"
                   data-hora-fin="<?= htmlspecialchars(substr($evento['hora_fin'] ?? '', 0, 5), ENT_QUOTES, 'UTF-8') ?>"
                   data-ubicacion="<?= htmlspecialchars($evento['ubicacion'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                   data-responsable="<?= htmlspecialchars($evento['responsable'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                   data-correo="<?= htmlspecialchars($evento['correo_contacto'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                   data-fuente="<?= htmlspecialchars($evento['fuente'], ENT_QUOTES, 'UTF-8') ?>">
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
                <div class="event-card-footer" style="padding:12px 16px; border-top:1px solid rgba(255,255,255,.07);">
                  <button class="btn-event-secondary btn-ver-detalle-evento" style="cursor:pointer;">
                    <i class="ri-information-line"></i> Ver detalles
                  </button>
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

  <!-- Modal: detalle de evento institucional -->
  <div class="modal fade" id="modalDetalleEvento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content" style="background:#11193a; color:#e6e9f4; border:1px solid rgba(255,255,255,.08); border-radius:16px;">
        <div class="modal-header" style="border-bottom:1px solid rgba(255,255,255,.08); padding:20px 24px;">
          <div>
            <span id="mde-tipo" style="display:inline-block; padding:4px 12px; border-radius:999px; font-size:12px; font-weight:600; background:rgba(79,70,229,.25); color:#a4b1ff; border:1px solid rgba(164,177,255,.25); margin-bottom:8px;"></span>
            <h5 class="modal-title" id="mde-titulo" style="margin:0; font-size:20px; font-weight:700;"></h5>
          </div>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="padding:24px;">
          <p id="mde-descripcion" style="color:#b8c2df; line-height:1.6; margin-bottom:20px;"></p>
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div id="mde-fecha-wrap" style="background:#0e1632; border-radius:10px; padding:14px 16px;">
              <div style="font-size:11px; color:#6b7898; text-transform:uppercase; letter-spacing:.06em; margin-bottom:4px;">Fecha</div>
              <div style="font-weight:600;" id="mde-fecha"></div>
            </div>
            <div id="mde-horario-wrap" style="background:#0e1632; border-radius:10px; padding:14px 16px;">
              <div style="font-size:11px; color:#6b7898; text-transform:uppercase; letter-spacing:.06em; margin-bottom:4px;">Horario</div>
              <div style="font-weight:600;" id="mde-horario"></div>
            </div>
            <div id="mde-ubicacion-wrap" style="background:#0e1632; border-radius:10px; padding:14px 16px;">
              <div style="font-size:11px; color:#6b7898; text-transform:uppercase; letter-spacing:.06em; margin-bottom:4px;"><i class="ri-map-pin-line"></i> Ubicación</div>
              <div id="mde-ubicacion"></div>
            </div>
            <div id="mde-responsable-wrap" style="background:#0e1632; border-radius:10px; padding:14px 16px;">
              <div style="font-size:11px; color:#6b7898; text-transform:uppercase; letter-spacing:.06em; margin-bottom:4px;"><i class="ri-user-line"></i> Responsable</div>
              <div id="mde-responsable"></div>
              <div id="mde-correo" style="font-size:12px; color:#6b7898; margin-top:4px;"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-docente.js?v=<?= $mainDocenteJsVersion ?>"></script>
  <script>
  (function () {
    const modal = document.getElementById('modalDetalleEvento');
    if (!modal) return;
    const bsModal = new bootstrap.Modal(modal);

    document.querySelectorAll('.btn-ver-detalle-evento').forEach(btn => {
      btn.addEventListener('click', function (e) {
        e.stopPropagation();
        const card = this.closest('.event-card');
        if (!card) return;

        const nombre      = card.dataset.nombre      || '';
        const descripcion = card.dataset.descripcion || '';
        const tipo        = card.dataset.tipo        || '';
        const fecha       = card.dataset.date        || '';
        const horaInicio  = card.dataset.horaInicio  || '';
        const horaFin     = card.dataset.horaFin     || '';
        const ubicacion   = card.dataset.ubicacion   || '';
        const responsable = card.dataset.responsable || '';
        const correo      = card.dataset.correo      || '';
        const fuente      = card.dataset.fuente      || 'evento';

        modal.querySelector('#mde-tipo').textContent        = tipo || (fuente === 'actividad' ? 'Actividad' : 'Evento');
        modal.querySelector('#mde-titulo').textContent      = nombre;
        modal.querySelector('#mde-descripcion').textContent = descripcion || 'Sin descripción.';

        // Fecha
        if (fecha) {
          const [y, m, d] = fecha.split('-');
          modal.querySelector('#mde-fecha').textContent = `${d}/${m}/${y}`;
        }

        // Horario
        const horario = horaInicio && horaFin ? `${horaInicio} — ${horaFin}`
                      : horaInicio ? `Desde ${horaInicio}` : 'Sin hora definida';
        modal.querySelector('#mde-horario').textContent = horario;

        // Ubicación y responsable solo aparecen para eventos institucionales
        const ubicWrap = modal.querySelector('#mde-ubicacion-wrap');
        const respWrap = modal.querySelector('#mde-responsable-wrap');
        if (fuente === 'evento' && (ubicacion || responsable)) {
          modal.querySelector('#mde-ubicacion').textContent   = ubicacion  || '—';
          modal.querySelector('#mde-responsable').textContent = responsable || '—';
          modal.querySelector('#mde-correo').textContent      = correo || '';
          if (ubicWrap) ubicWrap.style.display = '';
          if (respWrap) respWrap.style.display = '';
        } else {
          if (ubicWrap) ubicWrap.style.display = 'none';
          if (respWrap) respWrap.style.display = 'none';
        }

        bsModal.show();
      });
    });
  })();
  </script>
</body>

</html>
