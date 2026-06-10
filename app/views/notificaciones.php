<?php
  require_once BASE_PATH . '/app/helpers/session_helper.php';
  require_once BASE_PATH . '/app/helpers/notificacion_helper.php';
  redirectIfNoSession();
  $usuario = $_SESSION['user'] ?? [];

  // Variables pasadas por el controlador — fallback seguro si se accede directo
  if (!isset($notificaciones))  $notificaciones  = [];
  if (!isset($totalNoLeidas))   $totalNoLeidas    = 0;
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
        body { background: linear-gradient(180deg, #0f1e4a 0%, #0b1736 100%); overflow-x: hidden; }

        /* Sin sidebar derecho: el main ocupa todo el espacio disponible */
        #appGrid {
            grid-template-columns: auto 1fr !important;
            grid-template-areas: "sidebar main" !important;
        }
        #appGrid .rightbar { display: none !important; }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 24px;
        }

        .page-header h2 {
            font-size: 22px;
            color: white;
            margin: 0;
            font-family: 'Montserrat', sans-serif;
        }

        .page-header p {
            color: #c7cbe1;
            margin: 4px 0 0 0;
            font-size: 14px;
        }

        .header-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-mark-all {
            background: rgba(99, 102, 241, 0.15);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: #a4b1ff;
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-mark-all:hover {
            background: rgba(99, 102, 241, 0.25);
            border-color: #6366f1;
            color: #fff;
        }

        /* Filtros */
        .filter-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 7px 16px;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,.1);
            background: transparent;
            color: #a0a3bd;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .filter-tab.active, .filter-tab:hover {
            background: rgba(99, 102, 241, 0.2);
            border-color: #6366f1;
            color: #fff;
        }

        /* Grid de notificaciones */
        .notification-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 16px;
        }

        .notification-box {
            background: #11193a;
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 18px;
            padding: 20px;
            display: flex;
            gap: 16px;
            transition: all 0.3s ease;
            position: relative;
            box-shadow: 0 8px 30px rgba(0,0,0,.2);
        }

        .notification-box.unread {
            border-left: 3px solid #6366f1;
        }

        .notification-box.read {
            opacity: 0.7;
        }

        .notification-box:hover {
            border-color: rgba(255,255,255,.12);
            box-shadow: 0 12px 40px rgba(0,0,0,.3);
            transform: translateY(-2px);
            opacity: 1;
        }

        .notification-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
        }

        .notification-icon.info    { background: #0e142e; color: #a4b1ff; }
        .notification-icon.success { background: #0f2818; color: #4ade80; }
        .notification-icon.warning { background: #2d2210; color: #facc15; }

        .notification-content { flex: 1; min-width: 0; }

        .notification-title {
            margin: 0 0 5px 0;
            font-weight: 700;
            color: white;
            font-size: 14px;
            font-family: 'Montserrat', sans-serif;
            padding-right: 28px;
        }

        .notification-message {
            margin: 0 0 8px 0;
            color: #c7cbe1;
            font-size: 13px;
            line-height: 1.5;
        }

        .notification-footer {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .notification-time {
            color: #8a8fa6;
            font-size: 12px;
        }

        .btn-mark-read {
            background: none;
            border: 1px solid rgba(99,102,241,.3);
            color: #a4b1ff;
            font-size: 11px;
            padding: 3px 10px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-mark-read:hover { background: rgba(99,102,241,.2); color: #fff; }

        .btn-close-notif {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            color: #8a8fa6;
            cursor: pointer;
            font-size: 18px;
            padding: 4px;
            transition: all 0.2s ease;
            line-height: 1;
        }

        .btn-close-notif:hover { color: #ef4444; transform: rotate(90deg); }

        /* Estado vacío */
        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 80px 20px;
        }

        .empty-state i { font-size: 56px; color: #3a4559; display: block; margin-bottom: 16px; }
        .empty-state h3 { color: #8a8fa6; margin: 0 0 8px 0; font-size: 18px; }
        .empty-state p  { color: #6b7280; margin: 0; font-size: 14px; }

        /* Indicador de no leída */
        .unread-dot {
            width: 8px; height: 8px;
            background: #6366f1;
            border-radius: 50%;
            flex-shrink: 0;
            margin-top: 6px;
        }
    </style>
</head>
<body>
  <div class="app" id="appGrid">

    <!-- SIDEBAR IZQUIERDO -->
    <?php
      if (isset($usuario['rol'])) {
        switch ($usuario['rol']) {
          case 'Administrador':
            include_once BASE_PATH . '/app/views/layouts/sidebar_coordinador.php'; break;
          case 'Docente':
            include_once BASE_PATH . '/app/views/layouts/sidebar_docente.php'; break;
          case 'Estudiante':
            include_once BASE_PATH . '/app/views/layouts/sidebar_estudiante.php'; break;
          case 'superAdmin':
            include_once BASE_PATH . '/app/views/layouts/sidebar_superAdmin.php'; break;
          default:
            include_once BASE_PATH . '/app/views/layouts/sidebar_coordinador.php';
        }
      }
    ?>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="main">
      <div class="topbar">
        <div class="topbar-left">
          <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
            <i class="ri-menu-2-line"></i>
          </button>
          <div class="title">Notificaciones</div>
        </div>
      </div>

      <div style="padding: 24px; min-height: calc(100vh - 120px);">

        <!-- Encabezado con acciones -->
        <div class="page-header">
          <div>
            <h2>Notificaciones</h2>
            <p>
              <?php if ($totalNoLeidas > 0): ?>
                Tienes <strong style="color:#a4b1ff"><?= $totalNoLeidas ?></strong> notificación<?= $totalNoLeidas !== 1 ? 'es' : '' ?> sin leer
              <?php else: ?>
                Todas tus notificaciones están al día
              <?php endif; ?>
            </p>
          </div>
          <?php if (!empty($notificaciones)): ?>
          <div class="header-actions">
            <?php if ($totalNoLeidas > 0): ?>
            <button class="btn-mark-all" id="btnMarcarTodas">
              <i class="ri-check-double-line"></i> Marcar todas como leídas
            </button>
            <?php endif; ?>
          </div>
          <?php endif; ?>
        </div>

        <!-- Filtros -->
        <div class="filter-tabs">
          <button class="filter-tab active" data-filter="todas">Todas</button>
          <button class="filter-tab" data-filter="no-leidas">No leídas</button>
          <button class="filter-tab" data-filter="leidas">Leídas</button>
        </div>

        <!-- Grid de notificaciones -->
        <div class="notification-grid" id="notificationContainer">

          <?php if (empty($notificaciones)): ?>
            <div class="empty-state">
              <i class="ri-inbox-2-line"></i>
              <h3>Sin notificaciones</h3>
              <p>No tienes notificaciones por el momento</p>
            </div>

          <?php else: ?>
            <?php foreach ($notificaciones as $notif):
              [$icono, $colorClase] = metadataNotificacion($notif['tipo']);
              $esLeida    = (int)$notif['leida'] === 1;
              $claseBox   = $esLeida ? 'read' : 'unread';
              $fechaTexto = !empty($notif['created_at'])
                ? date('d/m/Y H:i', strtotime($notif['created_at']))
                : '';
            ?>
            <div class="notification-box <?= $claseBox ?>"
                 data-id="<?= (int)$notif['id'] ?>"
                 data-leida="<?= $esLeida ? '1' : '0' ?>">

              <div class="notification-icon <?= htmlspecialchars($colorClase) ?>">
                <i class="<?= htmlspecialchars($icono) ?>"></i>
              </div>

              <div class="notification-content">
                <h3 class="notification-title"><?= htmlspecialchars($notif['titulo']) ?></h3>
                <p class="notification-message"><?= htmlspecialchars($notif['mensaje']) ?></p>
                <div class="notification-footer">
                  <span class="notification-time"><?= $fechaTexto ?></span>
                  <?php if (!$esLeida): ?>
                  <button class="btn-mark-read" data-id="<?= (int)$notif['id'] ?>">
                    Marcar como leída
                  </button>
                  <?php endif; ?>
                  <?php if (!empty($notif['url_accion'])): ?>
                  <a href="<?= htmlspecialchars($notif['url_accion']) ?>"
                     style="font-size:11px;color:#6366f1;text-decoration:none">
                    Ver detalle →
                  </a>
                  <?php endif; ?>
                </div>
              </div>

              <?php if (!$esLeida): ?>
              <div class="unread-dot"></div>
              <?php endif; ?>

              <button class="btn-close-notif" data-id="<?= (int)$notif['id'] ?>" title="Eliminar notificación">
                <i class="ri-close-line"></i>
              </button>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>

        </div>
      </div>
    </main>

  </div>

  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const BASE_URL   = '<?= rtrim(BASE_URL, '/') ?>';
    const API_NOTIF  = BASE_URL + '/api/notificaciones';

    // ── Toggle sidebar izquierdo ──────────────────────────────────────────
    document.getElementById('toggleLeft').addEventListener('click', function () {
      document.querySelector('.app').classList.toggle('hide-left');
    });

    // ── Helpers ───────────────────────────────────────────────────────────
    function postAction(action, id) {
      const body = new URLSearchParams({ action });
      if (id) body.append('id', id);
      return fetch(API_NOTIF + '?action=' + action, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body
      }).then(function (r) { return r.json(); });
    }

    function animarYEliminar(box) {
      box.style.transition = 'opacity .3s, transform .3s';
      box.style.opacity    = '0';
      box.style.transform  = 'scale(0.95)';
      setTimeout(function () {
        box.remove();
        const container = document.getElementById('notificationContainer');
        // Solo reemplaza el DOM cuando NO queda ninguna tarjeta en absoluto
        if (container.querySelectorAll('.notification-box').length === 0) {
          container.innerHTML = '<div class="empty-state" style="grid-column:1/-1"><i class="ri-inbox-2-line"></i><h3>Sin notificaciones</h3><p>No tienes notificaciones por el momento</p></div>';
        }
      }, 300);
    }

    // checkEmpty: muestra/oculta un placeholder SIN destruir tarjetas del DOM.
    // Se usa solo al cambiar filtros — cuando un filtro no tiene resultados
    // las tarjetas siguen en el DOM (ocultas con display:none).
    function checkEmpty() {
      const container   = document.getElementById('notificationContainer');
      const allBoxes    = container.querySelectorAll('.notification-box');
      const visible     = Array.from(allBoxes).filter(function (b) {
        return b.style.display !== 'none';
      });
      let placeholder   = container.querySelector('.filter-placeholder');

      if (allBoxes.length > 0 && visible.length === 0) {
        if (!placeholder) {
          placeholder = document.createElement('div');
          placeholder.className = 'empty-state filter-placeholder';
          placeholder.style.gridColumn = '1 / -1';
          placeholder.innerHTML = '<i class="ri-filter-line"></i><h3>Sin resultados</h3><p>No hay notificaciones en este filtro</p>';
          container.appendChild(placeholder);
        }
      } else if (placeholder) {
        placeholder.remove();
      }
    }

    // ── Eliminar (descartar) una notificación ─────────────────────────────
    document.getElementById('notificationContainer').addEventListener('click', function (e) {
      const closeBtn = e.target.closest('.btn-close-notif');
      if (closeBtn) {
        const id  = closeBtn.dataset.id;
        const box = closeBtn.closest('.notification-box');
        postAction('descartar', id).then(function (data) {
          if (data.success) animarYEliminar(box);
        }).catch(function () { animarYEliminar(box); });
      }
    });

    // ── Marcar una como leída ─────────────────────────────────────────────
    document.getElementById('notificationContainer').addEventListener('click', function (e) {
      const readBtn = e.target.closest('.btn-mark-read');
      if (readBtn) {
        const id  = readBtn.dataset.id;
        const box = readBtn.closest('.notification-box');
        postAction('leer', id).then(function (data) {
          if (data.success) {
            box.classList.remove('unread');
            box.classList.add('read');
            box.dataset.leida = '1';
            const dot = box.querySelector('.unread-dot');
            if (dot) dot.remove();
            readBtn.remove();
          }
        });
      }
    });

    // ── Marcar todas como leídas ──────────────────────────────────────────
    const btnTodas = document.getElementById('btnMarcarTodas');
    if (btnTodas) {
      btnTodas.addEventListener('click', function () {
        postAction('leer-todas').then(function (data) {
          if (data.success) {
            document.querySelectorAll('.notification-box.unread').forEach(function (box) {
              box.classList.remove('unread');
              box.classList.add('read');
              box.dataset.leida = '1';
              const dot = box.querySelector('.unread-dot');
              if (dot) dot.remove();
              const readBtn = box.querySelector('.btn-mark-read');
              if (readBtn) readBtn.remove();
            });
            btnTodas.remove();
            const badge = document.getElementById('notifBadge');
            if (badge) badge.style.display = 'none';
          }
        });
      });
    }

    // ── Filtros por estado ────────────────────────────────────────────────
    document.querySelectorAll('.filter-tab').forEach(function (tab) {
      tab.addEventListener('click', function () {
        document.querySelectorAll('.filter-tab').forEach(function (t) { t.classList.remove('active'); });
        tab.classList.add('active');

        const filtro = tab.dataset.filter;
        document.querySelectorAll('.notification-box').forEach(function (box) {
          const leida = box.dataset.leida === '1';
          if (filtro === 'todas') {
            box.style.display = '';
          } else if (filtro === 'no-leidas') {
            box.style.display = leida ? 'none' : '';
          } else if (filtro === 'leidas') {
            box.style.display = leida ? '' : 'none';
          }
        });
        checkEmpty();
      });
    });
  </script>
</body>
</html>
