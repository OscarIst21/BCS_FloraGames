<?php
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../connection/database.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user'])) {
    header("Location: /BCS_FloraGames/view/login.php");
    exit();
}

// Obtener datos del usuario desde la base de datos
$userId = $_SESSION['usuario_id']; // Corregido de user_id a usuario_id
$userData = [];

try {
    // Usar la clase Database para la conexión
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("SELECT nombre, correo_electronico as email, foto_perfil, color_fondo FROM usuarios WHERE id = :id");
    $stmt->bindParam(':id', $userId);
    $stmt->execute();

    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si no hay foto de perfil, usar la predeterminada
    if (empty($userData['foto_perfil'])) {
        $userData['foto_perfil'] = 'usuario0.png';
    }

    // Si no hay color de fondo, usar el predeterminado
    if (empty($userData['color_fondo'])) {
        $userData['color_fondo'] = '#f0f0f0';
    }
} catch (PDOException $e) {
    // Error silencioso, usar datos de sesión como respaldo
    $userData = [
        'nombre' => $_SESSION['user'] ?? 'Usuario',
        'email' => $_SESSION['email'] ?? '',
        'foto_perfil' => 'usuario0.png'
    ];
}
?>
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
                        <img src="../img/foto_de_perfil/<?php echo htmlspecialchars($userData['foto_perfil']); ?>"
                            alt="Foto de perfil"
                            class="profile-pic"
                            style="background-color: <?php echo htmlspecialchars($userData['color_fondo']); ?>">
                        <a href="#" class="edit-pic-btn" id="cambiarFoto" data-bs-toggle="modal" data-bs-target="#avatarModal"><i class="fa-solid fa-pencil"></i></a>
                    </div>
                </div>

                <div class="profile-info">
                    <form action="../config/updateProfile.php" method="post" enctype="multipart/form-data" id="profileForm">
                        <div class="info-item">
                            <span class="info-label">Nombre: </span>
                            <span class="info-value" id="nombreDisplay"><?php echo htmlspecialchars($userData['nombre']); ?></span>
                            <a href="#" id="editarNombre" style="color: #436745;"><i class="fa-solid fa-pencil"></i></a>
                            <input type="text" name="nombre" id="nombreInput" value="<?php echo htmlspecialchars($userData['nombre']); ?>" style="display: none; width: 100%;" class="form-control">
                        </div>

                        <div class="info-item">
                            <span class="info-label">Correo electrónico: </span>
                            <span class="info-value"><?php echo htmlspecialchars($userData['email']); ?></span>
                        </div>

                        <!-- Campos ocultos para la foto de perfil -->
                        <input type="file" name="foto_perfil_upload" id="fotoPerfilInput" style="display: none;" accept="image/*">
                        <input type="hidden" name="avatar_seleccionado" id="avatarSeleccionado" value="">
                        <input type="hidden" name="color_fondo" id="colorFondo" value="#f0f0f0">
                        <input type="hidden" name="tipo_avatar" id="tipoAvatar" value="predefinido">

                        <div class="text-center mt-4">
                            <button type="submit" class="btnActions">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para seleccionar avatar -->
    <div class="modal fade" id="avatarModal" tabindex="-1" aria-labelledby="avatarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #246741; color: white;">
                    <h5 class="modal-title" id="avatarModalLabel">Selecciona tu avatar</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <div id="avatarPreview">
                            <img src="../img/foto_de_perfil/<?php echo htmlspecialchars($userData['foto_perfil']); ?>" alt="Vista previa">
                        </div>
                    </div>

                    <h6 class="text-center mb-3">Avatares predefinidos</h6>
                    <div class="avatar-grid">
                        <?php for ($i = 0; $i <= 7; $i++): ?>
                            <div class="avatar-option" data-avatar="usuario<?php echo $i; ?>.png">
                                <img src="../img/foto_de_perfil/usuario<?php echo $i; ?>.png" alt="Avatar <?php echo $i; ?>">
                            </div>
                        <?php endfor; ?>
                    </div>

                    <h6 class="text-center mb-2">Color de fondo</h6>
                    <div class="color-options">
                        <div class="color-option selected" style="background-color: #f0f0f0;" data-color="#f0f0f0"></div>
                        <div class="color-option" style="background-color: #ffcdd2;" data-color="#ffcdd2"></div>
                        <div class="color-option" style="background-color: #c8e6c9;" data-color="#c8e6c9"></div>
                        <div class="color-option" style="background-color: #bbdefb;" data-color="#bbdefb"></div>
                        <div class="color-option" style="background-color: #fff9c4;" data-color="#fff9c4"></div>
                        <div class="color-option" style="background-color: #e1bee7;" data-color="#e1bee7"></div>
                    </div>

                    <div class="upload-section">
                        <p>¿Prefieres usar tu propia imagen?</p>
                        <button type="button" class="btn btn-outline-success" id="subirImagenBtn">Subir imagen</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="guardarAvatarBtn">Aplicar</button>
                </div>
            </div>
        </div>
    </div>

    <?php include '../components/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    <script src="../js/profile.js"></script>
</body>

</html>
</body>

</html>