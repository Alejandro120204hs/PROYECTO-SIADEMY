<?php

require_once BASE_PATH . '/app/helpers/mailer_helper.php';

header('Content-Type: application/json');

$nombre  = trim($_POST['nombre']  ?? '');
$correo  = trim($_POST['correo']  ?? '');
$asunto  = trim($_POST['asunto']  ?? '');
$mensaje = trim($_POST['mensaje'] ?? '');

if ($nombre === '' || $correo === '' || $asunto === '' || $mensaje === '') {
    echo json_encode(['ok' => false, 'msg' => 'Todos los campos son obligatorios.']);
    exit();
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['ok' => false, 'msg' => 'Correo electrónico inválido.']);
    exit();
}

try {
    $mail = mailer_init();
    $mail->setFrom('soportesiademy@gmail.com', 'SIADEMY Soporte');
    $mail->addAddress('soportesiademy@gmail.com');
    $mail->addReplyTo($correo, $nombre);
    $mail->Subject = 'Consulta de soporte: ' . $asunto;
    $mail->Body = '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>SIADEMY - Mensaje de Soporte</title>
            <style>
                * { margin:0; padding:0; box-sizing:border-box; }
                body { font-family:"Segoe UI",Roboto,Arial,sans-serif; background:#0A0E27; padding:40px 20px; color:#fff; }
                .card { max-width:600px; margin:0 auto; border-radius:22px; overflow:hidden; background:#11193a; border:1px solid rgba(255,255,255,0.07); box-shadow:0px 25px 60px rgba(0,0,0,0.55); }
                .header { padding:45px 40px; background:linear-gradient(150deg,#141A33 0%,#0D1226 100%); text-align:center; position:relative; }
                .header::after { content:""; position:absolute; inset:0; background:radial-gradient(circle at 50% -20%,rgba(118,124,255,0.15),transparent 70%); z-index:1; }
                .header img { position:relative; z-index:2; width:220px; margin-bottom:15px; }
                .header p { position:relative; z-index:2; color:#9daafc; font-size:13px; letter-spacing:2px; text-transform:uppercase; }
                .content { padding:45px 40px; background:linear-gradient(180deg,#18204A,#11193A 65%); }
                .title { font-size:24px; text-align:center; font-weight:700; margin-bottom:28px; color:#fff; }
                .info-box { background:rgba(255,255,255,0.03); border-left:4px solid #6366F1; padding:22px 26px; border-radius:12px; margin-bottom:20px; box-shadow:0 8px 25px rgba(0,0,0,0.2); }
                .info-box p { color:#d9defc; font-size:15px; line-height:1.7; }
                .field-label { color:#9daafc; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:1px; margin-bottom:6px; }
                .field-value { color:#e2e5f0; font-size:15px; line-height:1.6; }
                .msg-box { background:rgba(99,102,241,0.08); border:1px solid rgba(99,102,241,0.25); border-radius:14px; padding:22px 26px; margin-bottom:28px; }
                .msg-box .field-value { white-space:pre-line; }
                .support { background:linear-gradient(135deg,#4F46E5,#6366F1); padding:26px; text-align:center; border-radius:16px; color:#fff; box-shadow:0 15px 40px rgba(79,70,229,0.4); }
                .support p { margin:0; line-height:1.6; }
                .support a { color:#fff; text-decoration:none; font-weight:600; border-bottom:1px solid rgba(255,255,255,0.5); }
                .footer { padding:32px; text-align:center; font-size:13px; color:#9AA0B8; background:#0B0E18; }
                .footer small { display:block; margin-top:14px; color:#6d748a; }
                .divider { border:none; border-top:1px solid rgba(255,255,255,0.07); margin:18px 0; }
                @media(max-width:600px){ .content,.header{ padding:30px 22px; } .title{ font-size:20px; } }
            </style>
        </head>
        <body>
            <div class="card">
                <div class="header">
                    <img src="https://raw.githubusercontent.com/Alejandro120204hs/Imagenes-Siademy/refs/heads/main/logosiademy.png" alt="Logo SIADEMY">
                    <p>Sistema de seguimiento académico</p>
                </div>
                <div class="content">
                    <h2 class="title">Nuevo mensaje de soporte</h2>

                    <div class="info-box">
                        <div class="field-label">Remitente</div>
                        <div class="field-value">' . htmlspecialchars($nombre) . '</div>
                        <hr class="divider">
                        <div class="field-label">Correo de contacto</div>
                        <div class="field-value">' . htmlspecialchars($correo) . '</div>
                        <hr class="divider">
                        <div class="field-label">Asunto</div>
                        <div class="field-value">' . htmlspecialchars($asunto) . '</div>
                    </div>

                    <div class="msg-box">
                        <div class="field-label">Mensaje</div>
                        <div class="field-value">' . nl2br(htmlspecialchars($mensaje)) . '</div>
                    </div>

                    <div class="support">
                        <p><strong>Responde directamente a este correo</strong></p>
                        <p>El remitente recibirá tu respuesta en<br>
                        <a href="mailto:' . htmlspecialchars($correo) . '">' . htmlspecialchars($correo) . '</a></p>
                    </div>
                </div>
                <div class="footer">
                    <p>Mensaje enviado desde el formulario de Ayuda y Soporte de SIADEMY.</p>
                    <small>© 2025 SIADEMY — Todos los derechos reservados.</small>
                </div>
            </div>
        </body>
        </html>
    ';

    $mail->send();
    echo json_encode(['ok' => true]);

} catch (Exception $e) {
    error_log('[contacto-soporte] ' . $e->getMessage());
    echo json_encode(['ok' => false, 'msg' => 'Error al enviar el mensaje. Intenta de nuevo.']);
}
exit();
