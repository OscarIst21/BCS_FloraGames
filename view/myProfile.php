<?php
require_once __DIR__.'/../config/init.php';
require_once __DIR__.'/../connection/database.php';

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
    
} catch(PDOException $e) {
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
    <style>
        .avatar-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        .avatar-option {
            cursor: pointer;
            border: 3px solid transparent;
            border-radius: 50%;
            transition: all 0.3s;
            width: 80px;
            height: 80px;
            margin: 0 auto;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .avatar-option.selected {
            border-color: #246741;
            box-shadow: 0 0 10px rgba(36, 103, 65, 0.5);
        }
        .avatar-option img {
            max-width: 100%;
            height: auto;
        }
        .color-options {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        .color-option {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid #ddd;
        }
        .color-option.selected {
            border-color: #246741;
            transform: scale(1.2);
        }
        .upload-section {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        #avatarPreview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin: 0 auto 15px;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        #avatarPreview img {
            max-width: 100%;
            height: auto;
        }
    </style>
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
                        <?php for($i = 0; $i <= 6; $i++): ?>
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
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Variables para el modal de avatar
        const avatarOptions = document.querySelectorAll('.avatar-option');
        const colorOptions = document.querySelectorAll('.color-option');
        const avatarPreview = document.getElementById('avatarPreview');
        const avatarSeleccionado = document.getElementById('avatarSeleccionado');
        const colorFondo = document.getElementById('colorFondo');
        const tipoAvatar = document.getElementById('tipoAvatar');
        const subirImagenBtn = document.getElementById('subirImagenBtn');
        const fotoPerfilInput = document.getElementById('fotoPerfilInput');
        const guardarAvatarBtn = document.getElementById('guardarAvatarBtn');
        const profileForm = document.getElementById('profileForm');
        
        let selectedAvatar = null;
        let selectedColor = '#f0f0f0';
        
        // Inicializar con el primer avatar seleccionado
        if (avatarOptions.length > 0) {
            avatarOptions[0].classList.add('selected');
            selectedAvatar = avatarOptions[0].dataset.avatar;
            avatarSeleccionado.value = selectedAvatar;
        }
        
        // Seleccionar avatar predefinido
        avatarOptions.forEach(option => {
            option.addEventListener('click', function() {
                // Quitar selección anterior
                avatarOptions.forEach(opt => opt.classList.remove('selected'));
                // Añadir selección actual
                this.classList.add('selected');
                
                selectedAvatar = this.dataset.avatar;
                tipoAvatar.value = 'predefinido';
                
                // Actualizar vista previa
                avatarPreview.style.backgroundColor = selectedColor;
                avatarPreview.querySelector('img').src = '../img/foto_de_perfil/' + selectedAvatar;
                
                // Actualizar campo oculto
                avatarSeleccionado.value = selectedAvatar;
                
                console.log('Avatar seleccionado:', selectedAvatar);
            });
        });
        
        // Seleccionar color de fondo
        colorOptions.forEach(option => {
            option.addEventListener('click', function() {
                // Quitar selección anterior
                colorOptions.forEach(opt => opt.classList.remove('selected'));
                // Añadir selección actual
                this.classList.add('selected');
                
                selectedColor = this.dataset.color;
                
                // Actualizar vista previa
                avatarPreview.style.backgroundColor = selectedColor;
                
                // Actualizar campo oculto
                colorFondo.value = selectedColor;
                
                console.log('Color seleccionado:', selectedColor);
            });
        });
        
        // Subir imagen propia
        subirImagenBtn.addEventListener('click', function() {
            fotoPerfilInput.click();
        });
        
        fotoPerfilInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    // Actualizar vista previa
                    avatarPreview.querySelector('img').src = e.target.result;
                    
                    // Actualizar tipo de avatar
                    tipoAvatar.value = 'personalizado';
                    
                    // Quitar selección de avatares predefinidos
                    avatarOptions.forEach(opt => opt.classList.remove('selected'));
                    
                    console.log('Imagen personalizada seleccionada');
                };
                
                reader.readAsDataURL(this.files[0]);
            }
        });
        
        // Guardar avatar seleccionado
        guardarAvatarBtn.addEventListener('click', function() {
            // Cerrar el modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('avatarModal'));
            modal.hide();
            
            // Actualizar la imagen de perfil visible
            const profilePic = document.querySelector('.profile-pic');
            if (tipoAvatar.value === 'predefinido' && selectedAvatar) {
                profilePic.src = '../img/foto_de_perfil/' + selectedAvatar;
                profilePic.style.backgroundColor = selectedColor;
                console.log('Aplicando avatar predefinido:', selectedAvatar, 'con color:', selectedColor);
            } else if (tipoAvatar.value === 'personalizado' && fotoPerfilInput.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profilePic.src = e.target.result;
                };
                reader.readAsDataURL(fotoPerfilInput.files[0]);
                console.log('Aplicando imagen personalizada');
            }
        });
        
        // Editar nombre
        const editarNombre = document.getElementById('editarNombre');
        const nombreDisplay = document.getElementById('nombreDisplay');
        const nombreInput = document.getElementById('nombreInput');
        
        editarNombre.addEventListener('click', function(e) {
            e.preventDefault();
            nombreDisplay.style.display = 'none';
            editarNombre.style.display = 'none';
            nombreInput.style.display = 'block';
            nombreInput.focus();
        });
        
        nombreInput.addEventListener('blur', function() {
            nombreDisplay.textContent = nombreInput.value;
            nombreDisplay.style.display = 'inline';
            editarNombre.style.display = 'inline';
            nombreInput.style.display = 'none';
        });
    });
    </script>
