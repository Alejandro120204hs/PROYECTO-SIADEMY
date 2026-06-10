-- =============================================================================
-- Migración: expandir tabla notificacion para sistema de notificaciones completo
--
-- Estado previo de la tabla (5 columnas):
--   id, id_institucion, mensaje VARCHAR(120), fecha DATE, id_destinatario
--
-- Estado final (12 columnas):
--   id, id_institucion, titulo, mensaje TEXT, created_at TIMESTAMP,
--   id_destinatario, tipo, leida, descartada, url_accion, entidad_tipo, entidad_id
--
-- Prerrequisito: la tabla debe estar vacía (0 filas).
-- Verificar con: SELECT COUNT(*) FROM notificacion;
--
-- INSTRUCCIONES DE EJECUCIÓN:
--   1. Abrir phpMyAdmin → base de datos siademy → pestaña SQL.
--   2. Pegar y ejecutar el bloque completo.
--   3. Verificar con el SELECT final al pie del archivo.
-- =============================================================================

-- ── 1. Añadir titulo, expandir mensaje a TEXT, renombrar fecha → created_at ──

ALTER TABLE `notificacion`
  ADD COLUMN `titulo`    VARCHAR(120) NOT NULL DEFAULT ''                   AFTER `id_institucion`,
  CHANGE COLUMN `mensaje`  `mensaje`  TEXT NOT NULL,
  CHANGE COLUMN `fecha`    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;

-- ── 2. Añadir columnas operacionales y de trazabilidad ────────────────────────

ALTER TABLE `notificacion`
  ADD COLUMN `tipo`         VARCHAR(60)  NOT NULL DEFAULT 'general'         AFTER `id_destinatario`,
  ADD COLUMN `leida`        TINYINT(1)   NOT NULL DEFAULT 0                  AFTER `tipo`,
  ADD COLUMN `descartada`   TINYINT(1)   NOT NULL DEFAULT 0                  AFTER `leida`,
  ADD COLUMN `url_accion`   VARCHAR(255) NULL                                AFTER `descartada`,
  ADD COLUMN `entidad_tipo` VARCHAR(60)  NULL                                AFTER `url_accion`,
  ADD COLUMN `entidad_id`   INT(11)      NULL                                AFTER `entidad_tipo`,
  ADD INDEX  `idx_dest_leida` (`id_destinatario`, `leida`, `descartada`);

-- ── 3. Verificación post-migración ───────────────────────────────────────────
-- Debe mostrar 12 columnas con los nombres y tipos correctos.
DESCRIBE `notificacion`;
