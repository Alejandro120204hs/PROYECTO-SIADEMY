<?php

/**
 * Clase de conexión a la base de datos.
 *
 * Implementa un singleton a nivel de PDO: por mucho que se haga `new Conexion()`
 * en distintos modelos dentro de una misma petición HTTP, solo se abre UNA conexión
 * real con MySQL.  La interfaz pública (constructor + getConexion()) se mantiene
 * idéntica para no romper el código existente.
 *
 * Seguridad:
 *  - Ya NO se hace die() con el mensaje de PDOException (evita exponer credenciales).
 *  - El mensaje técnico se escribe en error_log y se lanza RuntimeException genérica.
 */

class Conexion {

    /** @var PDO|null  Conexión compartida entre todas las instancias */
    private static ?PDO $pdo = null;

    /** @var PDO  Referencia local (apunta a la misma instancia que self::$pdo) */
    private PDO $conexion;

    public function __construct() {
        if (self::$pdo === null) {
            // Leer credenciales desde el entorno (cargadas por env_loader.php desde .env).
            // Fallbacks hardcodeados solo para desarrollo local sin .env.
            $host    = getenv('DB_HOST')    ?: 'localhost';
            $dbName  = getenv('DB_NAME')    ?: 'siademy';
            $usuario = getenv('DB_USER')    ?: 'root';
            // DB_PASS puede ser cadena vacía — getenv() devuelve '' (truthy falso),
            // por eso se comprueba con !== false en lugar de ?: para no pisar ''.
            $clave   = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
            $charset = getenv('DB_CHARSET') ?: 'utf8mb4';

            try {
                $dsn = "mysql:host={$host};dbname={$dbName};charset={$charset}";
                self::$pdo = new PDO($dsn, $usuario, $clave, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                // Registrar el error real solo en el log del servidor (nunca al cliente).
                error_log('[Siademy] Error de conexión a la base de datos: ' . $e->getMessage());
                throw new RuntimeException(
                    'No se pudo establecer la conexión con la base de datos. ' .
                    'Por favor, contacte al administrador del sistema.'
                );
            }
        }
        $this->conexion = self::$pdo;
    }

    /**
     * Devuelve la instancia PDO activa.
     */
    public function getConexion(): PDO {
        return $this->conexion;
    }
}
