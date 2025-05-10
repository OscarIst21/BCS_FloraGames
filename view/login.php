<?php
if(!isset($_SESSION)) {
    session_start();
}

if(isset($_SESSION['user'])) {
    header("Location: /BCS_FloraGames/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de sesión - Flora Games</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../img/logoFG.ico">
    <style>
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #777;
            background: none;
            border: none;
            padding: 0;
            z-index: 2;
        }
        .password-toggle:hover {
            color: #333;
        }
        .password-container {
            position: relative;
        }
    </style>
</head>
<body>
    <?php include '../components/header.php'; ?>

    <?php
    // Eliminar esta línea: session_start();
    if (isset($_SESSION['sweet_alert'])) {
        $alert = $_SESSION['sweet_alert'];
        echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-center',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: '" . $alert['type'] . "',
                    title: '" . $alert['title'] . "',
                    text: '" . $alert['text'] . "'
                });
            });
        </script>";
        unset($_SESSION['sweet_alert']);
    }
    ?>
    <div class="contenedor">
        <div class="form-container" id="loginForm">
            <h2 style="background-color: #246741; padding: 1rem; color: white;">Iniciar Sesión</h2>
            <form action="../config/loginActions.php" method="post" class="form-fields">
                <div class="form-group">
                    <label for="username">Correo electrónico</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" required>
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group checkbox">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Mantener sesión iniciada</label>
                </div>

                <div class="switch-form">
                    <a href="recuperatePassword.php">Olvidé mi contraseña</a>
                </div>
                <br>
                <button type="submit" class="button">Aceptar</button>
                <div class="switch-form">
                    ¿No tienes cuenta? <a href="register.php">Haz click aquí</a>
                </div>
            </form>
        </div>
    </div>

    <?php include '../components/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButton = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        
        if (toggleButton && passwordInput) {
            toggleButton.addEventListener('click', function() {
                const icon = this.querySelector('i');
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.replace('fa-eye', 'fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.replace('fa-eye-slash', 'fa-eye');
                }
            });
            console.log('Toggle de contraseña configurado correctamente');
        } else {
            console.error('No se encontraron los elementos necesarios');
        }
    });
    </script>
</body>
</html>