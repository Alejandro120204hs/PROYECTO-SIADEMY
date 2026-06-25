<?php
require_once BASE_PATH . '/app/helpers/session_administrador.php';
require_once BASE_PATH . '/app/models/administradores/acudiente.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ' . BASE_URL . '/administrador-panel-acudientes');
    exit();
}

if (session_status() === PHP_SESSION_NONE) session_start();
$id_institucion = $_SESSION['user']['id_institucion'];

$objetoAcudiente = new Acudiente();
$acudiente = $objetoAcudiente->listarAcudienteId((int)$_GET['id']);

if (!$acudiente) {
    header('Location: ' . BASE_URL . '/administrador-panel-acudientes');
    exit();
}

$estudiantes = $objetoAcudiente->listarEstudiantesVinculados($acudiente['id'], $id_institucion);

$edad = null;
if (!empty($acudiente['fecha_de_nacimiento'])) {
    $edad = (new DateTime($acudiente['fecha_de_nacimiento']))->diff(new DateTime())->y;
}

$iniciales = strtoupper(
    substr($acudiente['nombres'] ?? 'A', 0, 1) .
    substr($acudiente['apellidos'] ?? 'A', 0, 1)
);

$totalEstudiantes = count($estudiantes);

$gradientesAvatar = [
    'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
    'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
    'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
    'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
    'linear-gradient(135deg, #f7971e 0%, #ffd200 100%)',
];
$gradienteAvatar = $gradientesAvatar[ord($iniciales[0]) % count($gradientesAvatar)];

