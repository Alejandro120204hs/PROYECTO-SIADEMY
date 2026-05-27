<?php

/**
 * VIEW DATA - ESTUDIANTE
 * Funciones auxiliares que preparan los datos para las vistas del rol Estudiante.
 * Sigue la misma estructura que app/controllers/docente/view_data.php
 */

require_once __DIR__ . '/../perfil.php';                        // mostrarPerfil()
require_once __DIR__ . '/../../models/estudiante/materia.php';  // MateriaEstudiante
require_once BASE_PATH . '/config/database.php';

function obtenerPerfilEstudianteDesdeSesion()
{
    $idUsuario = (int)($_SESSION['user']['id'] ?? 0);
    return mostrarPerfil($idUsuario);
}

// ─────────────────────────────────────────────────────────────────────────────
// HELPERS GLOBALES
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Serializa un valor PHP a JSON seguro para usarlo en un atributo HTML.
 */
function estudianteJsonParaHtml(mixed $value): string
{
    return htmlspecialchars(
        json_encode(
            $value,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES |
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
        ),
        ENT_QUOTES,
        'UTF-8'
    );
}

// ─────────────────────────────────────────────────────────────────────────────
// HELPERS INTERNOS
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Obtiene el id de la tabla `estudiante` a partir del id_usuario en sesión.
 *
 * @return int  0 si no se encuentra
 */
function estudianteObtenerIdDesdeSesion()
{
    $idUsuario = (int)($_SESSION['user']['id'] ?? 0);
    if ($idUsuario === 0) {
        return 0;
    }

    try {
        $db  = new Conexion();
        $pdo = $db->getConexion();

        $stmt = $pdo->prepare("SELECT id FROM estudiante WHERE id_usuario = :id_usuario LIMIT 1");
        $stmt->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['id'] ?? 0);

    } catch (PDOException $e) {
        error_log("Error en estudianteObtenerIdDesdeSesion: " . $e->getMessage());
        return 0;
    }
}

/**
 * Convierte las actividades del estudiante al formato de evento de calendario
 * compatible con el JS del dashboard (mismo formato que docente).
 *
 * Campos requeridos por el JS:
 *   fecha_evento, tipo_evento, nombre_evento, descripcion, hora_inicio, fuente
 *
 * @param  array $actividadesRaw  Resultado de obtenerActividadesParaCalendario()
 * @return array
 */
function estudianteConstruirEventosCalendario(array $actividadesRaw)
{
    $hoy    = date('Y-m-d');
    $eventos = [];

    foreach ($actividadesRaw as $act) {
        $fecha = (string)($act['fecha_entrega'] ?? '');
        if ($fecha === '') {
            continue;
        }

        $tipo    = trim((string)($act['tipo']    ?? '')) ?: 'Actividad';
        $materia = trim((string)($act['materia'] ?? ''));
        $titulo  = trim((string)($act['titulo']  ?? '')) ?: 'Actividad académica';
        $desc    = trim((string)($act['descripcion'] ?? ''));

        // Incluir la materia en la descripción para que el modal lo muestre
        $descripcionCompleta = ($materia !== '' ? "Materia: {$materia}." : '')
                             . ($desc    !== '' ? " {$desc}" : '');

        $eventos[] = [
            'fecha_evento'  => $fecha,
            'tipo_evento'   => $tipo,
            'nombre_evento' => $titulo,
            'descripcion'   => trim($descripcionCompleta),
            'hora_inicio'   => '',
            'fuente'        => 'actividad',
            'is_upcoming'   => ($fecha >= $hoy),
        ];
    }

    return $eventos;
}

// ─────────────────────────────────────────────────────────────────────────────
// FUNCIÓN PRINCIPAL DEL DASHBOARD
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Prepara todos los datos necesarios para la vista /estudiante/dashboard.
 *
 * Variables devueltas:
 *   - todasMaterias              → array de materias con estadísticas
 *   - materiasBajoRendimiento    → subset con promedio < 3.0
 *   - estadisticas               → totales generales (materias, promedio, pendientes, en_riesgo)
 *   - eventosCalendarioEstudiante → array de eventos para el calendario
 *
 * @return array
 */
function obtenerDataVistaEstudianteDashboard()
{
    $idInstitucion = (int)($_SESSION['user']['id_institucion'] ?? 0);
    $anio          = (int)date('Y');
    $idEstudiante  = estudianteObtenerIdDesdeSesion();

    $materiaModel = new MateriaEstudiante();

    // ── 1. Todas las materias con estadísticas ──────────────────────────────
    $todasMaterias = $materiaModel->obtenerMateriasConEstadisticas(
        $idEstudiante, $idInstitucion, $anio
    );

    // ── 2. Estadísticas generales (KPIs) ────────────────────────────────────
    $estadisticas = $materiaModel->obtenerEstadisticasGenerales(
        $idEstudiante, $idInstitucion, $anio
    );

    // ── 3. Materias con Bajo rendimiento (promedio <= 3.0, escala académica) ──
    $materiasBajoRendimiento = array_values(
        array_filter($todasMaterias, function ($m) {
            $promedio = $m['promedio'];
            return $promedio !== null && (float)$promedio <= 3.0;
        })
    );

    // ── 4. Actividades para el calendario ───────────────────────────────────
    $actividadesRaw       = $materiaModel->obtenerActividadesParaCalendario(
        $idEstudiante, $idInstitucion, $anio
    );
    $eventosCalendarioEstudiante = estudianteConstruirEventosCalendario($actividadesRaw);

    return [
        'usuario'                     => obtenerPerfilEstudianteDesdeSesion(),
        'todasMaterias'               => $todasMaterias,
        'materiasBajoRendimiento'     => $materiasBajoRendimiento,
        'estadisticas'                => $estadisticas,
        'eventosCalendarioEstudiante' => $eventosCalendarioEstudiante,
    ];
}
