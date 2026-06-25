<?php
require_once BASE_PATH . '/app/helpers/session_administrador.php';
require_once BASE_PATH . '/app/models/administradores/docente.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ' . BASE_URL . '/administrador-panel-profesores');
    exit();
}

if (session_status() === PHP_SESSION_NONE) session_start();
$id_institucion = $_SESSION['user']['id_institucion'];

$modelDocente = new Docente();
$docente      = $modelDocente->listarId((int)$_GET['id']);

if (!$docente || (int)$docente['id_institucion'] !== (int)$id_institucion) {
    header('Location: ' . BASE_URL . '/administrador-panel-profesores');
    exit();
}

$asignaciones = $modelDocente->obtenerAsignaciones($docente['id']);

$edad = null;
if (!empty($docente['fecha_nacimiento'])) {
    $edad = (new DateTime($docente['fecha_nacimiento']))->diff(new DateTime())->y;
}

$iniciales = strtoupper(
    substr($docente['nombres'] ?? 'D', 0, 1) .
    substr($docente['apellidos'] ?? '', 0, 1)
);

$gradientes = [
    'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
    'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
    'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
    'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
    'linear-gradient(135deg, #f7971e 0%, #ffd200 100%)',
];
$gradiente = $gradientes[ord($iniciales[0]) % count($gradientes)];

$gradientesAsig = [
    'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
    'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
    'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
    'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
    'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
];

$totalAsignaciones  = count($asignaciones);
$totalEstudiantes   = array_sum(array_column($asignaciones, 'total_estudiantes'));
$estadoDocente      = $docente['estado'] ?? 'Activo';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Detalle Docente</title>
  <?php include_once __DIR__ . '/../../layouts/header_coordinador.php' ?>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-admin.css">
