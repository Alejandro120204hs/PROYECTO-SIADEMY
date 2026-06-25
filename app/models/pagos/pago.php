<?php

require_once __DIR__ . '/../../../config/database.php';

class Pago
{
    private $conexion;

    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->getConexion();
    }

    public function crear(array $data): bool
    {
        try {
            $sql = "INSERT INTO pago (id_institucion, id_usuario, referencia, concepto, monto_cents, moneda, estado)
                    VALUES (:id_institucion, :id_usuario, :referencia, :concepto, :monto_cents, :moneda, 'PENDING')";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_institucion', $data['id_institucion'], PDO::PARAM_INT);
            $stmt->bindParam(':id_usuario',     $data['id_usuario'],     PDO::PARAM_INT);
            $stmt->bindParam(':referencia',     $data['referencia']);
            $stmt->bindParam(':concepto',       $data['concepto']);
            $stmt->bindParam(':monto_cents',    $data['monto_cents'],    PDO::PARAM_INT);
            $stmt->bindParam(':moneda',         $data['moneda']);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en Pago::crear -> " . $e->getMessage());
            return false;
        }
    }

    public function actualizarPorReferencia(string $referencia, string $estado, string $wompiId, array $datos): bool
    {
        try {
            $sql = "UPDATE pago SET estado = :estado, wompi_id = :wompi_id, datos_respuesta = :datos, updated_at = NOW()
                    WHERE referencia = :referencia";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':estado',     $estado);
            $stmt->bindParam(':wompi_id',   $wompiId);
            $json = json_encode($datos, JSON_UNESCAPED_UNICODE);
            $stmt->bindParam(':datos',      $json);
            $stmt->bindParam(':referencia', $referencia);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en Pago::actualizarPorReferencia -> " . $e->getMessage());
            return false;
        }
    }

    public function buscarPorReferencia(string $referencia): ?array
    {
        try {
            $sql  = "SELECT * FROM pago WHERE referencia = :referencia LIMIT 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':referencia', $referencia);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            error_log("Error en Pago::buscarPorReferencia -> " . $e->getMessage());
            return null;
        }
    }

    public function listarPorUsuario(int $idUsuario): array
    {
        try {
            $sql  = "SELECT * FROM pago WHERE id_usuario = :id_usuario ORDER BY created_at DESC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Pago::listarPorUsuario -> " . $e->getMessage());
            return [];
        }
    }

    public function listarPorInstitucion(int $idInstitucion): array
    {
        try {
            $sql  = "SELECT p.*, u.correo
                     FROM pago p
                     INNER JOIN usuario u ON u.id = p.id_usuario
                     WHERE p.id_institucion = :id_institucion
                     ORDER BY p.created_at DESC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_institucion', $idInstitucion, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Pago::listarPorInstitucion -> " . $e->getMessage());
            return [];
        }
    }
}
