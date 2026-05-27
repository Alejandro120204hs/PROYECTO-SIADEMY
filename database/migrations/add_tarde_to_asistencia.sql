-- ============================================================
-- MIGRACIÓN: Agregar estado 'Tarde' al ENUM de asistencia
-- ============================================================
-- Ejecutar UNA SOLA VEZ en phpMyAdmin o consola MySQL.
-- Seguro ejecutar aunque ya exista: MySQL solo modifica el ENUM
-- sin tocar los registros existentes.
-- ============================================================

ALTER TABLE asistencia
    MODIFY COLUMN estado
        ENUM('Presente', 'Ausente', 'Justificado', 'Tarde')
        NOT NULL
        DEFAULT 'Presente';

-- Verificación: debe mostrar los 4 valores en el ENUM
SHOW COLUMNS FROM asistencia LIKE 'estado';
