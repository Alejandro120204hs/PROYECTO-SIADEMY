<?php

require_once __DIR__ . '/../perfil.php';
require_once __DIR__ . '/../../models/docente/curso.php';
require_once __DIR__ . '/../../models/docente/actividad.php';
require_once __DIR__ . '/../../models/docente/asistencia.php';
require_once __DIR__ . '/../../models/administradores/cursos.php';
require_once __DIR__ . '/../../models/administradores/matricula.php';
require_once __DIR__ . '/../../models/administradores/docente_asignatura.php';
require_once BASE_PATH . '/config/database.php';

function obtenerPerfilDocenteDesdeSesion()
{
    $idUsuario = (int) ($_SESSION['user']['id'] ?? 0);
    return mostrarPerfil($idUsuario);
}

function obtenerVersionesAssetsDocente()
{
    return [
        'mainDocenteJsVersion' => @filemtime(BASE_PATH . '/public/assets/dashboard/js/main-docente.js') ?: time(),
        'asistenciaCssVersion' => @filemtime(BASE_PATH . '/public/assets/dashboard/css/docente/asistencia.css') ?: time(),
        'asistenciaJsVersion' => @filemtime(BASE_PATH . '/public/assets/dashboard/js/docente/asistencia.js') ?: time(),
        'detalleCursoCssVersion' => @filemtime(BASE_PATH . '/public/assets/dashboard/css/docente/detalle-curso.css') ?: time(),
        'detalleCursoJsVersion' => @filemtime(BASE_PATH . '/public/assets/dashboard/js/docente/detalle-curso.js') ?: time(),
    ];
}

function docenteJsonParaHtml($value)
{
    return htmlspecialchars(
        json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT),
        ENT_QUOTES,
        'UTF-8'
    );
}

function docenteNormalizarTextoEvento($texto)
{
    $texto = (string) $texto;
    $texto = mb_strtolower($texto, 'UTF-8');
    $texto = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto);
    return $texto !== false ? $texto : '';
}

function docenteCategoriaEvento($tipoEvento, $fuente)
{
    $base = docenteNormalizarTextoEvento($tipoEvento);

    if (strpos($base, 'reunion') !== false || strpos($base, 'junta') !== false || strpos($base, 'consejo') !== false) {
        return 'meetings';
    }

    if (strpos($base, 'examen') !== false || strpos($base, 'parcial') !== false || strpos($base, 'quiz') !== false || strpos($base, 'prueba') !== false || strpos($base, 'evaluacion') !== false) {
        return 'exams';
    }

    if ($fuente === 'actividad') {
        if (strpos($base, 'tarea') !== false || strpos($base, 'proyecto') !== false || strpos($base, 'taller') !== false || strpos($base, 'laboratorio') !== false) {
            return 'activities';
        }
    }

    return 'activities';
}

function docenteNombreCategoriaEvento($category)
{
    switch ($category) {
        case 'meetings':
            return 'Reunion';
        case 'exams':
            return 'Examen';
        default:
            return 'Actividad';
    }
}

function docenteIconoCategoriaEvento($category)
{
    switch ($category) {
        case 'meetings':
            return 'ri-user-voice-line';
        case 'exams':
            return 'ri-file-edit-line';
        default:
            return 'ri-calendar-event-line';
    }
}

function docenteConstruirEventosDashboard($idInstitucion, $idDocente)
{
    $objetoCurso = new Curso_docente();
    return $objetoCurso->obtenerEventosCalendario((int) $idInstitucion, (int) $idDocente);
}

