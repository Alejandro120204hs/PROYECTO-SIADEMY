<?php

require_once __DIR__ . '/../perfil.php';
require_once __DIR__ . '/../../models/administradores/estudiante.php';
require_once __DIR__ . '/../../models/administradores/acudiente.php';
require_once __DIR__ . '/../../models/administradores/docente.php';
require_once __DIR__ . '/../../models/administradores/eventos.php';
require_once __DIR__ . '/../../models/administradores/cursos.php';
require_once __DIR__ . '/../../models/administradores/asignatura.php';
require_once __DIR__ . '/../../models/administradores/matricula.php';
require_once __DIR__ . '/../../models/administradores/periodo.php';
require_once __DIR__ . '/../../models/administradores/docente_asignatura.php';

function obtenerPerfilAdminDesdeSesion()
{
    $idUsuario = $_SESSION['user']['id'] ?? 0;
    return mostrarPerfil($idUsuario);
}

function obtenerVersionesAssetsAdmin()
{
    return [
        'adminCssVersion' => @filemtime(BASE_PATH . '/public/assets/dashboard/css/styles-admin.css') ?: time(),
        'mainAdminJsVersion' => @filemtime(BASE_PATH . '/public/assets/dashboard/js/main-admin.js') ?: time(),
    ];
}

function obtenerDataVistaAdminDashboard()
{
    $idInstitucion = (int) ($_SESSION['user']['id_institucion'] ?? 0);

    $objEstudiante = new Estudiante();
    $objAcudiente = new Acudiente();
    $objDocente = new Docente();
    $objEvento = new Evento();
    $objCurso = new Curso();
    $objAsignatura = new Asignatura();

    $totalEstudiantes = (int) $objEstudiante->contar($idInstitucion);
    $totalAcudientes = (int) $objAcudiente->contar($idInstitucion);
    $totalProfesores = (int) $objDocente->contar($idInstitucion);
    $totalEventos = (int) $objEvento->contar($idInstitucion);
    $totalCursos = (int) $objCurso->contar($idInstitucion);
    $totalAsignaturas = (int) $objAsignatura->contar($idInstitucion);

    $eventosInstitucion = $objEvento->listar($idInstitucion);
    $mesesAbreviados = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    $anioActual = (int) date('Y');
    $anioAnterior = $anioActual - 1;
    $serieAnioActual = array_fill(0, 12, 0);
    $serieAnioAnterior = array_fill(0, 12, 0);

    $hoy = new DateTimeImmutable('today');
    $inicioSemanaActual = $hoy->modify('monday this week');
    $finSemanaActual = $hoy->modify('sunday this week');
    $inicioSemanaAnterior = $inicioSemanaActual->modify('-7 days');
    $finSemanaAnterior = $inicioSemanaActual->modify('-1 day');
    $totalSemanaActual = 0;
    $totalSemanaAnterior = 0;
    $eventosCalendario = [];

    foreach ($eventosInstitucion as $evento) {
        $fechaCruda = (string) ($evento['fecha_evento'] ?? '');
        if ($fechaCruda === '') {
            continue;
        }

        $fechaEvento = DateTimeImmutable::createFromFormat('Y-m-d', substr($fechaCruda, 0, 10));
        if (!$fechaEvento) {
            continue;
        }

        $anioEvento = (int) $fechaEvento->format('Y');
        $mesEvento = (int) $fechaEvento->format('n') - 1;

        if ($anioEvento === $anioActual && isset($serieAnioActual[$mesEvento])) {
            $serieAnioActual[$mesEvento]++;
        }

        if ($anioEvento === $anioAnterior && isset($serieAnioAnterior[$mesEvento])) {
            $serieAnioAnterior[$mesEvento]++;
        }

        if ($fechaEvento >= $inicioSemanaActual && $fechaEvento <= $finSemanaActual) {
            $totalSemanaActual++;
        }

        if ($fechaEvento >= $inicioSemanaAnterior && $fechaEvento <= $finSemanaAnterior) {
            $totalSemanaAnterior++;
        }

        $eventosCalendario[] = [
            'id' => (int) ($evento['id'] ?? 0),
            'title' => (string) ($evento['nombre_evento'] ?? 'Evento academico'),
            'date' => $fechaEvento->format('Y-m-d'),
            'timeStart' => !empty($evento['hora_inicio']) ? substr((string) $evento['hora_inicio'], 0, 5) : '',
            'timeEnd' => !empty($evento['hora_fin']) ? substr((string) $evento['hora_fin'], 0, 5) : '',
            'type' => (string) ($evento['tipo_evento'] ?? 'evento'),
            'location' => (string) ($evento['ubicacion'] ?? ''),
        ];
    }

    usort($eventosCalendario, function ($a, $b) {
        $claveA = ($a['date'] ?? '') . ' ' . ($a['timeStart'] ?? '');
        $claveB = ($b['date'] ?? '') . ' ' . ($b['timeStart'] ?? '');
        return strcmp($claveA, $claveB);
    });

    $dashboardData = [
        'chart' => [
            'labels' => $mesesAbreviados,
            'currentYear' => $anioActual,
            'previousYear' => $anioAnterior,
            'currentSeries' => $serieAnioActual,
            'previousSeries' => $serieAnioAnterior,
        ],
        'totals' => [
            'currentWeek' => $totalSemanaActual,
            'previousWeek' => $totalSemanaAnterior,
        ],
        'calendar' => [
            'events' => $eventosCalendario,
        ],
    ];

    return [
        'usuario' => obtenerPerfilAdminDesdeSesion(),
        'totalEstudiantes' => $totalEstudiantes,
        'totalAcudientes' => $totalAcudientes,
        'totalProfesores' => $totalProfesores,
        'totalEventos' => $totalEventos,
        'totalCursos' => $totalCursos,
        'totalAsignaturas' => $totalAsignaturas,
        'totalSemanaActual' => $totalSemanaActual,
        'totalSemanaAnterior' => $totalSemanaAnterior,
        'dashboardData' => $dashboardData,
    ] + obtenerVersionesAssetsAdmin();
}

