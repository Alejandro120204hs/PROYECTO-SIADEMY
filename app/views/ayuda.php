<?php 
  require_once BASE_PATH . '/app/helpers/session_helper.php';
  redirectIfNoSession();
  
  // Obtener datos del usuario
  $usuario = $_SESSION['user'] ?? [];
  $rol = $usuario['rol'] ?? 'Administrador';
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
    <style>
        .title {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .faq-item {
            background: white;
            border-radius: 4px;
            margin-bottom: 8px;
            border-left: 4px solid #007bff;
        }

        .faq-question {
            padding: 16px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            color: #333;
            user-select: none;
        }

        .faq-question:hover {
            background: #f8f9fa;
        }

        .faq-question i {
            transition: transform 0.3s ease;
            color: #007bff;
        }

        .faq-item.open .faq-question i {
            transform: rotate(45deg);
        }

        .faq-answer {
            padding: 0 16px 16px 16px;
            color: #666;
            font-size: 14px;
            display: none;
            line-height: 1.6;
        }

        .faq-item.open .faq-answer {
            display: block;
        }

        .help-tabs {
            display: flex;
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 24px;
            gap: 8px;
        }

        .help-tab {
            background: none;
            border: none;
            padding: 12px 16px;
            cursor: pointer;
            color: #666;
            font-weight: 500;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .help-tab:hover {
            color: #007bff;
        }

        .help-tab.active {
            color: #007bff;
            border-bottom-color: #007bff;
        }

        .help-content {
            display: none;
        }

        .help-content.active {
            display: block;
        }

        .contact-form input,
        .contact-form textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            box-sizing: border-box;
        }

        .contact-form textarea {
            resize: vertical;
            min-height: 120px;
        }

        .contact-form button {
            background: #007bff;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s ease;
        }

        .contact-form button:hover {
            background: #0056b3;
        }

        .guide-item {
            background: white;
            padding: 16px;
            border-radius: 4px;
            margin-bottom: 12px;
            border-left: 4px solid #28a745;
        }

        .guide-item h4 {
            margin-top: 0;
            color: #333;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .guide-item p {
            margin: 8px 0 0 0;
            color: #666;
            font-size: 14px;
        }
    </style>
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
                <button class="toggle-btn" id="toggleRight" title="Mostrar/Ocultar panel derecho">
                    <i class="ri-layout-right-2-line"></i>
                </button>
            </div>

            <!-- CONTENT -->
            <div style="padding: 24px; max-width: 900px;">
                <h2 style="margin-top: 0; color: #333;">¿Cómo podemos ayudarte?</h2>
                <p style="color: #666; margin-bottom: 24px;">Selecciona una categoría para encontrar respuestas a tus preguntas</p>

                <!-- TAB BUTTONS -->
                <div class="help-tabs">
                    <button class="help-tab active" data-tab="faq" onclick="switchTab('faq')">
                        <i class="ri-question-line"></i> Preguntas Frecuentes
                    </button>
                    <button class="help-tab" data-tab="guias" onclick="switchTab('guias')">
                        <i class="ri-book-line"></i> Guías
                    </button>
                    <button class="help-tab" data-tab="contacto" onclick="switchTab('contacto')">
                        <i class="ri-mail-line"></i> Contacto
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
                    <div class="guide-item">
                        <h4><i class="ri-book-line"></i> Guía de Inicio Rápido</h4>
                        <p>Aprende los conceptos básicos de SIADEMY y cómo navegar por la plataforma en tu primer acceso.</p>
                    </div>

                    <div class="guide-item">
                        <h4><i class="ri-file-list-line"></i> Gestión de Actividades</h4>
                        <p>Guía completa sobre cómo enviar actividades, descargar materiales y seguimiento de tus entregas.</p>
                    </div>

                    <div class="guide-item">
                        <h4><i class="ri-chat-2-line"></i> Comunicación en SIADEMY</h4>
                        <p>Entiende cómo funcionan los mensajes, notificaciones y anuncios en nuestra plataforma.</p>
                    </div>

                    <div class="guide-item">
                        <h4><i class="ri-calendar-line"></i> Horarios y Calendarios</h4>
                        <p>Cómo interpretar tu horario académico, fechas importantes y eventos del calendario escolar.</p>
                    </div>
                </div>

                <!-- CONTACT CONTENT -->
                <div class="help-content" id="contacto">
                    <div style="background: white; padding: 24px; border-radius: 4px;">
                        <h3 style="margin-top: 0; color: #333;">Contactar con Soporte</h3>
                        <p style="color: #666; font-size: 14px;">Si no encuentras la respuesta que buscas, completa el formulario y nos pondremos en contacto pronto.</p>

                        <form class="contact-form" onsubmit="handleContactSubmit(event)">
                            <input type="text" placeholder="Tu nombre completo" required>
                            <input type="email" placeholder="Tu correo electrónico" required>
                            <input type="text" placeholder="Asunto de tu consulta" required>
                            <textarea placeholder="Describe tu problema o pregunta en detalle..."></textarea>
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
    <script>
        // Cambiar entre tabs
        function switchTab(tabName) {
            document.querySelectorAll('.help-content').forEach(content => {
                content.classList.remove('active');
            });

            document.querySelectorAll('.help-tab').forEach(tab => {
                tab.classList.remove('active');
            });

            document.getElementById(tabName).classList.add('active');
            document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
        }

        // Expandir/contraer preguntas FAQ
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', function() {
                const faqItem = this.parentElement;
                faqItem.classList.toggle('open');
            });
        });

        // Manejar envío del formulario de contacto
        function handleContactSubmit(event) {
            event.preventDefault();
            const form = event.target;
            
            // Aquí puedes agregar la lógica para enviar el formulario al servidor
            // Por ahora solo mostramos un mensaje de éxito
            Swal.fire({
                icon: 'success',
                title: '¡Mensaje Enviado!',
                text: 'Gracias por tu mensaje. Nos pondremos en contacto pronto.',
                confirmButtonColor: '#007bff'
            });
            
            form.reset();
        }
    </script>
</body>

</html>