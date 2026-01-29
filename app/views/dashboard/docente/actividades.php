
<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Actividades</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">

  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-docente.css">
    <!-- <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/docente/actividades.css"> -->
</head>

<body>
<div class="app" id="appGrid">

  <!-- SIDEBAR -->
  <?php include_once __DIR__ . '/../../layouts/sidebar_docente.php'; ?>

  <!-- MAIN -->
  <main class="main">

    <!-- TOPBAR -->
    <div class="topbar">
      <div class="topbar-left">
        <button class="toggle-btn" id="toggleLeft">
          <i class="ri-menu-2-line"></i>
        </button>
        <div class="title">Actividades · Grado 10° A</div>
      </div>

      <a href="#" class="btn-primary">
        <i class="ri-add-line"></i>
        Crear actividad
      </a>
    </div>

    <!-- INFO DEL CURSO -->
    <div class="teacher-info-bar">
      <div class="teacher-profile">
        <div class="teacher-avatar">MA</div>
        <div>
          <strong>Matemáticas Avanzadas</strong>
          <small>Grado 10° A · Jornada Mañana</small>
        </div>
      </div>

      <div class="teacher-stats">
        <div class="stat-item">
          <i class="ri-file-list-line"></i>
          <div>
            <strong>6</strong>
            <small>Actividades</small>
          </div>
        </div>
        <div class="stat-item">
          <i class="ri-time-line"></i>
          <div>
            <strong>2</strong>
            <small>Abiertas</small>
          </div>
        </div>
        <div class="stat-item">
          <i class="ri-lock-line"></i>
          <div>
            <strong>4</strong>
            <small>Cerradas</small>
          </div>
        </div>
      </div>
    </div>

    <!-- GRID ACTIVIDADES -->
    <section class="actividades-grid">

      <!-- ACTIVIDAD -->
      <div class="actividad-card">
        <div class="actividad-header">
          <h4>Trabajo de Álgebra</h4>
          <span class="badge badge-open">Abierta</span>
        </div>

        <p class="actividad-desc">
          Resolver los ejercicios del capítulo 3 del libro guía.
        </p>

        <div class="actividad-meta">
          <span><i class="ri-calendar-line"></i> Entrega: 05 Nov 2025</span>
          <span><i class="ri-file-list-line"></i> Tarea</span>
        </div>

        <div class="actividad-actions">
          <button class="btn-secondary">
            <i class="ri-eye-line"></i>
            Ver entregas
          </button>
          <button class="btn-secondary">
            <i class="ri-pencil-line"></i>
            Editar
          </button>
        </div>
      </div>

      <!-- ACTIVIDAD CERRADA -->
      <div class="actividad-card">
        <div class="actividad-header">
          <h4>Examen de Funciones</h4>
          <span class="badge badge-closed">Cerrada</span>
        </div>

        <p class="actividad-desc">
          Evaluación escrita sobre funciones cuadráticas.
        </p>

        <div class="actividad-meta">
          <span><i class="ri-calendar-line"></i> 28 Oct 2025</span>
          <span><i class="ri-file-list-line"></i> Examen</span>
        </div>

        <div class="actividad-actions">
          <button class="btn-secondary">
            <i class="ri-eye-line"></i>
            Ver calificaciones
          </button>
        </div>
      </div>

      <!-- CARD CREAR -->
      <div class="actividad-card actividad-nueva">
        <i class="ri-add-circle-line"></i>
        <p>Crear nueva actividad</p>
      </div>

    </section>

  </main>

  <!-- RIGHTBAR -->
  <?php include_once __DIR__ . '/../../layouts/rightbar_docente.php'; ?>

</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="<?= BASE_URL ?>/public/assets/dashboard/js/docente/actividades.js"></script>
</body>
</html>
