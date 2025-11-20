<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Incluir las clases de PHPMailer
require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

function enviarCorreoBienvenida($destinatario, $nombre) {
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    error_log("Intentando enviar correo a $destinatario");
    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'floragamesinc@gmail.com';
        $mail->Password = 'miph ihup lclt fydq'; // ⚠️ Considera usar una contraseña de aplicación
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Remitente y destinatario
        $mail->setFrom('floragamesinc@gmail.com', 'FloraGames 🌿');
        $mail->addAddress($destinatario, $nombre);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = '¡Bienvenido a BCS_FloraGames! 🌿 Tu cuenta ha sido creada con éxito';

        $mail->Body = "
            <p>Hola <strong>$nombre</strong>,</p>
            <p>¡Nos alegra darte la bienvenida a <strong>FloraGames</strong>, el juego web educativo donde podrás divertirte y aprender sobre la increíble flora de Baja California Sur!</p>
            <p>Tu cuenta ha sido creada exitosamente, y el siguiente paso es iniciar sesión y explorar los distintos juegos y actividades diseñados para que <strong>aprendas jugando</strong>.</p>
            <p>Si tienes alguna duda o necesitas ayuda, no dudes en escribirnos a este correo o visitar nuestra sección de soporte.</p>
            <p>Gracias por ser parte de nuestra comunidad y por unirte a esta misión de conocer y cuidar nuestra flora local.</p>
            <br>
            <p>🌱 <em>Saludos verdes,</em><br>El equipo de FloraGames</p>
        ";

        $mail->AltBody = "Hola $nombre,\n\nNos alegra darte la bienvenida a FloraGames, el juego web educativo donde podrás divertirte y aprender sobre la increíble flora de Baja California Sur.\n\nTu cuenta ha sido creada exitosamente. Puedes iniciar sesión y explorar los juegos y actividades.\n\nGracias por unirte a nuestra comunidad.\n\nSaludos verdes,\nEl equipo de FloraGames";

        $mail->send();
        error_log("Correo enviado correctamente a $destinatario");
        return true;
    } catch (Exception $e) {
        error_log("Error al enviar correo: {$mail->ErrorInfo}");
        file_put_contents(__DIR__ . '/error_email_log.txt', "Error al enviar correo: {$mail->ErrorInfo}\n", FILE_APPEND);
      
        return false;
    }
}


function enviarCodigoRecuperacion($email, $token) {
    // Modo desarrollo: simular envío exitoso si Gmail está bloqueado
    $desarrollo = true; // Cambiar a false en producción
    
    if ($desarrollo) {
        error_log("MODO DESARROLLO: Simulando envío de código $token a $email");
        // Guardar el token en un archivo para poder verlo
        file_put_contents(__DIR__ . '/ultimo_token.txt', "Email: $email\nToken: $token\nFecha: " . date('Y-m-d H:i:s'));
        return true;
    }
    
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'floragamesinc@gmail.com';
        $mail->Password = 'miph ihup lclt fydq';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Remitente y destinatario
        $mail->setFrom('floragamesinc@gmail.com', 'FloraGames 🌿');
        $mail->addAddress($email);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = 'Código de recuperación - FloraGames';
        
        $mail->Body = "
            <h2 style='color: #246741;'>Recuperación de contraseña</h2>
            <p>Has solicitado restablecer tu contraseña en FloraGames.</p>
            <p>Tu código de verificación es:</p>
            <div style='font-size: 24px; font-weight: bold; color: #246741; margin: 20px 0;'>$token</div>
            <p>Este código es válido por 15 minutos. Si no solicitaste este cambio, por favor ignora este mensaje.</p>
            <p>Saludos,<br>El equipo de FloraGames 🌿</p>
        ";
        
        $mail->AltBody = "Recuperación de contraseña\n\nTu código de verificación es: $token\n\nEste código es válido por 15 minutos.\n\nSaludos,\nEl equipo de FloraGames";

        $mail->send();
        error_log("Correo de recuperación enviado a: $email");
        return true;
    } catch (Exception $e) {
        error_log("Error al enviar correo de recuperación: " . $mail->ErrorInfo);
        // En caso de error de Gmail, activar modo desarrollo temporalmente
        if (strpos($mail->ErrorInfo, 'Daily user sending limit exceeded') !== false) {
            error_log("LÍMITE DE GMAIL EXCEDIDO: Activando modo desarrollo");
            file_put_contents(__DIR__ . '/ultimo_token.txt', "Email: $email\nToken: $token\nFecha: " . date('Y-m-d H:i:s'));
            return true;
        }
        return false;
    }
}

// Remove or comment out the old enviarCorreoToken function as it's no longer needed
?>
