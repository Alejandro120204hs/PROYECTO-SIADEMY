<?php 
  require_once BASE_PATH . '/app/helpers/session_administrador.php';
  require_once BASE_PATH . '/app/controllers/perfil.php';
  
  // LLAMAMOS EL ID QUE VIENE ATRAVEZ DEL METODO GET
  $id = $_SESSION['user']['id'];
  // LLAMAMOS LA FUNCION ESPECIFICA DEL CONTROLADOR
  $usuario = mostrarPerfil($id);
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • Formulario • Eventos</title>
    <?php 
        include_once __DIR__ . '/../../layouts/header_coordinador.php'
    ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/styles-tabla-formulario.css">

</head>
<body>
    <div class="app" id="appGrid">
        <!-- LEFT SIDEBAR -->
        <?php 
            include_once __DIR__ . '/../../layouts/sidebar_coordinador.php'
        ?>

        <!-- MAIN -->
        <main class="main">
            <div class="topbar">
                <div class="topbar-left">
                    <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
                        <i class="ri-menu-2-line"></i>
                    </button>
                    <div class="title">Agregar Evento</div>
                </div>
                                <div class="search"></div>
                <div class="topbar-actions">
                  <a href="<?= BASE_URL ?>/administrador-eventos" class="btn-action">
                    <i class="ri-arrow-left-line"></i>
                    <span>Volver</span>
                  </a>
                </div>
                                <div class="user">
                                <?php
                                    include_once BASE_PATH . '/app/views/layouts/boton_perfil_solo.php'
                                ?>
                                </div>
            </div>
            <div class="subtitulo"><p>Formulario de registro. Completa los siguientes campos para registrar un nuevo evento en el sistema académico. <br> Al finalizar, revisa la información antes de confirmar el registro para evitar errores en la base de datos institucional.</p></div>

            <!-- Formulario -->
            <div class="container-fluid py-3">

                <form id="formEvento" action="<?= BASE_URL ?>/administrador/guardar-evento" method="POST">

                    <!-- Datos del Evento -->
                    <div class="tabla-titulo mb-3">
                        <h5>Datos del Evento</h5>
                    </div>

                    <div class="row g-3">
                        <!-- Tipo de Evento -->
                        <div class="col-md-6">
                            <label for="tipo_evento">Tipo de Evento*</label>
                            <select id="tipo_evento" class="form-select" required name="tipo_evento">
                                <option value="">Selecciona el tipo de evento</option>
                                <option value="Reuniones">Reunión</option>
                                <option value="Examen">Examen</option>
                                <option value="Actividad">Actividad Académica</option>
                                <option value="Taller">Taller</option>
                                <option value="Conferencia">Conferencia</option>
                            </select>
                        </div>

                        <!-- Nombre del Evento -->
                        <div class="col-md-6">
                            <label for="nombre_evento">Nombre del Evento*</label>
                            <input type="text" id="nombre_evento" class="form-control" placeholder="Ej: Reunión de Padres - Grado 7°" required name="nombre_evento">
                        </div>

                        <!-- Descripción -->
                        <div class="col-md-12">
                            <label for="descripcion">Descripción*</label>
                            <textarea id="descripcion" class="form-control" rows="4" placeholder="Descripción detallada del evento..." required name="descripcion"></textarea>
                        </div>

                        <!-- Fecha -->
                        <div class="col-md-4">
                            <label for="fecha_evento">Fecha del Evento*</label>
                            <input type="date" id="fecha_evento" class="form-control" required name="fecha_evento">
                        </div>

                        <!-- Hora de Inicio -->
                        <div class="col-md-4">
                            <label for="hora_inicio">Hora de Inicio*</label>
                            <input type="time" id="hora_inicio" class="form-control" required name="hora_inicio">
                        </div>

                        <!-- Hora de Fin -->
                        <div class="col-md-4">
                            <label for="hora_fin">Hora de Fin*</label>
                            <input type="time" id="hora_fin" class="form-control" required name="hora_fin">
                        </div>

                        <!-- Ubicación -->
                        <div class="col-md-12">
                            <label for="ubicacion">Ubicación*</label>
                            <input type="text" id="ubicacion" class="form-control" placeholder="Ej: Auditorio Principal" required name="ubicacion">
                        </div>
                    </div>

                    <!-- Participantes y Detalles -->
                    <div class="tabla-titulo mb-3 mt-4">
                        <h5>Participantes y Detalles Adicionales</h5>
                    </div>

                    <div class="row g-3">
                        <!-- Grado -->
                        <div class="col-md-6">
                            <label for="grado">Curso/Grado</label>
                            <input type="text" id="grado" class="form-control" placeholder="Ej: 7°A, Todos los grados, etc." name="grado">
                        </div>

                        <!-- Número de Participantes -->
                        <div class="col-md-6">
                            <label for="participantes_esperados">N° Participantes Esperados</label>
                            <input type="number" id="participantes_esperados" class="form-control" placeholder="Ej: 50" min="0" name="participantes_esperados">
                        </div>

                        <!-- Responsable del Evento -->
                        <div class="col-md-6">
                            <label for="responsable">Responsable del Evento*</label>
                            <input type="text" id="responsable" class="form-control" placeholder="Nombre del responsable" required name="responsable">
                        </div>

                        <!-- Email de Contacto -->
                        <div class="col-md-6">
                            <label for="correo_contacto">Email de Contacto*</label>
                            <input type="email" id="correo_contacto" class="form-control" placeholder="correo@ejemplo.com" required name="correo_contacto">
                        </div>

                        <!-- Requiere Confirmación -->
                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="requiere_confirmacion" name="requiere_confirmacion" value="1">
                                <label class="form-check-label" for="requiere_confirmacion">
                                    ¿Requiere confirmación de asistencia?
                                </label>
                            </div>
                        </div>

                        <!-- Materiales Necesarios -->
                        <div class="col-md-12">
                            <label for="materiales">Materiales o Recursos Necesarios</label>
                            <textarea id="materiales" class="form-control" rows="3" placeholder="Lista de materiales, equipos o recursos necesarios..." name="materiales"></textarea>
                        </div>

                        <!-- Notas Adicionales -->
                        <div class="col-md-12">
                            <label for="notas_adicionales">Notas Adicionales</label>
                            <textarea id="notas_adicionales" class="form-control" rows="3" placeholder="Información adicional relevante..." name="notas_adicionales"></textarea>
                        </div>

                        <!-- Enviar Notificación -->
                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="enviar_notificacion" name="enviar_notificacion" value="1" checked>
                                <label class="form-check-label" for="enviar_notificacion">
                                    Enviar notificación automática a los participantes
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="botones mt-4">
                        <a href="<?= BASE_URL ?>/administrador-eventos" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-success">
                            <i class="ri-add-circle-line"></i> Crear Evento
                        </button>
                    </div>

                </form>
            </div>
        </main>
    </div>

    <!-- Bootstrap Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-docente.js"></script>
    <script src="<?= BASE_URL ?>/public/assets/dashboard/js/main-admin.js"></script>
</body>

</html>