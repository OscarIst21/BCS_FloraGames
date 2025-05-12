<?php
session_start();
require_once __DIR__ . '/../connection/database.php';

if (!isset($_SESSION['usuario_id']) || !isset($_POST['music_enabled'])) {
    http_response_code(400);
    exit('Invalid request');
}

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("UPDATE usuarios SET musica_activada = ? WHERE id = ?");
    $stmt->execute([
        (int)$_POST['music_enabled'],
        $_SESSION['usuario_id']
    ]);
    
    http_response_code(200);
    echo 'Success';
} catch (PDOException $e) {
    error_log("Error updating music preference: " . $e->getMessage());
    http_response_code(500);
    echo 'Error updating preference';
}
