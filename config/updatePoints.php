<?php
session_start();
require_once __DIR__ . '/../connection/database.php';

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Usuario no autenticado"]);
    exit;
}

if (!isset($_POST['points']) || !isset($_POST['game'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Faltan parámetros requeridos"]);
    exit;
}

$userId = $_SESSION['usuario_id'];
$points = intval($_POST['points']);
$game = $_POST['game'];

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // 1. Obtener datos actuales del usuario
    $stmt = $conn->prepare("SELECT puntos_ganados, nivel_de_usuario_id FROM usuarios WHERE id = ?");
    $stmt->execute([$userId]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $currentPoints = $userData['puntos_ganados'];
    $currentLevel = $userData['nivel_de_usuario_id'];
    
    // 2. Actualizar puntos (el trigger se encargará del nivel)
    $newPoints = $currentPoints + $points;
    $stmt = $conn->prepare("UPDATE usuarios SET puntos_ganados = ? WHERE id = ?");
    $stmt->execute([$newPoints, $userId]);
    
    // 3. Obtener el nuevo nivel después de la actualización
    $stmt = $conn->prepare("SELECT nivel_de_usuario_id FROM usuarios WHERE id = ?");
    $stmt->execute([$userId]);
    $newLevelData = $stmt->fetch(PDO::FETCH_ASSOC);
    $newLevel = $newLevelData['nivel_de_usuario_id'];
    
    // 4. Obtener información del nivel
    $stmt = $conn->prepare("SELECT nombre, imagen FROM nivel_de_usuario WHERE id = ?");
    $stmt->execute([$newLevel]);
    $levelInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $levelUp = ($newLevel > $currentLevel);
    
    header('Content-Type: application/json');
    echo json_encode([
        "success" => true,
        "message" => "Puntos actualizados correctamente",
        "levelUp" => $levelUp,
        "newLevel" => $newLevel,
        "levelName" => $levelInfo['nombre'],
        "levelImage" => $levelInfo['imagen'],
        "points" => $newPoints
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error al actualizar puntos: " . $e->getMessage()]);
    exit;
}
?>