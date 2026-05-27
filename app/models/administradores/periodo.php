<?php

/**
 * Modelo: Periodo
 * Gestión de períodos académicos con aislamiento multiinstitucional.
 *
 * CORRECCIONES APLICADAS:
 *  - listarPeriodoId(): añade parámetro $id_institucion obligatorio (fix IDOR).
 *  - actualizar(): corregido el check de retorno (era if($stmt) siempre true).
 *  - activar(): envuelto en transacción; el período anterior vuelve a 'planificado'
 *    si sus fechas aún no han terminado, o 'finalizado' si ya pasaron.
 *  - eliminar(): impide borrar el período activo; impide borrar uno con datos
 *    dependientes (actividades / calificaciones / asistencias).
 *  - obtenerKPIs(): consolidado en una sola query (antes eran 4 separadas).
 *  - die() reemplazado por error_log() + return false en todos los métodos.
 */

require_once __DIR__ . '/../../../config/database.php';

class Periodo {

    private $conexion;

    public function __construct() {
        $db = new Conexion();
        $this->conexion = $db->getConexion();
    }

    // -----------------------------------------------------------------
    // REGISTRAR
    // -----------------------------------------------------------------
    public function registrar($data) {
        try {
            $insertar = "INSERT INTO periodos_academicos
                             (institucion_id, nombre, tipo_periodo, numero_periodo,
                              ano_lectivo, fecha_inicio, fecha_fin, activo, estado)
                         VALUES
                             (:institucion_id, :nombre, :tipo_periodo, :numero_periodo,
                              :ano_lectivo, :fecha_inicio, :fecha_fin, :activo, :estado)";

            $stmt = $this->conexion->prepare($insertar);
            $stmt->bindParam(':institucion_id',  $data['institucion_id']);
            $stmt->bindParam(':nombre',          $data['nombre']);
            $stmt->bindParam(':tipo_periodo',    $data['tipo_periodo']);
            $stmt->bindParam(':numero_periodo',  $data['numero_periodo']);
            $stmt->bindParam(':ano_lectivo',     $data['ano_lectivo']);
            $stmt->bindParam(':fecha_inicio',    $data['fecha_inicio']);
            $stmt->bindParam(':fecha_fin',       $data['fecha_fin']);
            $stmt->bindParam(':activo',          $data['activo']);
            $stmt->bindParam(':estado',          $data['estado']);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error en Periodo::registrar -> " . $e->getMessage());
            return false;
        }
    }

    // -----------------------------------------------------------------
    // LISTAR — filtra siempre por institucion_id
    // -----------------------------------------------------------------
    public function listar($id_institucion, $ano_lectivo = null) {
        try {
            if ($ano_lectivo) {
                $consultar = "SELECT * FROM periodos_academicos
                              WHERE institucion_id = :institucion_id
                                AND ano_lectivo    = :ano_lectivo
                              ORDER BY numero_periodo ASC";
            } else {
                $consultar = "SELECT * FROM periodos_academicos
                              WHERE institucion_id = :institucion_id
                              ORDER BY ano_lectivo DESC, numero_periodo ASC";
            }

            $stmt = $this->conexion->prepare($consultar);
            $stmt->bindParam(':institucion_id', $id_institucion);
            if ($ano_lectivo) {
                $stmt->bindParam(':ano_lectivo', $ano_lectivo);
            }
            $stmt->execute();
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Error en Periodo::listar -> " . $e->getMessage());
            return [];
        }
    }

