<?php
    session_start();
    // Recuperar datos del formulario si existen
    $formData = $_SESSION['form_data'] ?? [];
    $error = $_SESSION['error'] ?? '';
    $success = $_SESSION['success'] ?? '';
    unset($_SESSION['form_data']);
    unset($_SESSION['error']);
    unset($_SESSION['success']);
    ?>
    
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Flora Games</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../img/logoFG.ico">
    <style>
        .alert {
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
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
        @media (max-width:480px){
            .contenedor {
                margin: 3rem 1rem !important;
            }
            header{
                padding: 0 !important;
            }
        }
    </style>
</head>
<body>
    <?php include '../components/header.php'; ?>

    
    <div class="contenedor">
        <div class="form-container" id="registerForm">
            <h2 style="background-color: #246741; padding: 1rem; color: white;">Registrarse</h2>
             <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            <form action="../config/loginActions.php" method="post" class="form-fields" id="registrationForm">
                <div class="form-group">
                    <label for="reg_name">Nombre o apodo</label>
                    <input type="text" id="reg_name" name="name" value="<?php echo htmlspecialchars($formData['name'] ?? ''); ?>" required>
                    <div class="error-message" id="name-error"></div>
                </div>
                <div class="form-group">
                    <label for="reg_birthdate">Fecha de nacimiento</label>
                    <input type="date" id="reg_birthdate" name="birthdate" value="<?php echo htmlspecialchars($formData['birthdate'] ?? ''); ?>" required>
                    <div class="error-message" id="birthdate-error"></div>
                </div>
                <div class="form-group">
                    <label for="reg_email">Correo electrónico</label>
                    <input type="email" id="reg_email" name="email" value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>" required>
                    <div class="error-message" id="email-error"></div>
                </div>
                <div class="form-group">
                    <label for="reg_password">Contraseña</label>
                    <div class="password-container">
                        <input type="password" id="reg_password" name="password" required>
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="error-message" id="password-error"></div>
                </div>
                <div class="form-group">
                    <label for="reg_confirm_password">Confirmar contraseña</label>
                    <div class="password-container">
                        <input type="password" id="reg_confirm_password" name="confirm_password" required>
                        <button type="button" class="password-toggle" id="toggleConfirmPassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="error-message" id="confirm-password-error"></div>
                </div>
                <button type="submit" class="button">Aceptar</button>
                <div class="switch-form">
                    ¿Tienes cuenta? <a href="login.php">Inicia sesión</a>
                </div>
            </form>
        </div>
    </div>

    <?php include '../components/footer.php'; ?>
    <script src="../js/register.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    
</body>
</html>