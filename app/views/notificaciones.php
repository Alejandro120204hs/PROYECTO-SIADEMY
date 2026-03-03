<?php 
  require_once BASE_PATH . '/app/helpers/session_helper.php';
  redirectIfNoSession();
  $usuario = $_SESSION['user'] ?? [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • Notificaciones</title>
    <?php include_once BASE_PATH . '/app/views/layouts/header_coordinador.php' ?> 
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/perfil.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-admin.css">
    <style>
        body {
            background: linear-gradient(180deg, #0f1e4a 0%, #0b1736 100%);
        }

        .title {
            font-size: 24px;
            font-weight: 600;
            color: white;
            margin: 0;
        }

        section {
            margin-bottom: 24px;
        }

        section h2 {
            font-size: 22px;
            color: white;
            margin: 0 0 16px 0;
            font-family: 'Montserrat', sans-serif;
        }

        section p {
            color: #c7cbe1;
            margin: 0 0 24px 0;
            font-size: 14px;
        }

        .notification-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 16px;
        }

        .notification-box {
            background: #11193a;
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 18px;
            padding: 20px;
            display: flex;
            gap: 16px;
            transition: all 0.3s ease;
            position: relative;
            box-shadow: 0 8px 30px rgba(0, 0, 0, .2);
        }

        .notification-box:hover {
            border-color: rgba(255, 255, 255, .12);
            box-shadow: 0 12px 40px rgba(0, 0, 0, .3);
            transform: translateY(-2px);
        }

        .notification-icon {
            width: 62px;
            height: 62px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            flex-shrink: 0;
            color: white;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .06);
        }

        .notification-icon.info {
            background: #0e142e;
            color: #a4b1ff;
        }

        .notification-icon.success {
            background: #0f2818;
            color: #4ade80;
        }

        .notification-icon.warning {
            background: #2d2210;
            color: #facc15;
        }

        .notification-icon.message {
            background: #0d2234;
            color: #06b6d4;
        }

        .notification-content {
            flex: 1;
            min-width: 0;
        }

        .notification-title {
            margin: 0 0 6px 0;
            font-weight: 700;
            color: white;
            font-size: 15px;
            font-family: 'Montserrat', sans-serif;
        }

        .notification-message {
            margin: 0 0 8px 0;
            color: #c7cbe1;
            font-size: 13px;
            line-height: 1.5;
        }

        .notification-time {
            color: #8a8fa6;
            font-size: 12px;
            margin: 0;
        }

        .btn-close-notif {
            position: absolute;
            top: 12px;
            right: 12px;
            background: none;
            border: none;
            color: #8a8fa6;
            cursor: pointer;
            padding: 4px 8px;
            font-size: 20px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-close-notif:hover {
            color: #a4b1ff;
            transform: rotate(90deg);
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            grid-column: 1 / -1;
        }

        .empty-state i {
            font-size: 56px;
            color: #3a4559;
            display: block;
            margin-bottom: 16px;
        }

        .empty-state h3 {
            color: #8a8fa6;
            margin: 0 0 8px 0;
            font-size: 18px;
        }

        .empty-state p {
            color: #6b7280;
            margin: 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
  <div class="app" id="appGrid">
    <!-- SIDEBAR -->
    <?php
      if (isset($usuario['rol'])) {
        switch ($usuario['rol']) {
          case 'Administrador':
            include_once BASE_PATH . '/app/views/layouts/sidebar_coordinador.php'; break;
          case 'Docente':
            include_once BASE_PATH . '/app/views/layouts/sidebar_docente.php'; break;
          case 'Estudiante':
            include_once BASE_PATH . '/app/views/layouts/sidebar_estudiante.php'; break;
          case 'Acudiente':
            include_once BASE_PATH . '/app/views/layouts/sidebar_acudiente.php'; break;
          default:
            include_once BASE_PATH . '/app/views/layouts/sidebar_coordinador.php';
        }
      }
    ?>

    <!-- MAIN CONTENT -->
    <main class="main">
      <div class="topbar">
        <div class="topbar-left">
          <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
            <i class="ri-menu-2-line"></i>
          </button>
          <div class="title">Notificaciones</div>
        </div>
        <button class="toggle-btn" id="toggleRight" title="Mostrar/Ocultar panel derecho">
          <i class="ri-layout-right-2-line"></i>
        </button>
      </div>

      <!-- CONTENT AREA -->
      <div style="padding: 24px; min-height: calc(100vh - 120px);">
        
        <!-- HEADER SECTION -->
        <section>
          <h2>Notificaciones</h2>
          <p>Gestiona y revisa todas tus notificaciones aquí</p>
        </section>

        <!-- NOTIFICATIONS GRID -->
        <div class="notification-grid" id="notificationContainer">
          <!-- Notificación 1 -->
          <div class="notification-box">
            <div class="notification-icon info">
              <i class="ri-book-2-line"></i>
            </div>
            <div class="notification-content">
              <h3 class="notification-title">Nueva actividad asignada</h3>
              <p class="notification-message">Se ha asignado una nueva actividad en Matemáticas que debe entregar antes del 15 de marzo.</p>
              <p class="notification-time">Hace 2 horas</p>
            </div>
            <button class="btn-close-notif" title="Cerrar notificación">
              <i class="ri-close-line"></i>
            </button>
          </div>

          <!-- Notificación 2 -->
          <div class="notification-box">
            <div class="notification-icon success">
              <i class="ri-check-double-fill"></i>
            </div>
            <div class="notification-content">
              <h3 class="notification-title">Calificación registrada</h3>
              <p class="notification-message">El profesor ha calificado tu trabajo: 4.5/5.0 en Física</p>
              <p class="notification-time">Hace 1 día</p>
            </div>
            <button class="btn-close-notif" title="Cerrar notificación">
              <i class="ri-close-line"></i>
            </button>
          </div>

          <!-- Notificación 3 -->
          <div class="notification-box">
            <div class="notification-icon warning">
              <i class="ri-alarm-warning-line"></i>
            </div>
            <div class="notification-content">
              <h3 class="notification-title">Recordatorio de entrega</h3>
              <p class="notification-message">Tienes 2 días para entregar la actividad de Inglés.</p>
              <p class="notification-time">Hace 2 días</p>
            </div>
            <button class="btn-close-notif" title="Cerrar notificación">
              <i class="ri-close-line"></i>
            </button>
          </div>

          <!-- Notificación 4 -->
          <div class="notification-box">
            <div class="notification-icon message">
              <i class="ri-message-2-line"></i>
            </div>
            <div class="notification-content">
              <h3 class="notification-title">Mensaje del profesor</h3>
              <p class="notification-message">Prof. Carlos Méndez ha dejado un comentario en tu entrega de Matemáticas.</p>
              <p class="notification-time">Hace 3 días</p>
            </div>
            <button class="btn-close-notif" title="Cerrar notificación">
              <i class="ri-close-line"></i>
            </button>
          </div>

        </div>
      </div>
    </main>

    <!-- RIGHT SIDEBAR -->
    <?php include_once BASE_PATH . '/app/views/layouts/sidebar_right_coordinador.php' ?>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Toggle sidebars
    document.getElementById('toggleLeft').addEventListener('click', function() {
      document.querySelector('.app').classList.toggle('hide-left');
    });
    document.getElementById('toggleRight').addEventListener('click', function() {
      document.querySelector('.app').classList.toggle('hide-right');
    });
    
    // Cerrar notificación
    document.querySelectorAll('.btn-close-notif').forEach(btn => {
      btn.addEventListener('click', function() {
        const notifBox = this.closest('.notification-box');
        if (notifBox) {
          notifBox.style.opacity = '0';
          notifBox.style.transform = 'scale(0.95)';
          setTimeout(() => {
            notifBox.remove();
            // Si no hay más notificaciones, mostrar estado vacío
            const container = document.getElementById('notificationContainer');
            if (container.children.length === 0) {
              container.innerHTML = `
                <div style="grid-column: 1 / -1;">
                  <div class="empty-state">
                    <i class="ri-inbox-2-line"></i>
                    <h3 style="color: #999; margin: 0 0 8px 0;">Sin notificaciones</h3>
                    <p style="color: #bbb; margin: 0;">No tienes notificaciones nuevas por el momento</p>
                  </div>
                </div>
              `;
            }
          }, 300);
        }
      });
    });
  </script>
</body>

</html>

