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
            <h3><?= $estadisticas['total_materias'] ?></h3>
            <p>Materias Activas</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon green">
            <i class="ri-medal-line"></i>
          </div>
          <div class="stat-content">
            <h3><?= number_format($estadisticas['promedio_general'], 1) ?></h3>
            <p>Promedio General</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon orange">
            <i class="ri-alert-line"></i>
          </div>
          <div class="stat-content">
            <h3><?= $estadisticas['en_riesgo'] ?></h3>
            <p>En Riesgo</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon red">
            <i class="ri-time-line"></i>
          </div>
          <div class="stat-content">
            <h3><?= $estadisticas['actividades_pendientes'] ?></h3>
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

        <?php if (empty($materias)): ?>
          <div style="grid-column: 1/-1; text-align: center; padding: 60px 20px; color: #97a1b6;">
            <i class="ri-book-line" style="font-size: 64px; opacity: 0.5;"></i>
            <h3 style="margin-top: 24px; color: #fff;">No tienes materias asignadas</h3>
            <p style="margin-top: 12px; font-size: 16px;">Contacta con tu coordinador académico</p>
          </div>
        <?php else: ?>
          <?php foreach ($materias as $materia): ?>
            <div class="materia-card" data-status="<?= $materia['estado_nota'] ?>">
              <div class="materia-status <?= $materia['estado_nota'] ?>"></div>
              <div class="materia-header">
                <div class="materia-icon" style="background: <?= $materia['color_icono'] ?>">
                  <i class="<?= $materia['icono'] ?>"></i>
                </div>
                <?php if ($materia['promedio']): ?>
                  <div class="materia-nota <?= $materia['estado_nota'] ?>"><?= number_format($materia['promedio'], 1) ?></div>
                <?php else: ?>
                  <div class="materia-nota sin-nota">--</div>
                <?php endif; ?>
              </div>
              <h3 class="materia-title"><?= htmlspecialchars($materia['materia']) ?></h3>
              <p class="materia-subtitle"><?= htmlspecialchars($materia['descripcion'] ?: 'Sin descripción') ?></p>

              <div class="materia-profesor">
                <div class="profesor-avatar"><?= $materia['iniciales_docente'] ?></div>
                <div class="profesor-info">
                  <strong>Prof. <?= htmlspecialchars($materia['docente_nombres'] . ' ' . $materia['docente_apellidos']) ?></strong>
                  <small><?= htmlspecialchars($materia['docente_correo']) ?></small>
                </div>
              </div>

              <div class="materia-stats">
                <div class="stat-item">
                  <i class="ri-file-list-line"></i>
                  <span><?= $materia['total_actividades'] ?> actividades</span>
                </div>
                <div class="stat-item <?= $materia['actividades_pendientes'] > 0 ? 'warning' : 'success' ?>">
                  <?php if ($materia['actividades_pendientes'] > 0): ?>
                    <i class="ri-time-line"></i>
                    <span><?= $materia['actividades_pendientes'] ?> pendientes</span>
                  <?php else: ?>
                    <i class="ri-checkbox-circle-line"></i>
                    <span>0 pendientes</span>
                  <?php endif; ?>
                </div>
                <div class="stat-item">
                  <i class="ri-calendar-check-line"></i>
                  <span>-- asistencia</span>
                </div>
              </div>

              <div class="materia-actions">
                <button class="btn-materia primary" onclick="window.location.href='<?= BASE_URL ?>/estudiante-materia-detalle?id=<?= $materia['id_asignatura_curso'] ?>'">
                  <i class="ri-file-list-3-line"></i> Ver Actividades
                </button>
                <button class="btn-materia secondary">
                  <i class="ri-folder-2-line"></i>
                </button>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
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
            <small><?= $estadisticas['actividades_pendientes'] ?> tareas pendientes</small>
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
        <?php if (empty($actividades_proximas)): ?>
          <div style="text-align: center; padding: 20px; color: #97a1b6;">
            <i class="ri-checkbox-circle-line" style="font-size: 32px; opacity: 0.5;"></i>
            <p style="margin-top: 12px; font-size: 14px;">¡Estás al día con tus actividades!</p>
          </div>
        <?php else: ?>
          <?php foreach ($actividades_proximas as $actividad): ?>
            <?php 
              $dias_restantes = floor((strtotime($actividad['fecha_entrega']) - time()) / (60 * 60 * 24));
              $es_urgente = $dias_restantes <= 1;
            ?>
            <div class="deadline-item <?= $es_urgente ? 'urgent' : '' ?>">
              <div class="deadline-date">
                <span class="day"><?= date('d', strtotime($actividad['fecha_entrega'])) ?></span>
                <span class="month"><?= obtenerMesAbreviado($actividad['fecha_entrega']) ?></span>
              </div>
              <div class="deadline-content">
                <strong><?= htmlspecialchars($actividad['titulo']) ?></strong>
                <small><?= htmlspecialchars($actividad['materia']) ?> - Prof. <?= htmlspecialchars($actividad['docente_nombres']) ?></small>
                <div class="deadline-time">
                  <i class="ri-time-line"></i> <?= formatearFechaProxima($actividad['fecha_entrega']) ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
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