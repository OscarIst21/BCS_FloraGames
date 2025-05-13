<?php
require_once __DIR__.'/../config/init.php';
require_once __DIR__.'/../connection/database.php'; // Añadir esta línea

// Obtener datos del ranking
$rankingData = [];
$userRank = null;
$currentUserId = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Consultar el top 5 del ranking
    $stmt = $conn->prepare("
        SELECT r.usuario_id, r.posicion, r.puntos_ganados, u.nombre 
        FROM ranking r
        JOIN usuarios u ON r.usuario_id = u.id
        ORDER BY r.posicion ASC
        LIMIT 5
    ");
    $stmt->execute();
    $rankingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Consultar la posición del usuario actual si está autenticado
    if ($currentUserId) {
        $stmt = $conn->prepare("
            SELECT r.posicion, r.puntos_ganados
            FROM ranking r
            WHERE r.usuario_id = ?
        ");
        $stmt->execute([$currentUserId]);
        $userRank = $stmt->fetch(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    // Manejar error de base de datos
    error_log("Error al obtener ranking: " . $e->getMessage());
}
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
                <?php if (empty($rankingData)): ?>
                    <li class="leaderboard-item">
                        <span class="leaderboard-position">-</span>
                        <div class="user-badge"><i class="fas fa-user"></i></div>
                        <span class="leaderboard-user">No hay datos disponibles</span>
                        <span class="leaderboard-score">0 pts</span>
                    </li>
                <?php else: ?>
                    <?php foreach ($rankingData as $index => $player): ?>
                        <?php 
                            $position = $player['posicion'];
                            $isCurrentUser = ($player['usuario_id'] == $currentUserId);
                            $positionClass = '';
                            $iconClass = '';
                            
                            if ($position == 1) {
                                $positionClass = 'first-place top-three';
                                $iconClass = 'fas fa-crown';
                            } elseif ($position == 2) {
                                $positionClass = 'second-place top-three';
                                $iconClass = 'fas fa-medal';
                            } elseif ($position == 3) {
                                $positionClass = 'third-place top-three';
                                $iconClass = 'fas fa-medal';
                            } else {
                                $iconClass = 'fas fa-user';
                            }
                            
                            if ($isCurrentUser) {
                                $positionClass .= ' current-user';
                            }
                        ?>
                        <li class="leaderboard-item <?php echo $positionClass; ?>">
                            <span class="leaderboard-position"><?php echo $position; ?></span>
                            <div class="user-badge"><i class="<?php echo $iconClass; ?>"></i></div>
                            <span class="leaderboard-user"><?php echo htmlspecialchars($player['nombre']); ?></span>
                            <span class="leaderboard-score"><?php echo $player['puntos_ganados']; ?> pts</span>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
            
            <?php if ($currentUserId && $userRank && $userRank['posicion'] > 5): ?>
            <div class="user-current-rank">
                <hr>
                <li class="leaderboard-item current-user">
                    <span class="leaderboard-position"><?php echo $userRank['posicion']; ?></span>
                    <div class="user-badge"><i class="fas fa-user"></i></div>
                    <span class="leaderboard-user">Tú</span>
                    <span class="leaderboard-score"><?php echo $userRank['puntos_ganados']; ?> pts</span>
                </li>
            </div>
            <?php endif; ?>
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