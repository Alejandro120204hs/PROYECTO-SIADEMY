-- ============================================================
-- MIGRACIÓN: Matrículas y Períodos Académicos
-- Ejecutar UNA SOLA VEZ en phpMyAdmin (en el orden indicado)
-- ============================================================

-- ─────────────────────────────────────────────────────────────
-- PASO 1: Agregar columna "estado" a la tabla matricula
--         Representa el estado académico de la matrícula,
--         independiente del estado del curso.
-- ─────────────────────────────────────────────────────────────
ALTER TABLE matricula
    ADD COLUMN estado
        ENUM('Activa','Retirada','Cancelada','Egresado','Repitente','Trasladado')
        NOT NULL
        DEFAULT 'Activa'
    AFTER fecha;

-- Marcar todos los registros históricos como Activos
UPDATE matricula SET estado = 'Activa' WHERE estado IS NULL OR estado = '';

-- ─────────────────────────────────────────────────────────────
-- PASO 2: Constraint UNIQUE en matrícula
--         Evita que el mismo estudiante quede matriculado dos
--         veces en el mismo curso y año (a nivel de BD).
--         Si ya tienes duplicados, este paso fallará.
--         En ese caso ejecuta primero el bloque de limpieza.
-- ─────────────────────────────────────────────────────────────

-- [OPCIONAL] Detectar duplicados antes de crear el constraint:
-- SELECT id_estudiante, id_curso, anio, COUNT(*) AS total
-- FROM matricula
-- GROUP BY id_estudiante, id_curso, anio
-- HAVING total > 1;

ALTER TABLE matricula
    ADD CONSTRAINT uk_matricula_unica
        UNIQUE (id_estudiante, id_curso, anio);

-- ─────────────────────────────────────────────────────────────
-- PASO 3: Constraint UNIQUE en periodos_academicos
--         Evita crear dos períodos con el mismo tipo, número y
--         año lectivo para la misma institución.
--         Si ya tienes duplicados, este paso fallará.
-- ─────────────────────────────────────────────────────────────

-- [OPCIONAL] Detectar duplicados antes:
-- SELECT institucion_id, tipo_periodo, numero_periodo, ano_lectivo, COUNT(*) AS total
-- FROM periodos_academicos
-- GROUP BY institucion_id, tipo_periodo, numero_periodo, ano_lectivo
-- HAVING total > 1;

ALTER TABLE periodos_academicos
    ADD CONSTRAINT uk_periodo_unico
        UNIQUE (institucion_id, tipo_periodo, numero_periodo, ano_lectivo);

-- ─────────────────────────────────────────────────────────────
-- VERIFICACIÓN FINAL
-- ─────────────────────────────────────────────────────────────
SHOW COLUMNS FROM matricula LIKE 'estado';
SHOW INDEX FROM matricula WHERE Key_name = 'uk_matricula_unica';
SHOW INDEX FROM periodos_academicos WHERE Key_name = 'uk_periodo_unico';
