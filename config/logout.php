<?php
// Solo iniciar sesión si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Destruir todas las variables de sesión
$_SESSION = array();

// Borrar la cookie de sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

// Destruir la sesión
session_destroy();

// Borrar cookie de "recordarme" si existe
setcookie('remember_me', '', time() - 3600, "/");

// Redirigir al login con mensaje
$_SESSION['sweet_alert'] = [
    'type' => 'success',
    'title' => 'Sesión cerrada',
    'text' => 'Has cerrado sesión correctamente.'
];

header("Location: /BCS_FloraGames/view/login.php");
exit();
?>