function obtenerDataVistaAdminCursos()
{
    $idInstitucion = (int) ($_SESSION['user']['id_institucion'] ?? 0);

    $objCurso = new Curso();
    $objEstudiante = new Estudiante();
    $objDocente = new Docente();

    return [
        'usuario' => obtenerPerfilAdminDesdeSesion(),
        'datos' => $objCurso->listar($idInstitucion),
        'totalCursos' => (int) $objCurso->contar($idInstitucion),
        'totalEstudiantes' => (int) $objEstudiante->contar($idInstitucion),
        'totalProfesores' => (int) $objDocente->contar($idInstitucion),
    ] + obtenerVersionesAssetsAdmin();
}

function obtenerDataVistaAdminAsignaturas()
{
    $idInstitucion = (int) ($_SESSION['user']['id_institucion'] ?? 0);

    $objAsignatura = new Asignatura();
    $objDocente = new Docente();

    return [
        'usuario' => obtenerPerfilAdminDesdeSesion(),
        'asignaturas' => $objAsignatura->listar($idInstitucion),
        'totalAsignaturas' => (int) $objAsignatura->contar($idInstitucion),
        'totalProfesores' => (int) $objDocente->contar($idInstitucion),
    ] + obtenerVersionesAssetsAdmin();
}

function obtenerDataVistaAdminMatriculas()
{
    $idInstitucion = (int) ($_SESSION['user']['id_institucion'] ?? 0);
    $datos = (new Matricula())->listar($idInstitucion);

    $totalMatriculas = count($datos);
    $cursosUnicos = array_values(array_unique(array_column($datos, 'id_curso')));
    $estudiantesUnicos = array_values(array_unique(array_column($datos, 'id_estudiante')));

    $anios = array_values(array_unique(array_column($datos, 'anio')));
    rsort($anios);

    $cursos = [];
    foreach ($datos as $dato) {
        $clave = ($dato['grado'] ?? '') . ' - ' . ($dato['nombre_curso'] ?? '');
        $cursos[$clave] = $dato['id_curso'];
    }
    ksort($cursos);

    $niveles = array_values(array_unique(array_column($datos, 'nivel_academico')));
    sort($niveles);

    return [
        'usuario' => obtenerPerfilAdminDesdeSesion(),
        'datos' => $datos,
        'totalMatriculas' => $totalMatriculas,
        'totalCursosActivosConMatricula' => count($cursosUnicos),
        'totalEstudiantesMatriculados' => count($estudiantesUnicos),
        'aniosFiltro' => $anios,
        'cursosFiltro' => $cursos,
        'nivelesFiltro' => $niveles,
    ];
}

