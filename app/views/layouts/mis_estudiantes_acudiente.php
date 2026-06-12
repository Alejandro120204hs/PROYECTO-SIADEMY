<?php
/**
 * Listado "Mis estudiantes" del acudiente: permite cambiar de estudiante activo.
 * Requiere en scope: $estudiantesAsociados, $estudianteSeleccionado.
 * Solo se renderiza si el acudiente tiene más de un estudiante asociado.
 */
if (count($estudiantesAsociados ?? []) > 1):
    $rutaActualSelector = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $basePathSelector = parse_url(BASE_URL, PHP_URL_PATH) ?: '';
    if ($basePathSelector !== '' && $basePathSelector !== '/' && strpos($rutaActualSelector, $basePathSelector) === 0) {
        $rutaActualSelector = substr($rutaActualSelector, strlen($basePathSelector));
    }
    if ($rutaActualSelector === '' || $rutaActualSelector[0] !== '/') {
        $rutaActualSelector = '/' . $rutaActualSelector;
    }
?>
<section class="card">
  <h3>Mis estudiantes</h3>
  <div class="students-grid">
    <?php foreach ($estudiantesAsociados as $est): ?>
      <form class="student-card-form" method="post" action="<?= BASE_URL ?>/acudiente/seleccionar-estudiante">
        <input type="hidden" name="id_estudiante" value="<?= (int)$est['id'] ?>">
        <input type="hidden" name="redirect" value="<?= htmlspecialchars($rutaActualSelector) ?>">
        <button type="submit" class="student-card <?= ((int)$est['id'] === (int)$estudianteSeleccionado['id']) ? 'active' : '' ?>">
          <img class="student-card-avatar" src="<?= BASE_URL ?>/public/uploads/estudiantes/<?= htmlspecialchars($est['foto'] ?: 'default.png') ?>" alt="" onerror="this.onerror=null; this.src='<?= BASE_URL ?>/public/uploads/estudiantes/default.png'">
          <div class="student-card-info">
            <strong><?= htmlspecialchars(trim($est['nombres'] . ' ' . $est['apellidos'])) ?></strong>
            <small><?= $est['id_curso'] ? htmlspecialchars($est['grado'] . '° - ' . $est['nombre_curso']) : 'Sin matrícula activa' ?></small>
          </div>
          <?php if ((int)$est['id'] === (int)$estudianteSeleccionado['id']): ?>
            <span class="student-card-badge"><i class="ri-checkbox-circle-fill"></i> Activo</span>
          <?php endif; ?>
        </button>
      </form>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>
