document.querySelectorAll('.filter-tab').forEach(function(btn) {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.filter-tab').forEach(function(b) { b.classList.remove('active'); });
    this.classList.add('active');
    var filter = this.getAttribute('data-filter');
    document.querySelectorAll('.act-item').forEach(function(item) {
      item.classList.toggle('hidden', filter !== 'todas' && item.getAttribute('data-estado') !== filter);
    });
  });
});
