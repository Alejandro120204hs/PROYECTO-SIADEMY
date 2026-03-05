/**
 * Sistema de Modo Claro/Oscuro
 * Este archivo gestiona la activación y desactivación del modo claro
 */

// Función para activar o desactivar el modo claro
function toggleLightMode() {
  const body = document.body;
  
  // Si ya tiene light-mode, lo quita. Si no, lo añade
  if (body.classList.contains('light-mode')) {
    body.classList.remove('light-mode');
    localStorage.setItem('theme', 'dark');
  } else {
    body.classList.add('light-mode');
    localStorage.setItem('theme', 'light');
  }
}

// Función para inicializar el tema guardado
function initializeTheme() {
  const savedTheme = localStorage.getItem('theme');
  
  if (savedTheme === 'light') {
    document.body.classList.add('light-mode');
  } else if (savedTheme === 'dark') {
    document.body.classList.remove('light-mode');
  } else {
    // Si no hay preferencia guardada, usar preferencia del sistema
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches) {
      document.body.classList.add('light-mode');
    }
  }
}

// Función para obtener el estado actual
function isLightModeEnabled() {
  return document.body.classList.contains('light-mode');
}

// Inicializar el tema cuando se carga la página
document.addEventListener('DOMContentLoaded', initializeTheme);

// Para desarrollo: permitir cambiar con tecla de atajo
document.addEventListener('keydown', function(e) {
  // Ctrl+Shift+L para cambiar tema
  if (e.ctrlKey && e.shiftKey && e.key === 'L') {
    toggleLightMode();
    console.log('Modo claro:', isLightModeEnabled());
  }
});
