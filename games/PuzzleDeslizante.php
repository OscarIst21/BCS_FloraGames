<?php
session_start();

// Reiniciar el juego si se solicita
if (isset($_GET['reset']) || !isset($_SESSION['puzzle_state'])) {
    unset($_SESSION['puzzle_state']);
    unset($_SESSION['puzzle_moves']);
    unset($_SESSION['puzzle_start_time']);
    unset($_SESSION['puzzle_image']);
    unset($_SESSION['puzzle_difficulty']); // También reiniciar la dificultad
}

// Inicializar variables de sesión si no existen
if (!isset($_SESSION['puzzle_difficulty'])) {
    $_SESSION['puzzle_difficulty'] = isset($_GET['difficulty']) ? $_GET['difficulty'] : '';
}

if (!isset($_SESSION['puzzle_image'])) {
    $_SESSION['puzzle_image'] = isset($_GET['image']) ? $_GET['image'] : rand(1, 15);
}

// Calcular tiempo transcurrido
$elapsedTime = 0;
if (isset($_SESSION['puzzle_start_time'])) {
    $elapsedTime = time() - $_SESSION['puzzle_start_time'];
}

// Inicializar el juego si no existe o si se cambió la dificultad
if (!isset($_SESSION['puzzle_state']) || (isset($_GET['difficulty']) && $_GET['difficulty'] != $_SESSION['puzzle_difficulty'])) {
    // Actualizar dificultad si se proporciona
    if (isset($_GET['difficulty'])) {
        $_SESSION['puzzle_difficulty'] = $_GET['difficulty'];
    }
    
    // Determinar número de piezas según la dificultad
    $gridSize = 3; // Valor predeterminado (3x3 = 9 piezas, 8 + espacio vacío)
    
    if ($_SESSION['puzzle_difficulty'] == 'hard') {
        $gridSize = 4; // 4x4 = 16 piezas, 15 + espacio vacío
    }
    
    // Crear estado inicial del puzzle (0 representa el espacio vacío)
    $totalTiles = ($gridSize * $gridSize) - 1;
    $tiles = range(1, $totalTiles);
    $tiles[] = 0; // Espacio vacío
    
    // Mezclar las piezas (asegurando que sea resoluble)
    do {
        shuffle($tiles);
    } while (!isPuzzleSolvable($tiles, $gridSize));
    
    $_SESSION['puzzle_state'] = $tiles;
    $_SESSION['puzzle_grid_size'] = $gridSize;
    $_SESSION['puzzle_moves'] = 0;
    $_SESSION['puzzle_start_time'] = time();
}

