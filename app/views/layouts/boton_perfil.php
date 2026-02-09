<div class="user">
  <div class="user-info">
    <div class="user-details">
      <span class="user-name"><?= $usuario['nombres'] ?></span>
      <span class="user-role"><?= $usuario['rol'] ?></span>
    </div>
  </div>
  <div class="avatar" id="userMenuBtn">
    <img src="<?= BASE_URL ?>/public/uploads/usuarios/<?= $usuario['foto'] ?>"
      alt="foto" width="40px" height="40px" style="border-radius: 50%; cursor: pointer;">
  </div>

  <!-- Dropdown Menu -->
  <div class="user-dropdown" id="userDropdown">
    <div class="dropdown-header">
      <img src="<?= BASE_URL ?>/public/uploads/usuarios/<?= $usuario['foto'] ?>"
        alt="foto" width="48px" height="48px" style="border-radius: 50%;">
      <div>
        <strong><?= $usuario['nombres'] ?></strong>
        <small><?= $usuario['rol'] ?></small>
      </div>
    </div>
    <div class="dropdown-divider"></div>
    <a href="<?= BASE_URL ?>/dashboard-perfil" class="dropdown-item">
      <i class="ri-user-line"></i>
      <span>Ver Perfil</span>
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