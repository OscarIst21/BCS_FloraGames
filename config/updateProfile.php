<?php
require_once __DIR__.'/init.php';
require_once __DIR__.'/../connection/database.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user'])) {
    header("Location: /BCS_FloraGames/view/login.php");
    exit();
}

$userId = $_SESSION['usuario_id'];
$updateSuccess = false;
$message = '';

try {
    // Usar la clase Database para la conexión
    $db = new Database();
    $conn = $db->getConnection();
    
    // Actualizar nombre si se envió
    if (isset($_POST['nombre']) && !empty($_POST['nombre'])) {
        $nombre = trim($_POST['nombre']);
        
        $stmt = $conn->prepare("UPDATE usuarios SET nombre = :nombre WHERE id = :id");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        
        // Actualizar la sesión
        $_SESSION['user'] = $nombre;
        $updateSuccess = true;
    }
    
    // Procesar avatar
    $fotoPerfilActualizada = false;
    
    // Caso 1: Avatar predefinido seleccionado
    if (isset($_POST['tipo_avatar']) && $_POST['tipo_avatar'] === 'predefinido' && !empty($_POST['avatar_seleccionado'])) {
        $avatarSeleccionado = $_POST['avatar_seleccionado'];
        $colorFondo = isset($_POST['color_fondo']) ? $_POST['color_fondo'] : '#f0f0f0';
        
        // Validar que el avatar existe
        $avatarPath = __DIR__ . '/../img/foto_de_perfil/' . $avatarSeleccionado;
        if (file_exists($avatarPath)) {
            // Guardar información en la base de datos
            $stmt = $conn->prepare("UPDATE usuarios SET foto_perfil = :foto, color_fondo = :color WHERE id = :id");
            $stmt->bindParam(':foto', $avatarSeleccionado);
            $stmt->bindParam(':color', $colorFondo);
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            
            // Actualizar la sesión con la nueva foto y color
            $_SESSION['foto_perfil'] = $avatarSeleccionado;
            $_SESSION['color_fondo'] = $colorFondo;
            
            $fotoPerfilActualizada = true;
            $updateSuccess = true;
        }
    }
    
    // Caso 2: Imagen personalizada subida
    elseif (isset($_FILES['foto_perfil_upload']) && $_FILES['foto_perfil_upload']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['foto_perfil_upload']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            // Crear nombre único para el archivo
            $newFilename = 'user_' . $userId . '_' . time() . '.' . $ext;
            $uploadPath = __DIR__ . '/../img/foto_de_perfil/' . $newFilename;
            
            // Mover el archivo subido
            if (move_uploaded_file($_FILES['foto_perfil_upload']['tmp_name'], $uploadPath)) {
                // Actualizar en la base de datos
                $stmt = $conn->prepare("UPDATE usuarios SET foto_perfil = :foto, color_fondo = NULL WHERE id = :id");
                $stmt->bindParam(':foto', $newFilename);
                $stmt->bindParam(':id', $userId);
                $stmt->execute();
                
                // Actualizar la sesión con la nueva foto
                $_SESSION['foto_perfil'] = $newFilename;
                $_SESSION['color_fondo'] = null; // Resetear el color de fondo en la sesión
                
                $fotoPerfilActualizada = true;
                $updateSuccess = true;
            } else {
                $message = 'Error al subir la imagen.';
            }
        } else {
            $message = 'Formato de imagen no permitido. Use JPG, PNG o GIF.';
        }
    }
    
    // Mensaje de éxito o error
    if ($updateSuccess) {
        $_SESSION['sweet_alert'] = [
            'type' => 'success',
            'title' => '¡Perfil actualizado!',
            'text' => 'Los cambios se han guardado correctamente.'
        ];
    } else if (!empty($message)) {
        $_SESSION['sweet_alert'] = [
            'type' => 'error',
            'title' => 'Error',
            'text' => $message
        ];
    }
    
} catch(PDOException $e) {
    $_SESSION['sweet_alert'] = [
        'type' => 'error',
        'title' => 'Error',
        'text' => 'Ha ocurrido un error al actualizar el perfil: ' . $e->getMessage()
    ];
}

// Redirigir de vuelta al perfil
header("Location: /BCS_FloraGames/view/myProfile.php");
exit();
?>