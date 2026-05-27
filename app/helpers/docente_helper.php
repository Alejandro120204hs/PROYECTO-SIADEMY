<?php

/**
 * Helpers compartidos para el módulo de docentes.
 *
 * Requiere que config/database.php esté cargado antes de incluir este archivo
 * (lo cual siempre ocurre porque los modelos lo cargan en su propio require_once).
 */

/**
 * Resuelve el `id` real de la tabla `docente` a partir del `id_usuario` en
 * sesión y del `id_institucion`.
 *
 * NUNCA debe usarse `$_SESSION['user']['id']` directamente como `id_docente`,
 * ya que `id_usuario` y `id_docente` son columnas de tablas distintas y sus
 * valores numéricos NO son intercambiables.
 *
 * @param int $id_usuario     Valor de $_SESSION['user']['id']
 * @param int $id_institucion Valor de $_SESSION['user']['id_institucion']
 * @return int|null           ID de la tabla `docente`, o null si no existe el registro
 */
function resolverIdDocente(int $id_usuario, int $id_institucion): ?int {
    try {
        $db   = new Conexion();
        $pdo  = $db->getConexion();
        $stmt = $pdo->prepare(
            'SELECT id
               FROM docente
              WHERE id_usuario    = :id_usuario
                AND id_institucion = :id_institucion
              LIMIT 1'
        );
        $stmt->bindParam(':id_usuario',    $id_usuario,    PDO::PARAM_INT);
        $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
        $stmt->execute();
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila ? (int)$fila['id'] : null;
    } catch (PDOException $e) {
        error_log('[Siademy] resolverIdDocente error: ' . $e->getMessage());
        return null;
    }
}
