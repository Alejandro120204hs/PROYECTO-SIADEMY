// ── Sidebar toggle acudiente (móvil: drawer overlay) ─────────
const appGrid     = document.getElementById('appGrid');
const leftSidebar = document.getElementById('leftSidebar');
const toggleLeft  = document.getElementById('toggleLeft');

const overlay = document.querySelector('.sidebar-overlay') || document.createElement('div');
if (!overlay.parentElement) { overlay.className = 'sidebar-overlay'; document.body.appendChild(overlay); }

function isMobile() { return window.innerWidth <= 768; }

function openMobileDrawer() {
  if (!leftSidebar) return;
  leftSidebar.classList.add('mobile-open');
  leftSidebar.classList.remove('hidden');
  overlay.classList.add('active');
}

function closeMobileDrawer() {
  if (!leftSidebar) return;
  leftSidebar.classList.remove('mobile-open');
  overlay.classList.remove('active');
}

overlay.onclick = closeMobileDrawer;

window.addEventListener('resize', function () {
  if (!isMobile()) {
    overlay.classList.remove('active');
    if (leftSidebar) leftSidebar.classList.remove('mobile-open');
  }
});

if (toggleLeft && appGrid && leftSidebar) {
  toggleLeft.addEventListener('click', function () {
    if (isMobile()) {
      leftSidebar.classList.contains('mobile-open') ? closeMobileDrawer() : openMobileDrawer();
    } else {
      appGrid.classList.toggle('hide-left');
      leftSidebar.classList.toggle('hidden');
    }
  });
}
