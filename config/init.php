<?php
// Definir constante con la ruta base del proyecto
define('BASE_PATH', realpath(__DIR__ . '/..'));

// Manejo de sesiones
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuraciones adicionales
date_default_timezone_set('America/Mexico_City');
mb_internal_encoding('UTF-8');

// Función para incluir archivos de manera segura
function requireSafe($relativePath) {
    $absolutePath = BASE_PATH . '/' . ltrim($relativePath, '/');
    if (file_exists($absolutePath)) {
        return require_once $absolutePath;
    }
    throw new Exception("Archivo no encontrado: " . $absolutePath);
}
?>