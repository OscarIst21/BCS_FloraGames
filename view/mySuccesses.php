<?php
require_once __DIR__.'/../config/init.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis logros - Flora Games</title>
    <link rel="stylesheet" href="../css/style.css">
     <link rel="stylesheet" href="../css/stylesMedia.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../img/logoFG.ico">

</head>
<body>
    <?php include '../components/header.php'; ?>

    <div class="contenedor" style="align-items: center; column-gap:8rem; row-gap:3rem;">
        <div class="leaderboard-container">
            <div class="leaderboard-header">
                <h1 class="leaderboard-title">Ranking</h1>
                <p class="leaderboard-subtitle">Los mejores jugadores de Flora Games</p>
            </div>  
            <ul class="leaderboard-list">
                <li class="leaderboard-item first-place top-three">
                    <span class="leaderboard-position">1</span>
                    <div class="user-badge"><i class="fas fa-crown"></i></div>
                    <span class="leaderboard-user">USERNAME_01</span>
                    <span class="leaderboard-score">1250 pts</span>
                </li>
                <li class="leaderboard-item second-place top-three">
                    <span class="leaderboard-position">2</span>
                    <div class="user-badge"><i class="fas fa-medal"></i></div>
                    <span class="leaderboard-user">USERNAME_02</span>
                    <span class="leaderboard-score">1150 pts</span>
                </li>
                <li class="leaderboard-item third-place top-three">
                    <span class="leaderboard-position">3</span>
                    <div class="user-badge"><i class="fas fa-medal"></i></div>
                    <span class="leaderboard-user">USERNAME_03</span>
                    <span class="leaderboard-score">1050 pts</span>
                </li>
                <li class="leaderboard-item">
                    <span class="leaderboard-position">4</span>
                    <div class="user-badge">4</div>
                    <span class="leaderboard-user">USERNAME_04</span>
                    <span class="leaderboard-score">950 pts</span>
                </li>
                <li class="leaderboard-item">
                    <span class="leaderboard-position">5</span>
                    <div class="user-badge">5</div>
                    <span class="leaderboard-user">USERNAME_05</span>
                    <span class="leaderboard-score">850 pts</span>
                </li>
            </ul>
        </div>

        <div class="contenedor-secundario">
            <div class="level-card-container">
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
                            <span>65%</span>
                        </div>
                    </div>
                    
                    <div class="level-stats">
                        <div class="stat-item">
                            <div class="stat-value">128</div>
                            <div class="stat-label">Puntos</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">#42</div>
                            <div class="stat-label">Ranking</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="badges-container">
                <div class="badges-header">
                    <h1 class="badges-title">Mis Insignias</h1>
                    <a href="" class="view-collection">
                        Ver colección
                    </a>
                </div>
        
                <div class="badges-grid">
                    <div class="badge-item">
                        <div class="badge-icon">
                            <img src="../img/insignias/Botánico_novato.png" alt="Botanico novato">
                        </div>
                        <span class="badge-name">Botánico novato I</span>
                        <span class="badge-date">15/05/2023</span>
                    </div>
            
                    <div class="badge-item">
                        <div class="badge-icon">
                            <img src="../img/insignias/Botánico_novatoII.png" alt="Botanico novato II">
                        </div>
                        <span class="badge-name">Botánico novato II</span>
                        <span class="badge-date">22/06/2023</span>
                    </div>
            
                    <div class="badge-item">
                        <div class="badge-icon">
                            <img src="../img/insignias/Semilla_curiosa.png" alt="Semilla curiosa">
                        </div>
                        <span class="badge-name">Semilla curiosa</span>
                        <span class="badge-date">10/07/2023</span>
                    </div>
            
                    <div class="badge-item">
                        <div class="badge-icon">
                            <img src="../img/insignias/Semilla_curiosaII.png" alt="Semilla curiosa II">
                        </div>
                        <span class="badge-name">Semilla curiosa II</span>
                        <span class="badge-date">---</span>
                    </div>
           
                </div>
            </div>
        </div>
    </div>
    <div class="contenedor">
        <div class="stats-card">
            <div class="stats-header">
                <h2><i class="fas fa-chart-line"></i> Mis estadísticas</h2>
            </div>
            
            <div class="stats-body">
                <!-- Porcentaje de juegos ganados -->
                <div class="stat-item">
                    <div class="stat-info">
                        <span class="stat-title">Porcentaje de juegos ganados</span>
                        <span class="stat-value">75%</span>
                    </div>
                    <div class="progress-containers">
                        <div class="progress-bars" style="width: 75%; background-color: #2E8B57;"></div>
                    </div>
                </div>
                
                <!-- Tiempo promedio -->
                <div class="stat-item">
                    <div class="stat-info">
                        <span class="stat-title">Tiempo promedio por juego</span>
                        <span class="stat-value">12 min 34s</span>
                    </div>
                    <div class="progress-containers">
                        <div class="progress-bars" style="width: 60%; background-color: #588157;"></div>
                    </div>
                </div>
                
                <!-- Plantas aprendidas -->
                <div class="stat-item">
                    <div class="stat-info">
                        <span class="stat-title">Plantas aprendidas</span>
                        <span class="stat-value">24/50</span>
                    </div>
                    <div class="progress-containers">
                        <div class="progress-bars" style="width: 48%; background-color: #3A5A40;"></div>
                    </div>
                </div>
                
                <!-- Juegos jugados -->
                <div class="stat-item">
                    <div class="stat-info">
                        <span class="stat-title">Juegos jugados</span>
                        <span class="stat-value">156</span>
                    </div>
                    <div class="progress-containers">
                        <div class="progress-bars" style="width: 100%; background-color: #344E41;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <?php include '../components/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>

</html>