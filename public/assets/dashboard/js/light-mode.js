/**
 * Siademy — Sistema de temas claro / oscuro
 *
 * Flujo:
 *  1. header_coordinador.php inyecta window.SIADEMY_THEME (desde sesión/BD)
 *     y un script inline aplica la clase al instante (anti-FOUC).
 *  2. Este archivo (cargado con defer) sincroniza el botón del dropdown
 *     y delega el guardado en BD a /api/tema.
 */
(function () {
  'use strict';

  /* ── Helpers ─────────────────────────────────────────────── */
  function currentTheme() {
    return document.documentElement.classList.contains('light-mode') ? 'light' : 'dark';
  }

  function applyTheme(theme) {
    var isLight = theme === 'light';
    document.documentElement.classList.toggle('light-mode', isLight);
    if (document.body) document.body.classList.toggle('light-mode', isLight);
    try { localStorage.setItem('siademy-theme', theme); } catch (e) {}
    syncToggleBtn(theme);
  }

  function syncToggleBtn(theme) {
    var icon   = document.getElementById('themeIcon');
    var label  = document.getElementById('themeLabel');
    var sw     = document.getElementById('themeSwitch');
    if (!icon || !label) return;

    if (theme === 'light') {
      icon.className  = 'ri-sun-line';
      label.textContent = 'Modo Claro';
      if (sw) sw.classList.add('active');
    } else {
      icon.className  = 'ri-moon-line';
      label.textContent = 'Modo Oscuro';
      if (sw) sw.classList.remove('active');
    }
  }

  /* ── Persistencia en BD ──────────────────────────────────── */
  function saveThemeToDB(theme) {
    var base = window.SIADEMY_BASE_URL || '';
    fetch(base + '/api/tema', {
      method:  'POST',
      headers: {
        'Content-Type':     'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({ tema: theme })
    }).catch(function () { /* falla silenciosa; ya guardado en localStorage */ });
  }

  /* ── Toggle ──────────────────────────────────────────────── */
  function toggleTheme() {
    var next = currentTheme() === 'light' ? 'dark' : 'light';
    applyTheme(next);
    saveThemeToDB(next);
  }

  /* ── Inicialización ──────────────────────────────────────── */
  function init() {
    /* El tema ya fue aplicado por el script inline en <head>.
       Solo necesitamos sincronizar el estado del botón. */
    var theme = window.SIADEMY_THEME || currentTheme();

    /* Si no había sesión, respetar localStorage o preferencia del sistema */
    if (!window.SIADEMY_THEME) {
      var ls = '';
      try { ls = localStorage.getItem('siademy-theme') || ''; } catch (e) {}
      if (!ls && window.matchMedia) {
        ls = window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
      }
      theme = ls || 'dark';
      applyTheme(theme);
    } else {
      syncToggleBtn(theme);
    }

    /* Bindear botón del dropdown */
    var btn = document.getElementById('toggleThemeBtn');
    if (btn) {
      btn.addEventListener('click', toggleTheme);
      btn.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggleTheme(); }
      });
    }

    /* Escuchar cambios de preferencia del sistema (solo si no hay sesión) */
    if (!window.SIADEMY_THEME && window.matchMedia) {
      window.matchMedia('(prefers-color-scheme: light)').addEventListener('change', function (e) {
        var ls = '';
        try { ls = localStorage.getItem('siademy-theme') || ''; } catch (err) {}
        if (!ls) applyTheme(e.matches ? 'light' : 'dark');
      });
    }
  }

  /* ── Atajo de teclado (Ctrl+Shift+L) ────────────────────── */
  document.addEventListener('keydown', function (e) {
    if (e.ctrlKey && e.shiftKey && e.key === 'L') toggleTheme();
  });

  /* ── Arrancar ────────────────────────────────────────────── */
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  /* API pública por si algún módulo la necesita */
  window.siademyTheme = { toggle: toggleTheme, apply: applyTheme, current: currentTheme };
})();
