<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Incluir las clases de PHPMailer
require_once __DIR__ . '/phpmailer/Exception.php';
require_once __DIR__ . '/phpmailer/PHPMailer.php';
require_once __DIR__ . '/phpmailer/SMTP.php';

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
        $mail->Subject = '¡Bienvenido a BSC_FloraGames! 🌿 Tu cuenta ha sido creada con éxito';

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

function enviarCorreoToken($destinatario, $nombre, $token) {
    $mail = new PHPMailer(true);

    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'floragamesinc@gmail.com';
    $mail->Password = 'FloraGamesInc#1';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('floragamesinc@gmail.com', 'FloraGames 🌿');
    $mail->addAddress($destinatario, $nombre);

    $mail->isHTML(true);
    $mail->Subject = 'Recuperación de contraseña - FloraGames';
    $mail->Body    = "
        Hola <strong>$nombre</strong>,<br><br>
        Tu código para recuperar tu cuenta es:<br><br>
        <h2 style='color:green;'>$token</h2><br>
        Úsalo pronto, tiene validez limitada.<br><br>
        🌿 Saludos del equipo FloraGames.
    ";
    $mail->AltBody = "Hola $nombre,\n\nTu código para recuperar tu cuenta es: $token\n\nSaludos de FloraGames.";

    $mail->send();
}

?>
