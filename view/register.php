<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Fauna Games</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../img/logoFG.ico">
    
</head>
<body>
    <?php include '../components/header.php'; ?>

    <?php
    session_start();
    // Recuperar datos del formulario si existen
    $formData = $_SESSION['form_data'] ?? [];
    unset($_SESSION['form_data']);
    
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
        <div class="form-container" id="registerForm">
            <h2 style="background-color: #246741; padding: 1rem; color: white;">Registrarse</h2>
            <form action="../config/login_actions.php" method="post" class="form-fields" id="registrationForm">
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