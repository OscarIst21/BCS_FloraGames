<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi perfil - Flora Games</title>
    <link rel="stylesheet" href="../css/style.css">
      <link rel="stylesheet" href="../css/stylesMedia.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../img/logoFG.ico">
</head>
<body>
    <?php include '../components/header.php'; ?>
    <div class="page-container">
        <div class="contenedor">
            <div class="profile-container">
                <div class="profile-header">
                    <h1>Mi Perfil</h1>
                    <div class="profile-pic-container">
                        <img src="../img/foto_de_perfil/usuario0.png" alt="Foto de perfil" class="profile-pic">
                        <a href="#" class="edit-pic-btn"><i class="fa-solid fa-pencil"></i></a>
                    </div>
                </div>
                
                <div class="profile-info">
                    <div class="info-item">
                        <span class="info-label">Nombre: </span>
                        <span class="info-value"><?php echo isset($_SESSION['nombre']) ? htmlspecialchars($_SESSION['nombre']) : 'Usuario'; ?></span>
                        <a href="" style="color: #436745;"><i class="fa-solid fa-pencil"></i></a>
                    </div>
                    
                    <div class="info-item">
                        <span class="info-label">Correo electrónico: </span>
                        <span class="info-value"><?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : 'usuario@gmal.com'; ?></span>
                    </div>

                    <form action="" method="get">
                            <p class="card-content">
                                <button type="submit" class="btnActions">Guardar</button>
                            </p>
                    </form>
                
                </div>
            </div>
        </div>
    </div>

    <?php include '../components/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>

</body>
</html>