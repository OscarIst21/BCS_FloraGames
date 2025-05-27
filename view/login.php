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
        @media (max-width:480px){
            .contenedor {
                margin: 3rem 1rem !important;
            }
            header{
                padding: 0 !important;
            }
        }

        .password-toggle:hover {
            color: #333;
        }
        .password-container {
            position: relative;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-danger {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
        }
        .alert-success {
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }
        .error-message {
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 0.25rem;
            display: none;
        }
        .error-input {
            border-color: #dc3545 !important;
        }
    </style>
</head>
<body>
    <?php include '../components/header.php'; ?>

    <div class="contenedor">
        <div class="form-container" id="loginForm">
            <h2 style="background-color: #246741; padding: 1rem; color: white;">Iniciar Sesión</h2>

            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?= $_SESSION['error']; ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            
            <?php if(isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?= $_SESSION['success']; ?>
                    <?php unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <form action="../config/loginActions.php" method="post" class="form-fields" id="loginFormElement">
                <div class="form-group">
                    <label for="username">Correo electrónico</label>
                    <input type="text" id="username" name="username" required>
                    <div class="error-message" id="username-error"></div>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" required>
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="error-message" id="password-error"></div>
                </div>
                <div class="form-group checkbox" style="display:none">
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
        // Toggle de contraseña
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
        }

        // Validación en tiempo real
        const usernameInput = document.getElementById('username');
        //const passwordInput = document.getElementById('password');
        const loginForm = document.getElementById('loginFormElement');

        if (usernameInput) {
            usernameInput.addEventListener('input', function() {
                validateUsername();
            });
        }

        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                validatePassword();
            });
        }

        if (loginForm) {
            loginForm.addEventListener('submit', function(e) {
                if (!validateForm()) {
                    e.preventDefault();
                }
            });
        }
    });

    function validateForm() {
        let isValid = true;
        
        if (!validateUsername()) {
            isValid = false;
        }
        
        if (!validatePassword()) {
            isValid = false;
        }
        
        return isValid;
    }

    function validateUsername() {
        const username = document.getElementById('username')?.value.trim();
        const errorElement = document.getElementById('username-error');
        
        if (!username) {
            showError('username', 'username-error', 'Por favor, ingresa tu correo');
            return false;
        } else {
            clearError('username', 'username-error');
            return true;
        }
    }

    function validatePassword() {
        const password = document.getElementById('password')?.value;
        const errorElement = document.getElementById('password-error');
        
        if (!password) {
            showError('password', 'password-error', 'Por favor, ingresa tu contraseña');
            return false;
        } else if (password.length < 6) {
            showError('password', 'password-error', 'La contraseña debe tener al menos 6 caracteres');
            return false;
        } else {
            clearError('password', 'password-error');
            return true;
        }
    }

    function showError(inputId, errorElementId, message) {
        const input = document.getElementById(inputId);
        const errorElement = document.getElementById(errorElementId);
        
        if (input && errorElement) {
            input.classList.add('error-input');
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }
    }

    function clearError(inputId, errorElementId) {
        const input = document.getElementById(inputId);
        const errorElement = document.getElementById(errorElementId);
        
        if (input && errorElement) {
            input.classList.remove('error-input');
            errorElement.textContent = '';
            errorElement.style.display = 'none';
        }
    }
    </script>
</body>
</html>