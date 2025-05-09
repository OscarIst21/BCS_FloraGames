<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña - Fauna Games</title>
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
        .form-container {
            display: none;
        }
        .form-container.active {
            display: block;
        }
    </style>
</head>
<body>
    <?php
    session_start();

     include '../components/header.php'; ?>

    <div class="contenedor">
        <!-- Paso 1: Solicitar email -->
        <div class="form-container <?php echo (!isset($_SESSION['step']) || $_SESSION['step'] === 'email') ? 'active' : ''; ?>" id="step-email">
            <h2 style="background-color: #246741; padding: 1rem; color: white;">Recuperar contraseña</h2>
            <form action="../config/recoverPassword.php" method="post" class="form-fields">
                <div class="form-group">  
                    <label for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email" required>
                </div>  
                <button type="submit" name="send_code" class="button">Enviar código de recuperación</button>
                <div class="switch-form">
                    Recordé mi contraseña, <a href="login.php">Inicia sesión</a>
                </div>
            </form>
        </div>

        <!-- Paso 2: Verificar código -->
        <div class="form-container <?php echo (isset($_SESSION['step']) && $_SESSION['step'] === 'verify') ? 'active' : ''; ?>" id="step-verify">
            <h2 style="background-color: #246741; padding: 1rem; color: white;">Verificar código</h2>
            <form action="../config/recoverPassword.php" method="post" class="form-fields">
                <input type="hidden" name="email" value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>">
                <div class="form-group">
                    <label for="token">Ingrese el código de 6 dígitos</label>
                    <input type="text" id="token" name="token" pattern="\d{6}" maxlength="6" required>
                </div>
                <button type="submit" name="verify_code" class="button">Verificar código</button>
                <div class="switch-form">
                    <a href="#" id="resend-code">Enviar código nuevamente</a>
                </div>
            </form>
        </div>

        <!-- Paso 3: Cambiar contraseña -->
        <div class="form-container <?php echo (isset($_SESSION['step']) && $_SESSION['step'] === 'reset') ? 'active' : ''; ?>" id="step-reset">
            <h2 style="background-color: #246741; padding: 1rem; color: white;">Restablecer contraseña</h2>
            <form action="../config/recoverPassword.php" method="post" class="form-fields" id="reset-form">
                <input type="hidden" name="email" value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>">
                <input type="hidden" name="token" value="<?php echo isset($_SESSION['token']) ? htmlspecialchars($_SESSION['token']) : ''; ?>">
                <div class="form-group">
                    <label for="new_password">Nueva contraseña</label>
                    <div class="password-container">
                        <input type="password" id="new_password" name="new_password" minlength="6" required>
                        <button type="button" class="password-toggle" id="toggleNewPassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirmar nueva contraseña</label>
                    <div class="password-container">
                        <input type="password" id="confirm_password" name="confirm_password" required>
                        <button type="button" class="password-toggle" id="toggleConfirmPassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="error-message" id="password-error"></div>
                </div>
                <button type="submit" name="reset_password" class="button">Cambiar contraseña</button>
            </form>
        </div>
    </div>

    <?php include '../components/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <?php
    if (isset($_SESSION['sweet_alert'])) {
        $alert = $_SESSION['sweet_alert'];
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: '" . $alert['type'] . "',
                    title: '" . $alert['title'] . "',
                    text: '" . $alert['text'] . "'
                });
            });
        </script>";
        unset($_SESSION['sweet_alert']);
    }
    ?>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle para mostrar/ocultar contraseña
        setupPasswordToggle('toggleNewPassword', 'new_password');
        setupPasswordToggle('toggleConfirmPassword', 'confirm_password');

        // Validación de contraseñas al enviar el formulario
        document.getElementById('reset-form').addEventListener('submit', function(e) {
            const password = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password.length < 6) {
                e.preventDefault();
                document.getElementById('password-error').textContent = 'La contraseña debe tener al menos 6 caracteres';
                document.getElementById('password-error').style.display = 'block';
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'La contraseña debe tener al menos 6 caracteres'
                });
                return;
            }
            
            if (password !== confirmPassword) {
                e.preventDefault();
                document.getElementById('password-error').textContent = 'Las contraseñas no coinciden';
                document.getElementById('password-error').style.display = 'block';
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Las contraseñas no coinciden'
                });
            }
        });

        // Reenviar código
        document.getElementById('resend-code').addEventListener('click', function(e) {
            e.preventDefault();
            const email = document.querySelector('#step-verify input[name="email"]').value;
            
            // Crear formulario dinámico para reenviar
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '../config/recoverPassword.php';
            
            const emailInput = document.createElement('input');
            emailInput.type = 'hidden';
            emailInput.name = 'email';
            emailInput.value = email;
            
            const sendCodeInput = document.createElement('input');
            sendCodeInput.type = 'hidden';
            sendCodeInput.name = 'send_code';
            sendCodeInput.value = '1';
            
            form.appendChild(emailInput);
            form.appendChild(sendCodeInput);
            document.body.appendChild(form);
            form.submit();
        });
    });

    function setupPasswordToggle(toggleId, passwordId) {
        const toggle = document.getElementById(toggleId);
        const password = document.getElementById(passwordId);
        const icon = toggle.querySelector('i');
        
        toggle.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    }
    </script>
</body>
</html>