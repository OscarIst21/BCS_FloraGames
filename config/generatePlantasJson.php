<?php
require_once __DIR__.'/init.php';
require_once __DIR__ . '/../connection/database.php';
$db = new Database();
$conn = $db->getConnection();
$stmt = $conn->prepare("SELECT * FROM ficha_planta");
$stmt->execute();
$plantas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ordenar alfabéticamente
usort($plantas, function($a, $b) {
    return strcmp(mb_strtolower($a['nombre_comun']), mb_strtolower($b['nombre_comun']));
});

file_put_contents(__DIR__.'/plantas.json', json_encode($plantas, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo "Archivo plantas.json generado correctamente.";