</body>
</html>
document.addEventListener('DOMContentLoaded', function() {
    // Variables para el modal de avatar
    const avatarOptions = document.querySelectorAll('.avatar-option');
    const colorOptions = document.querySelectorAll('.color-option');
    const avatarPreview = document.getElementById('avatarPreview');
    const avatarSeleccionado = document.getElementById('avatarSeleccionado');
    const colorFondo = document.getElementById('colorFondo');
    const tipoAvatar = document.getElementById('tipoAvatar');
    const subirImagenBtn = document.getElementById('subirImagenBtn');
    const fotoPerfilInput = document.getElementById('fotoPerfilInput');
    const guardarAvatarBtn = document.getElementById('guardarAvatarBtn');
    const profileForm = document.getElementById('profileForm');
    
    let selectedAvatar = null;
    let selectedColor = '#f0f0f0';
    
    // Inicializar con el primer avatar seleccionado
    if (avatarOptions.length > 0) {
        avatarOptions[0].classList.add('selected');
        selectedAvatar = avatarOptions[0].dataset.avatar;
        avatarSeleccionado.value = selectedAvatar;
    }
    
    // Seleccionar avatar predefinido
    avatarOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Quitar selección anterior
            avatarOptions.forEach(opt => opt.classList.remove('selected'));
            // Añadir selección actual
            this.classList.add('selected');
            
            selectedAvatar = this.dataset.avatar;
            tipoAvatar.value = 'predefinido';
            
            // Actualizar vista previa
            avatarPreview.style.backgroundColor = selectedColor;
            avatarPreview.querySelector('img').src = '../img/foto_de_perfil/' + selectedAvatar;
            
            // Actualizar campo oculto
            avatarSeleccionado.value = selectedAvatar;
            
            console.log('Avatar seleccionado:', selectedAvatar);
        });
    });
    
    // Inicializar el color seleccionado basado en el color guardado
    const savedColor = '<?php echo htmlspecialchars($userData['color_fondo']); ?>';
    selectedColor = savedColor;
    
    // Seleccionar la opción de color que coincide con el color guardado
    colorOptions.forEach(option => {
        if (option.dataset.color === savedColor) {
            option.classList.add('selected');
        } else {
            option.classList.remove('selected');
        }
    });
    
    // Aplicar el color al preview
    avatarPreview.style.backgroundColor = savedColor;
    
    // Actualizar campo oculto
    colorFondo.value = selectedColor;
    
    console.log('Color seleccionado:', selectedColor);
    });
    
    // Subir imagen propia
    subirImagenBtn.addEventListener('click', function() {
        fotoPerfilInput.click();
    });
    
    fotoPerfilInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                // Actualizar vista previa
                avatarPreview.querySelector('img').src = e.target.result;
                
                // Actualizar tipo de avatar
                tipoAvatar.value = 'personalizado';
                
                // Quitar selección de avatares predefinidos
                avatarOptions.forEach(opt => opt.classList.remove('selected'));
                
                console.log('Imagen personalizada seleccionada');
            };
            
            reader.readAsDataURL(this.files[0]);
        }
    });
    
    // Guardar avatar seleccionado
    guardarAvatarBtn.addEventListener('click', function() {
        // Cerrar el modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('avatarModal'));
        modal.hide();
        
        // Actualizar la imagen de perfil visible
        const profilePic = document.querySelector('.profile-pic');
        if (tipoAvatar.value === 'predefinido' && selectedAvatar) {
            profilePic.src = '../img/foto_de_perfil/' + selectedAvatar;
            profilePic.style.backgroundColor = selectedColor;
            console.log('Aplicando avatar predefinido:', selectedAvatar, 'con color:', selectedColor);
        } else if (tipoAvatar.value === 'personalizado' && fotoPerfilInput.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                profilePic.src = e.target.result;
            };
            reader.readAsDataURL(fotoPerfilInput.files[0]);
            console.log('Aplicando imagen personalizada');
        }
    });
    
    // Editar nombre
    const editarNombre = document.getElementById('editarNombre');
    const nombreDisplay = document.getElementById('nombreDisplay');
    const nombreInput = document.getElementById('nombreInput');
    
    editarNombre.addEventListener('click', function(e) {
        e.preventDefault();
        nombreDisplay.style.display = 'none';
        editarNombre.style.display = 'none';
        nombreInput.style.display = 'block';
        nombreInput.focus();
    });
    
    nombreInput.addEventListener('blur', function() {
        nombreDisplay.textContent = nombreInput.value;
        nombreDisplay.style.display = 'inline';
        editarNombre.style.display = 'inline';
        nombreInput.style.display = 'none';
    });
});
    </script>
</body>
</html>