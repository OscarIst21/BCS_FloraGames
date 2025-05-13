<?php
session_start();
require_once __DIR__ . '/../connection/database.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo "Usuario no autenticado";
    exit;
}

// Verificar si se recibieron los parámetros necesarios
if (!isset($_POST['points']) || !isset($_POST['game'])) {
    http_response_code(400);
    echo "Faltan parámetros requeridos";
    exit;
}

$userId = $_SESSION['usuario_id'];
$points = intval($_POST['points']);
$game = $_POST['game'];

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Actualizar puntos ganados del usuario
    $stmt = $conn->prepare("UPDATE usuarios SET puntos_ganados = puntos_ganados + ? WHERE id = ?");
    $stmt->execute([$points, $userId]);
    
    echo "Puntos actualizados correctamente";
} catch (PDOException $e) {
    http_response_code(500);
    echo "Error al actualizar puntos: " . $e->getMessage();
    exit;
}
?>