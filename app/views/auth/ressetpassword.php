<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña | SIADEMY</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/auth/css/resetpassword.css">
</head>

<body>
    <div class="logo">
        <img src="<?= BASE_URL ?>/public/assets/extras/img/LOGO-NEGATIVO 1 (1).png" alt="Logo SIADEMY">
    </div>

    <div class="container">
        <h1>Recupera tu acceso</h1>
        <p>Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.</p>

        <form action="generar-clave" method="post">
            <input type="email" name="correo" placeholder="Correo electrónico" required>
           

            <button type="submit">Enviar</button>
        </form>

        <div class="volver">
            <a href="login">← Volver al inicio de sesión</a>
        </div>
    </div>
</body>
</html>
