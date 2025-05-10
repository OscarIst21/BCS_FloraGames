<?php
session_start();

// Verificar si hay una cookie de "recordarme"
if (!isset($_SESSION['user']) && isset($_COOKIE['remember_me'])) {
    require_once '../connection/database.php';
    $db = new Database();
    $conn = $db->getConnection();
    
    list($user_id, $token) = explode(':', $_COOKIE['remember_me']);
    
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && hash('sha256', $user['contrasena']) === $token) {
        $_SESSION['user'] = $user;
    } else {
        // Cookie inválida, borrarla
        setcookie('remember_me', '', time() - 3600, "/");
    }
}

// Protección de rutas (ejemplo para páginas que requieren login)
function requireLogin() {
    if (!isset($_SESSION['user'])) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        $_SESSION['sweet_alert'] = [
            'type' => 'error',
            'title' => 'Acceso restringido',
            'text' => 'Debes iniciar sesión para acceder a esta página'
        ];
        header("Location: /BCS_FloraGames/view/login.php");
        exit();
    }
}

// Protección de rutas para usuarios no logueados (como el login)
function requireNoLogin() {
    if (isset($_SESSION['user'])) {
        header("Location: /BCS_FloraGames/index.php");
        exit();
    }
}
?>