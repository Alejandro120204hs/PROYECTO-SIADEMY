(function () {
  var btns     = document.querySelectorAll('.btn-view-toggle');
  var viewCards = document.getElementById('viewCards');
  var viewTable = document.getElementById('viewTable');

  if (!btns.length || !viewCards || !viewTable) return;

  btns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      btns.forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');

      if (btn.dataset.view === 'table') {
        viewCards.style.display = 'none';
        viewTable.style.display = '';
      } else {
        viewTable.style.display = 'none';
        viewCards.style.display = '';
      }
    });
  });
})();
