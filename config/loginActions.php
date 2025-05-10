<?php
session_start();
require_once '../connection/database.php';
require_once 'enviarCorreo.php';

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['name']) && isset($_POST['birthdate']) && isset($_POST['email']) && isset($_POST['password'])) {
        // Guardar datos del formulario en sesión para mostrarlos de nuevo si hay error
        $_SESSION['form_data'] = [
            'name' => $_POST['name'],
            'birthdate' => $_POST['birthdate'],
            'email' => $_POST['email']
        ];

        $nombre = trim($_POST['name']);
        $fecha_nacimiento = $_POST['birthdate'];
        $correo = trim($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Validate name
        if (strlen($nombre) < 2) {
            $_SESSION['sweet_alert'] = [
                'type' => 'error',
                'title' => 'Error de validación',
                'text' => 'El nombre debe tener al menos 2 caracteres'
            ];
            header("Location: ../view/register.php");
            exit();
        }

        // Validate email format
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['sweet_alert'] = [
                'type' => 'error',
                'title' => 'Error de validación',
                'text' => 'Por favor, ingresa un correo electrónico válido'
            ];
            header("Location: ../view/register.php");
            exit();
        }

        // Check if email already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE correo_electronico = ?");
        $stmt->execute([$correo]);
        if ($stmt->fetchColumn() > 0) {
            $_SESSION['sweet_alert'] = [
                'type' => 'error',
                'title' => 'Error de registro',
                'text' => 'Este correo electrónico ya está registrado'
            ];
            header("Location: ../view/register.php");
            exit();
        }

        // Validate passwords match
        if ($password !== $confirm_password) {
            $_SESSION['sweet_alert'] = [
                'type' => 'error',
                'title' => 'Error de validación',
                'text' => 'Las contraseñas no coinciden'
            ];
            header("Location: ../view/register.php");
            exit();
        }

        // Validate password length
        if (strlen($password) < 6) {
            $_SESSION['sweet_alert'] = [
                'type' => 'error',
                'title' => 'Error de validación',
                'text' => 'La contraseña debe tener al menos 6 caracteres'
            ];
            header("Location: ../view/register.php");
            exit();
        }

        // Validate birthdate
        $birthDate = new DateTime($fecha_nacimiento);
        $today = new DateTime();
        $age = $birthDate->diff($today)->y;
        
        if ($birthDate > $today || $age < 5) {
            $_SESSION['sweet_alert'] = [
                'type' => 'error',
                'title' => 'Error de validación',
                'text' => 'Fecha de naciemiento no valida'
            ];
            header("Location: ../view/register.php");
            exit();
        }

        // If all validations pass, proceed with registration
        $contrasena = password_hash($password, PASSWORD_DEFAULT);

        try {
            $query = "INSERT INTO usuarios (nombre, fecha_de_nacimiento, correo_electronico, contrasena, puntos_ganados, nivel_de_usuario_id, juegos_ganados, plantas_aprendidas, musica_activada, foto_perfil)
                      VALUES (:nombre, :fecha_nacimiento, :correo, :contrasena, 0, 1, 0, 0, 1, '')";
            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':nombre' => $nombre,
                ':fecha_nacimiento' => $fecha_nacimiento,
                ':correo' => $correo,
                ':contrasena' => $contrasena
            ]);

            // Obtener el ID del usuario recién insertado (corregido para PDO)
            $userId = $conn->lastInsertId();
            
            // Iniciar sesión automáticamente
            $_SESSION['usuario_id'] = $userId;
            $_SESSION['user'] = $nombre;
            $_SESSION['email'] = $correo;
            $_SESSION['nivel_id'] = 1; // Nivel inicial para nuevos usuarios

            // Enviar correo de bienvenida
            $correoEnviado = enviarCorreoBienvenida($correo, $nombre);

            if (!$correoEnviado) {
                error_log("Fallo al enviar el correo de bienvenida a $correo");
            }

            // Limpiar datos del formulario guardados
            unset($_SESSION['form_data']);

            $_SESSION['sweet_alert'] = [
                'type' => 'success',
                'title' => '¡Registro exitoso!',
                'text' => 'Bienvenido/a a Flora Games, ' . $nombre
            ];
            header("Location: ../index.php");
            exit();
        } catch (PDOException $e) {
            $_SESSION['sweet_alert'] = [
                'type' => 'error',
                'title' => 'Error al registrar',
                'text' => 'Ocurrió un error al crear la cuenta. Por favor, intenta nuevamente.'
            ];
            header("Location: ../view/register.php");
            exit();
        }
    }
    // Login section
    elseif (isset($_POST['username']) && isset($_POST['password'])) {
        $login = trim($_POST['username']);
        $password = trim($_POST['password']);

        // Validate empty fields
        if (empty($login) || empty($password)) {
            $_SESSION['sweet_alert'] = [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'Por favor, completa todos los campos'
            ];
            header("Location: ../view/login.php");
            exit();
        }

        try {
            $query = "SELECT * FROM usuarios WHERE correo_electronico = :login OR nombre = :login";
            $stmt = $conn->prepare($query);
            $stmt->execute([':login' => $login]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['contrasena'])) {
                // Establecer la sesión del usuario
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['user'] = $user['nombre'];
                $_SESSION['email'] = $user['correo_electronico'];
                $_SESSION['nivel_id'] = $user['nivel_de_usuario_id'];
                
                // Configurar cookie de "recordarme" si está marcado
                if (isset($_POST['remember']) && $_POST['remember'] == 'on') {
                    $cookie_value = $user['id'] . ':' . hash('sha256', $user['contrasena']);
                    setcookie('remember_me', $cookie_value, time() + 86400 * 30, "/"); // 30 días
                }
                
                $_SESSION['sweet_alert'] = [
                    'type' => 'success',
                    'title' => '¡Bienvenido!',
                    'text' => '¡Hola, ' . $user['nombre'] . '!'
                ];
                header("Location: ../index.php");
                exit();
            } else {
                $_SESSION['sweet_alert'] = [
                    'type' => 'error',
                    'title' => 'Error',
                    'text' => 'El Correo o contraseña son incorrectos'
                ];
                header("Location: ../view/login.php");
                exit();
            }
        } catch (PDOException $e) {
            error_log("Error de login: " . $e->getMessage());
            $_SESSION['sweet_alert'] = [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'Ocurrió un error al iniciar sesión. Por favor, inténtalo nuevamente.'
            ];
            header("Location: ../view/login.php");
            exit();
        }
    }
}
?>