function docenteConstruirEventosVistaEventos($idInstitucion, $idDocente)
{
    $eventosRaw = docenteConstruirEventosDashboard($idInstitucion, $idDocente);
    $hoy = date('Y-m-d');
    $eventosDocente = [];

    foreach ($eventosRaw as $evento) {
        $fecha = (string) ($evento['fecha_evento'] ?? '');
        if ($fecha === '') {
            continue;
        }

        $category = docenteCategoriaEvento($evento['tipo_evento'] ?? '', $evento['fuente'] ?? 'evento');

        $eventosDocente[] = [
            'fecha_evento' => $fecha,
            'tipo_evento' => (string) ($evento['tipo_evento'] ?? 'Evento'),
            'nombre_evento' => (string) ($evento['nombre_evento'] ?? 'Evento academico'),
            'descripcion' => (string) ($evento['descripcion'] ?? 'Sin descripcion'),
            'hora_inicio' => (string) ($evento['hora_inicio'] ?? ''),
            'fuente' => (string) ($evento['fuente'] ?? 'evento'),
            'category' => $category,
            'category_name' => docenteNombreCategoriaEvento($category),
            'icon' => docenteIconoCategoriaEvento($category),
            'is_upcoming' => ($fecha >= $hoy),
        ];
    }

    usort($eventosDocente, function ($a, $b) {
        $left = $a['fecha_evento'] . ' ' . ($a['hora_inicio'] ?: '23:59:59');
        $right = $b['fecha_evento'] . ' ' . ($b['hora_inicio'] ?: '23:59:59');
        return strcmp($left, $right);
    });

    $statsEventos = [
        'all' => count($eventosDocente),
        'upcoming' => 0,
        'meetings' => 0,
        'exams' => 0,
        'activities' => 0,
    ];

    foreach ($eventosDocente as $evento) {
        if (!empty($evento['is_upcoming'])) {
            $statsEventos['upcoming']++;
        }

        if (isset($statsEventos[$evento['category']])) {
            $statsEventos[$evento['category']]++;
        }
    }

    return [$eventosDocente, $statsEventos];
}

function docenteObtenerIconoTipoActividad($tipo)
{
    static $iconos = [
        'Taller' => 'ri-file-text-line',
        'Quiz' => 'ri-file-list-3-line',
        'Examen' => 'ri-file-paper-line',
        'Proyecto' => 'ri-folder-line',
        'Exposicion' => 'ri-presentation-line',
        'Exposición' => 'ri-presentation-line',
        'Laboratorio' => 'ri-flask-line',
        'Tarea' => 'ri-file-edit-line',
    ];

    return $iconos[$tipo] ?? 'ri-file-line';
}

function docenteFormatearFechaActividad($fecha)
{
    $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    $timestamp = strtotime((string) $fecha);

    if ($timestamp === false) {
        return '';
    }

    return date('d', $timestamp) . ' ' . $meses[((int) date('n', $timestamp)) - 1] . ' ' . date('Y', $timestamp);
}

function obtenerDataVistaDocenteDashboard()
{
    $idInstitucion = (int) ($_SESSION['user']['id_institucion'] ?? 0);
    $idDocente = (int) ($_SESSION['user']['id'] ?? 0);

    $objetoCurso = new Curso_docente();

    return [
        'usuario' => obtenerPerfilDocenteDesdeSesion(),
        'datos' => $objetoCurso->listar($idInstitucion, $idDocente),
        'estadisticas' => $objetoCurso->obtenerEstadisticasDashboard($idInstitucion, $idDocente),
        'estudiantesBajoRendimiento' => $objetoCurso->listarEstudiantesBajoRendimiento($idInstitucion, $idDocente, 20),
        'eventosCalendarioDocente' => $objetoCurso->obtenerEventosCalendario($idInstitucion, $idDocente),
    ] + obtenerVersionesAssetsDocente();
}

function obtenerDataVistaDocenteEventos()
{
    $idInstitucion = (int) ($_SESSION['user']['id_institucion'] ?? 0);
    $idDocente = (int) ($_SESSION['user']['id'] ?? 0);

    [$eventosDocente, $statsEventos] = docenteConstruirEventosVistaEventos($idInstitucion, $idDocente);

    return [
        'usuario' => obtenerPerfilDocenteDesdeSesion(),
        'eventosDocente' => $eventosDocente,
        'statsEventos' => $statsEventos,
    ] + obtenerVersionesAssetsDocente();
}

function obtenerDataVistaDocenteCursos()
{
    $idInstitucion = (int) ($_SESSION['user']['id_institucion'] ?? 0);
    $idDocente = (int) ($_SESSION['user']['id'] ?? 0);

    $objetoCurso = new Curso_docente();
    $datos = $objetoCurso->listar($idInstitucion, $idDocente);
    $totalCursos = count($datos ?? []);
    $totalEstudiantes = !empty($datos) ? array_sum(array_column($datos, 'total_estudiantes')) : 0;
    $asignaturasUnicas = !empty($datos) ? implode(', ', array_unique(array_column($datos, 'nombre_asignatura'))) : '-';

    $usuario = obtenerPerfilDocenteDesdeSesion();
    $nombreDocente = htmlspecialchars(
        trim((string) ($usuario['nombres'] ?? '') . ' ' . (string) ($usuario['apellidos'] ?? '')),
        ENT_QUOTES,
        'UTF-8'
    );

    $gradosUnicos = [];
    foreach ($datos as $curso) {
        $grado = $curso['grado'] ?? null;
        if ($grado !== null && !in_array($grado, $gradosUnicos, true)) {
            $gradosUnicos[] = $grado;
        }
    }

    return [
        'usuario' => $usuario,
        'datos' => $datos,
        'totalCursos' => $totalCursos,
        'totalEstudiantes' => $totalEstudiantes,
        'asignaturasUnicas' => $asignaturasUnicas,
        'nombreDocente' => $nombreDocente,
        'gradosUnicos' => $gradosUnicos,
    ];
}

