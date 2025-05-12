<?php
session_start();
require_once __DIR__ . '/../connection/database.php';

if (!isset($_SESSION['usuario_id']) || !isset($_POST['points']) || !isset($_POST['game'])) {
    http_response_code(400);
    exit('Invalid request');
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Insert game points record
    $stmt = $conn->prepare("INSERT INTO puntuaciones (usuario_id, juego, puntos, fecha) VALUES (?, ?, ?, NOW())");
    $stmt->execute([
        $_SESSION['usuario_id'],
        $_POST['game'],
        (int)$_POST['points']
    ]);

    // Update user's total points
    $stmt = $conn->prepare("UPDATE usuarios SET puntos = puntos + ? WHERE id = ?");
    $stmt->execute([
        (int)$_POST['points'],
        $_SESSION['usuario_id']
    ]);

    http_response_code(200);
    echo 'Success';
} catch (PDOException $e) {
    error_log("Error updating points: " . $e->getMessage());
    http_response_code(500);
    echo 'Error updating points';
}
