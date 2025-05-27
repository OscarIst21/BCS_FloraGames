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
        $_SESSION['flash']['email_error'] = 'No existe una cuenta con este correo electrónico';
        $_SESSION['step'] = 'email';
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
        $_SESSION['flash']['email_success'] = 'Revisa tu correo electrónico';
    } else {
        $_SESSION['flash']['email_error'] = 'Hubo un problema al enviar el correo, intenta nuevamente';
        // opcionalmente vuelve al paso email:
        $_SESSION['step'] = 'email';
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
        $_SESSION['step']  = 'reset';
        $_SESSION['token'] = $token;
    } else {
        $_SESSION['flash']['token_error'] = 'El código es incorrecto o ha expirado';
        $_SESSION['step'] = 'verify';
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
        // Fetch current hashed password from usuarios
        $stmt = $conn->prepare("SELECT contrasena FROM usuarios WHERE correo_electronico = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($newPassword, $user['contrasena'])) {
            $_SESSION['flash']['password_error'] = 'No puedes usar la contraseña anterior. Elige una nueva.';
            $_SESSION['step'] = 'reset';
            header("Location: ../view/recuperatePassword.php");
            exit();
        }
        // Actualizar contraseña
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuarios SET contrasena = ? WHERE correo_electronico = ?");
        $stmt->execute([$hashedPassword, $email]);
        
        // Eliminar token usado
        $stmt = $conn->prepare("DELETE FROM recuperacion_contrasena WHERE id = ?");
        $stmt->execute([$recovery['id']]);
        
        $_SESSION['step'] = 'reset';

        $_SESSION['flash']['password_changed'] = true;
        header("Location: ../view/recuperatePassword.php");
    } else {
        $_SESSION['flash']['password_error'] = 'El token ha expirado o es inválido';
        $_SESSION['step'] = 'reset';
        header("Location: ../view/recuperatePassword.php");
    }
    exit();
}
?>