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
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-acudiente.css?v=<?= @filemtime(BASE_PATH . '/public/assets/dashboard/css/styles-acudiente.css') ?: 1 ?>">
  <style>
    .student-avatar img {
      width: 100%;
      height: 100%;
      border-radius: 50%;
      object-fit: cover;
    }

    .empty-state {
      text-align: center;
      padding: 40px 20px;
      color: #c7cbe1;
    }

    .empty-state i {
      font-size: 48px;
      color: #4f46e5;
      margin-bottom: 12px;
      display: block;
    }

    .upcoming-list {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 12px;
    }

    .upcoming-list .upcoming-item {
      display: flex;
      align-items: center;
      gap: 10px;
      background: #171a28;
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 12px 14px;
      color: #c7cbe1;
      font-size: 14px;
    }

    .upcoming-list .upcoming-item i {
      font-size: 20px;
      color: #a4b1ff;
    }
  </style>
</head>
<body>
  <div class="app hide-right" id="appGrid">
    <?php include_once __DIR__ . '/../../layouts/sidebar_acudiente.php' ?>

    <!-- MAIN -->
    <main class="main">
      <div class="topbar">
        <div class="topbar-left">
          <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
            <i class="ri-menu-2-line"></i>
          </button>
          <div class="title">Panel de Acudiente</div>
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

        <!-- UPCOMING SECTIONS -->
        <section class="card">
          <h3>Próximamente</h3>
          <div class="upcoming-list">
            <div class="upcoming-item"><i class="ri-bar-chart-2-line"></i> Calificaciones</div>
            <div class="upcoming-item"><i class="ri-file-paper-2-line"></i> Boletines</div>
            <div class="upcoming-item"><i class="ri-calendar-check-line"></i> Asistencia</div>
            <div class="upcoming-item"><i class="ri-book-2-line"></i> Materias y horario</div>
            <div class="upcoming-item"><i class="ri-task-line"></i> Actividades</div>
            <div class="upcoming-item"><i class="ri-user-3-line"></i> Profesores</div>
            <div class="upcoming-item"><i class="ri-calendar-event-line"></i> Eventos académicos</div>
            <div class="upcoming-item"><i class="ri-notification-3-line"></i> Notificaciones</div>
          </div>
        </section>

      <?php endif; ?>
    </main>
  </div>

  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-acudiente.js"></script>
</body>
</html>