function obtenerDataVistaDocenteActividades()
{
    $idInstitucion = (int) ($_SESSION['user']['id_institucion'] ?? 0);
    $idUsuarioDocente = (int) ($_SESSION['user']['id'] ?? 0);
    $idDocente = (int) ($_SESSION['user']['id_docente'] ?? $idUsuarioDocente);

    $objetoCurso = new Curso_docente();
    $datos = $objetoCurso->listar($idInstitucion, $idUsuarioDocente);

    $idCursoSeleccionado = isset($_GET['id_curso']) ? (int) $_GET['id_curso'] : null;
    $actividades = [];
    $infoCurso = null;

    if ($idCursoSeleccionado) {
        $actividadModel = new Actividad_docente();
        $actividades = $actividadModel->listarPorCurso($idCursoSeleccionado, $idDocente, $idInstitucion);
        if (!empty($actividades)) {
            $infoCurso = $actividades[0];
        }
    }

    $totalActividades = count($actividades);
    $totalActivas = 0;
    $totalCerradas = 0;

    foreach ($actividades as $actividad) {
        if (($actividad['estado'] ?? '') === 'activa') {
            $totalActivas++;
        } elseif (($actividad['estado'] ?? '') === 'cerrada') {
            $totalCerradas++;
        }
    }

    $usuario = obtenerPerfilDocenteDesdeSesion();
    $nombreDocente = htmlspecialchars(
        trim((string) ($usuario['nombres'] ?? '') . ' ' . (string) ($usuario['apellidos'] ?? '')),
        ENT_QUOTES,
        'UTF-8'
    );

    $asignaturaInfo = $infoCurso ? htmlspecialchars((string) $infoCurso['nombre_asignatura'], ENT_QUOTES, 'UTF-8') : '';

    $anosUnicos = [];
    foreach ($actividades as $actividad) {
        $anio = date('Y', strtotime((string) ($actividad['fecha_entrega'] ?? '')));
        if ($anio && !in_array($anio, $anosUnicos, true)) {
            $anosUnicos[] = $anio;
        }
    }
    rsort($anosUnicos);

    return [
        'usuario' => $usuario,
        'datos' => $datos,
        'actividades' => $actividades,
        'id_curso_seleccionado' => $idCursoSeleccionado,
        'info_curso' => $infoCurso,
        'totalActividades' => $totalActividades,
        'totalActivas' => $totalActivas,
        'totalCerradas' => $totalCerradas,
        'nombreDocente' => $nombreDocente,
        'asignaturaInfo' => $asignaturaInfo,
        'anosUnicos' => $anosUnicos,
    ];
}

function obtenerDataVistaDocenteAgregarActividad()
{
    if (!isset($_GET['id_curso']) || (int) $_GET['id_curso'] <= 0) {
        header('Location: ' . BASE_URL . '/docente-cursos');
        exit;
    }

    $idCurso = (int) $_GET['id_curso'];
    $idInstitucion = (int) ($_SESSION['user']['id_institucion'] ?? 0);

    $db = new Conexion();
    $conn = $db->getConexion();

    $query = "SELECT c.*, ac.id as id_asignatura_curso, a.nombre as nombre_asignatura, a.id as id_asignatura
              FROM curso c
              INNER JOIN asignatura_curso ac ON ac.id_curso = c.id
              INNER JOIN asignatura a ON a.id = ac.id_asignatura
              WHERE c.id = :id_curso
              AND c.id_institucion = :id_institucion
              LIMIT 1";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_curso', $idCurso, PDO::PARAM_INT);
    $stmt->bindParam(':id_institucion', $idInstitucion, PDO::PARAM_INT);
    $stmt->execute();

    $curso = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$curso) {
        header('Location: ' . BASE_URL . '/docente/cursos');
        exit;
    }

    return [
        'curso' => $curso,
    ];
}

