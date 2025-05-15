<header>
    <div class="header-content">
        <!-- Botón para abrir el menú lateral -->
        <button class="btn menu-toggle" type="button" title="Menú" onclick="toggleSideMenu()">
            <i class="fa-solid fa-bars" style="color: white;"></i>
        </button>
        
        <!-- Logo -->
        <div>
            <img style="max-width: 12rem; margin-bottom: 8px;" src="/BCS_FloraGames/img/logoFG.png" alt="Flora Games">
        </div>
    </div>

    <div class="user-section">
            <?php if(isset($_SESSION['user'])): ?>
                    <button class="btn  d-flex align-items-center"style="color: white;">
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
            <?php else: ?>
                <button  class="btn btn_access" onclick="window.location.href='/BCS_FloraGames/view/login.php'">
                   <span>Acceder </span><i class="fa-regular fa-circle-user ms-1"></i>
                </button>
            <?php endif; ?>
        </div>

    <!-- Menú lateral -->
    <div class="side-menu" id="sideMenu">
        <div class="side-menu-header">
            <?php if(isset($_SESSION['user'])): ?>
                <div class="user-profile">
                    <?php if(!empty($_SESSION['foto_perfil'])): ?>
                        <div class="profile-image-container" style="<?php echo !empty($_SESSION['color_fondo']) ? 'background-color: '.$_SESSION['color_fondo'].';' : ''; ?>">
                            <img src="/BCS_FloraGames/img/foto_de_Perfil/<?php echo $_SESSION['foto_perfil']; ?>" alt="Foto de perfil">
                        </div>
                    <?php else: ?>
                        <div class="profile-icon" style="<?php echo !empty($_SESSION['color_fondo']) ? 'background-color: '.$_SESSION['color_fondo'].';' : ''; ?>">
                            <i class="fa-regular fa-circle-user"></i>
                        </div>
                    <?php endif; ?>
                    <span class="username"><?php echo htmlspecialchars($_SESSION['user']); ?></span>
                </div>
            <?php endif; ?>
            <button class="btn close-menu" onclick="toggleSideMenu()">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
        
        <ul class="side-menu-items">
            <!-- Sección principal -->
            <li>
                <a href="/BCS_FloraGames/index.php" class="side-menu-link">
                    <i class="fa-solid fa-seedling"></i> Aprendizaje
                </a>
            </li>
            <li>
                <a href="/BCS_FloraGames/view/gamesMenu.php" class="side-menu-link">
                    <i class="fa-solid fa-gamepad"></i> Juegos
                </a>
            </li>
            
            <!-- Separador -->
            <li class="menu-divider"></li>
            
            <!-- Sección de usuario -->
            <?php if(isset($_SESSION['user'])): ?>
                <li>
                    <a href="/BCS_FloraGames/view/mySuccesses.php" class="side-menu-link">
                        <i class="fa-solid fa-trophy"></i> Mis logros
                    </a>
                </li>
                <li>
                    <a href="/BCS_FloraGames/view/myProfile.php" class="side-menu-link">
                        <i class="fa-solid fa-user"></i> Mi perfil
                    </a>
                </li>
                
                <!-- Separador -->
                <li class="menu-divider"></li>
                
                <!-- Cerrar sesión (abajo) -->
                <li class="logout-item">
                    <a href="/BCS_FloraGames/config/logout.php" class="side-menu-link logout-link">
                        <i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión
                    </a>
                </li>
            <?php else: ?>
                <li>
                    <a href="/BCS_FloraGames/view/login.php" class="side-menu-link">
                        <i class="fa-regular fa-circle-user"></i> Iniciar sesión
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="side-menu-overlay" id="sideMenuOverlay" onclick="toggleSideMenu()"></div>
</header>

<style>
    /* Estilos para el menú lateral */
    .side-menu {
        position: fixed;
        top: 0;
        left: -300px;
        width: 280px;
        height: 100vh;
        background-color: #2E8B57;
        color: white;
        z-index: 1050;
        transition: all 0.3s ease;
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
    }
    
    .side-menu.active {
        left: 0;
    }
    
    .side-menu-header {
        padding: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: rgba(0,0,0,0.1);
    }
    
    .user-profile {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .profile-image-container, .profile-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }
    
    .profile-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .profile-icon i {
        font-size: 1.5rem;
        color: white;
    }
    
    .username {
        font-weight: 500;
    }
    
    .close-menu {
        color: white;
        font-size: 1.5rem;
        background: none;
        border: none;
    }
    
    .side-menu-items {
        list-style: none;
        padding: 0;
        margin: 0;
        flex-grow: 1;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
    }
    
    .side-menu-link {
        display: flex;
        align-items: center;
        padding: 1rem 1.5rem;
        color: white;
        text-decoration: none;
        font-size: 1rem;
        transition: all 0.2s ease;
        border-left: 4px solid transparent;
    }
    
    .side-menu-link:hover {
        background-color: rgba(255,255,255,0.1);
        border-left: 4px solid white;
    }
    
    .side-menu-link i {
        margin-right: 12px;
        width: 20px;
        text-align: center;
        font-size: 1.1rem;
    }
    
    .menu-divider {
        height: 1px;
        background-color: rgba(255,255,255,0.2);
        margin: 0.5rem 1.5rem;
    }
    
    .logout-item {
        margin-top: auto;
        border-top: 1px solid rgba(255,255,255,0.2);
    }
    
    .logout-link {
        color: white;
    }
    
    .logout-link:hover {
        background-color: rgba(255,0,0,0.1);
        color:rgb(36, 0, 0);
    }
    
    .side-menu-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        z-index: 1040;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }
    
    .side-menu-overlay.active {
        opacity: 1;
        visibility: visible;
    }
    
    /* Ajustes para el header */
    .header-content {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .menu-toggle {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: white;
    }
    .btn_access{
        color:white
    }
    .btn_access:hover, .btn_access:hover i{
       background-color: #5ED646 !important;
    }
    
    header {
        position: relative;
        z-index: 1030;
    }
    .btn{
        border-color:#2E8B57 !important;
    }


</style>

<script>
    function toggleSideMenu() {
        const sideMenu = document.getElementById('sideMenu');
        const overlay = document.getElementById('sideMenuOverlay');
        
        sideMenu.classList.toggle('active');
        overlay.classList.toggle('active');
        
        // Bloquear/desbloquear scroll del body
        document.body.style.overflow = sideMenu.classList.contains('active') ? 'hidden' : '';
    }

  
</script>