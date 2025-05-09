<?php
session_start();
require_once '../connection/database.php';
require_once 'enviarCorreo.php';

$db = new Database();
$conn = $db->getConnection();

// Paso 1: Enviar código de verificación
if (isset($_POST['send_code'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    // Verificar si el email existe
    $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE correo_electronico = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        $_SESSION['sweet_alert'] = [
            'type' => 'error',
            'title' => 'Error',
            'text' => 'No existe una cuenta con este correo electrónico'
        ];
        header("Location: ../view/recuperatePassword.php");
        exit();
    }

    // Generar token de 6 dígitos
    $token = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    
    // Guardar token en la base de datos con fecha de expiración (15 minutos)
    $stmt = $conn->prepare("
        INSERT INTO recuperacion_contrasena (usuario_id, token, fecha_solicitud, expira_en) 
        VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 15 MINUTE))
    ");
    $stmt->execute([$user['id'], $token]);

    // Enviar correo con el token
    // Replace the enviarCorreoToken call with:
    // Send email with recovery link
    $sent = enviarCodigoRecuperacion($email, $token);
    
    // Always proceed to next step, but show different messages
    $_SESSION['step'] = 'verify';
    $_SESSION['email'] = $email;
    
    if ($sent) {
        $_SESSION['sweet_alert'] = [
            'type' => 'success',
            'title' => 'Código enviado',
            'text' => 'Revisa tu correo electrónico'
        ];
    } else {
        $_SESSION['sweet_alert'] = [
            'type' => 'warning',
            'title' => 'Advertencia',
            'text' => 'Hubo un problema al enviar el correo, pero puedes intentar nuevamente'
        ];
    }
    header("Location: ../view/recuperatePassword.php");
    exit();
}

// Paso 2: Verificar código
if (isset($_POST['verify_code'])) {
    $email = $_POST['email'];
    $token = $_POST['token'];
    
    // Verificar token válido y no expirado
    $stmt = $conn->prepare("
        SELECT rc.* 
        FROM recuperacion_contrasena rc
        JOIN usuarios u ON rc.usuario_id = u.id
        WHERE u.correo_electronico = ? 
        AND rc.token = ? 
        AND rc.expira_en > NOW()
    ");
    $stmt->execute([$email, $token]);
    $recovery = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($recovery) {
        $_SESSION['step'] = 'reset';
        $_SESSION['token'] = $token;
        $_SESSION['sweet_alert'] = [
            'type' => 'success',
            'title' => 'Código válido',
            'text' => 'Ahora puedes establecer una nueva contraseña'
        ];
    } else {
        $_SESSION['step'] = 'verify';
        $_SESSION['sweet_alert'] = [
            'type' => 'error',
            'title' => 'Código inválido',
            'text' => 'El código es incorrecto o ha expirado'
        ];
    }
    
    header("Location: ../view/recuperatePassword.php");
    exit();
}

// Paso 3: Restablecer contraseña
if (isset($_POST['reset_password'])) {
    $email = $_POST['email'];
    $token = $_POST['token'];
    $newPassword = $_POST['new_password'];
    
    // Verificar token válido y no expirado
    $stmt = $conn->prepare("
        SELECT rc.* 
        FROM recuperacion_contrasena rc
        JOIN usuarios u ON rc.usuario_id = u.id
        WHERE u.correo_electronico = ? 
        AND rc.token = ? 
        AND rc.expira_en > NOW()
    ");
    $stmt->execute([$email, $token]);
    $recovery = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($recovery) {
        // Actualizar contraseña
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuarios SET contrasena = ? WHERE correo_electronico = ?");
        $stmt->execute([$hashedPassword, $email]);
        
        // Eliminar token usado
        $stmt = $conn->prepare("DELETE FROM recuperacion_contrasena WHERE id = ?");
        $stmt->execute([$recovery['id']]);
        
        // Limpiar sesión
        unset($_SESSION['step']);
        unset($_SESSION['email']);
        unset($_SESSION['token']);
        
        $_SESSION['sweet_alert'] = [
            'type' => 'success',
            'title' => 'Contraseña actualizada',
            'text' => 'Tu contraseña ha sido cambiada exitosamente'
        ];
        header("Location: ../view/login.php");
    } else {
        $_SESSION['sweet_alert'] = [
            'type' => 'error',
            'title' => 'Error',
            'text' => 'No se pudo actualizar la contraseña. El token ha expirado o es inválido'
        ];
        header("Location: ../view/recuperatePassword.php");
    }
    exit();
}
?>