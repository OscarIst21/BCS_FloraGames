<header>
    <div class="header-content">
        <div class="dropdown">
            <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border: none;">
                <a href="" style="color: white;"><i class="fa-solid fa-bars"></i></a>
            </button>
            <ul class="dropdown-menu dropdown-menu">
                <li><a class="dropdown-item" href="/BCS_FloraGames/index.php">Aprendizaje   🌿</a></li>
                <li><a class="dropdown-item" href="/BCS_FloraGames/view/gamesMenu.php">Juegos   🎮</a></li>
                
            </ul>
        </div>
        <div>
            <img style="max-width: 12rem; margin-bottom: 8px;" src="/BCS_FloraGames/img/logoFG.png" alt="">
        </div>
        
    </div>

    <div class="user-section">
            <?php if(isset($_SESSION['user'])): ?>
                <div class="dropdown">
                    <button class="btn dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: white;">
                        <?php if(!empty($_SESSION['foto_perfil'])): ?>
                            <div class="profile-image-container" style="<?php echo !empty($_SESSION['color_fondo']) ? 'background-color: '.$_SESSION['color_fondo'].';' : ''; ?> width: 32px; height: 32px; border-radius: 50%; display: flex; justify-content: center; align-items: center; overflow: hidden;">
                                <img src="/BCS_FloraGames/img/foto_de_Perfil/<?php echo $_SESSION['foto_perfil']; ?>" class="rounded-circle" width="30" height="30" alt="Foto de perfil">
                            </div>
                        <?php else: ?>
                            <div class="rounded-circle" style="<?php echo !empty($_SESSION['color_fondo']) ? 'background-color: '.$_SESSION['color_fondo'].';' : ''; ?> width: 32px; height: 32px; display: flex; justify-content: center; align-items: center;">
                                <i class="fa-regular fa-circle-user"></i>
                            </div>
                        <?php endif; ?>
                        <span class="ms-2 d-none d-sm-inline"><?php echo htmlspecialchars($_SESSION['user']?? 'Usuario'); ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                         <li><a class="dropdown-item" href="/BCS_FloraGames/view/mySuccesses.php">Mis logros</a></li>
                        <li><a class="dropdown-item" href="/BCS_FloraGames/view/myProfile.php">Mi perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/BCS_FloraGames/config/logout.php">Cerrar sesión</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <a href="/BCS_FloraGames/view/login.php" class="btn btn-outline-light">
                    Acceder <i class="fa-regular fa-circle-user ms-1"></i>
                </a>
            <?php endif; ?>
        </div>
</header>