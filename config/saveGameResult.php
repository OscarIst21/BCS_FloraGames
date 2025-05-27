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
    
    // Convertir duración de segundos a formato TIME (HH:MM:SS)
    $duracion_formatted = gmdate('H:i:s', $data['duracion']);
    
    // Preparar la consulta SQL para insertar en juego_usuario
    $stmt = $conn->prepare("
        INSERT INTO juego_usuario (usuario_id, fecha, duracion, fue_ganado)
        VALUES (:usuario_id, NOW(), :duracion, :fue_ganado)
    ");
    
    // Asignar valores
    $stmt->bindValue(':usuario_id', $data['usuario_id'], PDO::PARAM_INT);
    $stmt->bindValue(':duracion', $duracion_formatted, PDO::PARAM_STR);
    $stmt->bindValue(':fue_ganado', $data['fue_ganado'], PDO::PARAM_INT);
    
    // Ejecutar la consulta
    $stmt->execute();
    
    // Consultar si se asignó una nueva insignia
    $stmt = $conn->prepare('
        SELECT ui.insignia_id, i.nombre, i.descripcion, i.icono_url, ui.fecha_obtenida
        FROM usuario_insignias ui
        JOIN insignias i ON ui.insignia_id = i.id
        WHERE ui.usuario_id = :usuario_id
        ORDER BY ui.fecha_obtenida DESC, ui.insignia_id DESC
        LIMIT 1
    ');
    $stmt->bindValue(':usuario_id', $data['usuario_id'], PDO::PARAM_INT);
    $stmt->execute();
    $insignia = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Preparar respuesta
    $response = [
        'success' => true,
        'message' => 'Resultado guardado correctamente'
    ];
    
    // Incluir la insignia si existe
    if ($insignia) {
        // Verificar si la insignia es reciente comparando con las partidas ganadas
        $stmt = $conn->prepare('
            SELECT COUNT(*) AS partidas_ganadas
            FROM juego_usuario
            WHERE usuario_id = :usuario_id AND fue_ganado = 1
        ');
        $stmt->bindValue(':usuario_id', $data['usuario_id'], PDO::PARAM_INT);
        $stmt->execute();
        $partidas_ganadas = $stmt->fetchColumn();
        
        $insignia_esperada = ceil($partidas_ganadas / 10);
        // Incluir la insignia si su ID coincide con la esperada
        if ($insignia['insignia_id'] <= $insignia_esperada) {
            $response['insignia'] = [
                'insignia_id' => $insignia['insignia_id'],
                'nombre' => $insignia['nombre'],
                'descripcion' => $insignia['descripcion'],
                'icono_url' => $insignia['icono_url'],
                'fecha_obtenida' => $insignia['fecha_obtenida']
            ];
        }
    }
    
    // Guardar el timestamp del último guardado
    $_SESSION['ultimo_guardado'] = time();
    
    // Responder con JSON
    echo json_encode($response);
    
} catch (PDOException $e) {
    // Manejar error
    http_response_code(500);
    echo json_encode([
        'error' => 'Error al guardar el resultado',
        'details' => $e->getMessage()
    ]);
}
?>