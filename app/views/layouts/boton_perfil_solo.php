<?php
  $perfilData = (isset($usuario) && is_array($usuario)) ? $usuario : [];
  $perfilNombre = $perfilData['nombres'] ?? 'Usuario';
  $perfilRol = $perfilData['rol'] ?? ($_SESSION['user']['rol'] ?? 'Usuario');
  $perfilFoto = !empty($perfilData['foto']) ? $perfilData['foto'] : 'default.png';
  $perfilFotoFolder = 'usuarios';
  if ($perfilRol === 'Docente') {
    $perfilFotoFolder = 'docentes';
  } elseif ($perfilRol === 'Estudiante') {
    $perfilFotoFolder = 'estudiantes';
  }

  $perfilFotoArchivo = (string)$perfilFoto;
  $perfilFotoPath = '/public/uploads/' . $perfilFotoFolder . '/' . $perfilFotoArchivo;
  $perfilFotoUrlPath = '/public/uploads/' . $perfilFotoFolder . '/' . rawurlencode($perfilFotoArchivo);
  $perfilFotoVersion = @filemtime(BASE_PATH . $perfilFotoPath) ?: 1;

  $perfilDashboard = '/dashboard-perfil';
  if ($perfilRol === 'Administrador') {
    $perfilDashboard = '/administrador/dashboard';
  } elseif ($perfilRol === 'Docente') {
    $perfilDashboard = '/docente/dashboard';
  } elseif ($perfilRol === 'Estudiante') {
    $perfilDashboard = '/estudiante/dashboard';
  } elseif ($perfilRol === 'superAdmin') {
    $perfilDashboard = '/superAdmin-dashboard';
  } elseif ($perfilRol === 'Acudiente') {
    $perfilDashboard = '/acudiente/dashboard';
  }
?>

<?php
$_badgeCount = 0;
if (!empty($_SESSION['user']['id']) && !empty($_SESSION['user']['id_institucion'])) {
    try {
        require_once BASE_PATH . '/app/models/notificaciones.php';
        $_notifModel = new Notificacion();
        $_badgeCount = $_notifModel->contarNoLeidas(
            (int)$_SESSION['user']['id'],
            (int)$_SESSION['user']['id_institucion']
        );
    } catch (Throwable $_notifEx) {
        error_log('[badge] ' . $_notifEx->getMessage());
    }
}
?>

<div class="user">
  <div class="user-info">
    <div class="user-details">
      <span class="user-name"><?= htmlspecialchars($perfilNombre) ?></span>
      <span class="user-role"><?= htmlspecialchars($perfilRol) ?></span>
    </div>
  </div>
  <div class="avatar" id="userMenuBtn">
    <img src="<?= BASE_URL . $perfilFotoUrlPath ?>?v=<?= $perfilFotoVersion ?>"
      alt="foto" width="40px" height="40px" style="border-radius: 50%; cursor: pointer;">
  </div>

  <!-- Dropdown Menu -->
  <div class="user-dropdown" id="userDropdown">
    <div class="dropdown-header">
      <img src="<?= BASE_URL . $perfilFotoUrlPath ?>?v=<?= $perfilFotoVersion ?>"
        alt="foto" width="48px" height="48px" style="border-radius: 50%;">
      <div>
        <strong><?= htmlspecialchars($perfilNombre) ?></strong>
        <small><?= htmlspecialchars($perfilRol) ?></small>
      </div>
    </div>
    <div class="dropdown-divider"></div>
    <a href="<?= BASE_URL ?>/dashboard-perfil" class="dropdown-item">
      <i class="ri-user-line"></i>
      <span>Ver Perfil</span>
    </a>
    <a href="<?= BASE_URL . $perfilDashboard ?>" class="dropdown-item">
      <i class="ri-dashboard-line"></i>
      <span>Ir al Panel</span>
    </a>
    <a href="<?= BASE_URL ?>/configuracion" class="dropdown-item">
      <i class="ri-settings-3-line"></i>
      <span>Configuración</span>
    </a>
    <a href="<?= BASE_URL ?>/notificaciones" class="dropdown-item" id="notifDropdownLink">
      <i class="ri-notification-3-line"></i>
      <span>Notificaciones</span>
      <?php if ($_badgeCount > 0): ?>
      <span class="dropdown-badge" id="notifBadge"><?= min($_badgeCount, 99) ?></span>
      <?php else: ?>
      <span class="dropdown-badge" id="notifBadge" style="display:none">0</span>
      <?php endif; ?>
    </a>
    <a href="<?= BASE_URL ?>/ayuda" class="dropdown-item">
      <i class="ri-question-line"></i>
      <span>Ayuda y Soporte</span>
    </a>
    <div class="dropdown-divider"></div>
    <!-- Toggle de tema claro/oscuro -->
    <div class="dropdown-item" id="toggleThemeBtn" role="button" tabindex="0">
      <i class="ri-moon-line" id="themeIcon"></i>
      <span id="themeLabel">Modo Oscuro</span>
      <div class="theme-switch" id="themeSwitch">
        <div class="theme-switch-thumb"></div>
      </div>
    </div>
    <div class="dropdown-divider"></div>
    <a href="<?= BASE_URL ?>/logout" class="dropdown-item dropdown-item-danger">
      <i class="ri-logout-box-line"></i>
      <span>Cerrar Sesión</span>
    </a>
  </div>
