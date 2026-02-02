<?php

/**
 * Helper para manejo de archivos y uploads
 */
class UploadHelper {
    
    // Configuración
    const MAX_FILE_SIZE = 10485760; // 10MB en bytes
    const ALLOWED_EXTENSIONS = ['pdf'];
    const ALLOWED_MIME_TYPES = ['application/pdf'];
    const BASE_UPLOAD_DIR = 'public/uploads/entregas';

    /**
     * Validar que el archivo sea PDF y cumpla con los requisitos
     */
    public static function validarArchivoPDF($file) {
        $errores = [];

        // Verificar que no haya errores en la subida
        if (!isset($file['error']) || is_array($file['error'])) {
            $errores[] = "Error en la subida del archivo";
            return $errores;
        }

        // Verificar códigos de error de PHP
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                $errores[] = "No se seleccionó ningún archivo";
                return $errores;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $errores[] = "El archivo excede el tamaño máximo permitido (10MB)";
                return $errores;
            default:
                $errores[] = "Error desconocido al subir el archivo";
                return $errores;
        }

        // Verificar tamaño del archivo
        if ($file['size'] > self::MAX_FILE_SIZE) {
            $errores[] = "El archivo excede el tamaño máximo permitido (10MB)";
        }

        // Verificar extensión
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            $errores[] = "Solo se permiten archivos PDF";
        }

        // Verificar MIME type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        
        if (!in_array($mimeType, self::ALLOWED_MIME_TYPES)) {
            $errores[] = "El archivo no es un PDF válido";
        }

        return $errores;
    }

    /**
     * Generar nombre único para el archivo
     * Formato: [id_actividad]_[timestamp].pdf
     */
    public static function generarNombreUnico($id_actividad) {
        $timestamp = time();
        return $id_actividad . '_' . $timestamp . '.pdf';
    }

    /**
     * Crear estructura de carpetas si no existe
     * Estructura: entregas/[inst]/[curso]/[asignatura]/[estudiante]/[actividad]/
     */
    public static function crearEstructuraCarpetas($institucion_info, $curso_info, $asignatura_info, $estudiante_info, $actividad_info) {
        $ruta_base = BASE_PATH . '/' . self::BASE_UPLOAD_DIR;
        
        // Sanitizar nombres para carpetas
        $carpeta_institucion = self::sanitizarNombreCarpeta($institucion_info['nombre']);
        $carpeta_curso = self::sanitizarNombreCarpeta($curso_info['grado'] . '_' . $curso_info['nombre']);
        $carpeta_asignatura = self::sanitizarNombreCarpeta($asignatura_info['nombre']);
        $carpeta_estudiante = self::sanitizarNombreCarpeta($estudiante_info['nombres'] . '_' . $estudiante_info['apellidos']);
        $carpeta_actividad = self::sanitizarNombreCarpeta($actividad_info['titulo']);
        
        $ruta_completa = $ruta_base . '/' . $carpeta_institucion . '/' . $carpeta_curso . '/' . $carpeta_asignatura . '/' . $carpeta_estudiante . '/' . $carpeta_actividad;

        // Crear carpetas recursivamente si no existen
        if (!file_exists($ruta_completa)) {
            if (!mkdir($ruta_completa, 0755, true)) {
                return false;
            }
        }

        return [
            'ruta_completa' => $ruta_completa,
            'carpeta_institucion' => $carpeta_institucion,
            'carpeta_curso' => $carpeta_curso,
            'carpeta_asignatura' => $carpeta_asignatura,
            'carpeta_estudiante' => $carpeta_estudiante,
            'carpeta_actividad' => $carpeta_actividad
        ];
    }

    /**
     * Guardar archivo en el servidor
     */
    public static function guardarArchivo($file, $ruta_destino, $nombre_archivo) {
        $ruta_completa = $ruta_destino . '/' . $nombre_archivo;

        // Mover archivo temporal a la ubicación final
        if (move_uploaded_file($file['tmp_name'], $ruta_completa)) {
            // Cambiar permisos del archivo
            chmod($ruta_completa, 0644);
            return true;
        }

        return false;
    }

    /**
     * Eliminar archivo del servidor
     */
    public static function eliminarArchivo($ruta_archivo) {
        $ruta_completa = BASE_PATH . '/' . $ruta_archivo;
        
        if (file_exists($ruta_completa)) {
            return unlink($ruta_completa);
        }

        return false;
    }

    /**
     * Generar ruta relativa para guardar en la base de datos
     */
    public static function generarRutaRelativa($carpeta_institucion, $carpeta_curso, $carpeta_asignatura, $carpeta_estudiante, $carpeta_actividad, $nombre_archivo) {
        return self::BASE_UPLOAD_DIR . '/' . $carpeta_institucion . '/' . $carpeta_curso . '/' . $carpeta_asignatura . '/' . $carpeta_estudiante . '/' . $carpeta_actividad . '/' . $nombre_archivo;
    }

    /**
     * Obtener tamaño formateado del archivo
     */
    public static function formatearTamano($bytes) {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Validar que el archivo existe en el servidor
     */
    public static function archivoExiste($ruta_relativa) {
        $ruta_completa = BASE_PATH . '/' . $ruta_relativa;
        return file_exists($ruta_completa);
    }

    /**
     * Limpiar nombre de archivo (sanitizar)
     */
    public static function sanitizarNombreArchivo($nombre) {
        // Eliminar caracteres especiales y espacios
        $nombre = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $nombre);
        return $nombre;
    }

    /**
     * Sanitizar nombre de carpeta (sin puntos)
     */
    public static function sanitizarNombreCarpeta($nombre) {
        // Remover acentos
        $nombre = iconv('UTF-8', 'ASCII//TRANSLIT', $nombre);
        // Eliminar caracteres especiales, solo letras, números, guiones y guiones bajos
        $nombre = preg_replace('/[^A-Za-z0-9_\-]/', '_', $nombre);
        // Eliminar múltiples guiones bajos consecutivos
        $nombre = preg_replace('/_+/', '_', $nombre);
        // Eliminar guiones bajos al inicio y final
        $nombre = trim($nombre, '_');
        return $nombre;
    }
}
