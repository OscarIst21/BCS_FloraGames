<?php
require_once __DIR__ . '/init.php';
require_once __DIR__ . '/../connection/database.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /BCS_FloraGames/view/login.php");
    exit();
}

$userId = $_SESSION['usuario_id'];
$nombre = $_POST['nombre'] ?? '';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Iniciar con la actualización del nombre
    $updateFields = ["nombre = :nombre"];
    $params = [':nombre' => $nombre, ':id' => $userId];
    
    // Verificar si se está actualizando la foto de perfil
    $fotoPerfilNueva = '';
    $actualizarFoto = false;
    
    // Caso 1: Se subió un archivo nuevo
    if (isset($_FILES['foto_perfil_upload']) && $_FILES['foto_perfil_upload']['error'] === 0) {
        $actualizarFoto = true;
        
        // Procesar la imagen subida
        $archivo = $_FILES['foto_perfil_upload'];
        $nombreArchivo = uniqid() . '_' . $archivo['name'];
        $rutaDestino = __DIR__ . '/../img/foto_de_perfil/' . $nombreArchivo;
        
        if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            $fotoPerfilNueva = $nombreArchivo;
        }
    }
    // Caso 2: Se seleccionó un avatar predefinido
    elseif (isset($_POST['avatar_seleccionado']) && !empty($_POST['avatar_seleccionado']) && $_POST['tipo_avatar'] === 'predefinido') {
        $actualizarFoto = true;
        $fotoPerfilNueva = $_POST['avatar_seleccionado'];
    }
    
    // Si se va a actualizar la foto, agregar al query
    if ($actualizarFoto) {
        $updateFields[] = "foto_perfil = :foto_perfil";
        $params[':foto_perfil'] = $fotoPerfilNueva;
    }
    
    // Actualizar el color de fondo si se proporcionó
    if (isset($_POST['color_fondo']) && !empty($_POST['color_fondo'])) {
        $updateFields[] = "color_fondo = :color_fondo";
        $params[':color_fondo'] = $_POST['color_fondo'];
    }
    
    // Construir y ejecutar la consulta SQL
    $sql = "UPDATE usuarios SET " . implode(", ", $updateFields) . " WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    
    // Actualizar el nombre en la sesión
    $_SESSION['user'] = $nombre;
    
    // Actualizar la foto de perfil en la sesión si se cambió
    if ($actualizarFoto) {
        $_SESSION['foto_perfil'] = $fotoPerfilNueva;
    }
    
    // Actualizar el color de fondo en la sesión si se cambió
    if (isset($_POST['color_fondo']) && !empty($_POST['color_fondo'])) {
        $_SESSION['color_fondo'] = $_POST['color_fondo'];
    }
    
    // Redirigir de vuelta al perfil
    header("Location: /BCS_FloraGames/view/myProfile.php?actualizado=1");
    exit();
    
} catch (PDOException $e) {
    // Registrar el error y redirigir con mensaje de error
    error_log("Error al actualizar perfil: " . $e->getMessage());
    header("Location: /BCS_FloraGames/view/myProfile.php?error=1");
    exit();
}
?>