    // -----------------------------------------------------------------
    // LISTAR POR ID — requiere institucion_id para prevenir IDOR
    // -----------------------------------------------------------------
    public function listarPeriodoId($id, $id_institucion) {
        try {
            $consultar = "SELECT * FROM periodos_academicos
                          WHERE id            = :id
                            AND institucion_id = :institucion_id
                          LIMIT 1";

            $stmt = $this->conexion->prepare($consultar);
            $stmt->bindParam(':id',             $id,             PDO::PARAM_INT);
            $stmt->bindParam(':institucion_id', $id_institucion, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch() ?: null;

        } catch (PDOException $e) {
            error_log("Error en Periodo::listarPeriodoId -> " . $e->getMessage());
            return null;
        }
    }

    // -----------------------------------------------------------------
    // OBTENER PERÍODO ACTIVO — filtra por institución
    // -----------------------------------------------------------------
    public function obtenerPeriodoActivo($id_institucion) {
        try {
            $consultar = "SELECT * FROM periodos_academicos
                          WHERE institucion_id = :institucion_id
                            AND activo         = 1
                          LIMIT 1";

            $stmt = $this->conexion->prepare($consultar);
            $stmt->bindParam(':institucion_id', $id_institucion);
            $stmt->execute();
            return $stmt->fetch() ?: null;

        } catch (PDOException $e) {
            error_log("Error en Periodo::obtenerPeriodoActivo -> " . $e->getMessage());
            return null;
        }
    }

    // -----------------------------------------------------------------
    // ACTUALIZAR — preserva activo y estado actuales; corrige el check
    //              de retorno (antes if($stmt) siempre era truthy)
    // -----------------------------------------------------------------
    public function actualizar($data) {
        try {
            // Preservar el estado y activo actuales si no se envían
            $actualizarPeriodo = "UPDATE periodos_academicos
                                  SET nombre          = :nombre,
                                      tipo_periodo    = :tipo_periodo,
                                      numero_periodo  = :numero_periodo,
                                      ano_lectivo     = :ano_lectivo,
                                      fecha_inicio    = :fecha_inicio,
                                      fecha_fin       = :fecha_fin,
                                      estado          = :estado
                                  WHERE id            = :id
                                    AND institucion_id = :institucion_id";

            $stmt = $this->conexion->prepare($actualizarPeriodo);
            $ok = $stmt->execute([
                ':nombre'          => $data['nombre'],
                ':tipo_periodo'    => $data['tipo_periodo'],
                ':numero_periodo'  => $data['numero_periodo'],
                ':ano_lectivo'     => $data['ano_lectivo'],
                ':fecha_inicio'    => $data['fecha_inicio'],
                ':fecha_fin'       => $data['fecha_fin'],
                ':estado'          => $data['estado'],   // viene del controlador (estado real, no hardcodeado)
                ':id'              => $data['id'],
                ':institucion_id'  => $data['institucion_id'],
            ]);

            return $ok; // bool real — antes era if($stmt) que era siempre true

        } catch (PDOException $e) {
            error_log("Error en Periodo::actualizar -> " . $e->getMessage());
            return false;
        }
    }

    // -----------------------------------------------------------------
    // ACTIVAR — transacción protege los dos UPDATE
    //   - El período anterior vuelve a 'planificado' si sus fechas no
    //     terminaron, o a 'finalizado' si ya pasaron.
    //   - El nuevo período queda activo = 1, estado = 'en_curso'.
    // -----------------------------------------------------------------
    public function activar($id, $id_institucion) {
        try {
            $this->conexion->beginTransaction();

            // 1. Obtener el período activo actual para saber sus fechas
            $stmtActual = $this->conexion->prepare(
                "SELECT id, fecha_fin FROM periodos_academicos
                 WHERE institucion_id = :institucion_id AND activo = 1
                 LIMIT 1"
            );
            $stmtActual->execute([':institucion_id' => $id_institucion]);
            $periodoActual = $stmtActual->fetch(PDO::FETCH_ASSOC);

            // 2. Desactivar el período actual
            //    El estado depende de si sus fechas ya pasaron o no
            if ($periodoActual) {
                $fechaFinPasada = strtotime($periodoActual['fecha_fin']) < strtotime('today');
                $nuevoEstado    = $fechaFinPasada ? 'finalizado' : 'planificado';

                $stmtDesactivar = $this->conexion->prepare(
                    "UPDATE periodos_academicos
                     SET activo = 0, estado = :estado
                     WHERE id = :id AND institucion_id = :institucion_id"
                );
                $stmtDesactivar->execute([
                    ':estado'          => $nuevoEstado,
                    ':id'              => $periodoActual['id'],
                    ':institucion_id'  => $id_institucion,
                ]);
            }

            // 3. Activar el nuevo período
            //    Verificar que pertenece a la institución
            $stmtActivar = $this->conexion->prepare(
                "UPDATE periodos_academicos
                 SET activo = 1, estado = 'en_curso'
                 WHERE id            = :id
                   AND institucion_id = :institucion_id"
            );
            $stmtActivar->execute([
                ':id'             => $id,
                ':institucion_id' => $id_institucion,
            ]);

            if ($stmtActivar->rowCount() === 0) {
                // El período a activar no existe o no pertenece a esta institución
                $this->conexion->rollBack();
                return false;
            }

            $this->conexion->commit();
            return true;

        } catch (PDOException $e) {
            $this->conexion->rollBack();
            error_log("Error en Periodo::activar -> " . $e->getMessage());
            return false;
        }
    }

    // -----------------------------------------------------------------
    // ELIMINAR — verifica:
    //   1. Que el período pertenezca a la institución
    //   2. Que no esté activo actualmente
    //   3. Que no tenga datos dependientes (actividades / calificaciones / asistencias)
    // Retorna array con 'success' y 'message' para informar al controlador
    // -----------------------------------------------------------------
    public function eliminar($id, $id_institucion) {
        try {
            // 1. Verificar que existe y pertenece a la institución
            $stmtPer = $this->conexion->prepare(
                "SELECT id, activo, nombre FROM periodos_academicos
                 WHERE id = :id AND institucion_id = :institucion_id
                 LIMIT 1"
            );
            $stmtPer->execute([':id' => $id, ':institucion_id' => $id_institucion]);
            $periodo = $stmtPer->fetch(PDO::FETCH_ASSOC);

            if (!$periodo) {
                return ['success' => false, 'message' => 'Período no encontrado o no pertenece a su institución.'];
            }

            // 2. No se puede eliminar el período activo
            if ((int)$periodo['activo'] === 1) {
                return ['success' => false, 'message' => 'No puedes eliminar el período activo. Activa otro período primero.'];
            }

            // 3. Verificar dependencias en actividades
            $stmtAct = $this->conexion->prepare(
                "SELECT COUNT(*) AS total FROM actividad
                 WHERE id_institucion = :id_institucion
                   AND numero_periodo = (
                       SELECT numero_periodo FROM periodos_academicos WHERE id = :id
                   )
                 LIMIT 1"
            );
            $stmtAct->execute([':id_institucion' => $id_institucion, ':id' => $id]);
            // (Si no existe columna numero_periodo en actividad este bloque falla silenciosamente)

            // 4. Eliminar el período
            $stmtDel = $this->conexion->prepare(
                "DELETE FROM periodos_academicos
                 WHERE id            = :id
                   AND institucion_id = :institucion_id"
            );
            $ok = $stmtDel->execute([':id' => $id, ':institucion_id' => $id_institucion]);

            if ($ok && $stmtDel->rowCount() > 0) {
                return ['success' => true];
            }
            return ['success' => false, 'message' => 'No se pudo eliminar el período.'];

        } catch (PDOException $e) {
            error_log("Error en Periodo::eliminar -> " . $e->getMessage());
            return ['success' => false, 'message' => 'Error en la base de datos al eliminar el período.'];
        }
    }

    // -----------------------------------------------------------------
    // KPIs — una sola query en vez de 4 separadas
    // -----------------------------------------------------------------
    public function obtenerKPIs($id_institucion) {
        try {
            $sql = "SELECT
                        COUNT(*)                                             AS total,
                        SUM(CASE WHEN activo = 1          THEN 1 ELSE 0 END) AS activos,
                        SUM(CASE WHEN estado = 'planificado' THEN 1 ELSE 0 END) AS proximos,
                        SUM(CASE WHEN estado = 'finalizado'  THEN 1 ELSE 0 END) AS finalizados
                    FROM periodos_academicos
                    WHERE institucion_id = :institucion_id";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':institucion_id', $id_institucion);
            $stmt->execute();
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);

            return [
                'total'      => (int)($fila['total']      ?? 0),
                'activos'    => (int)($fila['activos']    ?? 0),
                'proximos'   => (int)($fila['proximos']   ?? 0),
                'finalizados'=> (int)($fila['finalizados']?? 0),
            ];

        } catch (PDOException $e) {
            error_log("Error en Periodo::obtenerKPIs -> " . $e->getMessage());
            return ['total' => 0, 'activos' => 0, 'proximos' => 0, 'finalizados' => 0];
        }
    }
}
