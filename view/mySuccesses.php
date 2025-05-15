<?php
require_once __DIR__.'/../config/init.php';
require_once __DIR__.'/../connection/database.php'; // Añadir esta línea

// Obtener datos del ranking
$rankingData = [];
$userRank = null;
$currentUserId = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;

// Variables para la información del nivel y las insignias
$nivelInfo = null;
$insignias = [];
$userPoints = 0;
$userGames = 0;
$nextLevelProgress = 0;
$userRanking = 0;

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
        
        // Obtener información del nivel del usuario
        $stmt = $conn->prepare("
            SELECT n.*, u.puntos_ganados, u.juegos_ganados 
            FROM nivel_de_usuario n
            JOIN usuarios u ON u.nivel_de_usuario_id = n.id
            WHERE u.id = ?
        ");
        $stmt->execute([$currentUserId]);
        $nivelInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($nivelInfo) {
            $userPoints = $nivelInfo['puntos_ganados'];
            $userGames = $nivelInfo['juegos_ganados'];
            $userRanking = $userRank ? $userRank['posicion'] : 0;
            
            // Calcular progreso para el siguiente nivel
            // Suponiendo que cada 20 juegos ganados se sube de nivel hasta el nivel 15
            $juegosParaNivel = 20;
            $nivelActual = $nivelInfo['id'];
            
            if ($nivelActual < 15) { // Si no es el último nivel
                $juegosRestantes = $juegosParaNivel - ($userGames % $juegosParaNivel);
                $nextLevelProgress = 100 - (($juegosRestantes / $juegosParaNivel) * 100);
            } else {
                $nextLevelProgress = 100; // Ya está en el nivel máximo
            }
            
            // Obtener insignias del usuario
            $stmt = $conn->prepare("
                SELECT i.*, ui.fecha_obtenida 
                FROM insignias i
                JOIN usuario_insignias ui ON i.id = ui.insignia_id
                WHERE ui.usuario_id = ?
                ORDER BY ui.fecha_obtenida DESC
            ");
            $stmt->execute([$currentUserId]);
            $insignias = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
} catch (PDOException $e) {
    // Manejar error de base de datos
    error_log("Error al obtener datos: " . $e->getMessage());
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
    <style>/* Estilos para la sección de insignias */
.badges-container {
    width: 100%;
    margin-top: 2rem;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
}

.badges-title {
    font-size: 1.5rem;
    color: #2c3e50;
    margin-bottom: 1rem;
    text-align: center;
}

.badges-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

.badge-item {
    display: flex;
    align-items: center;
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    transition: transform 0.3s ease;
}

.badge-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
}

.badge-icon {
    width: 60px;
    height: 60px;
    margin-right: 1rem;
}

.badge-icon img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.badge-info {
    flex: 1;
}

.badge-name {
    font-size: 1.1rem;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.badge-description {
    font-size: 0.9rem;
    color: #7f8c8d;
    margin-bottom: 0.5rem;
}

.badge-date {
    font-size: 0.8rem;
    color: #95a5a6;
}

.no-badges-message {
    text-align: center;
    padding: 2rem;
    color: #7f8c8d;
    grid-column: 1 / -1;
}</style>
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
                <?php if ($nivelInfo): ?>
                <div class="level-image"><img src="../img/niveles/<?php echo htmlspecialchars($nivelInfo['id']); ?>.png" alt="Nivel <?php echo htmlspecialchars($nivelInfo['id']); ?>"></div>
                <div class="level-content">
                    <h2 class="level-title">¡Buen trabajo!</h2>
                    <p class="level-message">
                        Has alcanzado el nivel <?php echo htmlspecialchars($nivelInfo['id']); ?> en Flora Games. Continúa aprendiendo y acumulando puntos 
                        para desbloquear nuevos niveles.
                    </p>
                    
                    <div class="progress-container">
                        <div class="progress-label">
                            <span>Progreso al siguiente nivel</span>
                            <span><?php echo round($nextLevelProgress); ?>%</span>
                        </div>
                    </div>
                    
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
                    <a href="#" class="view-collection" data-bs-toggle="modal" data-bs-target="#insigniasModal">
                        Ver colección
                    </a>
                </div>
        
                <div class="badges-grid">
                    <?php if (empty($insignias)): ?>
                        <div class="badge-item">
                            <span class="badge-name">Aún no tienes insignias</span>
                            <span class="badge-date">Sigue jugando para desbloquearlas</span>
                        </div>
                    <?php else: ?>
                        <?php foreach (array_slice($insignias, 0, 2) as $insignia): ?>
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
            </div>
        </div>
    </div>
    
    <!-- Modal para mostrar todas las insignias -->
        <style>/* Estilos para el modal de insignias */
    .badges-grid-modal {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.5rem;
        padding: 1rem;
    }

    .badge-item-modal {
        display: flex;
        align-items: center;
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        transition: transform 0.3s ease;
    }

    .badge-item-modal:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    }

    .badge-icon-modal {
        width: 60px;
        height: 60px;
        margin-right: 1rem;
    }

    .badge-icon-modal img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .badge-info-modal {
        flex: 1;
    }

    .badge-name-modal {
        font-size: 1.1rem;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .badge-description-modal {
        font-size: 0.9rem;
        color: #7f8c8d;
        margin-bottom: 0.5rem;
    }

    .badge-date-modal {
        font-size: 0.8rem;
        color: #95a5a6;
    }</style>
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
    
    <div class="contenedor">
    <div class="stats-card">
        <div class="stats-header">
            <h2><i class="fas fa-chart-line"></i> Mis estadísticas</h2>
        </div>
        
        <div class="stats-body">
            <?php
            // Obtener estadísticas del usuario
            $estadisticas = [];
            $porcentajeGanados = 0;
            $tiempoPromedio = '0 min 0s';
            $plantasAprendidas = 0;
            $juegosJugados = 0;
            
            if ($currentUserId) {
                try {
                    // Obtener juegos jugados y porcentaje de ganados
                    $stmt = $conn->prepare("
                        SELECT 
                            COUNT(*) as total_juegos,
                            SUM(CASE WHEN fue_ganado = 1 THEN 1 ELSE 0 END) as juegos_ganados
                        FROM juego_usuario
                        WHERE usuario_id = ?
                    ");
                    $stmt->execute([$currentUserId]);
                    $estadisticas = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($estadisticas && $estadisticas['total_juegos'] > 0) {
                        $juegosJugados = $estadisticas['total_juegos'];
                        $porcentajeGanados = round(($estadisticas['juegos_ganados'] / $estadisticas['total_juegos']) * 100);
                        
                        // Obtener tiempo promedio (duracion es de tipo TIME)
                        $stmt = $conn->prepare("
                            SELECT SEC_TO_TIME(AVG(TIME_TO_SEC(duracion))) as duracion_promedio
                            FROM juego_usuario
                            WHERE usuario_id = ?
                        ");
                        $stmt->execute([$currentUserId]);
                        $tiempoResult = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($tiempoResult && $tiempoResult['duracion_promedio']) {
                            // Formatear el tiempo TIME a minutos y segundos
                            $tiempo = explode(':', $tiempoResult['duracion_promedio']);
                            $horas = $tiempo[0];
                            $minutos = $tiempo[1];
                            $segundos = $tiempo[2];
                            
                            // Convertir todo a minutos y segundos
                            $totalMinutos = ($horas * 60) + $minutos;
                            $tiempoPromedio = $totalMinutos . ' min ' . $segundos . 's';
                        }
                        
                        // Obtener plantas aprendidas (de la tabla usuarios)
                        $stmt = $conn->prepare("
                            SELECT plantas_aprendidas 
                            FROM usuarios 
                            WHERE id = ?
                        ");
                        $stmt->execute([$currentUserId]);
                        $plantasResult = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($plantasResult) {
                            $plantasAprendidas = $plantasResult['plantas_aprendidas'];
                        }
                    }
                } catch (PDOException $e) {
                    error_log("Error al obtener estadísticas: " . $e->getMessage());
                }
            }
            ?>
            
            <!-- Porcentaje de juegos ganados -->
            <div class="stat-item">
                <div class="stat-info">
                    <span class="stat-title">Porcentaje de juegos ganados</span>
                    <span class="stat-value"><?php echo $porcentajeGanados; ?>%</span>
                </div>
                <div class="progress-containers">
                    <div class="progress-bars" style="width: <?php echo $porcentajeGanados; ?>%; background-color: #2E8B57;"></div>
                </div>
            </div>
            
            <!-- Tiempo promedio -->
            <div class="stat-item">
                <div class="stat-info">
                    <span class="stat-title">Tiempo promedio por juego</span>
                    <span class="stat-value"><?php echo $tiempoPromedio; ?></span>
                </div>
                <div class="progress-containers">
                    <div class="progress-bars" style="width: 60%; background-color: #588157;"></div>
                </div>
            </div>
            
            <!-- Plantas aprendidas -->
            <div class="stat-item">
                <div class="stat-info">
                    <span class="stat-title">Plantas aprendidas</span>
                    <span class="stat-value"><?php echo $plantasAprendidas; ?>/50</span>
                </div>
                <div class="progress-containers">
                    <div class="progress-bars" style="width: <?php echo ($plantasAprendidas / 50) * 100; ?>%; background-color: #3A5A40;"></div>
                </div>
            </div>
            
            <!-- Juegos jugados -->
            <div class="stat-item">
                <div class="stat-info">
                    <span class="stat-title">Juegos jugados</span>
                    <span class="stat-value"><?php echo $juegosJugados; ?></span>
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