// Procesar acciones AJAX
if (isset($_POST['action'])) {
    $response = ['success' => false];
    
    if ($_POST['action'] == 'move') {
        $index = (int)$_POST['index'];
        $state = $_SESSION['puzzle_state'];
        
        // Encontrar la posición del espacio vacío
        $emptyIndex = array_search(0, $state);
        
        // Verificar si el movimiento es válido (adyacente al espacio vacío)
        if (isValidMove($index, $emptyIndex)) {
            // Intercambiar la pieza con el espacio vacío
            $state[$emptyIndex] = $state[$index];
            $state[$index] = 0;
            
            // Actualizar el estado y contar el movimiento
            $_SESSION['puzzle_state'] = $state;
            $_SESSION['puzzle_moves']++;
            
            // Verificar si el puzzle está resuelto
            $solved = isPuzzleSolved($state);
            
            $response = [
                'success' => true,
                'state' => $state,
                'moves' => $_SESSION['puzzle_moves'],
                'solved' => $solved
            ];
            
            // Si está resuelto, calcular puntos
            if ($solved) {
                $endTime = time();
                $duration = $endTime - $_SESSION['puzzle_start_time'];
                $points = calculatePoints($_SESSION['puzzle_moves'], $duration);
                
                $response['points'] = $points;
                $response['time'] = formatTime($duration);
                
                // Guardar resultado en la base de datos si el usuario está autenticado
                if (isset($_SESSION['usuario_id'])) {
                    saveGameResult(true, $duration, $points);
                }
            }
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Función para verificar si un movimiento es válido
function isValidMove($tileIndex, $emptyIndex) {
    $gridSize = $_SESSION['puzzle_grid_size'];
    
    // Calcular la posición en la cuadrícula
    $tileRow = floor($tileIndex / $gridSize);
    $tileCol = $tileIndex % $gridSize;
    $emptyRow = floor($emptyIndex / $gridSize);
    $emptyCol = $emptyIndex % $gridSize;
    
    // El movimiento es válido si la pieza está adyacente al espacio vacío
    return (
        // Misma fila, columna adyacente
        ($tileRow == $emptyRow && abs($tileCol - $emptyCol) == 1) ||
        // Misma columna, fila adyacente
        ($tileCol == $emptyCol && abs($tileRow - $emptyRow) == 1)
    );
}

// Función para verificar si el puzzle está resuelto
function isPuzzleSolved($state) {
    $gridSize = $_SESSION['puzzle_grid_size'];
    $totalTiles = $gridSize * $gridSize;
    
    // El puzzle está resuelto si las piezas están en orden del 1 al N y el espacio vacío al final
    $solved = true;
    for ($i = 0; $i < $totalTiles - 1; $i++) {
        if ($state[$i] != $i + 1) {
            $solved = false;
            break;
        }
    }
    return $solved && $state[$totalTiles - 1] == 0;
}

// Función para verificar si un puzzle es resoluble
function isPuzzleSolvable($tiles, $gridSize) {
    $inversions = 0;
    $emptyPosition = array_search(0, $tiles);
    
    // Contar inversiones
    for ($i = 0; $i < count($tiles) - 1; $i++) {
        if ($tiles[$i] == 0) continue;
        
        for ($j = $i + 1; $j < count($tiles); $j++) {
            if ($tiles[$j] == 0) continue;
            
            if ($tiles[$i] > $tiles[$j]) {
                $inversions++;
            }
        }
    }
    
    // Para grid de tamaño impar (3x3, 5x5, etc.)
    if ($gridSize % 2 == 1) {
        return $inversions % 2 == 0;
    } 
    // Para grid de tamaño par (4x4, 6x6, etc.)
    else {
        $emptyRow = floor($emptyPosition / $gridSize);
        return ($emptyRow % 2 == 0) ? ($inversions % 2 == 1) : ($inversions % 2 == 0);
    }
}

// Función para calcular puntos
function calculatePoints($moves, $duration) {
    // Base de puntos
    $basePoints = 100;
    
    // Determinar multiplicador según dificultad
    $difficultyMultiplier = 1;
    if ($_SESSION['puzzle_difficulty'] == 'hard') {
        $difficultyMultiplier = 1.5;
    }
    
    // Penalización por movimientos (menos movimientos = más puntos)
    $movesFactor = max(0.5, 1 - ($moves / 100));
    
    // Penalización por tiempo (menos tiempo = más puntos)
    $timeFactor = max(0.5, 1 - ($duration / 300));
    
    // Calcular puntos totales
    return round($basePoints * $difficultyMultiplier * $movesFactor * $timeFactor);
}

// Función para formatear el tiempo
function formatTime($seconds) {
    $minutes = floor($seconds / 60);
    $seconds = $seconds % 60;
    return sprintf('%02d:%02d', $minutes, $seconds);
}

// Función para guardar el resultado del juego
function saveGameResult($won, $duration, $points) {
    // Preparar datos para la API
    $data = [
        'usuario_id' => $_SESSION['usuario_id'],
        'duracion' => $duration,
        'fue_ganado' => $won ? 1 : 0,
        'puntos' => $points  // Añadir los puntos a los datos enviados
    ];
    
    // Convertir a JSON
    $jsonData = json_encode($data);
    
    // Configurar la solicitud
    $ch = curl_init('../config/saveGameResult.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($jsonData)
    ]);
    
    // Ejecutar la solicitud
    $result = curl_exec($ch);
    curl_close($ch);
    
    return $result;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Puzzle Deslizante</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/styleGames.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="../img/logoFG.ico">
    <style>
     
        .game-info{
            display:none;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .puzzle-container {
            background-color: rgba(0, 0, 0, 0.4);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .puzzle-grid {
            display: grid;
            grid-template-columns: repeat(<?php echo $_SESSION['puzzle_grid_size']; ?>, 1fr);
            grid-gap: 5px;
            max-width: 500px;
            margin: 0 auto;
        }
        
        .puzzle-tile {
            position: relative;
            aspect-ratio: 1/1;
            background-color: #f0f0f0;
            border-radius: 5px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            overflow: hidden;
            transition: transform 0.2s;
        }
      
        .puzzle-tile:not(.empty):hover {
            transform: scale(0.95);
        }
        
        .puzzle-tile.empty {
            background-color: transparent;
            cursor: default;
        }
        
        .puzzle-tile .number {
            position: absolute;
            top: 5px;
            left: 5px;
            background-color: rgba(0, 0, 0, 0.3);
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 12px;
            z-index: 1;
        }
        
        .tile-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-repeat: no-repeat;
        }
        
        .image-selector {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            background-color: rgba(0, 0, 0, 0.4);
            border-radius: 10px;
            padding: 1rem;
        }
        
        .image-option {
            width: 60px;
            height: 60px;
            border-radius: 5px;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.2s;
            border: 2px solid transparent;
        }
        
        .image-option:hover {
            transform: scale(1.1);
        }
        
        .image-option.selected {
            border-color: #2E8B57;
        }
        
        .image-option img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        

        .victory-stats {
            margin: 1rem 0;
            text-align: center;
        }
        
        .level {
            font-weight: 600;
            color: #2E8B57;
            margin-bottom: 0.5rem;
        }
        
    
        
       
        .navbar {
            z-index: 1030;
        }
        
    </style>
</head>
<body>
    <?php include '../components/header.php'; ?>

    <div class="header-secundary" style="color:#246741; display: flex; align-items: center;">
        <div style="display:flex; flex-direction:row; gap:10px">
            <button class="reset-btn"onclick="window.location.href='../view/gamesMenu.php'" title="Volver al menú">
               <h5><i class="fas fa-sign-out-alt fa-flip-horizontal"></i></h5>
            </button>
            <button class="reset-btn" id="musicToggle"  title="Música">
                <h5><i class="fa-solid <?php echo $musicEnabled ? 'fa-volume-high' : 'fa-volume-xmark'; ?>" ></i></h5>
            </button>
            <audio id="gameMusic" loop <?php echo $musicEnabled ? 'autoplay' : ''; ?>>
                <source src="../assets/musica.mp3" type="audio/mp3">
            </audio>
            <button class="reset-btn btn-success" id="reset-btn"  title="Reiniciar"><h5><i class="fa-solid fa-arrow-rotate-right"></h5></i></button>
        </div>
        <div style="text-align:center">
            <h5 style="margin:0">Puzzle deslizante</h5>
            <div class="level">Modo facil - Nivel: <span id="level-display">1</span></div>
        </div>
        <div style="display:flex; flex-direction:row; gap:10px">
            <div>
                <div style="display:flex; flex-direction:row; gap:10px">
                    <div class="me-2">
                        <h5><span><i class="fa-solid fa-up-down-left-right me-2"></i><span id="moves"><?php echo $_SESSION['puzzle_moves']; ?></span></span></h5> 
                    </div>
                    <h5><i class="fa-solid fa-clock"></i></h5>
            <div class="timer"> <h5 id="timer"> 00:00</h5></div>
                </div>
            </div>
        </div>
    </div>
    <?php
    // Texto de dificultad para mostrar
    $difficultyText = 'Sin seleccionar';
    if (isset($_SESSION['puzzle_difficulty'])) {
        if ($_SESSION['puzzle_difficulty'] == 'easy') {
            $difficultyText = 'Fácil';
        } elseif ($_SESSION['puzzle_difficulty'] == 'hard') {
            $difficultyText = 'Difícil';
        }
    }
    ?>

    <div class="contenedor">
    <div class="container">
        <div class="game-info">
            <div class="info-item">
                <span><span id="image-number"><?php echo $_SESSION['puzzle_image']; ?></span></span>
            </div>
            
        </div>

        <div class="puzzle-container">
            <div class="puzzle-grid" id="puzzle-grid">
                <?php
                $imageNumber = $_SESSION['puzzle_image'];
                $state = $_SESSION['puzzle_state'];
                $gridSize = $_SESSION['puzzle_grid_size'];
                
                for ($i = 0; $i < $gridSize * $gridSize; $i++) {
                    $tileNumber = $state[$i];
                    $isEmpty = ($tileNumber == 0);
                    $tileClass = $isEmpty ? 'puzzle-tile empty' : 'puzzle-tile';
                    
                    echo "<div class='$tileClass' data-index='$i' data-tile='$tileNumber'>";
                    
                    if (!$isEmpty) {
                        // Calcular la posición de la pieza en la imagen original
                        $row = floor(($tileNumber - 1) / $gridSize);
                        $col = ($tileNumber - 1) % $gridSize;
                        
                        // Calcular el porcentaje para el clip-path
                        $percentPerTile = 100 / $gridSize;
                        
                        echo "<div class='number'>$tileNumber</div>";
                        echo "<div class='tile-image' style='background-image: url(\"../img/niveles/$imageNumber.png\"); background-size: " . ($gridSize * 100) . "%; background-position: " . ($col * 100 / ($gridSize - 1)) . "% " . ($row * 100 / ($gridSize - 1)) . "%;'></div>";
                    }
                    echo "</div>";
                }
                ?>
            </div>
        </div>
        
        <div class="image-selector mt-4">
            <h6 class="w-100 text-center mb-2" style="color:White">Selecciona una planta:</h6>
            <?php
            for ($i = 1; $i <= 15; $i++) {
                $selected = ($i == $_SESSION['puzzle_image']) ? 'selected' : '';
                echo "<div class='image-option $selected' data-image='$i'>";
                echo "<img src='../img/niveles/$i.png' alt='Imagen $i'>";
                echo "</div>";
            }
            ?>
        </div>
    </div>
    </div>

    <!-- Modal de selección de dificultad -->
    <div class="modal fade" id="difficultyModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="difficultyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="difficultyModalLabel">Selecciona la dificultad</h5>
                </div>
                <div class="modal-body">
                    <p><strong>Fácil:</strong> (8 piezas)</p>
                    <button class="difficulty-btn" data-difficulty="easy">Fácil</button>

                    <p><strong>Difícil:</strong> (15 piezas)</p>
                    <button class="difficulty-btn" data-difficulty="hard">Difícil</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="exit-btn">Salir</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de victoria -->
    <div class="modal fade" id="victoryModal" tabindex="-1" aria-labelledby="victoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="victoryModalLabel">¡Puzzle completado!</h5>
                </div>
                <div class="modal-body text-center">
                    <p>Has completado el puzzle correctamente</p>
                    <div class="victory-stats">
                        <p><i class="fas fa-image me-2"></i> Planta: <span id="victory-image"><?php echo $_SESSION['']; ?></span></p>
                        <p><i class="fas fa-sync-alt me-2"></i> Movimientos: <span id="victory-moves">0</span></p>
                        <p><i class="fas fa-clock me-2"></i> Tiempo: <span id="victory-time">00:00</span></p>
                        <p><i class="fas fa-star me-2"></i> Puntos: <span id="victory-points">0</span></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="continue-btn">Continuar</button>
                    <button type="button" class="btn btn-secondary" id="exit-btn">Salir</button>
                </div>
            </div>
        </div>
    </div>

     <?php include '../components/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Botones de selección de dificultad
            document.querySelectorAll('.difficulty-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const diff = btn.dataset.difficulty;         // easy | hard | notime
                    window.location.href = `?difficulty=${diff}`; // recarga con el parámetro
                });
            });

            // Elementos del DOM
            const puzzleGrid = document.getElementById('puzzle-grid');
            const movesElement = document.getElementById('moves');
            const timerElement = document.getElementById('timer');
            const resetButton = document.getElementById('reset-btn');
            const imageOptions = document.querySelectorAll('.image-option');
            
            // Variables del juego
            let timerInterval;
            let startTime = <?php echo isset($_SESSION['puzzle_start_time']) ? $_SESSION['puzzle_start_time'] : 'Math.floor(Date.now() / 1000)'; ?>;
            let elapsedTime = <?php echo isset($elapsedTime) ? $elapsedTime : 0; ?>;
            let gameMode = '<?php echo isset($_SESSION['puzzle_difficulty']) ? $_SESSION['puzzle_difficulty'] : ''; ?>';
            
            // Modales
            const difficultyModalElement = document.getElementById('difficultyModal');
            const difficultyModal = new bootstrap.Modal(difficultyModalElement, {
                backdrop: 'static',
                keyboard: false
            });
        
            const victoryModal = new bootstrap.Modal(document.getElementById('victoryModal'));
            
            // Función para guardar puntos en la base de datos
            function savePoints(points) {
                fetch('../config/updatePoints.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `points=${points}&game=puzzleDeslizante`
                })
                .then(response => response.text())
                .then(data => {
                    console.log('Puntos actualizados:', data);
                })
                .catch(error => {
                    console.error('Error al actualizar puntos:', error);
                });
            }
        
            // Mostrar modal de dificultad si no se ha seleccionado
            if (!gameMode) {
                // Asegurarse de que el modal se muestre correctamente
                setTimeout(() => {
                    difficultyModal.show();
                }, 500);
            } else {
                // Iniciar temporizador si el juego ya está en progreso
                startTimer();
            }
        
            // Configurar eventos de las piezas del puzzle
            const tiles = document.querySelectorAll('.puzzle-tile:not(.empty)');
            tiles.forEach(tile => {
                tile.addEventListener('click', () => moveTile(tile));
            });
        
            // Configurar botón de reinicio
            resetButton.addEventListener('click', () => {
                window.location.href = '?reset=1';
            });
        
            // Configurar selección de imagen
            imageOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const imageNumber = this.dataset.image;
                    window.location.href = `?image=${imageNumber}`;
                });
            });
        
            // Configurar botones del modal de victoria
            document.getElementById('continue-btn').addEventListener('click', function() {
                window.location.href = '?reset=1';
            });
        
            document.getElementById('exit-btn').addEventListener('click', function() {
                window.location.href = '../view/gamesMenu.php';
            });
            
            // Función para mover una pieza
            function moveTile(tile) {
                const index = parseInt(tile.dataset.index);
                
                // Enviar solicitud AJAX para mover la pieza
                fetch('PuzzleDeslizante.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=move&index=${index}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Actualizar el estado del puzzle
                        updatePuzzleState(data.state);
                        
                        // Actualizar contador de movimientos
                        movesElement.textContent = data.moves;
                        
                        // Verificar si el puzzle está resuelto
                        if (data.solved) {
                            // Detener el temporizador
                            clearInterval(timerInterval);
                            
                            // Actualizar estadísticas de victoria
                            document.getElementById('victory-moves').textContent = data.moves;
                            document.getElementById('victory-time').textContent = data.time;
                            document.getElementById('victory-points').textContent = data.points;
                            
                            // Cuando el usuario gana el juego
                            if (<?php echo isset($_SESSION['usuario_id']) ? 'true' : 'false'; ?>) {
                                // Guardar puntos en la base de datos
                                fetch('../config/updatePoints.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                    },
                                    body: `points=${data.points}&game=puzzleDeslizante`
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        return response.text().then(text => {
                                            throw new Error('Error al actualizar puntos: ' + text);
                                        });
                                    }
                                    return response.text();
                                })
                                .then(data => {
                                    console.log('Puntos actualizados correctamente:', data);
                                })
                                .catch(error => {
                                    console.error('Error completo:', error);
                                });
                            }
                            
                            // Mostrar modal de victoria
                            setTimeout(() => {
                                victoryModal.show();
                            }, 500);
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
            }
            
            // Función para iniciar el temporizador
            function startTimer() {
                // Actualizar el temporizador cada segundo
                timerInterval = setInterval(() => {
                    elapsedTime++;
                    
                    // Formatear el tiempo (minutos:segundos)
                    timerElement.textContent = formatTime(elapsedTime);
                }, 1000);
            }
            
            // Función para formatear el tiempo en JavaScript
            function formatTime(seconds) {
                const minutes = Math.floor(seconds / 60);
                const secs = seconds % 60;
                return `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
            }
            
            // Configurar botones del modal de victoria
            document.getElementById('continue-btn').addEventListener('click', function() {
                window.location.href = '?reset=1';
            });
        
            document.getElementById('exit-btn').addEventListener('click', function() {
                window.location.href = '../view/gamesMenu.php';
            });
            
            // Función para mover una pieza
            function moveTile(tile) {
                const index = parseInt(tile.dataset.index);
                
                // Enviar solicitud AJAX para mover la pieza
                fetch('PuzzleDeslizante.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=move&index=${index}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Actualizar el estado del puzzle
                        updatePuzzleState(data.state);
                        
                        // Actualizar contador de movimientos
                        movesElement.textContent = data.moves;
                        
                        // Verificar si el puzzle está resuelto
                        if (data.solved) {
                            // Detener el temporizador
                            clearInterval(timerInterval);
                            
                            // Actualizar estadísticas de victoria
                            document.getElementById('victory-moves').textContent = data.moves;
                            document.getElementById('victory-time').textContent = data.time;
                            document.getElementById('victory-points').textContent = data.points;
                            
                            // Cuando el usuario gana el juego
                            if (<?php echo isset($_SESSION['usuario_id']) ? 'true' : 'false'; ?>) {
                                // Guardar puntos en la base de datos
                                fetch('../config/updatePoints.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                    },
                                    body: `points=${data.points}&game=puzzleDeslizante`
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        return response.text().then(text => {
                                            throw new Error('Error al actualizar puntos: ' + text);
                                        });
                                    }
                                    return response.text();
                                })
                                .then(data => {
                                    console.log('Puntos actualizados correctamente:', data);
                                })
                                .catch(error => {
                                    console.error('Error completo:', error);
                                });
                            }
                            
                            // Mostrar modal de victoria
                            setTimeout(() => {
                                victoryModal.show();
                            }, 500);
                        }
                    }
                }).catch(error => console.error('Error:', error));
            }
            
            // Función para actualizar el estado del puzzle en la interfaz
            function updatePuzzleState(state) {
                // Limpiar el grid
                puzzleGrid.innerHTML = '';
                
                // Recrear las piezas con el nuevo estado
                const gridSize = Math.sqrt(state.length);
                const imageNumber = document.getElementById('image-number').textContent;
                
                for (let i = 0; i < state.length; i++) {
                    const tileNumber = state[i];
                    const isEmpty = (tileNumber == 0);
                    const tileClass = isEmpty ? 'puzzle-tile empty' : 'puzzle-tile';
                    
                    const tileElement = document.createElement('div');
                    tileElement.className = tileClass;
                    tileElement.dataset.index = i;
                    tileElement.dataset.tile = tileNumber;
                    
                    if (!isEmpty) {
                        // Calcular la posición de la pieza en la imagen original
                        const row = Math.floor((tileNumber - 1) / gridSize);
                        const col = (tileNumber - 1) % gridSize;
                        
                        // Agregar número
                        const numberElement = document.createElement('div');
                        numberElement.className = 'number';
                        numberElement.textContent = tileNumber;
                        tileElement.appendChild(numberElement);
                        
                        // Agregar imagen como fondo
                        const imageElement = document.createElement('div');
                        imageElement.className = 'tile-image';
                        imageElement.style.backgroundImage = `url("../img/niveles/${imageNumber}.png")`;
                        imageElement.style.backgroundSize = `${gridSize * 100}%`;
                        imageElement.style.backgroundPosition = `${col * 100 / (gridSize - 1)}% ${row * 100 / (gridSize - 1)}%`;
                        
                        tileElement.appendChild(imageElement);
                        
                        // Agregar evento de clic
                        tileElement.addEventListener('click', () => moveTile(tileElement));
                    }
                    
                    puzzleGrid.appendChild(tileElement);
                }
            }
        });
        </script>
</body>
</html>

