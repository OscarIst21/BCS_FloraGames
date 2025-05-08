<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña - Fauna Games</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../img/logoFG.ico">

</head>
<body>
    <?php include '../components/header.php'; ?>

    <div class="contenedor">
        <div class="form-container" id="recuperar-contraseña">
            <h2 style="background-color: #246741; padding: 1rem; color: white;">Recuperar contraseña</h2>
            <form action="" method="post" class="form-fields">
                <div class="form-group">  
                    <label for="username">Correo electrónico</label>
                    <input type="text" id="username" name="username" required>
                </div>  
                <button type="submit" class=" button btnActions">Enviar código de recuperación</button>
                <div class="switch-form">
                    Recordé mi contraseña, <a href="#" onclick="toggleForm('login')">Inicia sesión</a>
                </div>
            </form>

             <form action="" method="post" class="form-fields">
        
                <div class="form-group">
                    <label for="codigo">Ingrese el código</label>
                    <input type="number" id="codigo" name="codigo" required>
                </div>

                <button type="submit" class=" button btnActions">Aceptar</button>

                <div class="switch-form">
                 <a href="#" onclick="">Enviar código nuevamente</a>
                </div>
            </form>
        </div>

         <div class="form-container" id="verificar-codigo">
            <h2 style="background-color: #246741; padding: 1rem; color: white;">Recuperar contraseña</h2>
            <form action="" method="post" class="form-fields">
        
                <div class="form-group">
                    <label for="codigo">Ingrese el código</label>
                    <input type="number" id="codigo" name="codigo" required>
                </div>

                <button type="submit" class=" button btnActions">Aceptar</button>

                <div class="switch-form">
                 <a href="#" onclick="">Enviar código nuevamente</a>
                </div>
            </form>
        </div>
        
    
        <div class="form-container" id="restablecer-contraseña">
            <h2 style="background-color: #246741; padding: 1rem; color: white;">Restablecer contraseña</h2>
            <form action="" method="post" class="form-fields">
                
                <div class="form-group">
                    <label for="">Nueva contraseña</label>
                    <div style="position: relative;">
                        <input type="password" id="" name="password" required>
                        <input type="checkbox" id="" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
                    </div>
                </div>
                <div class="form-group">
                    <label for="">Confirmar nueva contraseña</label>
                    <div style="position: relative;">
                        <input type="password" id="" name="" required>
                        <input type="checkbox" id="" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
                    </div>
                </div>
                <button type="submit" class=" button btnActions">Aceptar</button>
            <!--Redirigir a login-->
            </form>
        </div>
        
    </div>

    <?php include '../components/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>

</html>