</div>

<?php
// El dropdown de perfil solo se inicializa aquí para roles sin su propio main-X.js
// Administrador → main-admin.js  |  superAdmin → main-superAdmin.js
if (in_array($perfilRol, ['Docente', 'Estudiante', 'Acudiente'])):
?>
<script>
(function () {
  if (window.__siademyProfileDropdownInit) return;
  window.__siademyProfileDropdownInit = true;

  function initProfileDropdown() {
    var userMenuBtn  = document.getElementById('userMenuBtn');
    var userDropdown = document.getElementById('userDropdown');

    if (!userMenuBtn || !userDropdown || userMenuBtn.dataset.dropdownInit === '1') return;
    userMenuBtn.dataset.dropdownInit = '1';

    var dropdownOverlay = document.querySelector('.dropdown-overlay') || document.createElement('div');
    if (!dropdownOverlay.parentElement) {
      dropdownOverlay.className = 'dropdown-overlay';
      document.body.appendChild(dropdownOverlay);
    }

    function openUserDropdown() {
      userDropdown.classList.add('show');
      dropdownOverlay.classList.add('show');
    }

    function closeUserDropdown() {
      userDropdown.classList.remove('show');
      dropdownOverlay.classList.remove('show');
    }

    userMenuBtn.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      userDropdown.classList.contains('show') ? closeUserDropdown() : openUserDropdown();
    });

    dropdownOverlay.addEventListener('click', closeUserDropdown);

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && userDropdown.classList.contains('show')) closeUserDropdown();
    });

    userDropdown.addEventListener('click', function (e) { e.stopPropagation(); });
  }

  /* ── Polling del badge cada 60 s ─────────────────────────── */
  function pollBadge() {
    var base = window.SIADEMY_BASE_URL || '';
    fetch(base + '/api/notificaciones?action=badge', {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function (r) { return r.ok ? r.json() : null; })
    .then(function (data) {
      if (!data || !data.success) return;
      var badge = document.getElementById('notifBadge');
      if (!badge) return;
      if (data.count > 0) {
        badge.textContent = Math.min(data.count, 99);
        badge.style.display = '';
      } else {
        badge.style.display = 'none';
      }
    })
    .catch(function () {});
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initProfileDropdown);
  } else {
    initProfileDropdown();
  }

  setInterval(pollBadge, 60000);
})();
</script>
<?php endif; ?>
