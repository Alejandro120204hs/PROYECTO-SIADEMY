<?php
require_once __DIR__ . '/../helpers/mailer_helper.php';

$correo = $_POST['correo'] ?? '';

if(!empty($correo)){
    try {
        $mail = mailer_init();
        $mail->setFrom('soportesiademy@gmail.com', 'SIADEMY');
        $mail->addAddress($correo);
        $mail->Subject = 'Información sobre SIADEMY';
        $mail->Body = '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>SIADEMY - Información</title>
            <style>
                *{
                    margin:0;
                    padding:0;
                    box-sizing:border-box;
                }

                body{
                    font-family: "Segoe UI", Roboto, Arial, sans-serif;
                    background: #0A0E27;
                    padding: 40px 20px;
                    color:#fff;
                }

                .card{
                    max-width:600px;
                    margin:0 auto;
                    border-radius:22px;
                    overflow:hidden;
                    background:#11193a;
                    border:1px solid rgba(255,255,255,0.07);
                    box-shadow:0px 25px 60px rgba(0,0,0,0.55);
                }

                .header{
                    padding:45px 40px;
                    background:linear-gradient(150deg, #141A33 0%, #0D1226 100%);
                    text-align:center;
                    position:relative;
                }

                .header::after{
                    content:"";
                    position:absolute;
                    inset:0;
                    background:radial-gradient(circle at 50% -20%, rgba(118,124,255,0.15), transparent 70%);
                    z-index:1;
                }

                .header img{
                    position:relative;
                    z-index:2;
                    width:220px;
                    margin-bottom:15px;
                }

                h2{
                    color: white;
                }

                .header p{
                    position:relative;
                    z-index:2;
                    color:#9daafc;
                    font-size:13px;
                    letter-spacing:2px;
                    text-transform:uppercase;
                }

                .content{
                    padding:45px 40px;
                    background:linear-gradient(180deg, #18204A, #11193A 65%);
                }

                .title{
                    font-size:26px;
                    text-align:center;
                    font-weight:700;
                    margin-bottom:28px;
                }

                .info-box{
                    background:rgba(255,255,255,0.03);
                    border-left:4px solid #6366F1;
                    padding:22px 26px;
                    border-radius:12px;
                    margin-bottom:30px;
                    box-shadow:0 8px 25px rgba(0,0,0,0.2);
                }

                .info-box p{
                    color:#d9defc;
                    font-size:15px;
                    line-height:1.7;
                    margin-bottom:15px;
                }

                .info-box ul{
                    color:#d9defc;
                    font-size:15px;
                    line-height:1.7;
                    padding-left:20px;
                }

                .info-box li{
                    margin-bottom:8px;
                }

                .support{
                    background:linear-gradient(135deg, #4F46E5, #6366F1);
                    padding:26px;
                    text-align:center;
                    border-radius:16px;
                    color:#fff;
                    box-shadow:0 15px 40px rgba(79,70,229,0.4);
                }

                .support p{
                    margin:0;
                    line-height:1.6;
                }

                .support a{
                    color:#fff;
                    text-decoration:none;
                    font-weight:600;
                    border-bottom:1px solid rgba(255,255,255,0.5);
                }

                .support a:hover{
                    border-bottom-color:#fff;
                }

                .footer{
                    padding:32px;
                    text-align:center;
                    font-size:13px;
                    color:#9AA0B8;
                    background:#0B0E18;
                }

                .footer small{
                    display:block;
                    margin-top:14px;
                    color:#6d748a;
                }

                @media (max-width:600px){
                    .content, .header{
                        padding:30px 22px;
                    }
                    .title{
                        font-size:22px;
                    }
                }
            </style>
        </head>
        <body>
            <div class="card">
                <div class="header">
                    <img src="https://raw.githubusercontent.com/Alejandro120204hs/Imagenes-Siademy/refs/heads/main/logosiademy.png" alt="Logo SIADEMY">
                    <p>Sistema de seguimiento académico</p>
                </div>

                <div class="content">
                    <h2 class="title">Bienvenido a SIADEMY</h2>

                    <div class="info-box">
                        <p>¡Hola! Gracias por interesarte en SIADEMY.</p>
                        <p>A continuación encontrarás información sobre nuestros servicios:</p>
                        <ul>
                            <li>Gestión de estudiantes y matrículas</li>
                            <li>Módulos para docentes y acudientes</li>
                            <li>Reportes académicos y seguimiento en tiempo real</li>
                            <li>Planes escalables según tus necesidades</li>
                        </ul>
                    </div>

                    <div class="support">
                        <p><strong>¿Necesitas más información?</strong></p>
                        <p>Estamos disponibles para asistirte.<br>
                        Escríbenos a <a href="mailto:soportesiademy@gmail.com">soportesiademy@gmail.com</a></p>
                    </div>
                </div>

                <div class="footer">
                    <p>Este es un mensaje automático, por favor no responder.</p>
                    <small>© 2025 SIADEMY — Todos los derechos reservados.</small>
                </div>
            </div>
        </body>
        </html>
        ';
        $mail->send();
    } catch (Exception $e) {
        error_log('Error al enviar correo: ' . $mail->ErrorInfo);
    }
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();