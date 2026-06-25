<?php
require_once BASE_PATH . '/app/helpers/session_administrador.php';
require_once BASE_PATH . '/app/models/administradores/estudiante.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ' . BASE_URL . '/administrador-panel-estudiantes');
    exit();
}

if (session_status() === PHP_SESSION_NONE) session_start();
$id_institucion = $_SESSION['user']['id_institucion'];

$modelEstudiante = new Estudiante();
$estudiante      = $modelEstudiante->listarId((int)$_GET['id']);

if (!$estudiante || (int)$estudiante['id_institucion'] !== (int)$id_institucion) {
    header('Location: ' . BASE_URL . '/administrador-panel-estudiantes');
    exit();
}

$matricula = $modelEstudiante->obtenerMatriculaActiva($estudiante['id']);

$edad = null;
if (!empty($estudiante['fecha_de_nacimiento'])) {
    $edad = (new DateTime($estudiante['fecha_de_nacimiento']))->diff(new DateTime())->y;
}

$iniciales = strtoupper(
    substr($estudiante['nombres'] ?? 'E', 0, 1) .
    substr($estudiante['apellidos'] ?? '', 0, 1)
);

$gradientes = [
    'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
    'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
    'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
    'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
    'linear-gradient(135deg, #f7971e 0%, #ffd200 100%)',
];
$gradiente = $gradientes[ord($iniciales[0]) % count($gradientes)];

