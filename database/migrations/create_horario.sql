-- =============================================================================
-- Migración: crear tabla horario
-- Propósito : Almacenar los bloques académicos semanales (horarios de clases).
--             Cada bloque representa qué asignatura dicta qué docente, en qué
--             curso, en qué día y franja horaria.
--
-- Diseño:
--   • id_docente_asignatura_curso → FK a docente_asignatura_curso (ya tiene
--     la relación docente+asignatura+curso+institución).
--   • id_institucion se desnormaliza aquí para filtrado multi-tenant O(1).
--   • dia_semana: 1=Lunes, 2=Martes, 3=Miércoles, 4=Jueves, 5=Viernes, 6=Sábado.
--   • Restricciones de conflicto se validan en la capa de aplicación antes de
--     INSERT/UPDATE; los índices únicos garantizan integridad en la BD.
--
-- INSTRUCCIONES:
--   1. Hacer backup de la BD antes de ejecutar.
--   2. Ejecutar en phpMyAdmin o MySQL Workbench.
--   3. Verificar con: DESCRIBE horario;
-- =============================================================================

CREATE TABLE IF NOT EXISTS horario (
    id                        INT AUTO_INCREMENT PRIMARY KEY,
    id_institucion            INT NOT NULL,
    id_docente_asignatura_curso INT NOT NULL,
    dia_semana                TINYINT NOT NULL COMMENT '1=Lunes 2=Martes 3=Miércoles 4=Jueves 5=Viernes 6=Sábado',
    hora_inicio               TIME NOT NULL,
    hora_fin                  TIME NOT NULL,
    aula                      VARCHAR(60) DEFAULT NULL,
    estado                    ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
    created_at                TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at                TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Integridad referencial
    CONSTRAINT fk_horario_dac
        FOREIGN KEY (id_docente_asignatura_curso)
        REFERENCES docente_asignatura_curso(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    -- Restricción: hora_fin debe ser mayor que hora_inicio
    CONSTRAINT chk_horario_horas CHECK (hora_fin > hora_inicio),

    -- Restricción: dia_semana entre 1 y 6
    CONSTRAINT chk_horario_dia CHECK (dia_semana BETWEEN 1 AND 6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Índices para consultas frecuentes ────────────────────────────────────────

-- Consultas por institución + día (panel admin)
CREATE INDEX idx_horario_inst_dia
    ON horario (id_institucion, dia_semana);

-- Consultas por DAC (obtener bloques de una asignación específica)
CREATE INDEX idx_horario_dac
    ON horario (id_docente_asignatura_curso);

-- Consultas por institución + estado (listados activos)
CREATE INDEX idx_horario_inst_estado
    ON horario (id_institucion, estado);

-- ── Verificación post-creación ────────────────────────────────────────────────
SELECT 'Tabla horario creada correctamente' AS resultado;
DESCRIBE horario;
