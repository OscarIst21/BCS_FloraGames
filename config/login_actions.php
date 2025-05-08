<?php
session_start();
require_once '../connection/database.php';

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Verifica si es registro (viene del form con "name", "birthdate", etc.)
    if (isset($_POST['name']) && isset($_POST['birthdate']) && isset($_POST['email']) && isset($_POST['password'])) {
        $nombre = $_POST['name'];
        $fecha_nacimiento = $_POST['birthdate'];
        $correo = $_POST['email'];
        $contrasena = password_hash($_POST['password'], PASSWORD_DEFAULT);
 
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

            $_SESSION['success'] = "¡Registro exitoso! Ahora puedes iniciar sesión.";
            header("Location: ../view/login.php");
            exit();
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error al registrar: " . $e->getMessage();
            header("Location: ../view/login.php");
            exit();
        }
    }

    // Si no venía del formulario de registro, entonces es login
    elseif (isset($_POST['username']) && isset($_POST['password'])) {
        $login = $_POST['username']; // This can be either email or name
        $password = $_POST['password'];

        try {
            // Modified query to check both email and name
            $query = "SELECT * FROM usuarios WHERE correo_electronico = :login OR nombre = :login";
            $stmt = $conn->prepare($query);
            $stmt->execute([':login' => $login]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['contrasena'])) {
                $_SESSION['success'] = "¡Bienvenido, " . $user['nombre'] . "!";
                $_SESSION['user'] = $user;
                header("Location: ../index.php");
                exit();
            } else {
                $_SESSION['error'] = "Nombre/Correo o contraseña incorrectos.";
                header("Location: ../view/login.php");
                exit();
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error al iniciar sesión: " . $e->getMessage();
            header("Location: ../view/login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Datos incompletos.";
        header("Location: ../view/login.php");
        exit();
    }
}
?>
