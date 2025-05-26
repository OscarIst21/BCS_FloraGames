<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña - Flora Games</title>
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
        .error-message {
            color: red;          /* rojo Bootstrap */
            font-size: 0.875rem;     /* 14 px aprox. */
            margin-top: .25rem;
        }

        .success-message {
            color: #28a745;          /* verde Bootstrap */
            font-size: 0.875rem;
            margin-top: .25rem;
        }

        .notificacion {
            position: fixed;
            top: 70px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #4CAF50;
            color: white;
            padding: 15px 25px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
            display: none;
        }

        .notificacion.mostrar {
            display: block;
            opacity: 1;
        }
    </style>
</head>
<body>
    <?php
    session_start();

     include '../components/header.php'; ?>

    <div class="page-container">
        <div class="contenedor">
        <!-- Paso 1: Solicitar email -->
            <div class="form-container <?php echo (!isset($_SESSION['step']) || $_SESSION['step'] === 'email') ? 'active' : ''; ?>" id="step-email">
                <h2 style="background-color: #246741; padding: 1rem; color: white;">Recuperar contraseña</h2>
                <form action="../config/recoverPassword.php" method="post" class="form-fields">
                    <div class="form-group">  
                        <label for="email">Correo electrónico</label>
                        <input type="email" id="email" name="email" required>
                        <div id="correoError" style="color: red; font-size: 0.9em; display: none; margin-top: 5px; text-align: right;">Ingrese un correo válido.</div>
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
                        <?php if (!empty($_SESSION['flash']['token_error'])): ?>
                            <p class=""></p>
                            <div id="tokenError" style="color: red; font-size: 0.9em; margin-top: 5px; text-align: right;"><?= htmlspecialchars($_SESSION['flash']['token_error']) ?></div>
                        <?php endif; ?>
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
                        <div id="newPsError" style="color: red; font-size: 0.9em; display: none; margin-top: 5px; text-align: right;">La contraseña debe tener al menos 6 caracteres</div>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirmar nueva contraseña</label>
                        <div class="password-container">
                            <input type="password" id="confirm_password" name="confirm_password" required>
                            <?php if (!empty($_SESSION['flash']['password_error'])): ?>
                                <p class="error-message" style="text-align:right"><?= htmlspecialchars($_SESSION['flash']['password_error']) ?></p>
                            <?php elseif (!empty($_SESSION['flash']['password_success'])): ?>
                                <p class="success-message"><?= htmlspecialchars($_SESSION['flash']['password_success']) ?></p>
                            <?php endif; ?>
                            <button type="button" class="password-toggle" id="toggleConfirmPassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div id="psConfError" style="color: red; font-size: 0.9em; display: none; margin-top: 5px; text-align: right;">La contraseña debe tener al menos 6 caracteres</div>
                        <div class="error-message" id="password-error" style="text-align:right"></div>
                    </div>
                    <button type="submit" name="reset_password" class="button">Cambiar contraseña</button>
                </form>
            </div>
        </div>
    </div>

     <div id="notificacion-exito" class="notificacion">
        <span id="mensaje-notificacion"></span>
    </div>
    <?php include '../components/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        function validateEmail() {
            const email = document.getElementById('email');
            const errorLabel = document.getElementById('correoError');
            
            email.addEventListener('input', function () {
                const emailValue = email.value.trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                if (emailValue.length === 0) {
                    errorLabel.style.display = 'none';
                } else if (!emailRegex.test(emailValue)) {
                    // Mostrar error solo si hay algo mal
                    errorLabel.style.display = 'block';
                    return false;
                    retur
                } else {
                    errorLabel.style.display = 'none';
                    return true;
                }

            });
        }
        validateEmail();

        const tokenInput = document.getElementById('token');
        const tokenError = document.getElementById('tokenError');

        if (tokenInput && tokenError) {
            tokenInput.addEventListener('input', () => {
                if (tokenInput.value.trim().length > 0) {
                    tokenError.style.display = 'none';
                }
            });
        }

        const passwordInput = document.getElementById('new_password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const newPsError = document.getElementById('newPsError');
        const psConfError = document.getElementById('psConfError');

        function validatePassword() {
            passwordInput.addEventListener('input', function () {
            const passwordValue = passwordInput.value.trim();

                if (passwordValue.length === 0) {
                    newPsError.style.display = 'none';
                } else if (passwordValue.length < 6) {
                    newPsError.style.display = 'block';
                    newPsError.textContent = 'La contraseña debe tener al menos 6 caracteres';
                    return false;
                } else {
                    newPsError.style.display = 'none';
                    return true;
                }
            });
        }

        function validatePasswordConfirm() {
                confirmPasswordInput.addEventListener('input', function () {
                const passwordValue = confirmPasswordInput.value.trim();

                if (passwordValue.length === 0) {
                    psConfError.style.display = 'none';
                } else if (passwordValue.length < 6) {
                    psConfError.style.display = 'block';
                    psConfError.textContent = 'La contraseña debe tener al menos 6 caracteres';
                    return false;
                } else {
                    psConfError.style.display = 'none';
                    return true;
                }
            });
        }

        validatePassword();
        validatePasswordConfirm();


        // Toggle para mostrar/ocultar contraseña
        setupPasswordToggle('toggleNewPassword', 'new_password');
        setupPasswordToggle('toggleConfirmPassword', 'confirm_password');

        // Validación de contraseñas al enviar el formulario
        document.getElementById('reset-form').addEventListener('submit', function(e) {

            const password = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const errorDiv = document.getElementById('password-error');

            if (password !== confirmPassword) {
                e.preventDefault();
                errorDiv.textContent = 'Las contraseñas no coinciden';
                errorDiv.style.display = 'block';

            } else {
                errorDiv.textContent = '';
                errorDiv.style.display = 'none';
            }
        });

        const newPasswordInput = document.getElementById('new_password');
        const errorDiv = document.getElementById('password-error');
        [newPasswordInput, confirmPasswordInput].forEach(input => {
            input.addEventListener('input', () => {
                if (errorDiv.style.display === 'block') {
                    errorDiv.textContent = '';
                    errorDiv.style.display = 'none';
                }
            });
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

    <?php if (!empty($_SESSION['flash']['password_changed'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const notificacion = document.getElementById('notificacion-exito');
            const mensaje = document.getElementById('mensaje-notificacion');

            mensaje.textContent = "Contraseña modificada exitosamente";
            notificacion.classList.add('mostrar');

            setTimeout(() => {
                notificacion.classList.remove('mostrar');
                window.location.href = 'login.php';
            }, 3000); // Mostrar por 3 segundos y luego redirigir
        });
    </script>
    <?php unset($_SESSION['flash']['password_changed']);
    unset($_SESSION['step']);
    unset($_SESSION['email']);
    unset($_SESSION['token']);
     ?>
    <?php endif; ?>

</body>
</html>



<?php
if (isset($_SESSION['flash'])) {
    unset($_SESSION['flash']);
}
?>