function obtenerDataVistaAdminPeriodo()
{
    $idInstitucion = (int) ($_SESSION['user']['id_institucion'] ?? 0);
    $objPeriodo = new Periodo();

    $kpis = $objPeriodo->obtenerKPIs($idInstitucion);
    $periodoActivo = $objPeriodo->obtenerPeriodoActivo($idInstitucion);
    $todosLosPeriodos = $objPeriodo->listar($idInstitucion);

    $anosDisponibles = [];
    foreach ($todosLosPeriodos as $periodo) {
        if (!in_array($periodo['ano_lectivo'], $anosDisponibles, true)) {
            $anosDisponibles[] = $periodo['ano_lectivo'];
        }
    }
    sort($anosDisponibles, SORT_NUMERIC);
    $anosDisponibles = array_reverse($anosDisponibles);

    $anoActual = isset($_GET['ano']) ? (string) $_GET['ano'] : (string) (end($anosDisponibles) ?: date('Y'));

    $periodosDelAno = array_values(array_filter($todosLosPeriodos, function ($p) use ($anoActual) {
        return (string) ($p['ano_lectivo'] ?? '') === (string) $anoActual;
    }));

    $periodoActivoResumen = null;
    if ($periodoActivo) {
        $inicio = new DateTime($periodoActivo['fecha_inicio']);
        $fin = new DateTime($periodoActivo['fecha_fin']);
        $ahora = new DateTime();

        $diasRestantes = (int) $ahora->diff($fin)->days;
        $totalDias = max(1, (int) $inicio->diff($fin)->days);
        $diasRecorridos = (int) $inicio->diff($ahora)->days;
        $porcentaje = (int) max(0, min(100, round(($diasRecorridos / $totalDias) * 100)));

        $periodoActivoResumen = [
            'nombre' => $periodoActivo['nombre'] ?? '',
            'ano_lectivo' => $periodoActivo['ano_lectivo'] ?? '',
            'fecha_inicio_fmt' => date('j M Y', strtotime($periodoActivo['fecha_inicio'])),
            'fecha_fin_fmt' => date('j M Y', strtotime($periodoActivo['fecha_fin'])),
            'total_dias' => $totalDias,
            'dias_restantes' => $diasRestantes,
            'porcentaje' => $porcentaje,
        ];
    }

    $periodosRender = [];
    foreach ($periodosDelAno as $periodo) {
        $estado = $periodo['estado'] ?? 'planificado';
        $activo = (int) ($periodo['activo'] ?? 0) === 1;

        $inicio = new DateTime($periodo['fecha_inicio']);
        $fin = new DateTime($periodo['fecha_fin']);
        $diasDiferencia = (int) $inicio->diff($fin)->days;

        if ($estado === 'en_curso') {
            $estadoHtml = '<i class="ri-radio-button-line"></i> Activo';
        } elseif ($estado === 'planificado') {
            $estadoHtml = '<i class="ri-time-line"></i> Proximo';
        } else {
            $estadoHtml = '<i class="ri-checkbox-circle-fill"></i> Finalizado';
        }

        $periodosRender[] = [
            'raw' => $periodo,
            'estado' => $estado,
            'activo' => $activo,
            'dias_diferencia' => $diasDiferencia,
            'fecha_inicio_fmt' => date('j M Y', strtotime($periodo['fecha_inicio'])),
            'fecha_fin_fmt' => date('j M Y', strtotime($periodo['fecha_fin'])),
            'estado_html' => $estadoHtml,
            'progreso_activo' => $periodoActivoResumen['porcentaje'] ?? 50,
        ];
    }

    return [
        'usuario' => obtenerPerfilAdminDesdeSesion(),
        'kpis' => $kpis,
        'periodoActivoResumen' => $periodoActivoResumen,
        'anosDisponibles' => $anosDisponibles,
        'anoActual' => $anoActual,
        'periodosRender' => $periodosRender,
        'periodosCount' => count($periodosRender),
    ] + obtenerVersionesAssetsAdmin() + [
        'periodosCssVersion' => @filemtime(BASE_PATH . '/public/assets/dashboard/css/styles-periodos.css') ?: time(),
    ];
}

function obtenerDataVistaAdminDetalleCurso($idCurso)
{
    $idCurso = (int) $idCurso;
    $curso = (new Curso())->listarCursoId($idCurso);

    $anioActual = date('Y');
    $estudiantes = (new Matricula())->listarPorCurso($idCurso, $anioActual);
    $asignaturas = (new DocenteAsignatura())->obtenerAsignaturasPorCurso($idCurso);

    $totalEstudiantes = count($estudiantes);
    $cupoMaximo = max(0, (int) ($curso['cupo_maximo'] ?? 0));
    $cupoDisponible = max(0, $cupoMaximo - $totalEstudiantes);
    $porcentajeOcupacion = $cupoMaximo > 0 ? ($totalEstudiantes / $cupoMaximo) * 100 : 0;

    return [
        'usuario' => obtenerPerfilAdminDesdeSesion(),
        'curso' => $curso,
        'estudiantes' => $estudiantes,
        'asignaturas' => $asignaturas,
        'totalEstudiantes' => $totalEstudiantes,
        'cupoDisponible' => $cupoDisponible,
        'porcentajeOcupacion' => $porcentajeOcupacion,
    ];
}
