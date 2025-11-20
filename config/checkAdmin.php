<?php
// Verificar si el usuario tiene rol de administrador
function isAdmin() {
    return isset($_SESSION['rol']) && trim($_SESSION['rol']) === 'admin';
}

// Redirigir si no es admin
function requireAdmin() {
    if (!isset($_SESSION['user'])) {
        header("Location: /BCS_FloraGames/view/login.php");
        exit();
    }
    
    if (!isAdmin()) {
        // Para depuración temporal
        $_SESSION['error'] = 'Acceso denegado. Solo administradores pueden acceder a esta página.';
        header("Location: /BCS_FloraGames/index.php");
        exit();
    }
}
?>