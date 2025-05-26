<?php
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../connection/database.php';
include_once '../config/dataSuccess.php'; 

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

     $stmt = $conn->prepare("SELECT nombre, correo_electronico as email, foto_perfil, color_fondo, fecha_registro, puntos_ganados, nivel_de_usuario_id FROM usuarios WHERE id = :id");
    $stmt->bindParam(':id', $userId);
    $stmt->execute();

    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Initialize progress variable
    $progressUser = 1;
    if ($userData['nivel_de_usuario_id'] == 1) {
        $progressUser = ($userData['nivel_de_usuario_id'] / 15) * 100; 
    }
    
    $fechaFormateada = date('d/m/Y', strtotime($userData['fecha_registro']));
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
     <link rel="stylesheet" href="../css/stylesPerfil.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../img/logoFG.ico">
    <style>
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
    <?php include '../components/header.php'; ?>
    <div class="page-container">
        <div class="contenedor">

            <section class="hero-section">
                <div class="hero-content">
                    <h1 class="hero-title">Mi perfil</h1>
                    <p>Consulta tus estadísticas, logros e insignias conseguidas</p>
                </div> 
            </section>

            <div class="profile-container">
                <div class="profile-header">
                    <h1>Información personal</h1>
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
                            <div>
                                <i class="fa-solid fa-user me-2"></i>
                            <span class="info-value me-2" id="nombreDisplay"><?php echo htmlspecialchars($userData['nombre']); ?></span>
                            </div>
                            <a href="#" id="editarNombre" style="color: #436745;"><i class="fa-solid fa-pencil"></i></a>
                            <input type="text" name="nombre" id="nombreInput" value="<?php echo htmlspecialchars($userData['nombre']); ?>" style="display: none; width: 100%;" class="form-control">
                        </div>
                         <div id="nombreError" style="color: red; font-size: 0.9em; display: none; margin-top: 5px;">El nombre debe tener al menos 2 caracteres.</div>

                        <div class="info-item">
                            <span class="info-value"><i class="fa-solid fa-envelope me-2"></i><?php echo htmlspecialchars($userData['email']); ?></span>
                        </div>

                        <!-- Campos ocultos para la foto de perfil -->
                        <input type="file" name="foto_perfil_upload" id="fotoPerfilInput" style="display: none;" accept="image/*">
                        <input type="hidden" name="avatar_seleccionado" id="avatarSeleccionado" value="<?php echo htmlspecialchars($userData['foto_perfil']); ?>">
                        <input type="hidden" name="color_fondo" id="colorFondo" value="<?php echo htmlspecialchars($userData['color_fondo']); ?>">
                        <input type="hidden" name="tipo_avatar" id="tipoAvatar" value="predefinido">

                        <div class="text-center mt-4">
                            <button type="submit" class="btnActions_Profile btnActions" id="guardarBtn" disabled>Guardar cambios</button>
                        </div>

                        <hr>
                    </form>
                </div>

                <div>
                    <h5>Tu progreso general</h5>
                    <div class="progress" role="progressbar" aria-label="Example with label" 
                        aria-valuenow="<?php echo round($progressUser); ?>" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar" style="width: <?php echo round($progressUser); ?>%">
                            <?php echo round($progressUser); ?>%
                        </div>
                    </div>

                    <hr>
                </div>
                <div>
                    <div class="cd-info">
                        <div class="cd-info-div">
                            <h5 class="cd-title fw-bold  mb-0"><i class="fa-solid fa-gamepad me-2"></i>Juegos jugados</h5>
                            <div class="d-flex justify-content-between ms-2">
                                <p><?php echo $juegosJugados; ?></p>
                            </div>
                        </div>
                        
                        <div class="cd-info-div">
                            <h5 class="cd-title fw-bold  mb-0"><i class="fa-regular fa-clock me-2"></i>Tiempo promedio de juego</h5>
                            <div class="d-flex justify-content-between ms-2">
                                <p><?php echo $tiempoPromedio; ?></p>
                            </div>
                        </div>

                       <div class="cd-info-div">
                         <h5 class="cd-title fw-bold  mb-0"><i class="fa-solid fa-star me-2"></i></i>Juegos ganados</h5>
                            <div class="d-flex justify-content-between ms-2">
                                <p><?php echo $porcentajeGanados; ?>% </p>
                            </div>
                       </div>

                       <div class="cd-info-div">
                             <h5 class="cd-title fw-bold  mb-0"><i class="fa-solid fa-calendar me-2"></i></i>Fecha de registro</h5>
                            <div class="d-flex justify-content-between ms-2">
                                <p><?php echo $fechaFormateada; ?> </p>
                            </div>
                       </div>

                    </div>
                </div>
            </div>

            <div class="status-profile">
                <!-- Nueva sección para mostrar las niveles -->
                 <div class="level-card-container">
                    <?php if ($nivelInfo): ?>
                    <div class="level-image"><img src="../img/niveles/<?php echo htmlspecialchars($nivelInfo['id']); ?>.png" alt="Nivel <?php echo htmlspecialchars($nivelInfo['id']); ?>"></div>
                    <div class="level-content">
                        <h2 class="level-title">¡Buen trabajo!</h2>
                        <p class="level-message">
                            Has alcanzado el nivel <?php echo htmlspecialchars($nivelInfo['id']); ?> en Flora Games. Continúa aprendiendo y acumulando puntos 
                            para desbloquear nuevos niveles.
                        </p>
                        
                        <div class="level-stats">
                            <div class="stat-item">
                                <div class="stat-value"><?php echo $userPoints; ?></div>
                                <div class="stat-label">Puntos</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">#<?php echo $userRanking; ?></div>
                                <div class="stat-label">Ranking</div>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="level-image"><img src="../img/niveles/1.png" alt=""></div>
                    <div class="level-content">
                        <h2 class="level-title">¡Buen trabajo!</h2>
                        <p class="level-message">
                            Has alcanzado el nivel 1 en Flora Games. Continúa aprendiendo y acumulando puntos 
                            para desbloquear nuevos niveles.
                        </p>
                        
                        <div class="progress-container">
                            <div class="progress-label">
                                <span>Progreso al siguiente nivel</span>
                                <span>0%</span>
                            </div>
                        </div>
                        
                        <div class="level-stats">
                            <div class="stat-item">
                                <div class="stat-value">0</div>
                                <div class="stat-label">Puntos</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">#0</div>
                                <div class="stat-label">Ranking</div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Nueva sección para mostrar las insignias -->
                <div class="badges-container">
                    <div class="badges-header">
                        <h1 class="badges-title">Mis Insignias</h1>   
                    </div>
            
                    <div class="badges-grid">
                        <?php if (empty($insignias)): ?>
                            <div class="badge-item">
                                <span class="badge-name">Aún no tienes insignias</span>
                                <span class="badge-date">Sigue jugando para desbloquearlas</span>
                            </div>
                        <?php else: ?>
                            <?php foreach (array_slice($insignias, 0, 6) as $insignia): ?>
                                <div class="badge-item">
                                    <div class="badge-icon">
                                        <img src="../img/insignias/<?php echo htmlspecialchars($insignia['icono_url']); ?>" alt="<?php echo htmlspecialchars($insignia['nombre']); ?>">
                                    </div>
                                    <span class="badge-name"><?php echo htmlspecialchars($insignia['nombre']); ?></span>
                                    <span class="badge-date"><?php echo $insignia['fecha_obtenida'] ? date('d/m/Y', strtotime($insignia['fecha_obtenida'])) : '---'; ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="badges-footer">
                        <a href="#" class="view-collection" data-bs-toggle="modal" data-bs-target="#insigniasModal">
                            Ver colección
                        </a>
                    </div>
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

    <div class="modal fade" id="insigniasModal" tabindex="-1" aria-labelledby="insigniasModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="insigniasModalLabel">Mi Colección de Insignias</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="badges-grid-modal">
                        <?php if (empty($insignias)): ?>
                            <div class="no-badges-message">
                                <p>Aún no has obtenido insignias. ¡Sigue jugando para desbloquearlas!</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($insignias as $insignia): ?>
                                <div class="badge-item-modal">
                                    <div class="badge-icon-modal">
                                        <img src="../img/insignias/<?php echo htmlspecialchars($insignia['icono_url']); ?>" alt="<?php echo htmlspecialchars($insignia['nombre']); ?>">
                                    </div>
                                    <div class="badge-info-modal">
                                        <h3 class="badge-name-modal"><?php echo htmlspecialchars($insignia['nombre']); ?></h3>
                                        <p class="badge-description-modal"><?php echo htmlspecialchars($insignia['descripcion']); ?></p>
                                        <p class="badge-date-modal">Obtenida: <?php echo $insignia['fecha_obtenida'] ? date('d/m/Y', strtotime($insignia['fecha_obtenida'])) : '---'; ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <div id="notificacion-exito" class="notificacion">
        <span id="mensaje-notificacion"></span>
    </div>

    <?php include '../components/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    <script src="../js/profile.js"></script>
</body>

</html>
</body>

</html>