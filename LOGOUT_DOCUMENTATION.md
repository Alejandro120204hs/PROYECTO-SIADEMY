# Sistema de Logout y Gestión de Sesiones - Documentación

## Descripción General

Se ha implementado un sistema robusto y centralizado para manejar el cierre de sesión y la gestión de sesiones en toda la aplicación SIADEMY. Este sistema funciona para todos los roles del sistema sin excepción.

## Archivos Creados/Modificados

### 1. **app/helpers/session_helper.php** (NUEVO)
Helper centralizado con funciones reutilizables para manejar sesiones:

```php
// Inicia sesión si no está activa
initSession();

// Verifica si hay sesión activa
if (isSessionActive()) { ... }

// Verifica si el usuario tiene un rol específico
if (hasRole('Administrador')) { ... }

// Obtiene los datos del usuario actual
$user = getCurrentUser();

// Establece la sesión del usuario
setUserSession($userData);

// Cierra la sesión de forma segura
destroySession();

// Redirige si no hay sesión
redirectIfNoSession();

// Redirige si no tiene el rol requerido
redirectIfNotRole('Docente');
```

### 2. **app/controllers/logoutController.php** (NUEVO)
Controlador especializado para manejar el logout:
- Inicia sesión si no está activa
- Elimina todas las variables de $_SESSION
- Destruye la sesión completamente
- Elimina la cookie de sesión
- Limpia cookies relacionadas a autenticación
- Redirige al login

### 3. **index.php** (MODIFICADO)
- Se agregó la inicialización del session_helper
- Se agregó initSession() al inicio
- Se agregó la ruta `/logout` que llama al controlador logoutController.php

### 4. **app/helpers/session_administrador.php** (MODIFICADO)
- Ahora utiliza las funciones del session_helper
- Más legible y mantenible
- Misma funcionalidad, mejor implementación

### 5. **app/helpers/session_docente.php** (MODIFICADO)
- Ahora utiliza las funciones del session_helper
- Más legible y mantenible
- Misma funcionalidad, mejor implementación

## Flujo de Logout

Cuando un usuario hace clic en "Cerrar Sesión":

```
1. El enlace apunta a: BASE_URL/logout
2. Se ejecuta: app/controllers/logoutController.php
3. El controlador:
   ✓ Inicia la sesión (si necesario)
   ✓ Vacía todas las variables de sesión
   ✓ Destruye completamente la sesión
   ✓ Elimina la cookie de sesión
   ✓ Limpia cookies de autenticación
   ✓ Redirige a /login
4. Usuario regresa al login correctamente cerrado de sesión
```

## Cómo Usar en Vistas/Controladores

### Verificar si hay sesión activa:
```php
<?php
require_once BASE_PATH . '/app/helpers/session_helper.php';

if (isSessionActive()) {
    echo "El usuario está autenticado";
}
?>
```

### Verificar rol del usuario:
```php
<?php
if (hasRole('Administrador')) {
    // Mostrar opciones de admin
}
?>
```

### Obtener datos del usuario:
```php
<?php
$user = getCurrentUser();
if ($user) {
    echo "Bienvenido " . $user['nombres'];
}
?>
```

### Proteger rutas que requieren autenticación:
```php
<?php
require_once BASE_PATH . '/app/helpers/session_helper.php';

// Esto redirige al login si no hay sesión
redirectIfNoSession();

// El código aquí solo se ejecuta si hay sesión activa
?>
```

### Proteger rutas que requieren rol específico:
```php
<?php
require_once BASE_PATH . '/app/helpers/session_helper.php';

// Esto redirige al login si no es Administrador
redirectIfNotRole('Administrador');

// El código aquí solo se ejecuta si el usuario es Administrador
?>
```

## Compatibilidad con Roles

El sistema de logout funciona para todos los roles sin importar cuál esté autenticado:

- ✓ Administrador
- ✓ Docente
- ✓ Estudiante
- ✓ Acudiente
- ✓ Secretaría Académica
- ✓ Super Admin
- ✓ Cualquier otro rol futuro

## Seguridad

El sistema implementa varias capas de seguridad:

1. **Limpieza completa de sesión**: Se vacían todas las variables de $_SESSION
2. **Destrucción de sesión**: Se destruye la sesión con session_destroy()
3. **Eliminación de cookies**: Se elimina la cookie de sesión PHP
4. **Limpieza de cookies personalizadas**: Se eliminan cookies que contengan 'auth' o 'user'
5. **Redirección HTTP 302**: Redirección segura al login

## Pruebas Recomendadas

1. **Autenticarse** como usuarios de diferentes roles
2. **Hacer clic en "Cerrar Sesión"** desde el menú de usuario
3. **Verificar** que se redirige al login correctamente
4. **Intentar acceder** directamente a URLs de dashboard
5. **Verificar** que no pueda acceder sin autenticación

## Notas Importantes

- El cookie de sesión se establece con tiempo -3600 (una hora en el pasado) para garantizar su eliminación
- El parámetro `true` en setcookie permite que se establezca en todas las rutas (`/`)
- La función `destroySession()` es segura incluso si se llama cuando no hay sesión activa
- El helper puede ser incluido múltiples veces sin duplicar funciones

## Migración Futura

Para mantener coherencia, se recomienda que futuros desarrolladores:
- Usen only `app/helpers/session_helper.php` para todas las operaciones de sesión
- Reemplacen gradualmente cualquier código que use sesiones directamente
- Utilicen las funciones del helper en lugar de `$_SESSION` directamente
