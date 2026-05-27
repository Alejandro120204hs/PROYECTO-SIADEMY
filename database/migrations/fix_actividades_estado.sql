-- ============================================================
-- FIX: Corregir estado de actividades contaminado por bug anterior
-- Ejecutar UNA SOLA VEZ en phpMyAdmin
--
-- El bug anterior cerraba actividades prematuramente (comparación
-- de fechas con error de zona horaria). Al editar, el modal
-- guardaba 'cerrada' en la BD aunque la fecha fuera futura.
--
-- Esta migración recalcula el estado correcto basado en la fecha:
--   fecha_entrega >= HOY  → 'activa'
--   fecha_entrega < HOY   → 'cerrada'
-- ============================================================

-- Ver cuántas actividades están afectadas (fechas futuras/hoy marcadas como cerradas)
SELECT COUNT(*) AS afectadas
FROM actividad
WHERE estado = 'cerrada'
  AND DATE(fecha_entrega) >= CURDATE();

-- Corrección: abrir actividades que tienen fecha hoy o futura
UPDATE actividad
SET estado = 'activa'
WHERE estado = 'cerrada'
  AND DATE(fecha_entrega) >= CURDATE();

-- Verificar resultado
SELECT id, titulo, fecha_entrega, estado
FROM actividad
ORDER BY fecha_entrega DESC
LIMIT 20;