function obtenerDataVistaDocenteAsistencia()
{
    $idUsuarioSesion = (int) ($_SESSION['user']['id'] ?? 0);
    $idInstitucion = (int) ($_SESSION['user']['id_institucion'] ?? 0);

    $cursoSeleccionado = isset($_GET['curso']) ? (int) $_GET['curso'] : null;
    $asignaturaSeleccionada = isset($_GET['asignatura']) ? (int) $_GET['asignatura'] : null;
    $fechaSeleccionada = !empty($_GET['fecha']) ? (string) $_GET['fecha'] : date('Y-m-d');

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaSeleccionada)) {
        $fechaSeleccionada = date('Y-m-d');
    }

    $objAsistencia = new AsistenciaDocente();
    $misCursosAsignaturas = $objAsistencia->obtenerCursosConAsignaturas($idUsuarioSesion, $idInstitucion);

    $cursoActual = null;
    $asignaturaActual = null;
    $asignaturasDelCurso = [];

    if ($cursoSeleccionado) {
        foreach ($misCursosAsignaturas as $curso) {
            if ((int) $curso['id_curso'] === (int) $cursoSeleccionado) {
                $cursoActual = $curso;
                $asignaturasDelCurso = $curso['asignaturas'];

                if ($asignaturaSeleccionada) {
                    foreach ($curso['asignaturas'] as $asig) {
                        if ((int) $asig['id'] === (int) $asignaturaSeleccionada) {
                            $asignaturaActual = $asig;
                            break;
                        }
                    }
                }
                break;
            }
        }
    }

    $mapaEstadoVista = ['Presente' => 'P', 'Ausente' => 'A', 'Justificado' => 'E'];
    $estudiantes = [];
    $historialAsistencia = [];

    if ($cursoSeleccionado && $asignaturaSeleccionada) {
        $rawEstudiantes = $objAsistencia->obtenerEstudiantesConAsistencia(
            (int) $cursoSeleccionado,
            (int) $asignaturaSeleccionada,
            $fechaSeleccionada,
            $idInstitucion
        );

        $idDocenteActual = $objAsistencia->obtenerIdDocente($idUsuarioSesion, $idInstitucion);

        if ($idDocenteActual > 0) {
            $historialAsistencia = $objAsistencia->obtenerHistorialAsistencia(
                (int) $cursoSeleccionado,
                (int) $asignaturaSeleccionada,
                (int) $idDocenteActual,
                $idInstitucion,
                20
            );
        }

        foreach ($rawEstudiantes as $estudiante) {
            $estadoDB = $estudiante['asistencia_estado'] ?? null;
            $estudiantes[] = [
                'id' => (int) $estudiante['id'],
                'nombres' => (string) $estudiante['nombres'],
                'apellidos' => (string) $estudiante['apellidos'],
                'documento' => (string) $estudiante['documento'],
                'foto' => !empty($estudiante['foto']) ? (string) $estudiante['foto'] : 'default.png',
                'asistencia_hoy' => $estadoDB !== null ? ($mapaEstadoVista[$estadoDB] ?? null) : null,
            ];
        }
    }

    $totalEstudiantes = count($estudiantes);
    $presentes = count(array_filter($estudiantes, fn($e) => ($e['asistencia_hoy'] ?? null) === 'P'));
    $ausentes = count(array_filter($estudiantes, fn($e) => ($e['asistencia_hoy'] ?? null) === 'A'));
    $tardanzas = count(array_filter($estudiantes, fn($e) => ($e['asistencia_hoy'] ?? null) === 'T'));
    $excusas = count(array_filter($estudiantes, fn($e) => ($e['asistencia_hoy'] ?? null) === 'E'));
    $sinMarcar = count(array_filter($estudiantes, fn($e) => ($e['asistencia_hoy'] ?? null) === null));
    $porcentajeAsistencia = $totalEstudiantes > 0 ? round(($presentes / $totalEstudiantes) * 100, 1) : 0;

    return [
        'usuario' => obtenerPerfilDocenteDesdeSesion(),
        'mis_cursos_asignaturas' => $misCursosAsignaturas,
        'curso_seleccionado' => $cursoSeleccionado,
        'asignatura_seleccionada' => $asignaturaSeleccionada,
        'fecha_seleccionada' => $fechaSeleccionada,
        'curso_actual' => $cursoActual,
        'asignatura_actual' => $asignaturaActual,
        'asignaturas_del_curso' => $asignaturasDelCurso,
        'estudiantes' => $estudiantes,
        'historial_asistencia' => $historialAsistencia,
        'totalEstudiantes' => $totalEstudiantes,
        'presentes' => $presentes,
        'ausentes' => $ausentes,
        'tardanzas' => $tardanzas,
        'excusas' => $excusas,
        'sinMarcar' => $sinMarcar,
        'porcentajeAsistencia' => $porcentajeAsistencia,
    ] + obtenerVersionesAssetsDocente();
}

