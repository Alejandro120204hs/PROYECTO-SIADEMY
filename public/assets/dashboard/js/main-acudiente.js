// Toggle sidebar izquierdo
const appGrid = document.getElementById('appGrid');
const leftSidebar = document.getElementById('leftSidebar');
const toggleLeft = document.getElementById('toggleLeft');

if (toggleLeft && appGrid && leftSidebar) {
  toggleLeft.addEventListener('click', function () {
    appGrid.classList.toggle('hide-left');
    leftSidebar.classList.toggle('hidden');
  });
}
