<?php
require_once '../connection/database.php';
require_once 'init.php';
require_once 'checkAdmin.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $conn = $db->getConnection();
    
    try {
        $nombre_comun = $_POST['nombre_comun'];
        $nombre_cientifico = $_POST['nombre_cientifico'];
        $distribucion = $_POST['distribucion'] ?? '';
        $situacion = $_POST['situacion'];
        $usos = isset($_POST['usos']) ? implode(', ', $_POST['usos']) : '';
        $caracteristicas = $_POST['caracteristicas'] ?? '';
        $habitat = $_POST['habitat'] ?? '';
        $curiosidad = $_POST['curiosidad'] ?? '';
        
        // Manejar archivos
        $foto = '';
        $audio = '';
        
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
            $foto = $nombre_comun . '.png';
            move_uploaded_file($_FILES['foto']['tmp_name'], '../img/plantas/' . $foto);
        }
        
        if (isset($_FILES['audio']) && $_FILES['audio']['error'] === 0) {
            $audio = $nombre_comun . '.wav';
            move_uploaded_file($_FILES['audio']['tmp_name'], '../assets/plantas/' . $audio);
        }
        
        $stmt = $conn->prepare("INSERT INTO ficha_planta (nombre_comun, nombre_cientifico, distribucion, situación, usos, caracteristicas, habitat, curiosidad, foto, audio) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nombre_comun, $nombre_cientifico, $distribucion, $situacion, $usos, $caracteristicas, $habitat, $curiosidad, $foto, $audio]);
        
        // Actualizar JSON
        include 'generatePlantasJson.php';
        
        $_SESSION['success'] = 'Planta agregada exitosamente';
        
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Error al agregar la planta: ' . $e->getMessage();
    }
}

header("Location: ../view/admin/index.php");
exit();
?>