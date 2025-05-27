<?php
session_start();
require_once __DIR__ . '/../connection/database.php';

// Initialize user and music preference
$userId = null;
$musicEnabled = true;

if (isset($_SESSION['usuario_id'])) {
    $userId = $_SESSION['usuario_id'];
    try {
        $db = new Database();
        $conn = $db->getConnection();
        $stmt = $conn->prepare("SELECT musica_activada FROM usuarios WHERE id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $musicEnabled = (bool)$result['musica_activada'];
    } catch (PDOException $e) {
        error_log("Error fetching music preference: " . $e->getMessage());
    }
}

// Always reset difficulty on initial load to force modal
if (!isset($_GET['difficulty']) && !isset($_POST['action']) && !isset($_GET['reset']) && !isset($_GET['image'])) {
    unset($_SESSION['puzzle_difficulty']);
}

// Reiniciar el juego si se solicita
if (isset($_GET['reset']) || !isset($_SESSION['puzzle_state'])) {
    unset($_SESSION['puzzle_state']);
    unset($_SESSION['puzzle_moves']);
    unset($_SESSION['puzzle_start_time']);
    unset($_SESSION['puzzle_image']);
    unset($_SESSION['puzzle_nombre']);
    unset($_SESSION['puzzle_difficulty']);
    unset($_SESSION['puzzle_grid_size']);
}

// Inicializar variables de sesión si no existen
if (!isset($_SESSION['puzzle_difficulty'])) {
    $_SESSION['puzzle_difficulty'] = isset($_GET['difficulty']) ? $_GET['difficulty'] : '';
}

if (!isset($_SESSION['puzzle_image'])) {
    if (isset($_GET['image'])) {
        $_SESSION['puzzle_image'] = $_GET['image'];
        $plantas = json_decode(file_get_contents(__DIR__ . '/../config/plantas.json'), true);
        foreach ($plantas as $planta) {
            if ($planta['foto'] === $_GET['image']) {
                $_SESSION['puzzle_nombre'] = $planta['nombre_comun'];
                break;
            }
        }
    } else {
        $plantas = json_decode(file_get_contents(__DIR__ . '/../config/plantas.json'), true);
        $plantaRandom = $plantas[array_rand($plantas)];
        $_SESSION['puzzle_image'] = $plantaRandom['foto'];
        $_SESSION['puzzle_nombre'] = $plantaRandom['nombre_comun'];
    }
}

// Calcular tiempo transcurrido
$elapsedTime = 0;
if (isset($_SESSION['puzzle_start_time'])) {
    $elapsedTime = time() - $_SESSION['puzzle_start_time'];
}

// Inicializar el juego si no existe o si se cambió la dificultad
if (!isset($_SESSION['puzzle_state']) || (isset($_GET['difficulty']) && $_GET['difficulty'] != $_SESSION['puzzle_difficulty'])) {
    if (isset($_GET['difficulty'])) {
        $_SESSION['puzzle_difficulty'] = $_GET['difficulty'];
    }
    
    $gridSize = 3; // Default (3x3)
    if ($_SESSION['puzzle_difficulty'] == 'hard') {
        $gridSize = 4; // 4x4
    }
    
    $totalTiles = ($gridSize * $gridSize) - 1;
    $tiles = range(1, $totalTiles);
    $tiles[] = 0; // Empty space
    
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
        
        $emptyIndex = array_search(0, $state);
        
        if (isValidMove($index, $emptyIndex)) {
            $state[$emptyIndex] = $state[$index];
            $state[$index] = 0;
            
            $_SESSION['puzzle_state'] = $state;
            $_SESSION['puzzle_moves']++;
            
            $solved = isPuzzleSolved($state);
            
            $response = [
                'success' => true,
                'state' => $state,
                'moves' => $_SESSION['puzzle_moves'],
                'solved' => $solved
            ];
            
            if ($solved) {
                $endTime = time();
                $duration = $endTime - $_SESSION['puzzle_start_time'];
                $points = calculatePoints($_SESSION['puzzle_moves'], $duration);
                
                $response['points'] = $points;
                $response['time'] = formatTime($duration);
                
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
    
    $tileRow = floor($tileIndex / $gridSize);
    $tileCol = $tileIndex % $gridSize;
    $emptyRow = floor($emptyIndex / $gridSize);
    $emptyCol = $emptyIndex % $gridSize;
    
    return (
        ($tileRow == $emptyRow && abs($tileCol - $emptyCol) == 1) ||
        ($tileCol == $emptyCol && abs($tileRow - $emptyRow) == 1)
    );
}

// Función para verificar si el puzzle está resuelto
function isPuzzleSolved($state) {
    $gridSize = $_SESSION['puzzle_grid_size'];
    $totalTiles = $gridSize * $gridSize;
    
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
    
    for ($i = 0; $i < count($tiles) - 1; $i++) {
        if ($tiles[$i] == 0) continue;
        for ($j = $i + 1; $j < count($tiles); $j++) {
            if ($tiles[$j] == 0) continue;
            if ($tiles[$i] > $tiles[$j]) {
                $inversions++;
            }
        }
    }
    
    if ($gridSize % 2 == 1) {
        return $inversions % 2 == 0;
    } else {
        $emptyRow = floor($emptyPosition / $gridSize);
        return ($emptyRow % 2 == 0) ? ($inversions % 2 == 1) : ($inversions % 2 == 0);
    }
}

// Función para calcular puntos
function calculatePoints($moves, $duration) {
    $basePoints = 300;
    $difficultyMultiplier = ($_SESSION['puzzle_difficulty'] == 'hard') ? 1.5 : 1;
    $movesFactor = max(0.5, 1 - ($moves / 100));
    $timeFactor = max(0.5, 1 - ($duration / 300));
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
    if (!isset($_SESSION['usuario_id'])) {
        return;
    }

    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("
            INSERT INTO resultados_juegos (usuario_id, juego, duracion, fue_ganado, puntos, fecha)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $_SESSION['usuario_id'],
            'puzzleDeslizante',
            $duration,
            $won ? 1 : 0,
            $points
        ]);

        $stmt = $conn->prepare("
            UPDATE usuarios 
            SET puntos_totales = puntos_totales + ?
            WHERE id = ?
        ");
        $stmt->execute([$points, $_SESSION['usuario_id']]);
    } catch (PDOException $e) {
        error_log("Error al guardar resultado: " . $e->getMessage());
    }
}

// Texto de dificultad para mostrar
$difficultyText = 'Fácil';
if (isset($_SESSION['puzzle_difficulty'])) {
    if ($_SESSION['puzzle_difficulty'] == 'hard') {
        $difficultyText = 'Difícil';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Puzzle Deslizante - BCS Flora Games</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/styleGames.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="../img/logoFG.ico">
    <style>
        .game-info {
            display: none;
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
            grid-template-columns: repeat(<?php echo $_SESSION['puzzle_grid_size'] ?? 3; ?>, 1fr);
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
            background-color: rgba(0, 0, 0);
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
        .navbar {
            z-index: 1030;
        }
    </style>
</head>
<body>
    <?php include '../components/header.php'; ?>

    <div class="header-secundary" style="color:#246741; display: flex; align-items: center;">
        <div class="hd-sec-gm" style="display:flex; flex-direction:row; gap:10px">
            <button class="reset-btn" onclick="window.location.href='../view/gamesMenu.php'" title="Volver al menú">
                <h5><i class="fas fa-sign-out-alt fa-flip-horizontal"></i></h5>
            </button>
            <button class="reset-btn" id="musicToggle" title="Música">
                <h5><i class="fa-solid <?php echo $musicEnabled ? 'fa-volume-high' : 'fa-volume-xmark'; ?>"></i></h5>
            </button>
            <audio id="gameMusic" loop <?php echo $musicEnabled ? 'autoplay' : ''; ?>>
                <source src="../assets/musica.mp3" type="audio/mp3">
            </audio>
            <button class="reset-btn btn-success" id="reset-btn" title="Reiniciar"><h5><i class="fa-solid fa-arrow-rotate-right"></i></h5></button>
        </div>
        <div style="text-align:center">
            <h5 style="margin:0">Puzzle Deslizante</h5>
            <div class="level">Modo: <?php echo $difficultyText; ?> - Planta: <?php echo htmlspecialchars($_SESSION['puzzle_nombre'] ?? 'Desconocida'); ?></div>
        </div>
        <div style="display:flex; flex-direction:row; gap:10px">
            <div>
                <div class="hd-sec-gm-v3" style="display:flex; flex-direction:row; gap:10px">
                    <div class="me-2">
                        <h5><i class="fa-solid fa-up-down-left-right me-2"></i><span id="moves"><?php echo $_SESSION['puzzle_moves'] ?? 0; ?></span></h5>
                    </div>
                    <div class="timer">
                        <h5><i class="fa-solid fa-clock me-2"></i><span id="timer"><?php echo formatTime($elapsedTime); ?></span></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="contenedor">
        <div class="container">
            <div class="game-info">
                <div class="info-item">
                    <span><span id="image-number"><?php echo htmlspecialchars($_SESSION['puzzle_image']); ?></span></span>
                </div>
            </div>

            <div class="puzzle-container">
                <div class="puzzle-grid" id="puzzle-grid">
                    <?php
                    $imageNumber = $_SESSION['puzzle_image'];
                    $state = $_SESSION['puzzle_state'] ?? [];
                    $gridSize = $_SESSION['puzzle_grid_size'] ?? 3;
                    
                    for ($i = 0; $i < $gridSize * $gridSize; $i++) {
                        $tileNumber = $state[$i] ?? 0;
                        $isEmpty = ($tileNumber == 0);
                        $tileClass = $isEmpty ? 'puzzle-tile empty' : 'puzzle-tile';
                        echo "<div class='$tileClass' data-index='$i' data-tile='$tileNumber'>";
                        if (!$isEmpty) {
                            $row = floor(($tileNumber - 1) / $gridSize);
                            $col = ($tileNumber - 1) % $gridSize;
                            echo "<div class='number'>$tileNumber</div>";
                            echo "<div class='tile-image' style='background-image: url(\"../img/plantas/{$imageNumber}\"); background-size: " . ($gridSize * 100) . "%; background-position: " . ($col * 100 / ($gridSize - 1)) . "% " . ($row * 100 / ($gridSize - 1)) . "%;'></div>";
                        }
                        echo "</div>";
                    }
                    ?>
                </div>
            </div>
            
            <div class="image-selector mt-4" style="display: none;">
                <h6 class="w-100 text-center mb-2" style="color:white">Selecciona una planta:</h6>
                <?php
                $plantas = json_decode(file_get_contents(__DIR__ . '/../config/plantas.json'), true);
                foreach ($plantas as $planta) {
                    $selected = ($planta['foto'] == $_SESSION['puzzle_image']) ? 'selected' : '';
                    echo "<div class='image-option $selected' data-image='{$planta['foto']}'>";
                    echo "<img src='../img/plantas/{$planta['foto']}' alt='{$planta['nombre_comun']}'>";
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
                    <button class="difficulty-btn " data-difficulty="easy">Fácil</button>
                    <p><strong>Difícil:</strong> (15 piezas)</p>
                    <button class="difficulty-btn " data-difficulty="hard">Difícil</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="comoJugar">¿Cómo jugar?</button>
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
                        <p><i class="fas fa-image me-2"></i> Planta: <span id="victory-image"><?php echo htmlspecialchars($_SESSION['puzzle_nombre'] ?? 'Desconocida'); ?></span></p>
                        <p><i class="fas fa-sync-alt me-2"></i> Movimientos: <span id="victory-moves">0</span></p>
                        <p><i class="fas fa-clock me-2"></i> Tiempo: <span id="victory-time">00:00</span></p>
                        <p><i class="fas fa-star me-2"></i> Puntos: <span id="victory-points">0</span></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="continue-btn">Continuar</button>
                    <button type="button" class="btn btn-secondary" id="exit-btn2">Salir</button>
                </div>
            </div>
        </div>
    </div>

     <!-- Modal de instrucciones -->
    <div class="modal fade" id="instructionsModal" tabindex="-1" aria-labelledby="instructionsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="instructionsModalLabel">Cómo jugar</h5>
                </div>
                <div class="modal-body">
                    <p>1.- Desliza las fichas usando la casilla vacía hasta ordenarlas correctamente.</p>
                    <p>2.- Solo puedes mover fichas junto a la casilla vacía.</p>
                    <p>3.- El juego termina cuando armes la imagen correctamente.</p>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-success" id="skipInstructions">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <?php include '../components/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let timerInterval=0;
        let elapsedTime = <?php echo $elapsedTime; ?>;
        let moves = <?php echo $_SESSION['puzzle_moves'] ?? 0; ?>;
        let gameMode = '<?php echo $_SESSION['puzzle_difficulty'] ?? ''; ?>';
        const isAuthenticated = <?php echo isset($_SESSION['usuario_id']) ? 'true' : 'false'; ?>;
        let difficultyModal;
        let victoryModal;

        function initGame() {
            // Initialize modals
            difficultyModal = new bootstrap.Modal(document.getElementById('difficultyModal'), {
                backdrop: 'static',
                keyboard: false
            });
            victoryModal = new bootstrap.Modal(document.getElementById('victoryModal'));

            // Show difficulty modal if no difficulty is set
            if (!gameMode) {
                difficultyModal.show();
            } else {
                startTimer();
            }

            // Set up event listeners
            document.querySelectorAll('.difficulty-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const diff = btn.dataset.difficulty;
                    clearInterval(timerInterval); // Stop any running timer
                    elapsedTime = 0; // Reset timer variable
                    document.getElementById('timer').textContent = '00:00'; // Update timer in header
                    difficultyModal.hide();
                    window.location.href = `?difficulty=${diff}`;
                });
            });

            const tiles = document.querySelectorAll('.puzzle-tile:not(.empty)');
            tiles.forEach(tile => {
                tile.addEventListener('click', () => moveTile(tile));
            });

            document.getElementById('reset-btn').addEventListener('click', () => {
                
                    clearInterval(timerInterval);
                    window.location.href = '?reset=1';
                
            });

            document.querySelectorAll('.image-option').forEach(option => {
                option.addEventListener('click', () => {
                    const image = option.dataset.image;
                    window.location.href = `?image=${image}`;
                });
            });

            document.getElementById('continue-btn').addEventListener('click', () => {
                window.location.href = '?reset=1';
            });

            document.getElementById('exit-btn').addEventListener('click', () => {
                window.location.href = '../view/gamesMenu.php';
            });

            document.getElementById('exit-btn2').addEventListener('click', () => {
                window.location.href = '../view/gamesMenu.php';
            });

            document.getElementById('musicToggle').addEventListener('click', () => {
                const audio = document.getElementById('gameMusic');
                if (audio.paused) {
                    audio.play();
                    this.querySelector('i').classList.remove('fa-volume-xmark');
                    this.querySelector('i').classList.add('fa-volume-high');
                    fetch('../config/updateMusicPreference.php?enable=1');
                } else {
                    audio.pause();
                    this.querySelector('i').classList.remove('fa-volume-high');
                    this.querySelector('i').classList.add('fa-volume-xmark');
                    fetch('../config/updateMusicPreference.php?enable=0');
                }
            });
        }

        function startTimer() {
            elapsedTime = 0;
            clearInterval(timerInterval);
            timerInterval = setInterval(function() {
                elapsedTime++;
                var minutes = Math.floor(elapsedTime / 60);
                var seconds = elapsedTime % 60;
                document.getElementById('timer').textContent =
                    (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
            }, 1000);
            // Actualiza inmediatamente el elemento del tiempo al iniciar
            document.getElementById('timer').textContent = '00:00';
        }

        function formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        }

        function moveTile(tile) {
            const index = parseInt(tile.dataset.index);
            
            fetch('PuzzleDeslizante.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=move&index=${index}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updatePuzzleState(data.state);
                    moves = data.moves;
                    document.getElementById('moves').textContent = moves;
                    
                    if (data.solved) {
                        clearInterval(timerInterval);
                        document.getElementById('victory-moves').textContent = data.moves;
                        document.getElementById('victory-time').textContent = data.time;
                        document.getElementById('victory-points').textContent = data.points;
                        
                        victoryModal.show();
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function updatePuzzleState(state) {
            const puzzleGrid = document.getElementById('puzzle-grid');
            puzzleGrid.innerHTML = '';
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
                    const row = Math.floor((tileNumber - 1) / gridSize);
                    const col = (tileNumber - 1) % gridSize;
                    
                    const numberElement = document.createElement('div');
                    numberElement.className = 'number';
                    numberElement.textContent = tileNumber;
                    tileElement.appendChild(numberElement);
                    
                    const imageElement = document.createElement('div');
                    imageElement.className = 'tile-image';
                    imageElement.style.backgroundImage = `url("../img/plantas/${imageNumber}")`;
                    imageElement.style.backgroundSize = `${gridSize * 100}%`;
                    imageElement.style.backgroundPosition = `${col * 100 / (gridSize - 1)}% ${row * 100 / (gridSize - 1)}%`;
                    tileElement.appendChild(imageElement);
                    
                    tileElement.addEventListener('click', () => moveTile(tileElement));
                }
                
                puzzleGrid.appendChild(tileElement);
            }
        }

        // Remove redundant savePoints and saveGameResult from JavaScript
        // Use PHP functions via AJAX in moveTile

        document.addEventListener('DOMContentLoaded', initGame);
        document.addEventListener('DOMContentLoaded', function() {
            var skipBtn = document.getElementById('skipInstructions');   
            var comoJugarBtn = document.getElementById('comoJugar');
            var instructionsModalEl = document.getElementById('instructionsModal');
            var instructionsModal = new bootstrap.Modal(instructionsModalEl, {backdrop: 'static', keyboard: false});
            var dificultadModalEl = document.getElementById('difficultyModal');
            var dificultadModal = dificultadModalEl ? bootstrap.Modal.getOrCreateInstance(dificultadModalEl) : null;
            
            comoJugarBtn.addEventListener('click', function() {
                if (dificultadModalEl && dificultadModalEl.classList.contains('show')) {
                    dificultadModal.hide();
                }
                instructionsModal.show();
            });
            skipBtn.addEventListener('click', function() {
                instructionsModal.hide();
                if (dificultadModal) {
                    dificultadModal.show();
                }
            });
        });
    </script>
</body>
</html>