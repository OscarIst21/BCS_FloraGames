<?php
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../connection/database.php';
include_once '../config/dataSuccess.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis logros - Flora Games</title>
    <link rel="stylesheet" href="../css/style.css">
     <link rel="stylesheet" href="../css/stylesMedia.css">
       <link rel="stylesheet" href="../css/styleSuccess.css">
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
                <h1 class="leaderboard-title">Ranking global</h1>
                <p class="leaderboard-subtitle">Compara tu progreso con otros jugadores y compite por los primeros puestos.</p>
            </div>  
            <div class="leaderboard-table">
    <div class="leaderboard-header-row">
        <div class="col-position">#</div>
        <div class="col-user">Usuario</div>
        <div class="col-level">Nivel</div>
        <div class="col-points">Puntos</div>
        <div class="col-badges">Insignias</div>
    </div>
    <ul class="leaderboard-list">
        <?php if (empty($rankingData)): ?>
            <li class="leaderboard-item">
                <div class="col-position">-</div>
                <div class="col-user">No hay datos disponibles</div>
                <div class="col-level">-</div>
                <div class="col-points">0 pts</div>
                <div class="col-badges">-</div>
            </li>
        <?php else: ?>
            <?php foreach ($rankingData as $index => $player): ?>
                <?php 
                    $position = $player['posicion'];
                    $isCurrentUser = ($player['usuario_id'] == $currentUserId);
                    $positionClass = '';
                    $iconClass = 'fas fa-user';
                    
                    if ($position == 1) {
                        $positionClass = 'first-place top-three';
                        $iconClass = 'fas fa-crown';
                    } elseif ($position == 2) {
                        $positionClass = 'second-place top-three';
                        $iconClass = 'fas fa-medal';
                    } elseif ($position == 3) {
                        $positionClass = 'third-place top-three';
                        $iconClass = 'fas fa-medal';
                    }

                    if ($isCurrentUser) {
                        $positionClass .= ' current-user';
                    }
                ?>
                <li class="leaderboard-item <?php echo $positionClass; ?>">
                    <div class="col-position"><?php echo $position; ?></div>
                    <div class="col-user">
                        <i class="<?php echo $iconClass; ?>"></i> <?php echo htmlspecialchars($player['nombre']); ?>
                    </div>
                    <div class="col-level"><?php echo $player['nivel_de_usuario_id']; ?></div>
                    <div class="col-points"><?php echo $player['puntos_ganados']; ?> pts</div>
                    <div class="col-badges"><?php echo $player['insignia_id'] ?? '-'; ?></div>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>

        </div>

    </div>

    <?php include '../components/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>

</html>