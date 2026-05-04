<?php
require_once '../connection/database.php';
require_once 'init.php';
require_once 'checkAdmin.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $conn = $db->getConnection();
    
    try {
        $id = $_POST['id'];
        
        // Obtener archivos para eliminar
        $stmt = $conn->prepare("SELECT foto, audio FROM ficha_planta WHERE id = ?");
        $stmt->execute([$id]);
        $planta = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Eliminar de la base de datos
        $stmt = $conn->prepare("DELETE FROM ficha_planta WHERE id = ?");
        $stmt->execute([$id]);
        
        // Eliminar archivos físicos
        if ($planta['foto'] && file_exists('../img/plantas/' . $planta['foto'])) {
            unlink('../img/plantas/' . $planta['foto']);
        }
        if ($planta['audio'] && file_exists('../assets/plantas/' . $planta['audio'])) {
            unlink('../assets/plantas/' . $planta['audio']);
        }
        
        // Actualizar JSON
        include 'generatePlantasJson.php';
        
        $_SESSION['success'] = 'Planta eliminada exitosamente';
        
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Error al eliminar la planta: ' . $e->getMessage();
    }
}

header("Location: ../view/admin/index.php");
exit();
?>