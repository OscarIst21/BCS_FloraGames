<header>
    <div class="header-content">
        <div class="dropdown">
            <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <a href="" style="color: white;"><i class="fa-solid fa-bars"></i></a>
            </button>
            <ul class="dropdown-menu dropdown-menu">
                <li><a class="dropdown-item" href="/BCS_FloraGames/index.php">Aprendizaje</a></li>
                <li><a class="dropdown-item" href="/BCS_FloraGames/view/gamesMenu.php">Juegos</a></li>
                <li><a class="dropdown-item" href="/BCS_FloraGames/view/mySuccesses.php">Mis logros</a></li>
                <li><hr class="dropdown-divider"></li>
                <?php if(isset($_SESSION['user'])): ?>
                    <li><a class="dropdown-item" href="/BCS_FloraGames/view/myProfile.php">Mi perfil</a></li>
                    <li><a class="dropdown-item" href="/BCS_FloraGames/config/logout.php">Cerrar sesión</a></li>
                <?php else: ?>
                    <li><a class="dropdown-item" href="/BCS_FloraGames/view/login.php">Iniciar sesión</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <div>
            <img style="max-width: 12rem; margin-bottom: 8px;" src="/BCS_FloraGames/img/logoFG.png" alt="">
        </div>
    </div>
</header>