$tieneGrado      = !empty($matricula['grado']) && !empty($matricula['curso']);
$tieneAcudiente  = !empty($estudiante['nombres_acudiente']);
$estadoEstudiante = $estudiante['estado'] ?? 'Activo';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Detalle Estudiante</title>
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
          <div class="title">Detalle del Estudiante</div>
        </div>
      </div>

      <!-- PERFIL HEADER -->
      <div class="student-profile-header">
        <div class="profile-main">
          <div class="profile-avatar" style="background: <?= $gradiente ?>;">
            <?= htmlspecialchars($iniciales) ?>
          </div>
          <div class="profile-info">
            <h2><?= htmlspecialchars($estudiante['nombres'] . ' ' . $estudiante['apellidos']) ?></h2>
            <p class="profile-subtitle">
              Estudiante<?= $tieneGrado ? ' • Grado ' . htmlspecialchars($matricula['curso']) : '' ?>
            </p>
            <div class="profile-badges">
              <span class="badge-item <?= $estadoEstudiante === 'Activo' ? 'badge-active' : 'badge-inactive' ?>">
                <i class="ri-checkbox-circle-fill"></i>
                <?= htmlspecialchars($estadoEstudiante) ?>
              </span>
              <?php if ($tieneGrado): ?>
              <span class="badge-item badge-info">
                <i class="ri-calendar-line"></i>
                <?= htmlspecialchars($matricula['anio']) ?>
              </span>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <div class="profile-actions">
          <a class="btn-profile-action btn-primary-action"
             href="<?= BASE_URL ?>/administrador/editar-estudiante?id=<?= $estudiante['id'] ?>">
            <i class="ri-edit-line"></i> Editar
          </a>
        </div>
      </div>

      <!-- STATS -->
      <div class="quick-stats">
        <div class="stat-card">
          <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <i class="ri-graduation-cap-line"></i>
          </div>
          <div class="stat-content">
            <span class="stat-label">Grado Actual</span>
            <strong class="stat-value">
              <?= $tieneGrado ? htmlspecialchars($matricula['curso']) : '—' ?>
            </strong>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <i class="ri-cake-line"></i>
          </div>
          <div class="stat-content">
            <span class="stat-label">Edad</span>
            <strong class="stat-value"><?= $edad !== null ? $edad . ' años' : '—' ?></strong>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
            <i class="ri-time-line"></i>
          </div>
          <div class="stat-content">
            <span class="stat-label">Jornada</span>
            <strong class="stat-value"><?= $tieneGrado ? htmlspecialchars($matricula['jornada']) : '—' ?></strong>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <i class="ri-shield-user-line"></i>
          </div>
          <div class="stat-content">
            <span class="stat-label">Estado</span>
            <strong class="stat-value" style="color: <?= $estadoEstudiante === 'Activo' ? '#10b981' : '#ef4444' ?>">
              <?= htmlspecialchars($estadoEstudiante) ?>
            </strong>
          </div>
        </div>
      </div>

      <!-- TABS -->
      <div class="tabs-navigation">
        <button class="tab-btn active" data-tab="informacion">
          <i class="ri-user-line"></i> Información Personal
        </button>
        <button class="tab-btn" data-tab="academico">
          <i class="ri-graduation-cap-line"></i> Información Académica
        </button>
        <button class="tab-btn" data-tab="familiar">
          <i class="ri-parent-line"></i> Acudiente
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
                  <span class="info-value"><?= htmlspecialchars($estudiante['nombres'] ?? '—') ?></span>
                </div>
                <div class="info-row">
                  <span class="info-label">Apellidos:</span>
                  <span class="info-value"><?= htmlspecialchars($estudiante['apellidos'] ?? '—') ?></span>
                </div>
                <div class="info-row">
                  <span class="info-label">Tipo documento:</span>
                  <span class="info-value"><?= htmlspecialchars($estudiante['tipo_documento'] ?? '—') ?></span>
                </div>
                <div class="info-row">
                  <span class="info-label">N° Identificación:</span>
                  <span class="info-value"><?= htmlspecialchars($estudiante['documento'] ?? '—') ?></span>
                </div>
                <div class="info-row">
                  <span class="info-label">Fecha de nacimiento:</span>
                  <span class="info-value">
                    <?= !empty($estudiante['fecha_de_nacimiento'])
                        ? date('d/m/Y', strtotime($estudiante['fecha_de_nacimiento'])) . ($edad !== null ? " ($edad años)" : '')
                        : '—' ?>
                  </span>
                </div>
                <div class="info-row">
                  <span class="info-label">Género:</span>
                  <span class="info-value"><?= htmlspecialchars($estudiante['genero'] ?? '—') ?></span>
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
                    <a href="mailto:<?= htmlspecialchars($estudiante['correo'] ?? '') ?>"
                       style="color:#818cf8; text-decoration:none;">
                      <?= htmlspecialchars($estudiante['correo'] ?? '—') ?>
                    </a>
                  </span>
                </div>
                <div class="info-row">
                  <span class="info-label">Teléfono:</span>
                  <span class="info-value"><?= htmlspecialchars($estudiante['telefono'] ?? '—') ?></span>
                </div>
                <div class="info-row">
                  <span class="info-label">Ciudad:</span>
                  <span class="info-value"><?= htmlspecialchars($estudiante['ciudad'] ?? '—') ?></span>
                </div>
                <div class="info-row">
                  <span class="info-label">Dirección:</span>
                  <span class="info-value"><?= htmlspecialchars($estudiante['direccion'] ?? '—') ?></span>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- TAB: INFORMACIÓN ACADÉMICA -->
        <div class="tab-pane" id="academico">
          <div class="info-grid">
            <div class="info-card">
              <div class="info-card-header">
                <h3><i class="ri-graduation-cap-line"></i> Matrícula Activa</h3>
              </div>
              <div class="info-card-body">
                <?php if ($tieneGrado): ?>
                <div class="info-row">
                  <span class="info-label">Grado:</span>
                  <span class="info-value"><?= htmlspecialchars($matricula['curso']) ?></span>
                </div>
                <div class="info-row">
                  <span class="info-label">Jornada:</span>
                  <span class="info-value"><?= htmlspecialchars($matricula['jornada'] ?? '—') ?></span>
                </div>
                <div class="info-row">
                  <span class="info-label">Año lectivo:</span>
                  <span class="info-value"><?= htmlspecialchars($matricula['anio'] ?? '—') ?></span>
                </div>
                <?php else: ?>
                <div style="text-align:center; padding:32px 0; color:#64748b;">
                  <i class="ri-book-close-line" style="font-size:40px; display:block; margin-bottom:8px;"></i>
                  Sin matrícula activa registrada.
                </div>
                <?php endif; ?>
              </div>
            </div>

            <div class="info-card">
              <div class="info-card-header">
                <h3><i class="ri-id-card-line"></i> Datos del Sistema</h3>
              </div>
              <div class="info-card-body">
                <div class="info-row">
                  <span class="info-label">Estado:</span>
                  <span class="info-value">
                    <span class="badge-status <?= $estadoEstudiante === 'Activo' ? 'status-active' : 'status-inactive' ?>">
                      <?= htmlspecialchars($estadoEstudiante) ?>
                    </span>
                  </span>
                </div>
                <div class="info-row">
                  <span class="info-label">Correo acceso:</span>
                  <span class="info-value">
                    <a href="mailto:<?= htmlspecialchars($estudiante['correo'] ?? '') ?>"
                       style="color:#818cf8; text-decoration:none;">
                      <?= htmlspecialchars($estudiante['correo'] ?? '—') ?>
                    </a>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- TAB: ACUDIENTE -->
        <div class="tab-pane" id="familiar">
          <?php if ($tieneAcudiente): ?>
          <div class="info-grid">
            <div class="info-card">
              <div class="info-card-header">
                <h3><i class="ri-parent-line"></i> Datos del Acudiente</h3>
              </div>
              <div class="info-card-body">
                <div class="info-row">
                  <span class="info-label">Nombres:</span>
                  <span class="info-value"><?= htmlspecialchars($estudiante['nombres_acudiente'] ?? '—') ?></span>
                </div>
                <div class="info-row">
                  <span class="info-label">Apellidos:</span>
                  <span class="info-value"><?= htmlspecialchars($estudiante['apellidos_acudiente'] ?? '—') ?></span>
                </div>
                <div class="info-row">
                  <span class="info-label">Parentesco:</span>
                  <span class="info-value"><?= htmlspecialchars($estudiante['parentesco_acudiente'] ?? '—') ?></span>
                </div>
                <div class="info-row">
                  <span class="info-label">Teléfono:</span>
                  <span class="info-value"><?= htmlspecialchars($estudiante['telefono_acudiente'] ?? '—') ?></span>
                </div>
                <div class="info-row">
                  <span class="info-label">Correo:</span>
                  <span class="info-value">
                    <?php if (!empty($estudiante['correo_acudiente'])): ?>
                    <a href="mailto:<?= htmlspecialchars($estudiante['correo_acudiente']) ?>"
                       style="color:#818cf8; text-decoration:none;">
                      <?= htmlspecialchars($estudiante['correo_acudiente']) ?>
                    </a>
                    <?php else: ?>—<?php endif; ?>
                  </span>
                </div>
              </div>
            </div>
          </div>
          <?php else: ?>
          <div style="text-align:center; padding:48px 24px; color:#64748b;">
            <i class="ri-user-unfollow-line" style="font-size:48px; display:block; margin-bottom:12px;"></i>
            Este estudiante no tiene acudiente vinculado.
          </div>
          <?php endif; ?>
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
