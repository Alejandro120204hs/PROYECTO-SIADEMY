<?php

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require_once __DIR__ . '/../../vendor/PHPMailer/Exception.php';
    require_once __DIR__ . '/../../vendor/PHPMailer/PHPMailer.php';
    require_once __DIR__ . '/../../vendor/PHPMailer/SMTP.php';

    function mailer_init(){
        $mail = new PHPMailer(true);
        //Server settings
                       //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'soportesiademy@gmail.com';                     //SMTP username
        $mail->Password   = 'qzhn whkb lkos cowm';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;
        $mail->CharSet = "UTF-8";
        $mail->isHTML(true);       

        return $mail;
    }

?>