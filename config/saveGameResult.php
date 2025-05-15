<?php
require_once __DIR__ . '/init.php';
require_once __DIR__ . '/../connection/database.php';

// Verificar si es una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// Obtener datos JSON del cuerpo de la petición
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

// Verificar si los datos son válidos
if (!$data || !isset($data['usuario_id']) || !isset($data['duracion']) || !isset($data['fue_ganado'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos incompletos']);
    exit;
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_id'] != $data['usuario_id']) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuario no autorizado']);
    exit;
}

// Verificar si ya se ha guardado un resultado similar recientemente (en los últimos 5 segundos)
if (isset($_SESSION['ultimo_guardado']) && (time() - $_SESSION['ultimo_guardado']) < 5) {
    // Si se ha guardado un resultado similar recientemente, responder con éxito pero sin guardar
    echo json_encode([
        'success' => true,
        'message' => 'Resultado ya registrado recientemente'
    ]);
    exit;
}

try {
    // Conectar a la base de datos
    $db = new Database();
    $conn = $db->getConnection();
    
    // Preparar la consulta SQL
    $stmt = $conn->prepare("INSERT INTO juego_usuario (usuario_id, fecha, duracion, fue_ganado) VALUES (:usuario_id, NOW(), :duracion, :fue_ganado)");
    
    // Asignar valores
    $stmt->bindValue(':usuario_id', $data['usuario_id'], PDO::PARAM_INT);
    $stmt->bindValue(':duracion', $data['duracion'], PDO::PARAM_INT);
    $stmt->bindValue(':fue_ganado', $data['fue_ganado'], PDO::PARAM_INT);
    
    // Ejecutar la consulta
    $stmt->execute();
    
    // Guardar el timestamp del último guardado
    $_SESSION['ultimo_guardado'] = time();
    
    // Responder con éxito
    echo json_encode([
        'success' => true,
        'message' => 'Resultado guardado correctamente'
    ]);
    
} catch (PDOException $e) {
    // Manejar error
    http_response_code(500);
    echo json_encode([
        'error' => 'Error al guardar el resultado',
        'details' => $e->getMessage()
    ]);
}
?>