<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de sesión - Fauna Games</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

</head>
<body>
    <?php include '../components/header.php'; ?>

    <?php
    session_start();
    if (isset($_SESSION['success'])) {
        echo '<div class="alert success">' . $_SESSION['success'] . '</div>';
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
        echo '<div class="alert error">' . $_SESSION['error'] . '</div>';
        unset($_SESSION['error']);
    }
    ?>
    <div class="contenedor">
        <div class="form-container" id="loginForm">
            <h2 style="background-color: #246741; padding: 1rem; color: white;">Iniciar Sesión</h2>
            <form action="login_actions.php" method="post" class="form-fields">
                <div class="form-group">
                    <label for="username">Usuario</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <div style="position: relative;">
                        <input type="password" id="password" name="password" required>
                        <input type="checkbox" id="showPassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
                    </div>
                </div>
                <div class="form-group checkbox">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Recordar usuario</label>
                </div>
                <button type="submit" class=" button btnActions">Aceptar</button>
                <div class="switch-form">
                    ¿No tienes cuenta? <a href="#" onclick="toggleForm('register')">Haz click aquí</a>
                </div>
            </form>
        </div>
    

        <div class="form-container" id="registerForm" style="display: none;">
            <h2 style="background-color: #246741; padding: 1rem; color: white;">Registrarse</h2>
            <form action="login_actions.php" method="post" onsubmit="return validatePasswords()" class="form-fields">
                <div class="form-group">
                    <label for="reg_name">Nombre</label>
                    <input type="text" id="reg_name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="reg_birthdate">Fecha de nacimiento</label>
                    <input type="date" id="reg_birthdate" name="birthdate" required>
                </div>
                <div class="form-group">
                    <label for="reg_email">Correo electrónico</label>
                    <input type="email" id="reg_email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="reg_password">Contraseña</label>
                    <div style="position: relative;">
                        <input type="password" id="reg_password" name="password" required>
                        <input type="checkbox" id="showRegPassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
                    </div>
                </div>
                <div class="form-group">
                    <label for="reg_confirm_password">Confirmar Contraseña</label>
                    <div style="position: relative;">
                        <input type="password" id="reg_confirm_password" name="confirm_password" required>
                        <input type="checkbox" id="showRegConfirmPassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
                    </div>
                </div>
                <button type="submit" class="button">Aceptar</button>
                <div class="switch-form">
                    ¿Tienes cuenta? <a href="#" onclick="toggleForm('login')">Inicia sesión</a>
                </div>
            </form>
        </div>
    </div>

    <?php include '../components/footer.php'; ?>
    <script src="../js/login.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>


</html>