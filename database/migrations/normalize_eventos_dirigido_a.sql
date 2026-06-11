-- Migración: normalizar campo grado (dirigido_a) en tabla eventos
-- Convierte texto libre a vocabulario controlado
-- Valores permitidos: Todos | Estudiantes | Docentes | Acudientes | Administradores

UPDATE eventos
SET grado = 'Todos'
WHERE grado IS NULL
   OR TRIM(grado) = ''
   OR LOWER(TRIM(grado)) LIKE '%todos%'
   OR LOWER(TRIM(grado)) LIKE '%general%';

UPDATE eventos SET grado = 'Estudiantes' WHERE LOWER(TRIM(grado)) LIKE '%estudi%' OR LOWER(TRIM(grado)) LIKE '%alumno%';
UPDATE eventos SET grado = 'Docentes'    WHERE LOWER(TRIM(grado)) LIKE '%docente%' OR LOWER(TRIM(grado)) LIKE '%profesor%';
UPDATE eventos SET grado = 'Acudientes'  WHERE LOWER(TRIM(grado)) LIKE '%acudiente%' OR LOWER(TRIM(grado)) LIKE '%padre%' OR LOWER(TRIM(grado)) LIKE '%familia%';
UPDATE eventos SET grado = 'Administradores' WHERE LOWER(TRIM(grado)) LIKE '%admin%';

-- Cualquier valor no mapeado cae a 'Todos'
UPDATE eventos
SET grado = 'Todos'
WHERE grado NOT IN ('Todos', 'Estudiantes', 'Docentes', 'Acudientes', 'Administradores');
