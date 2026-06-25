<?php
require_once BASE_PATH . '/app/helpers/session_acudiente.php';
require_once BASE_PATH . '/app/controllers/acudiente/view_data.php';

$dataVistaAcudienteDashboard = obtenerDataVistaAcudienteDashboard();
extract($dataVistaAcudienteDashboard, EXTR_SKIP);

$estudiante = $estudianteSeleccionado;

if ($estudiante) {
    $nombreCompleto = trim($estudiante['nombres'] . ' ' . $estudiante['apellidos']);
    $cursoActual = $estudiante['id_curso']
        ? $estudiante['grado'] . '° - ' . $estudiante['nombre_curso']
        : 'Sin matrícula activa';
}
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Panel de Acudiente</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <?php include_once __DIR__ . '/../../layouts/header_coordinador.php' ?>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-acudiente.css?v=<?= @filemtime(BASE_PATH . '/public/assets/dashboard/css/styles-acudiente.css') ?: 1 ?>">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/acudiente-dashboard.css?v=<?= @filemtime(BASE_PATH . '/public/assets/dashboard/css/acudiente-dashboard.css') ?: 1 ?>">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/modo-claro-acudiente.css">
</head>
<body>
  <div class="app hide-right" id="appGrid" data-eventos="<?= htmlspecialchars($eventosJson ?? '[]', ENT_QUOTES, 'UTF-8') ?>">
    <?php include_once __DIR__ . '/../../layouts/sidebar_acudiente.php' ?>

    <!-- MAIN -->
    <main class="main">
      <div class="topbar">
        <div class="topbar-left">
          <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
            <i class="ri-menu-2-line"></i>
          </button>
          <div class="title">
            <span class="title-full">Panel de Acudiente</span>
            <span class="title-short">P. Acudiente</span>
          </div>
        </div>
        <?php include_once BASE_PATH . '/app/views/layouts/boton_perfil_solo.php'; ?>
      </div>

      <?php if (empty($estudiantesAsociados)): ?>
        <section class="card">
          <div class="empty-state">
            <i class="ri-user-search-line"></i>
            <h3>No tienes estudiantes asociados</h3>
            <p>Si crees que esto es un error, comunícate con la institución para verificar la vinculación.</p>
          </div>
        </section>
      <?php else: ?>

        <?php include __DIR__ . '/../../layouts/mis_estudiantes_acudiente.php'; ?>

        <!-- STUDENT PROFILE -->
        <div class="student-profile">
          <div class="student-avatar">
            <img src="<?= BASE_URL ?>/public/uploads/estudiantes/<?= htmlspecialchars($estudiante['foto'] ?: 'default.png') ?>" alt="" onerror="this.onerror=null; this.src='<?= BASE_URL ?>/public/uploads/estudiantes/default.png'">
          </div>
          <div class="student-info">
            <h2><?= htmlspecialchars($nombreCompleto) ?></h2>
            <div class="student-meta">
              <span><i class="ri-book-line"></i> <?= htmlspecialchars($cursoActual) ?></span>
              <span><i class="ri-account-circle-line"></i> <?= htmlspecialchars($estudiante['tipo_documento'] . ': ' . $estudiante['documento']) ?></span>
              <?php if ($estudiante['jornada']): ?>
                <span><i class="ri-time-line"></i> Jornada <?= htmlspecialchars($estudiante['jornada']) ?></span>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- MODULE LINKS -->
        <section class="card">
          <h3>Acceso Rápido</h3>
          <div class="module-links">
            <a class="module-link" href="<?= BASE_URL ?>/acudiente/calificaciones">
              <i class="ri-bar-chart-2-line"></i>
              Calificaciones
            </a>
            <a class="module-link" href="<?= BASE_URL ?>/acudiente/boletines">
              <i class="ri-file-paper-2-line"></i>
              Boletines
            </a>
            <a class="module-link" href="<?= BASE_URL ?>/acudiente/asistencia">
              <i class="ri-calendar-check-line"></i>
              Asistencia
            </a>
            <a class="module-link" href="<?= BASE_URL ?>/acudiente/horario">
              <i class="ri-book-2-line"></i>
              Horario
            </a>
            <a class="module-link" href="<?= BASE_URL ?>/acudiente/actividades">
              <i class="ri-task-line"></i>
              Actividades
            </a>
            <a class="module-link" href="<?= BASE_URL ?>/acudiente/profesores">
              <i class="ri-user-3-line"></i>
              Profesores
            </a>
            <a class="module-link" href="<?= BASE_URL ?>/acudiente/eventos">
              <i class="ri-calendar-event-line"></i>
              Eventos
            </a>
            <a class="module-link" href="<?= BASE_URL ?>/notificaciones">
              <i class="ri-notification-3-line"></i>
              Notificaciones
            </a>
          </div>
        </section>

      <?php endif; ?>
    </main>
  </div>

  <!-- Modal: eventos del día (clic en celda del calendario) -->
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

  <!-- Modal: detalle completo de un evento -->
  <div class="modal fade" id="modalDetalleEvento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content" style="background:#11193a;color:#e6e9f4;border:1px solid rgba(255,255,255,.08);border-radius:16px;">
        <div class="modal-header" style="border-bottom:1px solid rgba(255,255,255,.08);padding:20px 24px;">
          <div>
            <span id="mde-tipo" style="display:inline-block;padding:4px 12px;border-radius:999px;font-size:12px;font-weight:600;background:rgba(79,70,229,.25);color:#a4b1ff;border:1px solid rgba(164,177,255,.25);margin-bottom:8px;"></span>
            <h5 class="modal-title" id="mde-titulo" style="margin:0;font-size:20px;font-weight:700;"></h5>
          </div>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="padding:24px;">
          <p id="mde-descripcion" style="color:#b8c2df;line-height:1.6;margin-bottom:20px;"></p>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
            <div style="background:#0e1632;border-radius:10px;padding:14px 16px;">
              <div style="font-size:11px;color:#6b7898;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Fecha</div>
              <div style="font-weight:600;" id="mde-fecha"></div>
            </div>
            <div style="background:#0e1632;border-radius:10px;padding:14px 16px;">
              <div style="font-size:11px;color:#6b7898;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Horario</div>
              <div style="font-weight:600;" id="mde-horario"></div>
            </div>
            <div id="mde-ubicacion-wrap" style="background:#0e1632;border-radius:10px;padding:14px 16px;">
              <div style="font-size:11px;color:#6b7898;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;"><i class="ri-map-pin-line"></i> Ubicación</div>
              <div id="mde-ubicacion"></div>
            </div>
            <div id="mde-responsable-wrap" style="background:#0e1632;border-radius:10px;padding:14px 16px;">
              <div style="font-size:11px;color:#6b7898;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;"><i class="ri-user-line"></i> Responsable</div>
              <div id="mde-responsable"></div>
              <div id="mde-correo" style="font-size:12px;color:#6b7898;margin-top:4px;"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-acudiente.js"></script>
  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/acudiente-eventos.js?v=<?= @filemtime(BASE_PATH . '/public/assets/dashboard/js/acudiente-eventos.js') ?: 1 ?>"></script>
</body>
</html>
