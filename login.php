<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login -Fauna Games</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            color: #1a73e8;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #202124;
        }
        .form-group input[type="text"],
        .form-group input[type="password"],
        .form-group input[type="email"],
        .form-group input[type="date"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .form-group.checkbox {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .form-group.checkbox input[type="checkbox"] {
            margin: 0;
        }
        .button {
            width: 100%;
            padding: 12px;
            background-color: #1a73e8;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .button:hover {
            background-color: #1557b0;
        }
        .switch-form {
            text-align: center;
            margin-top: 20px;
            color: #5f6368;
        }
        .switch-form a {
            color: #1a73e8;
            text-decoration: none;
        }
        .switch-form a:hover {
            text-decoration: underline;
        }
        .alert {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            padding: 15px 30px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            z-index: 1000;
            animation: slideDown 0.5s ease-out;
        }
        .alert.success {
            background-color: #34a853;
        }
        .alert.error {
            background-color: #ea4335;
        }
        @keyframes slideDown {
            from {
                top: -100px;
                opacity: 0;
            }
            to {
                top: 20px;
                opacity: 1;
            }
        }
    </style>
</head>
<body>
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
    <div class="form-container" id="loginForm">
        <h2>Iniciar Sesión</h2>
        <form action="login_actions.php" method="post">
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
            <button type="submit" class="button">Aceptar</button>
            <div class="switch-form">
                ¿No tienes cuenta? <a href="#" onclick="toggleForm('register')">Haz click aquí</a>
            </div>
        </form>
    </div>

    <div class="form-container" id="registerForm" style="display: none;">
        <h2>Registro</h2>
        <form action="login_actions.php" method="post" onsubmit="return validatePasswords()">
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

    <script>
        // Existing toggle form function
        function toggleForm(form) {
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            
            if (form === 'register') {
                loginForm.style.display = 'none';
                registerForm.style.display = 'block';
            } else {
                loginForm.style.display = 'block';
                registerForm.style.display = 'none';
            }
        }

        // Password visibility toggle for login
        document.getElementById('showPassword').addEventListener('change', function() {
            const passwordInput = document.getElementById('password');
            passwordInput.type = this.checked ? 'text' : 'password';
        });

        // Password visibility toggle for registration
        document.getElementById('showRegPassword').addEventListener('change', function() {
            const passwordInput = document.getElementById('reg_password');
            passwordInput.type = this.checked ? 'text' : 'password';
        });

        document.getElementById('showRegConfirmPassword').addEventListener('change', function() {
            const passwordInput = document.getElementById('reg_confirm_password');
            passwordInput.type = this.checked ? 'text' : 'password';
        });

        // Password validation for registration
        function validatePasswords() {
            const password = document.getElementById('reg_password').value;
            const confirmPassword = document.getElementById('reg_confirm_password').value;

            if (password !== confirmPassword) {
                alert('Las contraseñas no coinciden');
                return false;
            }
            return true;
        }
    </script>
</body>


</html>