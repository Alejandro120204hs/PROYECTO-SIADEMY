<?php

    // IMPORTAMOS LAS DEPENDENCIAS NECESARIAS
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../helpers/mailer_helper.php';


    class Recovery{
        // LLAMAMOS LA BASE DE DATOS
        private $conexion;

        public function __construct(){
            $db = new Conexion();
            $this -> conexion = $db -> getConexion();
        }

        public function recuperarClave($correo){

            try{
                // VERIFICAMOS QUE EL CORREO EXISTE
                $consultar = "SELECT * FROM usuario WHERE correo = :correo AND estado='Activo' LIMIT 1";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $this->conexion->prepare($consultar);
                $resultado->bindParam(':correo',$correo);
                $resultado->execute();
                $user = $resultado -> fetch();

                if($user){
                    // GENERAMOS LA NUEVA CLAVE APARTIR DE UNA BASE DE CARACTERES Y UN RANDOM
                    $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                    // MEZCLAMOS LA CADENA DE CRACTERES 
                    $random = str_shuffle($caracteres);
                    // SUBSTRAEMOS UNA CANTIDAD DEFINIDA DE ESTE RANDOM
                    $nueva_clave = substr($random, 0,8); 
                    // 0 SIGNIFICA DESDE DONDE VOY A EMPEZAR A CONTAR, EL 8 LA CANTIDAD DE CARACTERES

                    // CLAVE ENCRIPTADA
                    $clave_encriptada = password_hash($nueva_clave, PASSWORD_DEFAULT);
                    // ACTUALIZAMOS LA CLAVE
                    $actualizar = "UPDATE usuario SET clave=:clave_encriptada WHERE id=:id";
                    // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                    $resultado2 = $this->conexion->prepare($actualizar);
                    $resultado2->bindParam(':clave_encriptada',$clave_encriptada);
                    $resultado2->bindParam(':id',$user['id']);
                    $resultado2 -> execute();

                    // ENVIAMOS EL CORREO
                    //Create an instance; passing `true` enables exceptions
            

          
                       
                //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
                 

                $mail = mailer_init();

                //Recipients
                // EMISOR Y NOMBRE DE LA PERSONA O ROL
                $mail->setFrom('soportesiademy@gmail.com', 'Soporte Siademy');
                // RECEPTOR
                $mail->addAddress($user['correo']);     //Add a recipient
                // $mail->addAddress('ellen@example.com');               //Name is optional
                // $mail->addReplyTo('info@example.com', 'Information');
                // $mail->addCC('cc@example.com');
                // $mail->addBCC('bcc@example.com');

                //Attachments
                // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
                // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

                //Content
                
                                              //Set email format to HTML
                $mail->Subject = 'SIADEMY - NUEVA CLAVE GENERADA';
                $mail->Body    = '
                    <!DOCTYPE html>
                    <html lang="es">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>SIADEMY - Recuperación de Contraseña</title>

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

                            /* HEADER */
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

                            /* MAIN CONTENT */
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
                            }

                            .password-box{
                                background:rgba(99,102,241,0.12);
                                padding:22px;
                                border-radius:14px;
                                text-align:center;
                                border:1px solid rgba(99,102,241,0.3);
                                margin-bottom:35px;
                            }

                            .password-title{
                                font-size:15px;
                                margin-bottom:10px;
                                color:#e5e7ff;
                                font-weight:600;
                            }

                            .password{
                                font-size:24px;
                                font-family:"Courier New", monospace;
                                letter-spacing:3px;
                                font-weight:700;
                                color:#9dabff;
                                background:rgba(255,255,255,0.05);
                                padding:16px 20px;
                                border-radius:10px;
                                display:inline-block;
                            }

                            .warning{
                                background:linear-gradient(135deg, #2C2415, #1D180E);
                                padding:24px 28px;
                                border-radius:14px;
                                border-left:4px solid #FBBF24;
                                box-shadow:0 10px 25px rgba(0,0,0,0.25);
                                margin-bottom:35px;
                            }

                            .warning-title{
                                color:#FBBF24;
                                font-size:14px;
                                font-weight:700;
                                margin-bottom:10px;
                                text-transform:uppercase;
                            }

                            .warning p{
                                color:#fff;
                                font-size:14px;
                                line-height:1.6;
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

                            /* FOOTER */
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

                            /* RESPONSIVE */
                            @media (max-width:600px){
                                .content, .header{
                                    padding:30px 22px;
                                }
                                .title{
                                    font-size:22px;
                                }
                                .password{
                                    font-size:20px;
                                }
                            }
                        </style>
                    </head>

                    <body>
                        <div class="card">

                            <!-- HEADER -->
                            <div class="header">
                                <img src="https://raw.githubusercontent.com/Alejandro120204hs/Imagenes-Siademy/refs/heads/main/logosiademy.png" alt="Logo SIADEMY">
                                <p>Sistema de seguimiento académico</p>
                            </div>

                            <!-- CONTENT -->
                            <div class="content">

                                <h2 class="title">Recuperación de Contraseña</h2>

                                <div class="info-box">
                                    <p>Has solicitado restablecer tu contraseña. A continuación encontrarás una nueva clave temporal creada especialmente para ti.</p>
                                </div>

                                <div class="password-box">
                                    <p class="password-title">Tu nueva contraseña temporal es:</p>
                                    <div class="password">'.$nueva_clave.'</div>
                                </div>

                                <div class="warning">
                                    <div class="warning-title">⚠️ Importante</div>
                                    <p>
                                        Por tu seguridad, te recomendamos cambiar esta contraseña temporal después de iniciar sesión.
                                        Este mensaje ha sido generado automáticamente por el sistema.
                                    </p>
                                </div>

                                <div class="support">
                                    <p><strong>¿Necesitas ayuda?</strong></p>
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
                $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                $mail->send();
            
                return true;
           

                }else{
                    return ['error' => 'Usuario no encontrado o inactivo'];
                }

            }catch(PDOException $e){
                // CAPTURAMOS ERRORES Y LOS REGISTRAMOS EN EL LOG DEL SERVIDOR
                error_log("Error en el modelo Login: " . $e->getMessage());
                return ['error' => 'Error interno del servidor'];
            }



            
            
            


        }
    }

?>