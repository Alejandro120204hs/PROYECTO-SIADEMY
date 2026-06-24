<?php
header('Content-Type: text/plain');
?>
User-agent: *
Allow: /
Allow: /login

Disallow: /dashboard-perfil
Disallow: /administrador/
Disallow: /superAdmin-
Disallow: /docente/
Disallow: /docente-
Disallow: /estudiante/
Disallow: /estudiante-
Disallow: /acudiente/
Disallow: /secretaria-academica/
Disallow: /super-admin/
Disallow: /api/
Disallow: /iniciar-sesion
Disallow: /generar-clave
Disallow: /enviar-correo
Disallow: /logout
Disallow: /public/uploads/

Sitemap: <?= BASE_URL ?>/sitemap.xml
