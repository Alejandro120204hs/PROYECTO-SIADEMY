<?php 
  require_once BASE_PATH . '/app/helpers/session_administrador.php';
?>

<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Asignar Docentes</title>
  <?php
    include_once __DIR__ . '/../../layouts/header_coordinador.php'
  ?>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-admin.css">
  
  <!-- Select2 para mejorar los selects -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  
  <style>
    /* Layout de 2 columnas sin sidebar derecho */
    .app {
      grid-template-columns: 260px 1fr !important;
    }

    .app.hide-left {
      grid-template-columns: 0 1fr !important;
    }

    /* Estilos específicos para esta página */
    .form-card {
      background: #151d3e;
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 28px;
      margin-bottom: 24px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .form-card h3 {
      font-size: 20px;
      font-weight: 600;
      margin-bottom: 8px;
      color: #fff;
    }

    .form-card p {
      color: var(--muted);
      font-size: 14px;
      margin-bottom: 24px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      color: #d7d9df;
      font-size: 14px;
      font-weight: 500;
      margin-bottom: 8px;
    }

    .form-group label i {
      color: var(--brand);
      margin-right: 6px;
    }

    .form-control, .form-select {
      width: 100%;
      background: #0f1736;
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 12px 16px;
      color: #fff;
      font-size: 14px;
      transition: all 0.2s ease;
    }

    .form-control:focus, .form-select:focus {
      border-color: var(--brand);
      outline: none;
      box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    .btn-submit {
      background: var(--brand);
      color: white;
      border: none;
      border-radius: 10px;
      padding: 12px 28px;
      font-weight: 600;
      font-size: 14px;
      cursor: pointer;
      transition: all 0.2s ease;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }

    .btn-submit:hover {
      background: #4338ca;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }

    .table-card {
      background: #151d3e;
      border: 1px solid var(--border);
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .table-card-header {
      padding: 20px 28px;
      border-bottom: 1px solid var(--border);
    }

    .table-card-header h3 {
      font-size: 18px;
      font-weight: 600;
      color: #fff;
      margin: 0;
    }

    .table-responsive {
      overflow-x: auto;
    }

    .data-table {
      width: 100%;
      border-collapse: collapse;
    }

    .data-table thead {
      background: #0f1736;
    }

    .data-table thead th {
      padding: 16px;
      text-align: left;
      color: var(--muted);
      font-size: 12px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      border-bottom: 1px solid var(--border);
    }

    .data-table tbody td {
      padding: 16px;
      color: #d7d9df;
      font-size: 14px;
      border-bottom: 1px solid var(--border);
    }

    .data-table tbody tr:hover {
      background: #0f1736;
    }

    .data-table tbody tr:last-child td {
      border-bottom: none;
    }

    .badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 12px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 500;
    }

    .badge-success {
      background: rgba(16, 185, 129, 0.15);
      color: #10b981;
    }

    .badge-secondary {
      background: rgba(148, 163, 184, 0.15);
      color: #94a3b8;
    }

    .badge-info {
      background: rgba(59, 130, 246, 0.15);
      color: #3b82f6;
    }

    .btn-action {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 32px;
      height: 32px;
      border-radius: 8px;
      border: none;
      cursor: pointer;
      transition: all 0.2s ease;
      font-size: 14px;
    }

    .btn-action:hover {
      transform: translateY(-2px);
    }

    .btn-warning {
      background: rgba(255, 176, 32, 0.15);
      color: var(--accent);
    }

    .btn-warning:hover {
      background: var(--accent);
      color: #000;
    }

    .btn-danger {
      background: rgba(239, 68, 68, 0.15);
      color: #ef4444;
    }

    .btn-danger:hover {
      background: #ef4444;
      color: #fff;
    }

    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: var(--muted);
    }

    .empty-state i {
      font-size: 64px;
      margin-bottom: 16px;
      opacity: 0.3;
    }

    .empty-state h5 {
      font-size: 18px;
      font-weight: 600;
      color: #d7d9df;
      margin-bottom: 8px;
    }

    .alert {
      padding: 16px 20px;
      border-radius: 12px;
      margin-bottom: 24px;
      display: flex;
      align-items: center;
      gap: 12px;
      border: 1px solid;
    }

    .alert-success {
      background: rgba(16, 185, 129, 0.1);
      border-color: #10b981;
      color: #10b981;
    }

    .alert-danger {
      background: rgba(239, 68, 68, 0.1);
      border-color: #ef4444;
      color: #ef4444;
    }

    .alert-warning {
      background: rgba(255, 176, 32, 0.1);
      border-color: var(--accent);
      color: var(--accent);
    }

    .btn-close {
      margin-left: auto;
      background: transparent;
      border: none;
      color: inherit;
      cursor: pointer;
      opacity: 0.7;
    }

    .btn-close:hover {
      opacity: 1;
    }

    .info-box {
      background: rgba(79, 70, 229, 0.1);
      border-left: 4px solid var(--brand);
      padding: 16px;
      border-radius: 12px;
      margin-bottom: 24px;
      color: #d7d9df;
    }

    .info-box i {
      color: var(--brand);
      margin-right: 8px;
    }

    /* Select2 customization */
    .select2-container--default .select2-selection--single {
      background: #0f1736;
      border: 1px solid var(--border);
      border-radius: 10px;
      height: 48px;
      padding: 8px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
      color: #fff;
      line-height: 32px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
      height: 46px;
    }

    .select2-dropdown {
      background: #151d3e;
      border: 1px solid var(--border);
      border-radius: 10px;
    }

    .select2-container--default .select2-results__option {
      color: #d7d9df;
      padding: 12px;
    }

    .select2-container--default .select2-results__option--highlighted {
      background: var(--brand);
      color: #fff;
    }

    @media (max-width: 768px) {
      .form-card, .table-card {
        padding: 16px;
      }
      
      .data-table {
        font-size: 12px;
      }

      .data-table thead th,
      .data-table tbody td {
        padding: 12px 8px;
      }
    }

    select:disabled {
  background-color: #e9ecef !important;
}
  </style>
</head>

<body>
  <div class="app" id="appGrid">
    <!-- LEFT SIDEBAR -->
    <?php include_once __DIR__ . '/../../layouts/sidebar_coordinador.php'; ?>

    <!-- MAIN -->
    <main class="main">
      <div class="topbar">
        <div class="topbar-left">
          <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
            <i class="ri-menu-2-line"></i>
          </button>
          <div class="title">Agregar Asignatura</div>
        </div>
      </div>

      <?php 
      // Mostrar alerta si existe
      if(isset($_SESSION['alerta'])){
          $alerta = $_SESSION['alerta'];
          echo '<div class="alert alert-'.$alerta['tipo'].'">';
          echo '<i class="ri-information-line"></i>';
          echo '<span>'.$alerta['mensaje'].'</span>';
          echo '<button class="btn-close" onclick="this.parentElement.remove()"><i class="ri-close-line"></i></button>';
          echo '</div>';
          unset($_SESSION['alerta']);
      }
      ?>

      <!-- FORMULARIO DE ASIGNACIÓN -->
      <div class="form-card">
        <h3><i class="ri-add-circle-line"></i> Nueva Asignación</h3>
        <p>Selecciona la asignatura y el docente del curso asignado</p>

        <!-- <div class="info-box">
          <i class="ri-information-line"></i>
          <strong>Nota:</strong> El sistema creará automáticamente la relación entre asignatura-curso si no existe
        </div> -->

        <?php if(isset($_GET['curso']) && !empty($_GET['curso'])): ?>
          <div class="alert alert-success">
            <i class="ri-checkbox-circle-line"></i>
            <span><strong>¡Curso pre-seleccionado!</strong> El curso actual ya está seleccionado en el formulario.</span>
          </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/administrador/asignar-docentes" method="POST">
          <input type="hidden" name="accion" value="asignar">
          
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
             <!-- SELECCIONAR ASIGNATURA -->
            <div class="form-group">
              <label>
                <i class="ri-book-2-line"></i> Asignatura
              </label>
              <select class="form-select select2" name="asignatura" required>
                <option value="">Seleccione una asignatura...</option>
                <?php foreach($asignaturas as $asignatura): ?>
                  <option value="<?= $asignatura['id'] ?>">
                    <?= htmlspecialchars($asignatura['nombre']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <!-- SELECCIONAR DOCENTE -->
            <div class="form-group">
              <label>
                <i class="ri-user-3-line"></i> Docente
              </label>
              <select class="form-select select2" name="docente" required>
                <option value="">Seleccione un docente...</option>
                <?php foreach($docentes as $docente): ?>
                  <option value="<?= $docente['id'] ?>">
                    <?= htmlspecialchars($docente['nombre_completo']) ?>
                    <?php if(isset($docente['profesion'])): ?>
                      - <?= htmlspecialchars($docente['profesion']) ?>
                    <?php endif; ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

           

            <!-- SELECCIONAR CURSO -->
          <div class="form-group">
  <label>
    <i class="ri-team-line"></i> Curso
  </label>
  <?php
  // Obtener el ID del curso desde el parámetro GET
  $curso_preseleccionado_id = $_GET['curso'] ?? null;
  $curso_preseleccionado_texto = '';
  
  // Buscar el nombre completo del curso preseleccionado
  if ($curso_preseleccionado_id) {
    foreach ($cursos as $curso) {
      if ($curso['id'] == $curso_preseleccionado_id) {
        $curso_preseleccionado_texto = $curso['nombre_curso'] . ' - ' . $curso['jornada'];
        if (isset($curso['director']) && $curso['director']) {
          $curso_preseleccionado_texto .= ' (' . $curso['director'] . ')';
        }
        break;
      }
    }
  }
  ?>
  <input 
    type="text" 
    class="form-control" 
    name="curso_display" 
    id="inputCursoDisplay"
    value="<?= htmlspecialchars($curso_preseleccionado_texto) ?>" 
    placeholder="Ingrese el curso..."
    required
    readonly
  >
  <!-- Campo oculto con el ID real -->
  <input type="hidden" name="curso" id="inputCurso" value="<?= htmlspecialchars($curso_preseleccionado_id ?? '') ?>">
</div>
          </div>

          <div style="text-align: right; margin-top: 24px;">
            <button type="submit" class="btn-submit">
              <i class="ri-save-line"></i>
              Asignar Docente
            </button>
          </div>
        </form>
      </div>

      <!-- TABLA DE ASIGNACIONES -->
      <div class="table-card">
        <div class="table-card-header" style="display: flex; justify-content: space-between; align-items: center;">
          <h3>
            <i class="ri-list-check"></i> Asignaciones Actuales (<?= count($asignaciones) ?>)
            <?php if(isset($_GET['curso']) && !empty($_GET['curso'])): ?>
              <span style="font-size: 14px; color: var(--muted); font-weight: 400; margin-left: 8px;">
                • Filtrando por curso
              </span>
            <?php endif; ?>
          </h3>
          <?php if(isset($_GET['curso']) && !empty($_GET['curso'])): ?>
            <a href="<?= BASE_URL ?>/administrador/asignar-docentes" 
               style="padding: 8px 16px; background: rgba(255, 176, 32, 0.15); color: var(--accent); border-radius: 8px; text-decoration: none; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s ease;"
               onmouseover="this.style.background='rgba(255, 176, 32, 0.25)'"
               onmouseout="this.style.background='rgba(255, 176, 32, 0.15)'">
              <i class="ri-filter-off-line"></i>
              Mostrar Todas
            </a>
          <?php endif; ?>
        </div>
        
        <?php if(empty($asignaciones)): ?>
          <div class="empty-state">
            <i class="ri-file-list-3-line"></i>
            <?php if(isset($_GET['curso']) && !empty($_GET['curso'])): ?>
              <h5>No hay asignaciones para este curso</h5>
              <p>Este curso aún no tiene docentes asignados. Usa el formulario de arriba para asignar el primer docente.</p>
            <?php else: ?>
              <h5>No hay asignaciones registradas</h5>
              <p>Comienza asignando docentes a las asignaturas usando el formulario de arriba</p>
            <?php endif; ?>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="data-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th><i class="ri-user-line"></i> Docente</th>
                  <th><i class="ri-book-line"></i> Asignatura</th>
                  <th><i class="ri-team-line"></i> Curso</th>
                  <th><i class="ri-sun-line"></i> Jornada</th>
                  <th><i class="ri-toggle-line"></i> Estado</th>
                  <th><i class="ri-calendar-line"></i> Fecha</th>
                  <th style="text-align: center;"><i class="ri-tools-line"></i> Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($asignaciones as $index => $asig): ?>
                  <tr>
                    <td><strong><?= $index + 1 ?></strong></td>
                    <td>
                      <i class="ri-user-3-line" style="color: var(--brand); margin-right: 8px;"></i>
                      <?= htmlspecialchars($asig['docente']) ?>
                    </td>
                    <td>
                      <i class="ri-book-2-line" style="color: #10b981; margin-right: 8px;"></i>
                      <?= htmlspecialchars($asig['asignatura']) ?>
                    </td>
                    <td>
                      <strong><?= htmlspecialchars($asig['curso']) ?></strong>
                    </td>
                    <td>
                      <span class="badge badge-info">
                        <i class="ri-sun-line"></i>
                        <?= htmlspecialchars($asig['jornada']) ?>
                      </span>
                    </td>
                    <td>
                      <?php if($asig['estado'] === 'activo'): ?>
                        <span class="badge badge-success">
                          <i class="ri-checkbox-circle-line"></i>
                          Activo
                        </span>
                      <?php else: ?>
                        <span class="badge badge-secondary">
                          <i class="ri-close-circle-line"></i>
                          Inactivo
                        </span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <small style="color: var(--muted);">
                        <?= date('d/m/Y', strtotime($asig['creado_en'])) ?>
                      </small>
                    </td>
                    <td style="text-align: center;">
                    <div style="display: inline-flex; gap: 8px;">
                    <?php 
                    // Mantener el parámetro de curso en las acciones
                    $curso_param = isset($_GET['curso']) ? '&curso=' . $_GET['curso'] : '';
                    ?>
                    <a href="<?= BASE_URL ?>/administrador/asignar-docentes?accion=cambiar_estado&id=<?= $asig['id'] ?>&estado=<?= $asig['estado'] ?><?= $curso_param ?>" 
                       class="btn-action btn-warning" 
                    title="Cambiar estado">
                    <i class="ri-toggle-line"></i>
                    </a>
                    <a href="<?= BASE_URL ?>/administrador/asignar-docentes?accion=eliminar&id=<?= $asig['id'] ?><?= $curso_param ?>" 
                       class="btn-action btn-danger" 
                         onclick="return confirm('¿Está seguro de eliminar esta asignación?')" 
                           title="Eliminar">
                          <i class="ri-delete-bin-line"></i>
                        </a>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>

    </main>
  </div>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/dashboard.js"></script>
  
  <script>
    $(document).ready(function() {
      // Inicializar Select2
      $('.select2').select2({
        theme: 'default',
        width: '100%',
        placeholder: function() {
          return $(this).data('placeholder') || 'Seleccione una opción...';
        }
      });

      // Si hay un curso pre-seleccionado, hacer scroll hasta el formulario
      <?php if(isset($_GET['curso']) && !empty($_GET['curso'])): ?>
        // Resaltar visualmente el select de curso
        $('#selectCurso').parent().find('.select2-container').css({
          'border': '2px solid #10b981',
          'border-radius': '10px',
          'box-shadow': '0 0 0 3px rgba(16, 185, 129, 0.1)'
        });

        // Remover el resaltado después de 3 segundos
        setTimeout(function() {
          $('#selectCurso').parent().find('.select2-container').css({
            'border': '',
            'box-shadow': ''
          });
        }, 3000);
      <?php endif; ?>

      // Toggle sidebar izquierdo
      document.getElementById('toggleLeft').addEventListener('click', function() {
        document.getElementById('appGrid').classList.toggle('hide-left');
      });
    });
  </script>
</body>
</html>
