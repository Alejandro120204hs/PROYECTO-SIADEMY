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

  $perfilDashboard = '/dashboard-perfil';
  if ($perfilRol === 'Administrador') {
    $perfilDashboard = '/administrador/dashboard';
  } elseif ($perfilRol === 'Docente') {
    $perfilDashboard = '/docente/dashboard';
  } elseif ($perfilRol === 'Estudiante') {
    $perfilDashboard = '/estudiante/dashboard';
  } elseif ($perfilRol === 'superAdmin') {
    $perfilDashboard = '/superAdmin-dashboard';
  }
?>

<?php if ($perfilRol === 'Estudiante'): ?>
<style>
  .topbar .user {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 12px;
    margin-bottom: 0;
  }

  .topbar .user-info {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .topbar .user-details {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 2px;
  }

  .topbar .user-name {
    font-size: 14px;
    font-weight: 600;
    color: #fff;
    line-height: 1.2;
  }

  .topbar .user-role {
    font-size: 12px;
    color: #a0a3bd;
    line-height: 1.2;
    text-transform: capitalize;
  }

  .topbar .avatar {
    position: relative;
    z-index: 10;
    width: 44px;
    height: 44px;
    border-radius: 999px;
    background: #2d3353;
    display: grid;
    place-items: center;
  }

  .topbar .user-dropdown {
    position: absolute;
    top: calc(100% + 12px);
    right: 0;
    width: 280px;
    background: linear-gradient(180deg, #161821 0%, #101117 100%);
    border: 1px solid var(--border);
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.05) inset;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 1000;
    overflow: hidden;
  }

  .topbar .user-dropdown.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
    animation: siademyDropdownSlideIn 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .topbar .user-dropdown::before {
    content: '';
    position: absolute;
    top: -6px;
    right: 20px;
    width: 12px;
    height: 12px;
    background: #161821;
    border-left: 1px solid var(--border);
    border-top: 1px solid var(--border);
    transform: rotate(45deg);
  }

  .topbar .dropdown-header {
    padding: 20px 18px;
    display: flex;
    align-items: center;
    gap: 14px;
    background: rgba(79, 70, 229, 0.05);
    border-bottom: 1px solid var(--border);
  }

  .topbar .dropdown-header img {
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
  }

  .topbar .dropdown-header strong {
    display: block;
    font-size: 15px;
    font-weight: 600;
    color: #fff;
    margin-bottom: 4px;
    line-height: 1.2;
  }

  .topbar .dropdown-header small {
    display: block;
    font-size: 12px;
    color: #a0a3bd;
    text-transform: capitalize;
    line-height: 1.2;
  }

  .topbar .dropdown-divider {
    height: 1px;
    background: var(--border);
    margin: 6px 0;
  }

  .topbar .dropdown-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 18px;
    color: #d7d9df;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s ease;
    cursor: pointer;
    text-decoration: none;
    position: relative;
  }

  .topbar .dropdown-item:hover {
    background: #1d2030;
    color: #fff;
  }

  .topbar .dropdown-item:active {
    background: #232636;
  }

  .topbar .dropdown-item i:first-child {
    width: 20px;
    height: 20px;
    font-size: 20px;
    color: #aab1c1;
    flex-shrink: 0;
    transition: all 0.2s ease;
  }

  .topbar .dropdown-item:hover i:first-child {
    color: #6366f1;
    transform: scale(1.1);
  }

  .topbar .dropdown-item span:not(.dropdown-badge) {
    flex: 1;
  }

  .topbar .dropdown-badge {
    background: #ef4444;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 10px;
    min-width: 20px;
    text-align: center;
    animation: siademyPulse 2s infinite;
  }

  .topbar .dropdown-arrow {
    margin-left: auto;
    font-size: 16px;
    opacity: 0.5;
    transition: all 0.2s ease;
  }

  .topbar .dropdown-item:hover .dropdown-arrow {
    opacity: 1;
    transform: translateX(4px);
  }

  .topbar .dropdown-item-danger {
    color: #fca5a5;
  }

  .topbar .dropdown-item-danger:hover {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
  }

  .topbar .dropdown-item-danger:hover i {
    color: #ef4444;
  }

  .dropdown-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: transparent;
    z-index: 999;
    display: none;
  }

  .topbar .dropdown-overlay.show,
  .dropdown-overlay.show {
    display: block;
  }

  @keyframes siademyDropdownSlideIn {
    from {
      opacity: 0;
      transform: translateY(-10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes siademyPulse {
    0%, 100% {
      box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
    }
    50% {
      box-shadow: 0 0 0 4px rgba(239, 68, 68, 0);
    }
  }
  
  @media (max-width: 480px) {
    .topbar .user-dropdown {
      width: 260px;
      right: -10px;
    }

    .topbar .user-dropdown::before {
      right: 30px;
    }
  }
</style>
<?php endif; ?>

<div class="user">
  <div class="user-info">
    <div class="user-details">
      <span class="user-name"><?= htmlspecialchars($perfilNombre) ?></span>
      <span class="user-role"><?= htmlspecialchars($perfilRol) ?></span>
    </div>
  </div>
  <div class="avatar" id="userMenuBtn">
    <img src="<?= BASE_URL ?>/public/uploads/<?= htmlspecialchars($perfilFotoFolder) ?>/<?= htmlspecialchars($perfilFoto) ?>"
      alt="foto" width="40px" height="40px" style="border-radius: 50%; cursor: pointer;">
  </div>

  <!-- Dropdown Menu -->
  <div class="user-dropdown" id="userDropdown">
    <div class="dropdown-header">
      <img src="<?= BASE_URL ?>/public/uploads/<?= htmlspecialchars($perfilFotoFolder) ?>/<?= htmlspecialchars($perfilFoto) ?>"
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
    <a href="<?= BASE_URL ?>/notificaciones" class="dropdown-item">
      <i class="ri-notification-3-line"></i>
      <span>Notificaciones</span>
      <span class="dropdown-badge">3</span>
    </a>
    <a href="<?= BASE_URL ?>/ayuda" class="dropdown-item">
      <i class="ri-question-line"></i>
      <span>Ayuda y Soporte</span>
    </a>
    <div class="dropdown-divider"></div>
    <a href="#" class="dropdown-item" id="toggleThemeBtn">
      <i class="ri-contrast-2-line"></i>
      <span>Cambiar Modo</span>
      <i class="ri-arrow-right-s-line dropdown-arrow"></i>
    </a>
    <a href="#" class="dropdown-item">
      <i class="ri-moon-line"></i>
      <span>Modo Descanso</span>
    </a>
    <div class="dropdown-divider"></div>
    <a href="<?= BASE_URL ?>/logout" class="dropdown-item dropdown-item-danger">
      <i class="ri-logout-box-line"></i>
      <span>Cerrar Sesión</span>
    </a>
  </div>
</div>

<?php if ($perfilRol === 'Docente' || $perfilRol === 'Estudiante'): ?>
<script>
  (function () {
    if (window.__siademyProfileDropdownInit) {
      return;
    }
    window.__siademyProfileDropdownInit = true;

    function initProfileDropdown() {
      const userMenuBtn = document.getElementById('userMenuBtn');
      const userDropdown = document.getElementById('userDropdown');

      if (!userMenuBtn || !userDropdown || userMenuBtn.dataset.dropdownInit === '1') {
        return;
      }

      userMenuBtn.dataset.dropdownInit = '1';

      const dropdownOverlay = document.querySelector('.dropdown-overlay') || document.createElement('div');
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

      userMenuBtn.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        const isOpen = userDropdown.classList.contains('show');
        if (isOpen) {
          closeUserDropdown();
        } else {
          openUserDropdown();
        }
      });

      dropdownOverlay.addEventListener('click', closeUserDropdown);

      document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && userDropdown.classList.contains('show')) {
          closeUserDropdown();
        }
      });

      userDropdown.addEventListener('click', function (event) {
        event.stopPropagation();
      });
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initProfileDropdown);
    } else {
      initProfileDropdown();
    }
  })();
</script>
<?php endif; ?>