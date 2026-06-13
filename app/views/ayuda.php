<?php
  require_once BASE_PATH . '/app/helpers/session_helper.php';
  require_once BASE_PATH . '/app/controllers/perfil.php';
  redirectIfNoSession();

  $usuario = mostrarPerfil((int)($_SESSION['user']['id'] ?? 0));
  if (!$usuario) $usuario = $_SESSION['user'] ?? [];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIADEMY • Ayuda y Soporte</title>
    <?php 
      include_once BASE_PATH . '/app/views/layouts/header_coordinador.php'
    ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/perfil.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/dashboard/css/ayuda.css">
</head>

<body>
    <div class="app" id="appGrid">
        <!-- LEFT SIDEBAR -->
        <?php
        if (isset($usuario['rol'])) {
            switch ($usuario['rol']) {
                case 'Administrador':
                    include_once BASE_PATH . '/app/views/layouts/sidebar_coordinador.php';
                    break;
                case 'Docente':
                    include_once BASE_PATH . '/app/views/layouts/sidebar_docente.php';
                    break;
                case 'Estudiante':
                    include_once BASE_PATH . '/app/views/layouts/sidebar_estudiante.php';
                    break;
                case 'Acudiente':
                    include_once BASE_PATH . '/app/views/layouts/sidebar_acudiente.php';
                    break;
                default:
                    include_once BASE_PATH . '/app/views/layouts/sidebar_coordinador.php';
            }
        }
        ?>

        <!-- MAIN CONTENT -->
        <main class="main">
            <div class="topbar">
                <div class="topbar-left">
                    <button class="toggle-btn" id="toggleLeft" title="Mostrar/Ocultar menú lateral">
                        <i class="ri-menu-2-line"></i>
                    </button>
                    <div class="title">Ayuda y Soporte</div>
                </div>
                <?php include_once BASE_PATH . '/app/views/layouts/boton_perfil_solo.php'; ?>
            </div>

            <!-- CONTENT -->
            <div style="padding: 24px 28px;">
                <div class="ayuda-header">
                    <h2>¿Cómo podemos ayudarte?</h2>
                    <p>Selecciona una categoría para encontrar respuestas a tus preguntas</p>
                </div>

                <!-- TAB BUTTONS -->
                <div class="help-tabs">
                    <button class="help-tab active" data-tab="faq">
                        <i class="ri-question-line"></i> Preguntas Frecuentes
                    </button>
                    <button class="help-tab" data-tab="guias">
                        <i class="ri-book-open-line"></i> Guías
                    </button>
                    <button class="help-tab" data-tab="contacto">
                        <i class="ri-mail-send-line"></i> Contacto
                    </button>
                </div>

                <!-- FAQ CONTENT -->
                <div class="help-content active" id="faq">
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>¿Cómo entregar una actividad?</span>
                            <i class="ri-add-line"></i>
                        </div>
                        <div class="faq-answer">
                            Para entregar una actividad, ve a "Mis Materias", selecciona la materia, busca la actividad pendiente y haz clic en "Entregar". Adjunta los archivos necesarios y confirma tu envío.
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <span>¿Cómo ver mis calificaciones?</span>
                            <i class="ri-add-line"></i>
                        </div>
                        <div class="faq-answer">
                            Accede a la sección "Calificaciones" en el menú principal. Allí podrás ver todas tus notas por materia, actividad y período académico.
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <span>¿Cómo cambiar mi contraseña?</span>
                            <i class="ri-add-line"></i>
                        </div>
                        <div class="faq-answer">
                            Haz clic en tu perfil (arriba a la derecha) y selecciona "Configuración". En la pestaña "Cambiar Contraseña", ingresa tu contraseña actual y la nueva contraseña dos veces.
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <span>¿Cómo contactar a mis profesores?</span>
                            <i class="ri-add-line"></i>
                        </div>
                        <div class="faq-answer">
                            Ve a la sección "Mis Profesores" donde podrás ver el listado de tus docentes con sus datos de contacto y horarios de atención disponibles.
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <span>¿Qué debo hacer si olvidé mi contraseña?</span>
                            <i class="ri-add-line"></i>
                        </div>
                        <div class="faq-answer">
                            En la página de inicio de sesión, haz clic en "¿Olvidaste tu contraseña?" e ingresa tu correo electrónico. Recibirás instrucciones para recuperar tu acceso.
                        </div>
                    </div>
                </div>

                <!-- GUIDES CONTENT -->
                <div class="help-content" id="guias">
                    <div class="guides-grid">
                        <div class="guide-item">
                            <div class="guide-icon"><i class="ri-rocket-line"></i></div>
                            <h4>Guía de Inicio Rápido</h4>
                            <p>Aprende los conceptos básicos de SIADEMY y cómo navegar por la plataforma en tu primer acceso.</p>
                            <a class="guide-download" href="<?= BASE_URL ?>/public/docs/guias/guia-inicio-rapido.pdf" download>
                                <i class="ri-download-2-line"></i> Descargar guía
                            </a>
                        </div>
                        <div class="guide-item">
                            <div class="guide-icon"><i class="ri-file-list-3-line"></i></div>
                            <h4>Gestión de Actividades</h4>
                            <p>Guía completa sobre cómo enviar actividades, descargar materiales y hacer seguimiento de tus entregas.</p>
                            <a class="guide-download" href="<?= BASE_URL ?>/public/docs/guias/guia-gestion-actividades.pdf" download>
                                <i class="ri-download-2-line"></i> Descargar guía
                            </a>
                        </div>
                        <div class="guide-item">
                            <div class="guide-icon"><i class="ri-notification-3-line"></i></div>
                            <h4>Comunicación en SIADEMY</h4>
                            <p>Entiende cómo funcionan los mensajes, notificaciones y anuncios en nuestra plataforma.</p>
                            <a class="guide-download" href="<?= BASE_URL ?>/public/docs/guias/guia-comunicacion.pdf" download>
                                <i class="ri-download-2-line"></i> Descargar guía
                            </a>
                        </div>
                        <div class="guide-item">
                            <div class="guide-icon"><i class="ri-calendar-event-line"></i></div>
                            <h4>Horarios y Calendarios</h4>
                            <p>Cómo interpretar tu horario académico, fechas importantes y eventos del calendario escolar.</p>
                            <a class="guide-download" href="<?= BASE_URL ?>/public/docs/guias/guia-horarios-calendarios.pdf" download>
                                <i class="ri-download-2-line"></i> Descargar guía
                            </a>
                        </div>
                    </div>
                </div>

                <!-- CONTACT CONTENT -->
                <div class="help-content" id="contacto">
                    <div class="contact-card">
                        <h3>Contactar con Soporte</h3>
                        <p>Si no encuentras la respuesta que buscas, completa el formulario y nos pondremos en contacto pronto.</p>

                        <form class="contact-form" id="contactForm">
                            <div class="form-group">
                                <label>Nombre completo</label>
                                <input type="text" name="nombre" placeholder="Tu nombre completo" required>
                            </div>
                            <div class="form-group">
                                <label>Correo electrónico</label>
                                <input type="email" name="correo" placeholder="Tu correo electrónico" required>
                            </div>
                            <div class="form-group">
                                <label>Asunto</label>
                                <input type="text" name="asunto" placeholder="Asunto de tu consulta" required>
                            </div>
                            <div class="form-group">
                                <label>Mensaje</label>
                                <textarea name="mensaje" placeholder="Describe tu problema o pregunta en detalle..." required></textarea>
                            </div>
                            <button type="submit">
                                <i class="ri-send-plane-2-line"></i> Enviar Mensaje
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </main>

        <!-- RIGHT SIDEBAR -->
        <?php
        include_once BASE_PATH . '/app/views/layouts/sidebar_right_coordinador.php';
        ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>window.BASE_URL = '<?= BASE_URL ?>';</script>
    <script src="<?= BASE_URL ?>/public/assets/dashboard/js/ayuda.js"></script>
</body>

</html>