</head>
<body>
  <div class="app hide-right" id="appGrid">
    <?php include_once __DIR__ . '/../../layouts/sidebar_coordinador.php' ?>

    <main class="main">
      <div class="topbar">
        <div class="topbar-left">
          <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
            <i class="ri-menu-2-line"></i>
          </button>
          <button class="btn-back" onclick="window.history.back()">
            <i class="ri-arrow-left-line"></i> Volver
          </button>
          <div class="title">Detalle del Docente</div>
        </div>
      </div>

      <!-- PERFIL HEADER -->
      <div class="teacher-profile-header">
        <div class="profile-main">
          <div class="profile-avatar" style="background: <?= $gradiente ?>;">
            <?= htmlspecialchars($iniciales) ?>
          </div>
          <div class="profile-info">
            <h2><?= htmlspecialchars($docente['nombres'] . ' ' . $docente['apellidos']) ?></h2>
            <p class="profile-subtitle">
              Docente<?= !empty($docente['profesion']) ? ' • ' . htmlspecialchars($docente['profesion']) : '' ?>
            </p>
            <div class="profile-badges">
              <span class="badge-item <?= $estadoDocente === 'Activo' ? 'badge-active' : 'badge-inactive' ?>">
                <i class="ri-checkbox-circle-fill"></i>
                <?= htmlspecialchars($estadoDocente) ?>
              </span>
              <?php if (!empty($docente['tipo_contrato'])): ?>
              <span class="badge-item badge-info">
                <i class="ri-briefcase-line"></i>
                <?= htmlspecialchars($docente['tipo_contrato']) ?>
              </span>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <div class="profile-actions">
          <a class="btn-profile-action btn-primary-action"
             href="<?= BASE_URL ?>/administrador/editar-docente?id=<?= $docente['id'] ?>">
            <i class="ri-edit-line"></i> Editar
          </a>
        </div>
      </div>

      <!-- STATS -->
      <div class="quick-stats">
        <div class="stat-card">
          <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <i class="ri-book-open-line"></i>
          </div>
          <div class="stat-content">
            <span class="stat-label">Asignaturas</span>
            <strong class="stat-value"><?= $totalAsignaciones ?></strong>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <i class="ri-team-line"></i>
          </div>
          <div class="stat-content">
            <span class="stat-label">Estudiantes</span>
            <strong class="stat-value"><?= $totalEstudiantes ?></strong>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
            <i class="ri-cake-line"></i>
          </div>
          <div class="stat-content">
            <span class="stat-label">Edad</span>
            <strong class="stat-value"><?= $edad !== null ? $edad . ' años' : '—' ?></strong>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <i class="ri-shield-user-line"></i>
          </div>
          <div class="stat-content">
            <span class="stat-label">Estado</span>
            <strong class="stat-value" style="color: <?= $estadoDocente === 'Activo' ? '#10b981' : '#ef4444' ?>">
              <?= htmlspecialchars($estadoDocente) ?>
            </strong>
          </div>
        </div>
      </div>

      <!-- TABS -->
      <div class="tabs-navigation">
        <button class="tab-btn active" data-tab="informacion">
          <i class="ri-user-line"></i> Información Personal
        </button>
        <button class="tab-btn" data-tab="laboral">
          <i class="ri-briefcase-line"></i> Información Laboral
        </button>
        <button class="tab-btn" data-tab="asignaturas">
          <i class="ri-book-2-line"></i> Asignaturas (<?= $totalAsignaciones ?>)
        </button>
      </div>

      <div class="tabs-content">

        <!-- TAB: INFORMACIÓN PERSONAL -->
        <div class="tab-pane active" id="informacion">
          <div class="info-grid">

            <div class="info-card">
              <div class="info-card-header">
                <h3><i class="ri-user-3-line"></i> Información Básica</h3>
              </div>
              <div class="info-card-body">
                <div class="info-row">
                  <span class="info-label">Nombres:</span>
                  <span class="info-value"><?= htmlspecialchars($docente['nombres'] ?? '—') ?></span>
                </div>
                <div class="info-row">
                  <span class="info-label">Apellidos:</span>
                  <span class="info-value"><?= htmlspecialchars($docente['apellidos'] ?? '—') ?></span>
                </div>
                <div class="info-row">
                  <span class="info-label">Tipo documento:</span>
                  <span class="info-value"><?= htmlspecialchars($docente['tipo_documento'] ?? '—') ?></span>
                </div>
                <div class="info-row">
                  <span class="info-label">N° Identificación:</span>
                  <span class="info-value"><?= htmlspecialchars($docente['documento'] ?? '—') ?></span>
                </div>
                <div class="info-row">
                  <span class="info-label">Fecha de nacimiento:</span>
                  <span class="info-value">
                    <?= !empty($docente['fecha_nacimiento'])
                        ? date('d/m/Y', strtotime($docente['fecha_nacimiento'])) . ($edad !== null ? " ($edad años)" : '')
                        : '—' ?>
                  </span>
                </div>
                <div class="info-row">
                  <span class="info-label">Género:</span>
                  <span class="info-value"><?= htmlspecialchars($docente['genero'] ?? '—') ?></span>
                </div>
              </div>
            </div>

            <div class="info-card">
              <div class="info-card-header">
                <h3><i class="ri-contacts-line"></i> Información de Contacto</h3>
              </div>
              <div class="info-card-body">
                <div class="info-row">
                  <span class="info-label">Correo:</span>
                  <span class="info-value">
                    <a href="mailto:<?= htmlspecialchars($docente['correo'] ?? '') ?>"
                       style="color:#818cf8; text-decoration:none;">
                      <?= htmlspecialchars($docente['correo'] ?? '—') ?>
                    </a>
                  </span>
                </div>
                <div class="info-row">
                  <span class="info-label">Teléfono:</span>
                  <span class="info-value"><?= htmlspecialchars($docente['telefono'] ?? '—') ?></span>
                </div>
                <div class="info-row">
                  <span class="info-label">Ciudad:</span>
                  <span class="info-value"><?= htmlspecialchars($docente['ciudad'] ?? '—') ?></span>
                </div>
                <div class="info-row">
                  <span class="info-label">Dirección:</span>
                  <span class="info-value"><?= htmlspecialchars($docente['direccion'] ?? '—') ?></span>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- TAB: INFORMACIÓN LABORAL -->
        <div class="tab-pane" id="laboral">
          <div class="info-grid">
            <div class="info-card">
              <div class="info-card-header">
                <h3><i class="ri-building-line"></i> Datos Laborales</h3>
              </div>
              <div class="info-card-body">
                <div class="info-row">
                  <span class="info-label">Profesión:</span>
                  <span class="info-value"><?= htmlspecialchars($docente['profesion'] ?? '—') ?></span>
                </div>
                <div class="info-row">
                  <span class="info-label">Tipo de contrato:</span>
                  <span class="info-value"><?= htmlspecialchars($docente['tipo_contrato'] ?? '—') ?></span>
                </div>
                <div class="info-row">
                  <span class="info-label">Fecha de ingreso:</span>
                  <span class="info-value">
                    <?= !empty($docente['fecha_ingreso'])
                        ? date('d/m/Y', strtotime($docente['fecha_ingreso']))
                        : '—' ?>
                  </span>
                </div>
                <div class="info-row">
                  <span class="info-label">Fin de contrato:</span>
                  <span class="info-value">
                    <?= !empty($docente['fecha_fin_contrato'])
                        ? date('d/m/Y', strtotime($docente['fecha_fin_contrato']))
                        : 'Indefinido' ?>
                  </span>
                </div>
                <div class="info-row">
                  <span class="info-label">Estado:</span>
                  <span class="info-value">
                    <span class="badge-status <?= $estadoDocente === 'Activo' ? 'status-active' : 'status-inactive' ?>">
                      <?= htmlspecialchars($estadoDocente) ?>
                    </span>
                  </span>
                </div>
                <div class="info-row">
                  <span class="info-label">Correo acceso:</span>
                  <span class="info-value">
                    <a href="mailto:<?= htmlspecialchars($docente['correo'] ?? '') ?>"
                       style="color:#818cf8; text-decoration:none;">
                      <?= htmlspecialchars($docente['correo'] ?? '—') ?>
                    </a>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- TAB: ASIGNATURAS -->
        <div class="tab-pane" id="asignaturas">
          <div class="teacher-subjects-section">
            <h3 class="section-title">Asignaturas Asignadas</h3>

            <?php if (empty($asignaciones)): ?>
              <div style="text-align:center; padding:48px 24px; color:#64748b;">
                <i class="ri-book-close-line" style="font-size:48px; display:block; margin-bottom:12px;"></i>
                Este docente no tiene asignaturas asignadas.
              </div>
            <?php else: ?>
            <div class="subjects-grid-teacher">
              <?php foreach ($asignaciones as $i => $asig):
                $grad = $gradientesAsig[$i % count($gradientesAsig)];
              ?>
              <div class="teacher-subject-card">
                <div class="subject-header-teacher">
                  <div class="subject-icon-teacher" style="background: <?= $grad ?>;">
                    <i class="ri-book-2-line"></i>
                  </div>
                  <span class="subject-status-teacher status-active">Activa</span>
                </div>
                <h4><?= htmlspecialchars($asig['nombre_asignatura']) ?></h4>
                <p class="subject-course">Grado <?= htmlspecialchars($asig['curso']) ?></p>
                <div class="subject-stats-teacher">
                  <div class="stat-item-teacher">
                    <i class="ri-user-line"></i>
                    <div>
                      <span class="stat-label-teacher">Estudiantes</span>
                      <strong><?= (int)$asig['total_estudiantes'] ?></strong>
                    </div>
                  </div>
                  <div class="stat-item-teacher">
                    <i class="ri-time-line"></i>
                    <div>
                      <span class="stat-label-teacher">Jornada</span>
                      <strong><?= htmlspecialchars($asig['jornada'] ?? '—') ?></strong>
                    </div>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
          </div>
        </div>

      </div>
    </main>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-admin.js"></script>
  <script src="<?= BASE_URL ?>/public/assets/dashboard/js/administrador/detalle-estudiante.js"></script>
</body>
</html>
