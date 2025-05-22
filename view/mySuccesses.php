
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