function obtenerDataVistaDocenteDetalleCurso($idCurso)
{
    $idCurso = (int) $idCurso;
    if ($idCurso <= 0) {
        header('Location: ' . BASE_URL . '/docente-cursos');
        exit;
    }

    $cursoModel = new Curso();
    $curso = $cursoModel->listarCursoId($idCurso);

    if (!$curso) {
        header('Location: ' . BASE_URL . '/docente-cursos');
        exit;
    }

    $idUsuarioSesion = (int) ($_SESSION['user']['id'] ?? 0);
    $idInstitucion = (int) ($_SESSION['user']['id_institucion'] ?? 0);

    $db = new Conexion();
    $pdo = $db->getConexion();

    $stmtDocente = $pdo->prepare('SELECT id, nombres, apellidos FROM docente WHERE id_usuario = ?');
    $stmtDocente->execute([$idUsuarioSesion]);
    $docenteInfo = $stmtDocente->fetch(PDO::FETCH_ASSOC);

    if (!$docenteInfo) {
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    $idDocente = (int) ($docenteInfo['id'] ?? 0);
    $anioActual = (int) date('Y');

    $matriculaObj = new Matricula();
    $estudiantes = $matriculaObj->listarPorCurso($idCurso, $anioActual) ?: [];

    $docenteAsignaturaObj = new DocenteAsignatura();
    $asignaturas = $docenteAsignaturaObj->obtenerAsignaturasPorCurso($idCurso) ?: [];

    $misAsignaturas = [];
    foreach ($asignaturas as $asignatura) {
        if (empty($asignatura['docentes'])) {
            continue;
        }

        foreach ($asignatura['docentes'] as $docente) {
            if ((int) ($docente['id_docente'] ?? 0) === $idDocente && ($docente['estado'] ?? '') === 'activo') {
                $misAsignaturas[] = $asignatura;
                break;
            }
        }
    }

    $totalEstudiantes = count($estudiantes);
    $totalMisAsignaturas = count($misAsignaturas);

    $actividadesPendientesCalificar = 0;
    $estudiantesEnRiesgo = 0;
    $proximasActividades = 0;
    $promedioGeneral = 0;

    $perfilAcademicoPorEstudiante = [];
    $calificacionesPorEstudiante = [];

    foreach ($estudiantes as $estudianteBase) {
        $idEstudianteBase = (int) ($estudianteBase['id_estudiante'] ?? 0);
        if ($idEstudianteBase <= 0) {
            continue;
        }

        $perfilAcademicoPorEstudiante[$idEstudianteBase] = [
            'nombre' => trim((string) ($estudianteBase['estudiante_nombres'] ?? '') . ' ' . (string) ($estudianteBase['estudiante_apellidos'] ?? '')),
            'documento' => (string) ($estudianteBase['estudiante_documento'] ?? ''),
            'foto' => (string) ($estudianteBase['foto'] ?? 'default.png'),
            'fecha_matricula' => (string) ($estudianteBase['fecha'] ?? ''),
            'total_actividades' => 0,
            'total_entregadas' => 0,
            'total_calificadas' => 0,
            'promedio_general' => null,
            'ultima_calificacion' => null,
        ];

        $calificacionesPorEstudiante[$idEstudianteBase] = [];
    }

    try {
        $sqlPerfilAcademico = "SELECT
                                m.id_estudiante,
                                COUNT(DISTINCT a.id) AS total_actividades,
                                COUNT(DISTINCT ea.id) AS total_entregadas,
                                COUNT(DISTINCT c.id) AS total_calificadas,
                                AVG(c.nota) AS promedio_general,
                                MAX(c.fecha_calificacion) AS ultima_calificacion
                            FROM matricula m
                            LEFT JOIN asignatura_curso ac
                                ON ac.id_curso = m.id_curso
                               AND ac.estado = 'activo'
                            LEFT JOIN docente_asignatura_curso dac
                                ON dac.id_asignatura_curso = ac.id
                               AND dac.id_docente = :id_docente
                               AND dac.estado = 'activo'
                            LEFT JOIN actividad a
                                ON a.id_asignatura_curso = ac.id
                               AND a.id_docente = :id_docente_actividad
                               AND a.id_institucion = :id_institucion_actividad
                            LEFT JOIN entrega_actividad ea
                                ON ea.id_actividad = a.id
                               AND ea.id_estudiante = m.id_estudiante
                            LEFT JOIN calificacion c
                                ON c.id_entrega = ea.id
                            WHERE m.id_curso = :id_curso
                              AND m.id_institucion = :id_institucion
                              AND m.anio = :anio
                              AND dac.id IS NOT NULL
                            GROUP BY m.id_estudiante";

        $stmtPerfilAcademico = $pdo->prepare($sqlPerfilAcademico);
        $stmtPerfilAcademico->bindValue(':id_docente', $idDocente, PDO::PARAM_INT);
        $stmtPerfilAcademico->bindValue(':id_docente_actividad', $idDocente, PDO::PARAM_INT);
        $stmtPerfilAcademico->bindValue(':id_institucion_actividad', $idInstitucion, PDO::PARAM_INT);
        $stmtPerfilAcademico->bindValue(':id_curso', $idCurso, PDO::PARAM_INT);
        $stmtPerfilAcademico->bindValue(':id_institucion', $idInstitucion, PDO::PARAM_INT);
        $stmtPerfilAcademico->bindValue(':anio', $anioActual, PDO::PARAM_INT);
        $stmtPerfilAcademico->execute();

        $perfilesDb = $stmtPerfilAcademico->fetchAll(PDO::FETCH_ASSOC) ?: [];

        foreach ($perfilesDb as $perfilDb) {
            $idEstudiantePerfil = (int) ($perfilDb['id_estudiante'] ?? 0);
            if ($idEstudiantePerfil <= 0 || !isset($perfilAcademicoPorEstudiante[$idEstudiantePerfil])) {
                continue;
            }

            $perfilAcademicoPorEstudiante[$idEstudiantePerfil]['total_actividades'] = (int) ($perfilDb['total_actividades'] ?? 0);
            $perfilAcademicoPorEstudiante[$idEstudiantePerfil]['total_entregadas'] = (int) ($perfilDb['total_entregadas'] ?? 0);
            $perfilAcademicoPorEstudiante[$idEstudiantePerfil]['total_calificadas'] = (int) ($perfilDb['total_calificadas'] ?? 0);
            $perfilAcademicoPorEstudiante[$idEstudiantePerfil]['promedio_general'] = $perfilDb['promedio_general'] !== null
                ? round((float) $perfilDb['promedio_general'], 2)
                : null;
            $perfilAcademicoPorEstudiante[$idEstudiantePerfil]['ultima_calificacion'] = $perfilDb['ultima_calificacion'] ?: null;
        }

        $sqlCalificaciones = "SELECT
                                m.id_estudiante,
                                a.id AS id_actividad,
                                a.titulo,
                                a.tipo,
                                a.fecha_entrega AS fecha_limite,
                                asig.nombre AS asignatura,
                                ea.id AS id_entrega,
                                ea.estado AS estado_entrega,
                                c.nota,
                                c.observacion
                            FROM matricula m
                            LEFT JOIN asignatura_curso ac
                                ON ac.id_curso = m.id_curso
                               AND ac.estado = 'activo'
                            LEFT JOIN docente_asignatura_curso dac
                                ON dac.id_asignatura_curso = ac.id
                               AND dac.id_docente = :id_docente_det
                               AND dac.estado = 'activo'
                            LEFT JOIN actividad a
                                ON a.id_asignatura_curso = ac.id
                               AND a.id_docente = :id_docente_act_det
                               AND a.id_institucion = :id_inst_act_det
                            LEFT JOIN asignatura asig
                                ON asig.id = a.id_asignatura
                            LEFT JOIN entrega_actividad ea
                                ON ea.id_actividad = a.id
                               AND ea.id_estudiante = m.id_estudiante
                            LEFT JOIN calificacion c
                                ON c.id_entrega = ea.id
                            WHERE m.id_curso = :id_curso_det
                              AND m.id_institucion = :id_inst_det
                              AND m.anio = :anio_det
                              AND dac.id IS NOT NULL
                              AND a.id IS NOT NULL
                            ORDER BY m.id_estudiante, a.fecha_entrega DESC, a.id DESC";

        $stmtCalificaciones = $pdo->prepare($sqlCalificaciones);
        $stmtCalificaciones->bindValue(':id_docente_det', $idDocente, PDO::PARAM_INT);
        $stmtCalificaciones->bindValue(':id_docente_act_det', $idDocente, PDO::PARAM_INT);
        $stmtCalificaciones->bindValue(':id_inst_act_det', $idInstitucion, PDO::PARAM_INT);
        $stmtCalificaciones->bindValue(':id_curso_det', $idCurso, PDO::PARAM_INT);
        $stmtCalificaciones->bindValue(':id_inst_det', $idInstitucion, PDO::PARAM_INT);
        $stmtCalificaciones->bindValue(':anio_det', $anioActual, PDO::PARAM_INT);
        $stmtCalificaciones->execute();

        $calificacionesDb = $stmtCalificaciones->fetchAll(PDO::FETCH_ASSOC) ?: [];

        foreach ($calificacionesDb as $calificacionDb) {
            $idEstudianteCal = (int) ($calificacionDb['id_estudiante'] ?? 0);
            if ($idEstudianteCal <= 0 || !isset($calificacionesPorEstudiante[$idEstudianteCal])) {
                continue;
            }

            $idActividad = (int) ($calificacionDb['id_actividad'] ?? 0);

            $calificacionesPorEstudiante[$idEstudianteCal][] = [
                'id_actividad' => $idActividad,
                'id_entrega' => isset($calificacionDb['id_entrega']) ? (int) $calificacionDb['id_entrega'] : null,
                'titulo' => (string) ($calificacionDb['titulo'] ?? 'Actividad sin titulo'),
                'tipo' => (string) ($calificacionDb['tipo'] ?? 'Sin tipo'),
                'asignatura' => (string) ($calificacionDb['asignatura'] ?? 'Sin asignatura'),
                'fecha_limite' => (string) ($calificacionDb['fecha_limite'] ?? ''),
                'estado_entrega' => (string) ($calificacionDb['estado_entrega'] ?? 'Pendiente'),
                'nota' => $calificacionDb['nota'] !== null ? round((float) $calificacionDb['nota'], 2) : null,
                'observacion' => (string) ($calificacionDb['observacion'] ?? ''),
                'url_entregas' => BASE_URL . '/docente/ver-entregas?id_actividad=' . $idActividad,
            ];
        }
    } catch (Throwable $e) {
        error_log('Error preparando datos de estudiantes en detalle curso docente: ' . $e->getMessage());
    }

    $detalleActividadesPorAsignatura = [];
    $resumenCalificacionesPorAsignatura = [];

    foreach ($misAsignaturas as $asignatura) {
        $idAsignatura = (int) ($asignatura['id_asignatura'] ?? 0);
        if ($idAsignatura > 0) {
            $detalleActividadesPorAsignatura[$idAsignatura] = [];
            $resumenCalificacionesPorAsignatura[$idAsignatura] = [
                'total_actividades' => 0,
                'total_entregas' => 0,
                'total_calificadas' => 0,
                'promedio_general' => null,
                'actividades' => [],
            ];
        }
    }

    try {
        $sqlActividades = "SELECT
                            a.id,
                            a.id_asignatura,
                            a.titulo,
                            a.tipo,
                            a.estado,
                            a.fecha_entrega,
                            a.ponderacion,
                            COUNT(DISTINCT ea.id) AS total_entregas,
                            COUNT(DISTINCT c.id) AS total_calificadas,
                            AVG(c.nota) AS promedio_notas
                        FROM actividad a
                        INNER JOIN asignatura_curso ac ON a.id_asignatura_curso = ac.id
                        LEFT JOIN entrega_actividad ea ON ea.id_actividad = a.id
                        LEFT JOIN calificacion c ON c.id_entrega = ea.id
                        WHERE ac.id_curso = :id_curso
                          AND a.id_docente = :id_docente
                          AND a.id_institucion = :id_institucion
                        GROUP BY a.id, a.id_asignatura, a.titulo, a.tipo, a.estado, a.fecha_entrega, a.ponderacion
                        ORDER BY a.fecha_entrega DESC, a.id DESC";

        $stmtActividades = $pdo->prepare($sqlActividades);
        $stmtActividades->bindValue(':id_curso', $idCurso, PDO::PARAM_INT);
        $stmtActividades->bindValue(':id_docente', $idDocente, PDO::PARAM_INT);
        $stmtActividades->bindValue(':id_institucion', $idInstitucion, PDO::PARAM_INT);
        $stmtActividades->execute();

        $actividadesCurso = $stmtActividades->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $acumuladoresPromedio = [];

        foreach ($actividadesCurso as $actividad) {
            $idAsignaturaActividad = (int) ($actividad['id_asignatura'] ?? 0);

            if ($idAsignaturaActividad <= 0 || !isset($detalleActividadesPorAsignatura[$idAsignaturaActividad])) {
                continue;
            }

            $promedioActividad = $actividad['promedio_notas'] !== null ? round((float) $actividad['promedio_notas'], 2) : null;

            $actividadDetalle = [
                'id' => (int) $actividad['id'],
                'titulo' => (string) ($actividad['titulo'] ?? 'Actividad sin titulo'),
                'tipo' => (string) ($actividad['tipo'] ?? 'Sin tipo'),
                'estado' => (string) ($actividad['estado'] ?? 'activa'),
                'fecha_entrega' => (string) ($actividad['fecha_entrega'] ?? ''),
                'ponderacion' => isset($actividad['ponderacion']) ? (float) $actividad['ponderacion'] : 0,
                'total_entregas' => (int) ($actividad['total_entregas'] ?? 0),
                'total_calificadas' => (int) ($actividad['total_calificadas'] ?? 0),
                'promedio_notas' => $promedioActividad,
                'url_entregas' => BASE_URL . '/docente/ver-entregas?id_actividad=' . (int) $actividad['id'],
            ];

            $detalleActividadesPorAsignatura[$idAsignaturaActividad][] = $actividadDetalle;
            $resumenCalificacionesPorAsignatura[$idAsignaturaActividad]['total_actividades']++;
            $resumenCalificacionesPorAsignatura[$idAsignaturaActividad]['total_entregas'] += $actividadDetalle['total_entregas'];
            $resumenCalificacionesPorAsignatura[$idAsignaturaActividad]['total_calificadas'] += $actividadDetalle['total_calificadas'];
            $resumenCalificacionesPorAsignatura[$idAsignaturaActividad]['actividades'][] = $actividadDetalle;

            if ($promedioActividad !== null) {
                if (!isset($acumuladoresPromedio[$idAsignaturaActividad])) {
                    $acumuladoresPromedio[$idAsignaturaActividad] = [];
                }
                $acumuladoresPromedio[$idAsignaturaActividad][] = $promedioActividad;
            }
        }

        foreach ($acumuladoresPromedio as $idAsignaturaPromedio => $promedios) {
            if (!empty($promedios) && isset($resumenCalificacionesPorAsignatura[$idAsignaturaPromedio])) {
                $resumenCalificacionesPorAsignatura[$idAsignaturaPromedio]['promedio_general'] = round(array_sum($promedios) / count($promedios), 2);
            }
        }
    } catch (Throwable $e) {
        error_log('Error preparando modales de detalle curso docente: ' . $e->getMessage());
    }

    return [
        'usuario' => obtenerPerfilDocenteDesdeSesion(),
        'curso' => $curso,
        'anioActual' => $anioActual,
        'id_curso' => $idCurso,
        'id_docente' => $idDocente,
        'docente_info' => $docenteInfo,
        'nombre_docente' => trim((string) ($docenteInfo['nombres'] ?? '') . ' ' . (string) ($docenteInfo['apellidos'] ?? '')),
        'estudiantes' => $estudiantes,
        'mis_asignaturas' => $misAsignaturas,
        'totalEstudiantes' => $totalEstudiantes,
        'totalMisAsignaturas' => $totalMisAsignaturas,
        'actividadesPendientesCalificar' => $actividadesPendientesCalificar,
        'estudiantesEnRiesgo' => $estudiantesEnRiesgo,
        'proximasActividades' => $proximasActividades,
        'promedioGeneral' => $promedioGeneral,
        'perfilAcademicoPorEstudiante' => $perfilAcademicoPorEstudiante,
        'calificacionesPorEstudiante' => $calificacionesPorEstudiante,
        'detalleActividadesPorAsignatura' => $detalleActividadesPorAsignatura,
        'resumenCalificacionesPorAsignatura' => $resumenCalificacionesPorAsignatura,
    ] + obtenerVersionesAssetsDocente();
}
