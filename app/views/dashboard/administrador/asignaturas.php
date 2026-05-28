<?php 
  require_once BASE_PATH . '/app/helpers/session_administrador.php';
  require_once BASE_PATH . '/app/controllers/administrador/view_data.php';

  extract(obtenerDataVistaAdminAsignaturas(), EXTR_SKIP);
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
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-admin.css?v=<?= $adminCssVersion ?>">
  
</head>

<body class="admin-asignaturas-page">
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
          <div class="title">Gestión de Asignaturas</div>
        </div>
        <div class="search">
          <i class="ri-search-2-line"></i>
          <input type="text" placeholder="Buscar asignatura o profesor...">
        </div>        
        <div class="topbar-actions">
          <button class="btn-agregar-estudiante" onclick="window.location.href='administrador/registrar-asignatura'">
            <i class="ri-add-line"></i> Agregar Asignatura
          </button>
          <button class="btn-agregar-estudiante" onclick="window.location.href='<?= BASE_URL ?>/administrador/asignar-docentes'" >
            <i class="ri-user-add-line"></i> Asignar Docentes
          </button>
        </div>

        
        <?php
  include_once BASE_PATH . '/app/views/layouts/boton_perfil_solo.php'
?>
      </div>

      <!-- KPI CARDS -->
      <div class="kpis">
        <div class="kpi">
          <div class="icon"><i class="ri-booklet-line"></i></div>
          <div>
            <small>Total Asignaturas</small>
            <strong><?php echo $totalAsignaturas; ?></strong>
          </div>
        </div>
        <div class="kpi">
          <div class="icon"><i class="ri-user-star-line"></i></div>
          <div>
            <small>Profesores</small>
            <strong><?php echo $totalProfesores; ?></strong>
          </div>
        </div>
        <div class="kpi">
          <div class="icon"><i class="ri-line-chart-line"></i></div>
          <div>
            <small>Promedio General</small>
            <strong><?= $promedioGeneral ?></strong>
          </div>
        </div>
      </div>

   

      <!-- SUBJECTS GRID -->
      <section class="subjects-section">
        <div class="subjects-header">
          
          <h3>Asignaturas Activas (<?= $totalAsignaturas ?>)</h3>
          <div class="view-toggle">
            <button class="view-btn active" data-view="grid"><i class="ri-grid-line"></i></button>
            <button class="view-btn" data-view="list"><i class="ri-list-check"></i></button>
          </div>
        </div>

        <!-- VISTA GRID (cards) -->
        <div class="subjects-grid" id="viewGrid">
          <?php if(!empty($asignaturas)): ?>
          <?php foreach($asignaturas as $asignatura): ?>

          <div class="subject-card">
            <div class="subject-header">
              <div class="subject-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <i class="ri-calculator-line"></i>
              </div>
              <div class="subject-status status-active <?= htmlspecialchars($asignatura['estado']) ?>"><?= htmlspecialchars($asignatura['estado']) ?></div>
            </div>

            <h4><?= htmlspecialchars($asignatura['nombre']) ?></h4>
            <p class="subject-area"><?= htmlspecialchars($asignatura['descripcion']) ?></p>

            <div class="subject-info">
              <div class="info-item">
                <i class="ri-user-line"></i>
                <div>
                  <span class="info-label">Profesores</span>
                  <strong><?= (int)$asignatura['stat_docentes'] ?></strong>
                </div>
              </div>
              <div class="info-item">
                <i class="ri-book-open-line"></i>
                <div>
                  <span class="info-label">Cursos</span>
                  <strong><?= (int)$asignatura['stat_cursos'] ?></strong>
                </div>
              </div>
            </div>

            <div class="subject-stats">
              <div class="stat-box">
                <span class="stat-label">Promedio</span>
                <strong class="stat-value grade-good"><?= $asignatura['stat_promedio'] !== '—' ? number_format((float)$asignatura['stat_promedio'], 1) : '—' ?></strong>
              </div>
              <div class="stat-box">
                <span class="stat-label">Estudiantes</span>
                <strong class="stat-value"><?= (int)$asignatura['stat_estudiantes'] ?></strong>
              </div>
            </div>

            <div class="subject-actions">
              <button class="btn-secondary" onclick="window.location.href='<?= BASE_URL ?>/administrador/detalle-asignatura?id=<?= (int)$asignatura['id'] ?>'"><i class="bi bi-eye"></i></button>
              <button class="btn-secondary"><a href="<?= BASE_URL ?>/administrador/editar-asignatura?id=<?= (int)$asignatura['id'] ?>"><i class="bi bi-pencil-square"></i></a></button>
              <button class="btn-secondary"><a href="<?= BASE_URL ?>/administrador/eliminar-asignatura?accion=eliminar&id=<?= (int)$asignatura['id'] ?>"><i class="bi bi-trash3-fill"></i></a></button>
            </div>
          </div>
          <?php endforeach; ?>
          <?php else: ?>
            <p style="color:#8b91a3;padding:24px;">No hay asignaturas registradas</p>
          <?php endif; ?>
        </div>

        <!-- VISTA TABLA -->
        <div id="viewList" style="display:none; padding: 0 0 24px;">
          <div class="datatable-card table-scroll-x">
            <table class="table table-dark table-hover" style="margin:0;">
              <thead>
                <tr>
                  <th>Asignatura</th>
                  <th>Estado</th>
                  <th style="text-align:center">Profesores</th>
                  <th style="text-align:center">Cursos</th>
                  <th style="text-align:center">Promedio</th>
                  <th style="text-align:center">Estudiantes</th>
                  <th style="text-align:center">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php if(!empty($asignaturas)): ?>
                <?php foreach($asignaturas as $asignatura): ?>
                <tr>
                  <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                      <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="ri-calculator-line" style="color:#fff;font-size:15px;"></i>
                      </div>
                      <div>
                        <strong style="font-size:14px;"><?= htmlspecialchars($asignatura['nombre']) ?></strong>
                        <?php if($asignatura['descripcion']): ?>
                          <br><small style="color:#8b91a3;"><?= htmlspecialchars($asignatura['descripcion']) ?></small>
                        <?php endif; ?>
                      </div>
                    </div>
                  </td>
                  <td>
                    <?php if($asignatura['estado'] === 'Activo'): ?>
                      <span style="background:rgba(16,185,129,.15);color:#10b981;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:600;">Activo</span>
                    <?php else: ?>
                      <span style="background:rgba(239,68,68,.15);color:#ef4444;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:600;">Inactivo</span>
                    <?php endif; ?>
                  </td>
                  <td style="text-align:center;"><?= (int)$asignatura['stat_docentes'] ?></td>
                  <td style="text-align:center;"><?= (int)$asignatura['stat_cursos'] ?></td>
                  <td style="text-align:center;">
                    <?php
                      $prom = $asignatura['stat_promedio'];
                      if ($prom !== '—') {
                        $p = (float)$prom;
                        $c = $p >= 4.5 ? '#10b981' : ($p >= 4.0 ? '#3b82f6' : ($p > 3.0 ? '#f59e0b' : '#ef4444'));
                        echo '<strong style="color:'.$c.'">'.number_format($p,1).'</strong>';
                      } else {
                        echo '<span style="color:#8b91a3;">—</span>';
                      }
                    ?>
                  </td>
                  <td style="text-align:center;"><?= (int)$asignatura['stat_estudiantes'] ?></td>
                  <td style="text-align:center;">
                    <div class="acciones">
                      <a class="btn-action" href="<?= BASE_URL ?>/administrador/detalle-asignatura?id=<?= (int)$asignatura['id'] ?>" title="Ver detalle"><i class="bi bi-eye"></i></a>
                      <a class="btn-action" href="<?= BASE_URL ?>/administrador/editar-asignatura?id=<?= (int)$asignatura['id'] ?>" title="Editar"><i class="bi bi-pencil-square"></i></a>
                      <a class="btn-action" href="<?= BASE_URL ?>/administrador/eliminar-asignatura?accion=eliminar&id=<?= (int)$asignatura['id'] ?>" title="Eliminar" onclick="return confirm('¿Eliminar esta asignatura?')"><i class="bi bi-trash3-fill"></i></a>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr><td colspan="7" style="text-align:center;color:#8b91a3;padding:32px;">No hay asignaturas registradas</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

      </section>

    </main>

    
  </div>

  <!-- FOOTER -->
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-admin.js?v=<?= $mainAdminJsVersion ?>"></script>
 
</body>

</html>