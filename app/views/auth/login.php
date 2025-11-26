<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Iniciar Sesión - Siademy</title>
    <link rel="stylesheet" href="public/assets/extras/css/login.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body>
    <div class="login-container">
        <div class="login-left">
            <div>
                <img src="public/assets/extras/img/LOGO-VERTICAL-NEGATIVO 1.png" alt="Logo de Siademy" class="logo">
            </div>
            <div class="login-box">
                <h2>Iniciar Sesión</h2>
                <form action="iniciar-sesion" method="POST">
                    <div class="input-group">
                        <i class="bi bi-person-fill"></i>
                        <input type="text" placeholder="Correo" required name="correo">
                    </div>
                    <div class="input-group">
                        <i class="bi bi-lock-fill"></i>
                        <input type="password" placeholder="Contraseña" required name="clave">
                    </div>
                    <button type="submit"
                        onclick="window.location.href='../dashboard/admin/admin.html'">Ingresar</button>
                </form>
                <a href="<?= BASE_URL ?>/recuperar-clave" class="forgot">¿Olvidaste tu contraseña?</a>
            </div>
        </div>

        <div class="login-right">
            <div class="image-shape">
                <img src="public/assets/extras/img/imagen-00230304.jpg" alt="Persona usando laptop">
            </div>
        </div>
    </div>
</body>

</html>