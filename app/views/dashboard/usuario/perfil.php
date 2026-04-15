<?php
  require_once BASE_PATH . '/app/helpers/session_helper.php';

  initSession();

  if (!isset($_SESSION['user'])) {
    header('Location: ' . BASE_URL . '/login');
    exit();
  }

  $rolUsuario = $_SESSION['user']['rol'] ?? '';
  if ($rolUsuario !== 'Administrador' && $rolUsuario !== 'superAdmin' && $rolUsuario !== 'Docente' && $rolUsuario !== 'Estudiante') {
    header('Location: ' . BASE_URL . '/login');
    exit();
  }

  $esSuperAdmin = $rolUsuario === 'superAdmin';
  $esDocente = $rolUsuario === 'Docente';
  $esEstudiante = $rolUsuario === 'Estudiante';
  $perfilFotoFolder = $esDocente ? 'docentes' : ($esEstudiante ? 'estudiantes' : 'usuarios');

  //ENLAZAMOS LA DEPENDENCIA DEL CONTROLADOR QUE TIENE LA FUNCION PARA MOSTRAR LOS DATOS
    require_once BASE_PATH . '/app/controllers/perfil.php';
    
    // LLAMAMOS EL ID QUE VIENE ATRAVEZ DEL METODO GET
    $id = $_SESSION['user']['id'];
    // LLAMAMOS LA FUNCION ESPECIFICA DEL CONTROLADOR
    $usuario = mostrarPerfil($id);
?>



<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIADEMY • Panel de Usuario</title>
  <?php
  include_once __DIR__ . '/../../layouts/header_coordinador.php'
  ?> 
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/perfil.css">
</head>