$gradientesEst = [
    'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
    'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
    'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
    'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
    'linear-gradient(135deg, #f7971e 0%, #ffd200 100%)',
];
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Detalle Acudiente</title>
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
          <div class="title">Detalle del Acudiente</div>
        </div>
      </div>

      <!-- PERFIL HEADER -->
      <div class="guardian-profile-header">
        <div class="profile-main">
          <div class="profile-avatar" style="background: <?= $gradienteAvatar ?>;">
            <?= htmlspecialchars($iniciales) ?>
          </div>
          <div class="profile-info">
            <h2><?= htmlspecialchars($acudiente['nombres'] . ' ' . $acudiente['apellidos']) ?></h2>
            <p class="profile-subtitle">Acudiente • <?= htmlspecialchars($acudiente['parentesco'] ?? '—') ?></p>
            <div class="profile-badges">
              <span class="badge-item <?= ($acudiente['estado'] ?? '') === 'Activo' ? 'badge-active' : 'badge-inactive' ?>">
                <i class="ri-user-heart-line"></i>
                <?= htmlspecialchars($acudiente['estado'] ?? 'Sin estado') ?>
              </span>
              <span class="badge-item badge-info">
                <i class="ri-group-line"></i>
                <?= $totalEstudiantes ?> Estudiante<?= $totalEstudiantes !== 1 ? 's' : '' ?> a cargo
              </span>
            </div>
          </div>
        </div>
        <div class="profile-actions">
          <a class="btn-profile-action btn-primary-action"
             href="<?= BASE_URL ?>/administrador/editar-acudiente?id=<?= $acudiente['id'] ?>">
            <i class="ri-edit-line"></i> Editar
          </a>
        </div>
      </div>

      <!-- STATS -->
      <div class="quick-stats">
        <div class="stat-card">
          <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <i class="ri-parent-line"></i>
          </div>
          <div class="stat-content">
            <span class="stat-label">Estudiantes a Cargo</span>
            <strong class="stat-value"><?= $totalEstudiantes ?></strong>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <i class="ri-id-card-line"></i>
          </div>
          <div class="stat-content">
            <span class="stat-label">Parentesco</span>
            <strong class="stat-value"><?= htmlspecialchars($acudiente['parentesco'] ?? '—') ?></strong>
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
          <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <i class="ri-shield-user-line"></i>
          </div>
          <div class="stat-content">
            <span class="stat-label">Estado</span>
            <strong class="stat-value" style="color: <?= ($acudiente['estado'] ?? '') === 'Activo' ? '#10b981' : '#ef4444' ?>">
              <?= htmlspecialchars($acudiente['estado'] ?? '—') ?>
            </strong>
          </div>
        </div>
      </div>

      <!-- TABS -->
      <div class="tabs-navigation">
        <button class="tab-btn active" data-tab="informacion">
          <i class="ri-user-line"></i> Información Personal
        </button>
        <button class="tab-btn" data-tab="estudiantes">
          <i class="ri-team-line"></i> Estudiantes (<?= $totalEstudiantes ?>)
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
                  <span class="info-value"><?= htmlspecialchars($acudiente['nombres'] ?? '—') ?></span>
                </div>
                <div class="info-row">
                  <span class="info-label">Apellidos:</span>
                  <span class="info-value"><?= htmlspecialchars($acudiente['apellidos'] ?? '—') ?></span>
                </div>
                <div class="info-row">
                  <span class="info-label">Tipo documento:</span>
                  <span class="info-value"><?= htmlspecialchars($acudiente['tipo_documento'] ?? '—') ?></span>
                </div>
                <div class="info-row">
                  <span class="info-label">N° Identificación:</span>
                  <span class="info-value"><?= htmlspecialchars($acudiente['documento'] ?? '—') ?></span>
                </div>
                <div class="info-row">
                  <span class="info-label">Fecha de nacimiento:</span>
                  <span class="info-value">
                    <?= !empty($acudiente['fecha_de_nacimiento'])
                        ? date('d/m/Y', strtotime($acudiente['fecha_de_nacimiento'])) . ($edad !== null ? " ($edad años)" : '')
                        : '—' ?>
                  </span>
                </div>
                <div class="info-row">
                  <span class="info-label">Género:</span>
                  <span class="info-value"><?= htmlspecialchars($acudiente['genero'] ?? '—') ?></span>
                </div>
                <div class="info-row">
                  <span class="info-label">Parentesco:</span>
                  <span class="info-value"><?= htmlspecialchars($acudiente['parentesco'] ?? '—') ?></span>
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
                    <a href="mailto:<?= htmlspecialchars($acudiente['correo'] ?? '') ?>"
                       style="color: #818cf8; text-decoration: none;">
                      <?= htmlspecialchars($acudiente['correo'] ?? '—') ?>
                    </a>
                  </span>
                </div>
                <div class="info-row">
                  <span class="info-label">Teléfono:</span>
                  <span class="info-value"><?= htmlspecialchars($acudiente['telefono'] ?? '—') ?></span>
                </div>
                <div class="info-row">
                  <span class="info-label">Ciudad:</span>
                  <span class="info-value"><?= htmlspecialchars($acudiente['ciudad'] ?? '—') ?></span>
                </div>
                <div class="info-row">
                  <span class="info-label">Dirección:</span>
                  <span class="info-value"><?= htmlspecialchars($acudiente['direccion'] ?? '—') ?></span>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- TAB: ESTUDIANTES -->
        <div class="tab-pane" id="estudiantes">
          <div class="guardian-students-section">
            <h3 class="section-title">Estudiantes a Cargo</h3>

            <?php if (empty($estudiantes)): ?>
              <div style="text-align:center; padding: 48px 24px; color: #64748b;">
                <i class="ri-user-unfollow-line" style="font-size: 48px; display:block; margin-bottom:12px;"></i>
                Este acudiente no tiene estudiantes vinculados.
              </div>
            <?php else: ?>
            <div class="students-grid-guardian">
              <?php foreach ($estudiantes as $i => $est):
                $estIniciales = strtoupper(
                    substr($est['nombres'] ?? 'E', 0, 1) .
                    substr($est['apellidos'] ?? '', 0, 1)
                );
                $gradEst    = $gradientesEst[$i % count($gradientesEst)];
                $tieneGrado = !empty($est['grado']) && !empty($est['nombre_curso']);
                $estadoEst  = $est['estado'] ?? 'Activo';
              ?>
              <div class="student-card-guardian">
                <div class="student-header-guardian">
                  <div class="student-avatar-guardian" style="background: <?= $gradEst ?>;">
                    <?= htmlspecialchars($estIniciales) ?>
                  </div>
                  <div class="student-main-info">
                    <h4><?= htmlspecialchars($est['nombres'] . ' ' . $est['apellidos']) ?></h4>
                    <p class="student-grade">
                      <?= $tieneGrado
                          ? 'Grado ' . htmlspecialchars($est['nombre_curso'])
                          : 'Sin matrícula activa' ?>
                    </p>
                    <span class="badge-status <?= $estadoEst === 'Activo' ? 'status-active' : 'status-inactive' ?>">
                      <?= htmlspecialchars($estadoEst) ?>
                    </span>
                  </div>
                </div>

                <div class="info-card-body" style="margin-top:12px;">
                  <?php if (!empty($est['documento'])): ?>
                  <div class="info-row">
                    <span class="info-label">Documento:</span>
                    <span class="info-value"><?= htmlspecialchars($est['documento']) ?></span>
                  </div>
                  <?php endif; ?>
                  <?php if ($tieneGrado && !empty($est['jornada'])): ?>
                  <div class="info-row">
                    <span class="info-label">Jornada:</span>
                    <span class="info-value"><?= htmlspecialchars($est['jornada']) ?></span>
                  </div>
                  <?php endif; ?>
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
</body>
</html>
