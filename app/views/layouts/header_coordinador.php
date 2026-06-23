<?php
  /* ── Variables de tema para JS ─────────────────────────────── */
  $headerTema  = htmlspecialchars($_SESSION['user']['tema'] ?? 'dark', ENT_QUOTES, 'UTF-8');
  $headerBaseUrl = rtrim(BASE_URL, '/');

  /* ── Versiones de cache-busting ────────────────────────────── */
  $vDropdownCss  = @filemtime(BASE_PATH . '/public/assets/dashboard/css/topbar-dropdown.css') ?: 1;
  $vModoClaroCss = @filemtime(BASE_PATH . '/public/assets/dashboard/css/modo-claro-admin.css') ?: 1;
  $vLightModeJs  = @filemtime(BASE_PATH . '/public/assets/dashboard/js/light-mode.js') ?: 1;
?>
<?php /* ── Script anti-FOUC: se ejecuta ANTES de cualquier CSS ─────
         Aplica el tema del servidor (o localStorage) al instante,
         sin esperar al DOM, evitando el parpadeo blanco inicial. */ ?>
<script>
(function () {
  var st = '<?= $headerTema ?>';
  var ls = '';
  try { ls = localStorage.getItem('siademy-theme') || ''; } catch (e) {}
  var theme = (st === 'light' || st === 'dark') ? st : (ls || 'dark');
  if (theme === 'light') document.documentElement.classList.add('light-mode');
  /* Globals para light-mode.js */
  window.SIADEMY_BASE_URL = '<?= $headerBaseUrl ?>';
  window.SIADEMY_THEME    = theme;
})();
</script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Dropdown de perfil (estilos compartidos por todos los roles) -->
<link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/topbar-dropdown.css?v=<?= $vDropdownCss ?>">

<!-- Sistema de temas claro/oscuro -->
<link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/modo-claro-admin.css?v=<?= $vModoClaroCss ?>">
<script src="<?= BASE_URL ?>/public/assets/dashboard/js/light-mode.js?v=<?= $vLightModeJs ?>" defer></script>
