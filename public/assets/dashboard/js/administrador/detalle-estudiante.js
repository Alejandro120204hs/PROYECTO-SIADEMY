const tabButtons = document.querySelectorAll('.tab-btn');
const tabPanes = document.querySelectorAll('.tab-pane');

tabButtons.forEach(function (button) {
  button.addEventListener('click', function () {
    const targetTab = button.dataset.tab;

    tabButtons.forEach(function (btn) { btn.classList.remove('active'); });
    tabPanes.forEach(function (pane) { pane.classList.remove('active'); });

    button.classList.add('active');
    document.getElementById(targetTab).classList.add('active');
  });
});

document.getElementById('toggleLeft').addEventListener('click', function () {
  document.querySelector('.sidebar').classList.toggle('collapsed');
});

document.getElementById('toggleRight').addEventListener('click', function () {
  document.querySelector('.rightbar').classList.toggle('hidden');
});
