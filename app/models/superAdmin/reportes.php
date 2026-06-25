<?php

require_once __DIR__ . '/../../../config/database.php';

class ReportesSuperAdmin
{
    private $db;

    public function __construct()
    {
        $conn = new Conexion();
        $this->db = $conn->getConexion();
    }

    // ── KPIs PRINCIPALES ──────────────────────────────────────────────

    public function ingresosDelMes(): int
    {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(monto_cents), 0) AS total
            FROM pago
            WHERE estado = 'APPROVED'
              AND YEAR(created_at)  = YEAR(CURDATE())
              AND MONTH(created_at) = MONTH(CURDATE())
        ");
        $stmt->execute();
        return (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    }

    public function ingresosDelAnio(): int
    {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(monto_cents), 0) AS total
            FROM pago
            WHERE estado = 'APPROVED'
              AND YEAR(created_at) = YEAR(CURDATE())
        ");
        $stmt->execute();
        return (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    }

    public function ingresosDelMesAnterior(): int
    {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(monto_cents), 0) AS total
            FROM pago
            WHERE estado = 'APPROVED'
              AND YEAR(created_at)  = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
              AND MONTH(created_at) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
        ");
        $stmt->execute();
        return (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    }

    public function pagosPendientes(): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM pago WHERE estado = 'PENDING'");
        $stmt->execute();
        return (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    }

    public function pagosAprobadosHoy(): array
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS cantidad, COALESCE(SUM(monto_cents), 0) AS monto
            FROM pago
            WHERE estado = 'APPROVED' AND DATE(created_at) = CURDATE()
        ");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['cantidad' => 0, 'monto' => 0];
    }

    public function instituciones(): array
    {
        $stmt = $this->db->prepare("
            SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN estado = 'Activo' THEN 1 ELSE 0 END) AS activas
            FROM institucion
        ");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['total' => 0, 'activas' => 0];
    }

    // La tabla institucion no tiene created_at — retorna total de instituciones como referencia
    public function institucionesNuevasEsteMes(): int
    {
        return 0; // sin columna de fecha en la tabla
    }

    // ── GRÁFICAS ──────────────────────────────────────────────────────

    /** Ingresos aprobados por mes (últimos 12 meses) */
    public function ingresosPorMes(): array
    {
        $stmt = $this->db->prepare("
            SELECT
                DATE_FORMAT(created_at, '%Y-%m') AS mes,
                DATE_FORMAT(created_at, '%b %Y') AS etiqueta,
                COALESCE(SUM(monto_cents), 0)    AS total
            FROM pago
            WHERE estado = 'APPROVED'
              AND created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY mes ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Instituciones que realizaron pagos aprobados por mes (últimos 12 meses) */
    public function institucionesPorMes(): array
    {
        $stmt = $this->db->prepare("
            SELECT
                DATE_FORMAT(created_at, '%Y-%m') AS mes,
                DATE_FORMAT(created_at, '%b %Y') AS etiqueta,
                COUNT(DISTINCT id_institucion)   AS total
            FROM pago
            WHERE estado = 'APPROVED'
              AND created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY mes ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Distribución de pagos por estado */
    public function distribucionEstados(): array
    {
        $stmt = $this->db->prepare("
            SELECT estado, COUNT(*) AS total FROM pago GROUP BY estado
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Top 5 instituciones por ingresos generados */
    public function topInstituciones(): array
    {
        $stmt = $this->db->prepare("
            SELECT
                i.nombre,
                COUNT(p.id)                    AS pagos,
                COALESCE(SUM(p.monto_cents), 0) AS total
            FROM pago p
            INNER JOIN institucion i ON i.id = p.id_institucion
            WHERE p.estado = 'APPROVED'
            GROUP BY p.id_institucion, i.nombre
            ORDER BY total DESC
            LIMIT 5
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ── TABLA DE PAGOS ────────────────────────────────────────────────

    public function listarPagos(string $estado = '', string $desde = '', string $hasta = ''): array
    {
        $where  = ['1=1'];
        $params = [];

        if ($estado !== '') {
            $where[]           = 'p.estado = :estado';
            $params[':estado'] = $estado;
        }
        if ($desde !== '') {
            $where[]          = 'DATE(p.created_at) >= :desde';
            $params[':desde'] = $desde;
        }
        if ($hasta !== '') {
            $where[]          = 'DATE(p.created_at) <= :hasta';
            $params[':hasta'] = $hasta;
        }

        $sql  = "
            SELECT
                p.*,
                i.nombre AS nombre_institucion,
                u.correo AS correo_usuario
            FROM pago p
            INNER JOIN institucion i ON i.id = p.id_institucion
            INNER JOIN usuario     u ON u.id = p.id_usuario
            WHERE " . implode(' AND ', $where) . "
            ORDER BY p.created_at DESC
        ";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
