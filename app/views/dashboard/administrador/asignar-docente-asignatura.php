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
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/administrador/asignar-docente-asignatura.css">
</head>

<body>
  <div class="app hide-right" id="appGrid">
    <!-- LEFT SIDEBAR -->
    <?php include_once __DIR__ . '/../../layouts/sidebar_coordinador.php'; ?>

    <!-- MAIN -->
    <main class="main">
      <div class="topbar">
        <div class="topbar-left">
          <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
            <i class="ri-menu-2-line"></i>
          </button>
          <button class="btn-back" onclick="window.history.back()">
            <i class="ri-arrow-left-line"></i> Volver
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
  <?php $curso_preseleccionado_id = $_GET['curso'] ?? ''; ?>
  <select class="form-select select2" name="curso" id="inputCurso" required>
    <option value="">Seleccione un curso...</option>
    <?php foreach ($cursos as $c): ?>
      <?php
        $texto = htmlspecialchars($c['nombre_curso'] . ' - ' . $c['jornada']);
        if (!empty($c['director'])) {
          $texto .= ' (' . htmlspecialchars($c['director']) . ')';
        }
        $selected = ($c['id'] == $curso_preseleccionado_id) ? 'selected' : '';
      ?>
      <option value="<?= $c['id'] ?>" <?= $selected ?>><?= $texto ?></option>
    <?php endforeach; ?>
  </select>
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
                    $curso_param = isset($_GET['curso']) ? '&curso=' . $_GET['curso'] : '';
                    ?>
                    <button class="btn-action btn-edit"
                            onclick="abrirModalEditar(<?= (int)$asig['id'] ?>,'<?= htmlspecialchars($asig['docente'],ENT_QUOTES) ?>','<?= htmlspecialchars($asig['asignatura'],ENT_QUOTES) ?>','<?= htmlspecialchars($asig['curso'],ENT_QUOTES) ?>')"
                            title="Cambiar docente">
                        <i class="ri-edit-line"></i>
                    </button>
                    <a href="<?= BASE_URL ?>/administrador/asignar-docentes?accion=cambiar_estado&id=<?= $asig['id'] ?>&estado=<?= $asig['estado'] ?><?= $curso_param ?>"
                       class="btn-action btn-warning"
                       title="Activar / Desactivar">
                        <i class="ri-toggle-line"></i>
                    </a>
                    <a href="<?= BASE_URL ?>/administrador/asignar-docentes?accion=eliminar&id=<?= $asig['id'] ?><?= $curso_param ?>"
                       class="btn-action btn-danger"
                       onclick="return confirm('¿Eliminar esta asignación definitivamente?')"
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

  <!-- MODAL: Cambiar docente -->
  <div class="modal fade" id="modalCambiarDocente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content" style="background:#11193a;border:1px solid rgba(255,255,255,.1);border-radius:14px;">
        <div class="modal-header" style="border-bottom:1px solid rgba(255,255,255,.08);padding:18px 22px;">
          <h5 class="modal-title" style="font-size:15px;font-weight:600;color:#e2e8f0;">
            <i class="ri-edit-line" style="color:#4f46e5;margin-right:6px"></i> Cambiar Docente
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="padding:22px;">
          <div id="modal-info" style="background:rgba(79,70,229,.1);border:1px solid rgba(79,70,229,.25);border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#cbd5e1;line-height:1.7;"></div>

          <form action="<?= BASE_URL ?>/administrador/asignar-docentes" method="POST">
            <input type="hidden" name="accion" value="actualizar_docente">
            <input type="hidden" name="id_asignacion" id="modal-id">
            <?php if (isset($_GET['curso']) && $_GET['curso'] !== ''): ?>
              <input type="hidden" name="curso" value="<?= (int)$_GET['curso'] ?>">
            <?php endif; ?>

            <div class="form-group" style="margin-bottom:20px;">
              <label style="display:block;font-size:13px;color:#8b91a3;margin-bottom:8px;font-weight:600;">
                <i class="ri-user-star-line"></i> Nuevo Docente
              </label>
              <select name="nuevo_docente" class="form-select" required
                      style="background:#0e1632;border:1px solid rgba(255,255,255,.12);color:#e2e8f0;border-radius:8px;padding:10px 14px;width:100%;">
                <option value="">Seleccione el nuevo docente...</option>
                <?php foreach ($docentes as $d): ?>
                  <option value="<?= (int)$d['id'] ?>"><?= htmlspecialchars($d['nombre_completo']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px;">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                      style="background:rgba(255,255,255,.08);border:none;color:#e2e8f0;padding:9px 18px;border-radius:8px;font-size:13px;cursor:pointer;">
                Cancelar
              </button>
              <button type="submit"
                      style="background:#4f46e5;border:none;color:#fff;padding:9px 20px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                <i class="ri-save-line"></i> Guardar cambio
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-admin.js"></script>
  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/administrador/asignar-docente-asignatura.js"></script>
  <script>
    function abrirModalEditar(id, docente, asignatura, curso) {
      document.getElementById('modal-id').value = id;
      document.getElementById('modal-info').innerHTML =
        '<strong>Asignatura:</strong> ' + asignatura +
        '<br><strong>Curso:</strong> ' + curso +
        '<br><strong>Docente actual:</strong> ' + docente;
      new bootstrap.Modal(document.getElementById('modalCambiarDocente')).show();
    }
  </script>
</body>
</html>
