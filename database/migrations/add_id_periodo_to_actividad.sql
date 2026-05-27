-- =============================================================================
-- Migración: agregar id_periodo a la tabla actividad
-- Propósito : Establecer una relación explícita entre actividades y períodos
--             académicos, eliminando la dependencia de inferencia por rango de
--             fechas (fecha_entrega BETWEEN fecha_inicio AND fecha_fin).
--
-- Diseño    : Columna NULLABLE — actividades antiguas sin período asignado
--             conservan NULL y siguen funcionando con la lógica de fechas.
--             La FK usa ON DELETE SET NULL para que eliminar un período no
--             borre las actividades asociadas.
--
-- INSTRUCCIONES DE EJECUCIÓN:
--   1. Hacer backup de la BD antes de ejecutar.
--   2. Ejecutar en MySQL Workbench o phpMyAdmin.
--   3. Verificar con: SELECT id, titulo, id_periodo FROM actividad LIMIT 20;
-- =============================================================================

-- ── 1. Agregar columna nullable ──────────────────────────────────────────────
ALTER TABLE actividad
    ADD COLUMN id_periodo INT NULL DEFAULT NULL
    AFTER id_asignatura_curso;

-- ── 2. Crear FK hacia periodos_academicos ────────────────────────────────────
ALTER TABLE actividad
    ADD CONSTRAINT fk_actividad_periodo
        FOREIGN KEY (id_periodo)
        REFERENCES periodos_academicos(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE;

-- Índice para acelerar JOINs por período.
CREATE INDEX idx_actividad_id_periodo ON actividad(id_periodo);

-- ── 3. Backfill: asignar id_periodo a actividades existentes ─────────────────
-- Asigna el período cuyo rango de fechas (fecha_inicio – fecha_fin) contiene
-- la fecha_entrega de la actividad Y que pertenece a la misma institución.
-- Si una actividad cae en varios períodos solapados, se elige el que tenga
-- menor numero_periodo (el más temprano).
UPDATE actividad a
INNER JOIN (
    SELECT act.id                   AS id_actividad,
           MIN(pa.id)               AS id_periodo_asignado
    FROM actividad act
    INNER JOIN periodos_academicos pa
           ON pa.institucion_id = act.id_institucion
          AND DATE(act.fecha_entrega) BETWEEN pa.fecha_inicio AND pa.fecha_fin
    WHERE act.id_periodo IS NULL
    GROUP BY act.id
) backfill ON backfill.id_actividad = a.id
SET a.id_periodo = backfill.id_periodo_asignado
WHERE a.id_periodo IS NULL;

-- ── 4. Verificación post-migración ───────────────────────────────────────────
-- Muestra cuántas actividades quedaron sin período (esperado: 0 si las fechas
-- de los períodos cubren todos los rangos de fecha_entrega existentes).
SELECT
    COUNT(*)                                          AS total_actividades,
    SUM(CASE WHEN id_periodo IS NOT NULL THEN 1 END)  AS con_periodo,
    SUM(CASE WHEN id_periodo IS NULL     THEN 1 END)  AS sin_periodo
FROM actividad;
