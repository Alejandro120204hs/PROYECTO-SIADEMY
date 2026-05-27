-- ============================================================
-- MIGRACIÓN: Corregir columna tipo en tabla actividad
-- Ejecutar UNA SOLA VEZ en phpMyAdmin
--
-- PROBLEMA DETECTADO:
--   La columna `tipo` era ENUM('Tarea','Examen','Proyecto','Quiz').
--   El formulario enviaba valores válidos como 'Taller', 'Exposición' y
--   'Laboratorio' que NO estaban en el ENUM. MySQL (sin strict mode)
--   los rechazaba silenciosamente y guardaba '' (string vacío), haciendo
--   que las actividades aparecieran con "Sin tipo" en la interfaz.
--
-- SOLUCIÓN:
--   Cambiar a VARCHAR(50) que acepta cualquier valor sin restricción.
-- ============================================================

-- Paso 1: Cambiar la columna (ya ejecutado vía PHP, incluido aquí para referencia)
ALTER TABLE actividad
    MODIFY COLUMN tipo VARCHAR(50) NULL DEFAULT NULL;

-- Paso 2: Ver qué actividades quedaron con tipo vacío (para editarlas manualmente)
SELECT id, titulo, tipo, fecha_entrega
FROM actividad
WHERE tipo IS NULL OR TRIM(tipo) = ''
ORDER BY id DESC;

-- Paso 3 (OPCIONAL): Si quieres asignar 'Tarea' como tipo por defecto a las
-- actividades sin tipo, descomenta la siguiente línea:
-- UPDATE actividad SET tipo = 'Tarea' WHERE tipo IS NULL OR TRIM(tipo) = '';
