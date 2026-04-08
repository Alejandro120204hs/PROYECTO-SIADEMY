<?php 
  require_once BASE_PATH . '/app/helpers/session_administrador.php';
  require_once BASE_PATH . '/app/controllers/perfil.php';
  require_once BASE_PATH . '/app/controllers/administrador/eventos.php';

  $id = $_SESSION['user']['id'];
  $usuario = mostrarPerfil($id);

  $id_evento = $_GET['id'] ?? null;
  if(!$id_evento){
    header('Location: ' . BASE_URL . '/administrador-eventos');
    exit();
  }

  $evento = mostrarEventoId($id_evento);
  if(!$evento || $evento['id_institucion'] != $_SESSION['user']['id_institucion']){
    header('Location: ' . BASE_URL . '/administrador-eventos');
    exit();
  }
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • Editar Evento</title>
    <?php include_once __DIR__ . '/../../layouts/header_coordinador.php' ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-tabla-formulario.css">
</head>
<body>
    <div class="app" id="appGrid">
        <?php include_once __DIR__ . '/../../layouts/sidebar_coordinador.php' ?>

        <main class="main">
            <div class="topbar">
                <div class="topbar-left">
                    <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
                        <i class="ri-menu-2-line"></i>
                    </button>
                    <div class="title">Editar Evento</div>
                </div>
                <div class="search"></div>
                <div class="topbar-actions">
                    <a href="<?= BASE_URL ?>/administrador-eventos" class="btn-action">
                        <i class="ri-arrow-left-line"></i>
                        <span>Volver</span>
                    </a>
                </div>
                <div class="user">
                <?php include_once BASE_PATH . '/app/views/layouts/boton_perfil_solo.php' ?>
                </div>
            </div>

            <div class="subtitulo"><p>Actualiza la información del evento y confirma los cambios para mantener la agenda institucional al día.</p></div>

            <div class="container-fluid py-3">
                <form id="formEvento" action="<?= BASE_URL ?>/administrador/actualizar-evento" method="POST">
                    <input type="hidden" name="accion" value="actualizar">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($evento['id']) ?>">

                    <div class="tabla-titulo mb-3">
                        <h5>Datos del Evento</h5>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="tipo_evento">Tipo de Evento*</label>
                            <select id="tipo_evento" class="form-select" required name="tipo_evento">
                                <?php $tipos = ['Reuniones' => 'Reunión', 'Examen' => 'Examen', 'Actividad' => 'Actividad Académica', 'Taller' => 'Taller', 'Conferencia' => 'Conferencia']; ?>
                                <option value="">Selecciona el tipo de evento</option>
                                <?php foreach($tipos as $valor => $label): ?>
                                    <option value="<?= $valor ?>" <?= $evento['tipo_evento'] === $valor ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="nombre_evento">Nombre del Evento*</label>
                            <input type="text" id="nombre_evento" class="form-control" required name="nombre_evento" value="<?= htmlspecialchars($evento['nombre_evento']) ?>">
                        </div>

                        <div class="col-md-12">
                            <label for="descripcion">Descripción*</label>
                            <textarea id="descripcion" class="form-control" rows="4" required name="descripcion"><?= htmlspecialchars($evento['descripcion']) ?></textarea>
                        </div>

                        <div class="col-md-4">
                            <label for="fecha_evento">Fecha del Evento*</label>
                            <input type="date" id="fecha_evento" class="form-control" required name="fecha_evento" value="<?= htmlspecialchars($evento['fecha_evento']) ?>">
                        </div>

                        <div class="col-md-4">
                            <label for="hora_inicio">Hora de Inicio*</label>
                            <input type="time" id="hora_inicio" class="form-control" required name="hora_inicio" value="<?= htmlspecialchars(substr($evento['hora_inicio'], 0, 5)) ?>">
                        </div>

                        <div class="col-md-4">
                            <label for="hora_fin">Hora de Fin*</label>
                            <input type="time" id="hora_fin" class="form-control" required name="hora_fin" value="<?= htmlspecialchars(substr($evento['hora_fin'], 0, 5)) ?>">
                        </div>

                        <div class="col-md-12">
                            <label for="ubicacion">Ubicación*</label>
                            <input type="text" id="ubicacion" class="form-control" required name="ubicacion" value="<?= htmlspecialchars($evento['ubicacion']) ?>">
                        </div>
                    </div>

                    <div class="tabla-titulo mb-3 mt-4">
                        <h5>Participantes y Detalles Adicionales</h5>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="grado">Curso/Grado</label>
                            <input type="text" id="grado" class="form-control" name="grado" value="<?= htmlspecialchars($evento['grado']) ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="participantes_esperados">N° Participantes Esperados</label>
                            <input type="number" id="participantes_esperados" class="form-control" min="0" name="participantes_esperados" value="<?= htmlspecialchars($evento['participantes_esperados']) ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="responsable">Responsable del Evento*</label>
                            <input type="text" id="responsable" class="form-control" required name="responsable" value="<?= htmlspecialchars($evento['responsable']) ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="correo_contacto">Email de Contacto*</label>
                            <input type="email" id="correo_contacto" class="form-control" required name="correo_contacto" value="<?= htmlspecialchars($evento['correo_contacto']) ?>">
                        </div>

                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="requiere_confirmacion" name="requiere_confirmacion" value="1" <?= !empty($evento['requiere_confirmacion']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="requiere_confirmacion">¿Requiere confirmación de asistencia?</label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label for="materiales">Materiales o Recursos Necesarios</label>
                            <textarea id="materiales" class="form-control" rows="3" name="materiales"><?= htmlspecialchars($evento['materiales']) ?></textarea>
                        </div>

                        <div class="col-md-12">
                            <label for="notas_adicionales">Notas Adicionales</label>
                            <textarea id="notas_adicionales" class="form-control" rows="3" name="notas_adicionales"><?= htmlspecialchars($evento['notas_adicionales']) ?></textarea>
                        </div>

                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="enviar_notificacion" name="enviar_notificacion" value="1" <?= !empty($evento['enviar_notificacion']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="enviar_notificacion">Enviar notificación automática a los participantes</label>
                            </div>
                        </div>
                    </div>

                    <div class="botones mt-4">
                        <a href="<?= BASE_URL ?>/administrador-eventos" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-success"><i class="ri-save-line"></i> Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-docente.js"></script>
    <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-admin.js"></script>
</body>
</html>
