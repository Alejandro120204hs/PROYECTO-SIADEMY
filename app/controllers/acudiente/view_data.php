<?php

/**
 * VIEW DATA - ACUDIENTE
 * Funciones auxiliares que preparan los datos para las vistas del rol Acudiente.
 * Sigue la misma estructura que app/controllers/estudiante/view_data.php
 */

require_once __DIR__ . '/../perfil.php';                         // mostrarPerfil()
require_once __DIR__ . '/../../models/acudiente/estudiante.php'; // EstudianteAcudiente

function obtenerPerfilAcudienteDesdeSesion()
{
    $idUsuario = (int)($_SESSION['user']['id'] ?? 0);
    return mostrarPerfil($idUsuario);
}

/**
 * Obtiene el id de la tabla `acudiente` a partir de la sesión actual.
 * Si la sesión es anterior a que el login empezara a guardar
 * `id_acudiente`, se resuelve consultando la BD y se cachea en sesión.
 */
function acudienteObtenerIdDesdeSesion()
{
    if (!empty($_SESSION['user']['id_acudiente'])) {
        return (int)$_SESSION['user']['id_acudiente'];
    }

    $idUsuario = (int)($_SESSION['user']['id'] ?? 0);
    if ($idUsuario <= 0) {
        return 0;
    }

    require_once __DIR__ . '/../../../config/database.php';
    $db = new Conexion();
    $conn = $db->getConexion();

    try {
        $stmt = $conn->prepare("SELECT id FROM acudiente WHERE id_usuario = :id_usuario");
        $stmt->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        $acudiente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($acudiente) {
            $_SESSION['user']['id_acudiente'] = (int)$acudiente['id'];
            return (int)$acudiente['id'];
        }
    } catch (PDOException $e) {
        error_log("Error al resolver id_acudiente desde sesión -> " . $e->getMessage());
    }

    return 0;
}

/**
 * Resuelve el estudiante actualmente seleccionado por el acudiente.
 * Si no hay selección previa o la selección ya no pertenece a la lista
 * de estudiantes asociados, se usa el primero y se guarda en sesión.
 *
 * @param array $estudiantes Lista de estudiantes asociados (de obtenerEstudiantesAsociados)
 * @return array|null
 */
function acudienteObtenerEstudianteSeleccionado(array $estudiantes)
{
    if (empty($estudiantes)) {
        unset($_SESSION['acudiente']['id_estudiante_seleccionado']);
        return null;
    }

    $idSeleccionado = (int)($_SESSION['acudiente']['id_estudiante_seleccionado'] ?? 0);

    foreach ($estudiantes as $estudiante) {
        if ((int)$estudiante['id'] === $idSeleccionado) {
            return $estudiante;
        }
    }

    $primero = $estudiantes[0];
    $_SESSION['acudiente']['id_estudiante_seleccionado'] = (int)$primero['id'];
    return $primero;
}

/**
 * Prepara todos los datos necesarios para la vista /acudiente/dashboard.
 *
 * Variables devueltas:
 *   - usuario                 -> perfil del acudiente (nombre, foto, institución)
 *   - estudiantesAsociados    -> lista de estudiantes asociados al acudiente
 *   - estudianteSeleccionado  -> estudiante actualmente activo (o null)
 *
 * @return array
 */
function obtenerDataVistaAcudienteDashboard()
{
    $idInstitucion = (int)($_SESSION['user']['id_institucion'] ?? 0);
    $idAcudiente   = acudienteObtenerIdDesdeSesion();
    $anio          = (int)date('Y');

    $estudianteModel = new EstudianteAcudiente();

    $estudiantesAsociados = $estudianteModel->obtenerEstudiantesAsociados(
        $idAcudiente, $idInstitucion, $anio
    );

    $estudianteSeleccionado = acudienteObtenerEstudianteSeleccionado($estudiantesAsociados);

    return [
        'usuario'                => obtenerPerfilAcudienteDesdeSesion(),
        'estudiantesAsociados'   => $estudiantesAsociados,
        'estudianteSeleccionado' => $estudianteSeleccionado,
    ];
}