<body>
  <div class="app" id="appGrid">
    <!-- LEFT SIDEBAR -->
    <?php if ($esSuperAdmin): ?>
      <?php include_once __DIR__ . '/../../layouts/sidebar_superAdmin.php'; ?>
    <?php elseif ($esDocente): ?>
      <?php include_once __DIR__ . '/../../layouts/sidebar_docente.php'; ?>
    <?php elseif ($esEstudiante): ?>
      <?php include_once __DIR__ . '/../../layouts/sidebar_estudiante.php'; ?>
    <?php else: ?>
      <?php include_once __DIR__ . '/../../layouts/sidebar_coordinador.php'; ?>
    <?php endif; ?>

    <!-- MAIN -->
    <main class="main">
      <div class="topbar">
        <div class="topbar-left">
          <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
            <i class="ri-menu-2-line"></i>
          </button>
          <div class="title">Panel De Usuario</div>
        </div>
        <div class="search">
          <i class="ri-search-2-line"></i>
          <input type="text" placeholder="Buscar">
        </div>
        <button class="toggle-btn" id="toggleRight" title="Mostrar/Ocultar panel derecho">
          <i class="ri-layout-right-2-line"></i>
        </button>
      </div>

      <!-- PROFILE CARD -->
      <div class="profile-card">
        <div class="profile-header-bg"></div>
        <div class="profile-content">
          <div class="profile-avatar-container">
            <div class="profile-avatar" id="profileAvatar">
              <img src="<?= BASE_URL ?>/public/uploads/<?= $perfilFotoFolder ?>/<?= $usuario['foto'] ?>" 
              alt="foto">
              <div class="avatar-loading" id="avatarLoading">
                <i class="ri-loader-4-line"></i>
              </div>
            </div>
            <button class="avatar-edit-btn" id="avatarEditBtn">
              <i class="ri-camera-line"></i>
            </button>
            <input type="file" id="avatarInput" accept="image/*" style="display: none;">
          </div>
          <h2 class="profile-name"><?= $usuario['nombres'] ?></h2>
          <p class="profile-role"><?= $usuario['rol'] ?></p>
          <p class="profile-location">
            <i class="ri-map-pin-line"></i>
            <?= $usuario['direccion_institucion'] ?> - <?= $usuario['nombre_institucion'] ?>
          </p>

          <div class="profile-details">
            <div class="detail-item">
              <div class="detail-icon">
                <i class="ri-phone-line"></i>
              </div>
              <div class="detail-text">
                <span class="detail-label">Teléfono</span>
                <span class="detail-value"><?= $usuario['telefono'] ?></span>
              </div>
            </div>
            <div class="detail-item">
              <div class="detail-icon">
                <i class="ri-mail-line"></i>
              </div>
              <div class="detail-text">
                <span class="detail-label">Email</span>
                <span class="detail-value"><?= $usuario['correo'] ?></span>
              </div>
            </div>
          </div>
          <div class="profile-menu">
            <button class="menu-dots" id="menuDots">
              <i class="ri-more-2-fill"></i>
            </button>
            <div class="dropdown-menu" id="dropdownMenu">
              <button class="dropdown-item" data-tab="edit-profile">
                <i class="ri-edit-line"></i>
                Editar Perfil
              </button>
              <button class="dropdown-item" data-tab="change-password">
                <i class="ri-lock-line"></i>
                Cambiar Contraseña
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- MODAL DE CONFIGURACIÓN -->
      <div class="modal-overlay" id="modalOverlay">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">Configuración de Perfil</h3>
            <button class="modal-close" id="modalClose">
              <i class="ri-close-line"></i>
            </button>
          </div>
          
          <div class="modal-tabs">
            <button class="tab-btn active" data-tab="edit-profile">Editar Perfil</button>
            <button class="tab-btn" data-tab="change-password">Cambiar Contraseña</button>
          </div>

          <div class="tab-content">
            <!-- FORMULARIO EDITAR PERFIL -->
            <form class="tab-pane active" id="edit-profile-form" action="<?= BASE_URL ?>/configuracion" method="POST">
              <input type="hidden" name="accion" value="actualizar-perfil">
              <div class="form-group">
                <label for="fullName">Nombres</label>
                <input type="text" name="nombres" id="fullName" value="<?= htmlspecialchars($usuario['nombres'] ?? '') ?>" class="form-input" required>
              </div>

              <div class="form-group">
                <label for="lastName">Apellidos</label>
                <input type="text" name="apellidos" id="lastName" value="<?= htmlspecialchars($usuario['apellidos'] ?? '') ?>" class="form-input" required>
              </div>
              
              <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" name="correo" id="email" value="<?= htmlspecialchars($usuario['correo'] ?? '') ?>" class="form-input" required>
              </div>
              
              <div class="form-group">
                <label for="phone">Teléfono</label>
                <input type="tel" name="telefono" id="phone" value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>" class="form-input" required>
              </div>
              
              <div class="form-group">
                <label for="location">Edad</label>
                <input type="number" name="edad" id="location" value="<?= htmlspecialchars($usuario['edad'] ?? '') ?>" class="form-input" min="1" max="120" required>
              </div>
              
              <div class="form-actions">
                <button type="button" class="btn btn-secondary" id="cancelEdit">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
              </div>
            </form>

            <!-- FORMULARIO CAMBIAR CONTRASEÑA -->
            <form class="tab-pane" id="change-password-form" action="<?= BASE_URL ?>/actualizar-clave" method="POST">
              <input type="hidden" name="accion" value="actualizar-clave">
              <div class="form-group">
                <label for="currentPassword">Contraseña Actual</label>
                <input type="password" name="cActual" id="currentPassword" class="form-input" placeholder="Ingresa tu contraseña actual" required>
              </div>
              
              <div class="form-group">
                <label for="newPassword">Nueva Contraseña</label>
                <input type="password" name="cNueva" id="newPassword" class="form-input" placeholder="Ingresa nueva contraseña" minlength="8" required>
              </div>
              
              <div class="form-group">
                <label for="confirmPassword">Confirmar Contraseña</label>
                <input type="password" name="conClave" id="confirmPassword" class="form-input" placeholder="Confirma nueva contraseña" minlength="8" required>
              </div>
              
              <div class="password-strength">
                <div class="strength-bar">
                  <div class="strength-fill" data-strength="0"></div>
                </div>
                <span class="strength-text">Seguridad de la contraseña</span>
              </div>
              
              <div class="form-actions">
                <button type="button" class="btn btn-secondary" id="cancelPassword">Cancelar</button>
                <button type="submit" class="btn btn-primary">Actualizar Contraseña</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- MODAL CAMBIAR FOTO -->
      <div class="avatar-modal" id="avatarModal">
        <div class="avatar-modal-content">
          <div class="avatar-modal-header">
            <h3 class="avatar-modal-title">Cambiar Foto de Perfil</h3>
            <button class="avatar-modal-close" id="avatarModalClose">
              <i class="ri-close-line"></i>
            </button>
          </div>
          
          <div class="avatar-preview" id="avatarPreview">
            <img src="" alt="Vista previa" id="avatarPreviewImage">
            <div class="avatar-preview-placeholder" id="avatarPreviewPlaceholder">
              <i class="ri-user-line"></i>
              <span>Vista previa</span>
            </div>
          </div>
          
          <div class="avatar-upload-options">
            <label class="avatar-upload-btn" for="avatarUploadInput">
              <i class="ri-upload-cloud-line"></i>
              <div class="avatar-upload-btn-content">
                <div class="avatar-upload-btn-title">Subir desde dispositivo</div>
                <div class="avatar-upload-btn-subtitle">JPG, PNG o GIF (Max. 5MB)</div>
              </div>
            </label>
            <input type="file" id="avatarUploadInput" accept="image/*" style="display: none;">
            
            <button class="avatar-upload-btn" id="avatarTakePhoto">
              <i class="ri-camera-line"></i>
              <div class="avatar-upload-btn-content">
                <div class="avatar-upload-btn-title">Tomar foto</div>
                <div class="avatar-upload-btn-subtitle">Usar cámara web</div>
              </div>
            </button>
          </div>
          
          <div class="avatar-actions">
            <button class="avatar-btn avatar-btn-secondary" id="avatarCancel">Cancelar</button>
            <button class="avatar-btn avatar-btn-primary" id="avatarSave" disabled>Guardar Foto</button>
          </div>
        </div>
      </div>

      <!-- CARDS GRID -->
      <div class="cards-grid">
        <!-- CONTACTOS -->
        <div class="user-card">
          <div class="card-header">
            <div>
              <h3 class="card-title">Contactos</h3>
              <p class="card-subtitle">Lista de Contactos</p>
            </div>
            <button class="add-button">+</button>
          </div>
          <div class="card-search">
            <i class="ri-search-line"></i>
            <input type="text" placeholder="Buscar Aquí">
          </div>
          <div class="contact-list">
            <div class="contact-item">
              <div class="contact-avatar"></div>
              <div class="contact-info">
                <div class="contact-name">Samantha William</div>
                <div class="contact-role">Estudiante</div>
              </div>
              <button class="contact-action">
                <i class="ri-mail-line"></i>
              </button>
            </div>
            <div class="contact-item">
              <div class="contact-avatar"></div>
              <div class="contact-info">
                <div class="contact-name">Tony Soap</div>
                <div class="contact-role">Estudiante</div>
              </div>
              <button class="contact-action active">
                <i class="ri-message-3-line"></i>
              </button>
            </div>
            <div class="contact-item">
              <div class="contact-avatar"></div>
              <div class="contact-info">
                <div class="contact-name">Karen Hope</div>
                <div class="contact-role">Clase VII A</div>
              </div>
              <button class="contact-action">
                <i class="ri-mail-line"></i>
              </button>
            </div>
            <div class="contact-item">
              <div class="contact-avatar"></div>
              <div class="contact-info">
                <div class="contact-name">Jordan Nico</div>
                <div class="contact-role">Estudiante</div>
              </div>
              <button class="contact-action">
                <i class="ri-mail-line"></i>
              </button>
            </div>
            <div class="contact-item">
              <div class="contact-avatar"></div>
              <div class="contact-info">
                <div class="contact-name">Nadila Adja</div>
                <div class="contact-role">Estudiante</div>
              </div>
              <button class="contact-action">
                <i class="ri-mail-line"></i>
              </button>
            </div>
          </div>
          <div class="view-more">Ver Más</div>
        </div>

        <!-- MENSAJES -->
        <div class="user-card">
          <div class="card-header">
            <div>
              <h3 class="card-title">Mensajes</h3>
            </div>
          </div>
          <div class="card-search">
            <i class="ri-search-line"></i>
            <input type="text" placeholder="Buscar Aquí">
          </div>
          <div class="contact-list">
            <div class="contact-item">
              <div class="contact-avatar"></div>
              <div class="contact-info">
                <div class="contact-name">Samantha William</div>
                <div class="contact-role">Estudiante</div>
              </div>
              <div class="message-meta">
                <span class="message-time">12:45 PM</span>
                <span class="message-badge">1</span>
              </div>
            </div>
            <div class="contact-item">
              <div class="contact-avatar"></div>
              <div class="contact-info">
                <div class="contact-name">Tony Soap</div>
                <div class="contact-role">Estudiante</div>
              </div>
              <div class="message-meta">
                <span class="message-time">12:45 PM</span>
                <span class="message-badge">1</span>
              </div>
            </div>
            <div class="contact-item">
              <div class="contact-avatar"></div>
              <div class="contact-info">
                <div class="contact-name">Karen Hope</div>
                <div class="contact-role">Estudiante</div>
              </div>
              <div class="message-meta">
                <span class="message-time">12:45 PM</span>
              </div>
            </div>
            <div class="contact-item">
              <div class="contact-avatar"></div>
              <div class="contact-info">
                <div class="contact-name">Jordan Nico</div>
                <div class="contact-role">Estudiante</div>
              </div>
              <div class="message-meta">
                <span class="message-time">12:45 PM</span>
                <span class="message-badge">2</span>
              </div>
            </div>
            <div class="contact-item">
              <div class="contact-avatar"></div>
              <div class="contact-info">
                <div class="contact-name">Nadila Adja</div>
                <div class="contact-role">Estudiante</div>
              </div>
              <div class="message-meta">
                <span class="message-time">12:45 PM</span>
              </div>
            </div>
          </div>
          <div class="view-more">Ver Más</div>
        </div>
      </div>
    </main>

    <!-- RIGHT SIDEBAR -->
    <aside class="rightbar" id="rightSidebar">
      <div class="user">
      
          <div class="avatar" title="Profesor"><img src="<?= BASE_URL ?>/public/uploads/<?= $perfilFotoFolder ?>/<?= $usuario['foto'] ?>" 
              alt="foto"  width="40px" height="40px" style="border-radius: 50%;"></div>
      </div>

      <div class="panel-title">Actividades y notificaciones</div>
      <p class="muted">Últimas actualizaciones</p>

      <div class="timeline">
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-content">
            <p class="timeline-text">Samantha William publicó en Fotografías...</p>
            <p class="timeline-date">5 marzo 2021 8:00</p>
          </div>
        </div>
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-content">
            <p class="timeline-text">Tony Soap publicó en Fotografías...</p>
            <p class="timeline-date">6 marzo 2021 12:45 PM</p>
          </div>
        </div>
      </div>
    </aside>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const menuDots = document.getElementById('menuDots');
    const dropdownMenu = document.getElementById('dropdownMenu');
    const modalOverlay = document.getElementById('modalOverlay');
    const modalClose = document.getElementById('modalClose');
    const cancelEdit = document.getElementById('cancelEdit');
    const cancelPassword = document.getElementById('cancelPassword');
    const tabBtns = document.querySelectorAll('.tab-btn');
    const dropdownItems = document.querySelectorAll('.dropdown-item');
    const newPassword = document.getElementById('newPassword');
    const strengthFill = document.querySelector('.strength-fill');
    const strengthText = document.querySelector('.strength-text');
    
    // Elementos para cambiar avatar
    const avatarEditBtn = document.getElementById('avatarEditBtn');
    const avatarModal = document.getElementById('avatarModal');
    const avatarModalClose = document.getElementById('avatarModalClose');
    const avatarUploadInput = document.getElementById('avatarUploadInput');
    const avatarPreview = document.getElementById('avatarPreview');
    const avatarPreviewImage = document.getElementById('avatarPreviewImage');
    const avatarPreviewPlaceholder = document.getElementById('avatarPreviewPlaceholder');
    const avatarSave = document.getElementById('avatarSave');
    const avatarCancel = document.getElementById('avatarCancel');
    const avatarTakePhoto = document.getElementById('avatarTakePhoto');
    const profileAvatar = document.getElementById('profileAvatar');
    const avatarImage = document.getElementById('avatarImage');
    const avatarLoading = document.getElementById('avatarLoading');

    // Toggle dropdown menu
    menuDots.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdownMenu.classList.toggle('show');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function() {
        dropdownMenu.classList.remove('show');
    });

    // Dropdown item click handlers
    dropdownItems.forEach(item => {
        item.addEventListener('click', function() {
            const tab = this.getAttribute('data-tab');
            openModal(tab);
            dropdownMenu.classList.remove('show');
        });
    });

    // Tab switching
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const tab = this.getAttribute('data-tab');
            switchTab(tab);
        });
    });

    // Open modal with specific tab
    function openModal(tab) {
        modalOverlay.classList.add('show');
        switchTab(tab);
    }

    // Switch tabs
    function switchTab(tab) {
        // Update tab buttons
        tabBtns.forEach(btn => {
            btn.classList.remove('active');
            if (btn.getAttribute('data-tab') === tab) {
                btn.classList.add('active');
            }
        });

        // Update tab content
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.remove('active');
        });
        document.getElementById(`${tab}-form`).classList.add('active');
    }

    // Close modal
    function closeModal() {
        modalOverlay.classList.remove('show');
    }

    // Event listeners for closing modal
    modalClose.addEventListener('click', closeModal);
    cancelEdit.addEventListener('click', closeModal);
    cancelPassword.addEventListener('click', closeModal);

    // Close modal when clicking outside content
    modalOverlay.addEventListener('click', function(e) {
        if (e.target === modalOverlay) {
            closeModal();
        }
    });

    // Password strength indicator
    newPassword.addEventListener('input', function() {
        const password = this.value;
        let strength = 0;

        if (password.length >= 8) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;

        strengthFill.setAttribute('data-strength', strength);
        
        const strengthLabels = ['Muy débil', 'Débil', 'Moderada', 'Fuerte', 'Muy fuerte'];
        strengthText.textContent = `Seguridad: ${strengthLabels[strength]}`;
    });

    // Form submissions (validación mínima en cliente)
    document.getElementById('change-password-form').addEventListener('submit', function(e) {
      const newPasswordValue = document.getElementById('newPassword').value;
      const confirmPasswordValue = document.getElementById('confirmPassword').value;

      if (newPasswordValue !== confirmPasswordValue) {
        e.preventDefault();
        alert('Las contraseñas no coinciden');
      }
    });

    // Avatar functionality
    avatarEditBtn.addEventListener('click', function() {
        avatarModal.classList.add('show');
    });

    avatarModalClose.addEventListener('click', closeAvatarModal);
    avatarCancel.addEventListener('click', closeAvatarModal);

    function closeAvatarModal() {
        avatarModal.classList.remove('show');
        resetAvatarPreview();
    }

    avatarModal.addEventListener('click', function(e) {
        if (e.target === avatarModal) {
            closeAvatarModal();
        }
    });

    // Handle file upload
    avatarUploadInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 5 * 1024 * 1024) {
                alert('El archivo es demasiado grande. Máximo 5MB.');
                return;
            }
            
            if (!file.type.match('image.*')) {
                alert('Por favor selecciona una imagen válida.');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                avatarPreviewImage.src = e.target.result;
                avatarPreviewImage.style.display = 'block';
                avatarPreviewPlaceholder.style.display = 'none';
                avatarSave.disabled = false;
            };
            reader.readAsDataURL(file);
        }
    });

    // Take photo (placeholder - en un entorno real usaría la API de cámara)
    avatarTakePhoto.addEventListener('click', function() {
        alert('Funcionalidad de cámara no disponible en este demo. Por favor sube una imagen desde tu dispositivo.');
    });

    // Save avatar
    avatarSave.addEventListener('click', function() {
        if (avatarPreviewImage.src) {
            // Simular carga
            avatarLoading.classList.add('spinning');
            
            setTimeout(() => {
                // Actualizar avatar principal
                avatarImage.src = avatarPreviewImage.src;
                avatarImage.style.display = 'block';
                
                // Simular guardado en servidor
                avatarLoading.classList.remove('spinning');
                alert('Foto de perfil actualizada correctamente');
                closeAvatarModal();
            }, 1500);
        }
    });

    function resetAvatarPreview() {
        avatarPreviewImage.src = '';
        avatarPreviewImage.style.display = 'none';
        avatarPreviewPlaceholder.style.display = 'grid';
        avatarSave.disabled = true;
        avatarUploadInput.value = '';
    }

    // Toggle sidebars
    const appGrid = document.getElementById('appGrid');
    const leftSidebar = document.getElementById('leftSidebar');
    const rightSidebar = document.getElementById('rightSidebar');
    const toggleLeft = document.getElementById('toggleLeft');
    const toggleRight = document.getElementById('toggleRight');
    const sidebarOverlay = document.querySelector('.sidebar-overlay') || document.createElement('div');

    if (!sidebarOverlay.parentElement) {
      sidebarOverlay.className = 'sidebar-overlay';
      document.body.appendChild(sidebarOverlay);
    }

    let leftVisible = localStorage.getItem('leftSidebarVisible') !== 'false';
    let rightVisible = localStorage.getItem('rightSidebarVisible') !== 'false';

    function isMobile() {
      return window.innerWidth <= 768;
    }

    function openMobileDrawer() {
      if (!leftSidebar) return;
      leftSidebar.classList.add('mobile-open');
      leftSidebar.classList.remove('hidden');
      sidebarOverlay.classList.add('active');
    }

    function closeMobileDrawer() {
      if (!leftSidebar) return;
      leftSidebar.classList.remove('mobile-open');
      leftSidebar.classList.add('hidden');
      sidebarOverlay.classList.remove('active');
    }

    function updateGridState() {
      if (!appGrid) return;

      appGrid.classList.remove('hide-left', 'hide-right', 'hide-both');

      if (isMobile()) {
        if (!rightVisible) {
          appGrid.classList.add('hide-right');
        }
        return;
      }

      if (!leftVisible && !rightVisible) {
        appGrid.classList.add('hide-both');
      } else if (!leftVisible) {
        appGrid.classList.add('hide-left');
      } else if (!rightVisible) {
        appGrid.classList.add('hide-right');
      }
    }

    function toggleLeftSidebar() {
      if (isMobile()) {
        const isOpen = leftSidebar && leftSidebar.classList.contains('mobile-open');
        isOpen ? closeMobileDrawer() : openMobileDrawer();
        return;
      }

      leftVisible = !leftVisible;
      if (leftSidebar) {
        leftSidebar.classList.toggle('hidden', !leftVisible);
      }
      localStorage.setItem('leftSidebarVisible', leftVisible);
      updateGridState();
    }

    function toggleRightSidebar() {
      rightVisible = !rightVisible;
      if (rightSidebar) {
        rightSidebar.classList.toggle('hidden', !rightVisible);
      }
      localStorage.setItem('rightSidebarVisible', rightVisible);
      updateGridState();
    }

    if (toggleLeft) {
      toggleLeft.addEventListener('click', function(event) {
        event.preventDefault();
        toggleLeftSidebar();
      });
    }

    if (toggleRight) {
      toggleRight.addEventListener('click', function(event) {
        event.preventDefault();
        toggleRightSidebar();
      });
    }

    sidebarOverlay.addEventListener('click', closeMobileDrawer);

    window.addEventListener('resize', function() {
      if (!isMobile()) {
        sidebarOverlay.classList.remove('active');
        if (leftSidebar) {
          leftSidebar.classList.remove('mobile-open');
          leftSidebar.classList.toggle('hidden', !leftVisible);
        }
      }
      updateGridState();
    });

    if (!isMobile() && leftSidebar) {
      leftSidebar.classList.toggle('hidden', !leftVisible);
    }

    if (rightSidebar) {
      rightSidebar.classList.toggle('hidden', !rightVisible);
    }

    updateGridState();

    // Si viene de /configuracion, abrir automáticamente el modal
    <?php if(isset($activeTab) && ($activeTab === 'edit-profile' || $activeTab === 'change-password')): ?>
      openModal('<?= $activeTab ?>');
    <?php endif; ?>
});
  </script>
</body>
</html>