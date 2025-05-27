<?php
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../connection/database.php';
require_once __DIR__ . '/../config/dataPlanta.php';

// Reiniciar el juego solo si no hay parámetros relevantes
if (!isset($_GET['check_difficulty']) && !isset($_GET['set_difficulty']) && !isset($_POST['action']) && !isset($_GET['difficulty'])) {
    unset($_SESSION['memorama_cards']);
    unset($_SESSION['memorama_flipped']);
    unset($_SESSION['memorama_matched']);
    unset($_SESSION['memorama_moves']);
    unset($_SESSION['memorama_start_time']);
    unset($_SESSION['memorama_difficulty']);
    unset($_SESSION['memorama_pairs']);
}

// Manejar peticiones AJAX para verificar dificultad
if (isset($_GET['check_difficulty'])) {
    $numPairs = 6; // Valor predeterminado
    if (isset($_SESSION['memorama_difficulty'])) {
        if ($_SESSION['memorama_difficulty'] == 'easy') {
            $numPairs = 6;
        } elseif ($_SESSION['memorama_difficulty'] == 'hard') {
            $numPairs = 8;
        } elseif ($_SESSION['memorama_difficulty'] == 'notime') {
            $numPairs = 8;
        }
        $_SESSION['memorama_pairs'] = $numPairs; // Guardar en sesión
    }
    $response = [
        'difficulty_set' => isset($_SESSION['memorama_difficulty']),
        'difficulty' => isset($_SESSION['memorama_difficulty']) ? $_SESSION['memorama_difficulty'] : null,
        'total_pairs' => $numPairs
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Manejar establecimiento de dificultad
if (isset($_GET['set_difficulty'])) {
    $_SESSION['memorama_difficulty'] = $_GET['set_difficulty'];
    $numPairs = 6; // Valor predeterminado
    if ($_SESSION['memorama_difficulty'] == 'easy') {
        $numPairs = 6;
    } elseif ($_SESSION['memorama_difficulty'] == 'hard') {
        $numPairs = 8;
    } elseif ($_SESSION['memorama_difficulty'] == 'notime') {
        $numPairs = 8;
    }
    $_SESSION['memorama_pairs'] = $numPairs;
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}

// Función para obtener imágenes de plantas
function obtenerImagenesPlantas() {
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT id, nombre_comun FROM ficha_planta");
    $stmt->execute();
    $plantas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $plantasConImagenes = [];
    foreach ($plantas as $planta) {
        $nombreImagen = strtolower(str_replace(' ', '', explode(',', $planta['nombre_comun'])[0]));
        if (file_exists(__DIR__ . "/../img/plantas/{$nombreImagen}.png")) {
            $plantasConImagenes[] = [
                'id' => $planta['id'],
                'nombre' => $planta['nombre_comun'],
                'imagen' => $nombreImagen
            ];
        }
    }
    
    shuffle($plantasConImagenes);
    return $plantasConImagenes;
}

// Variables para usuarios no autenticados
$userId = null;
$musicEnabled = true;

if (isset($_SESSION['user'])) {
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

// Reiniciar el juego si se solicita o al salir
if (isset($_GET['reset']) || isset($_GET['exit'])) {
    unset($_SESSION['memorama_cards']);
    unset($_SESSION['memorama_flipped']);
    unset($_SESSION['memorama_matched']);
    unset($_SESSION['memorama_moves']);
    unset($_SESSION['memorama_start_time']);
    unset($_SESSION['memorama_difficulty']);
    unset($_SESSION['memorama_pairs']);
}

// Inicializar dificultad desde GET si no existe en sesión
if (isset($_GET['difficulty']) && !isset($_SESSION['memorama_difficulty'])) {
    $_SESSION['memorama_difficulty'] = $_GET['difficulty'];
}

// Establecer número de pares según la dificultad
if (isset($_SESSION['memorama_difficulty'])) {
    $numPairs = 6; // Valor predeterminado
    if ($_SESSION['memorama_difficulty'] == 'easy') {
        $numPairs = 6;
    } elseif ($_SESSION['memorama_difficulty'] == 'hard') {
        $numPairs = 8;
    } elseif ($_SESSION['memorama_difficulty'] == 'notime') {
        $numPairs = 8;
    }
    $_SESSION['memorama_pairs'] = $numPairs; // Siempre guardar en sesión
}

// Inicializar el juego si hay dificultad y no hay cartas
if (isset($_SESSION['memorama_difficulty']) && !isset($_SESSION['memorama_cards'])) {
    $plantasDisponibles = obtenerImagenesPlantas();
    $plantasSeleccionadas = array_slice($plantasDisponibles, 0, $numPairs);
    $cards = [];
    foreach ($plantasSeleccionadas as $planta) {
        $cards[] = $planta;
        $cards[] = $planta;
    }
    shuffle($cards);
    $_SESSION['memorama_cards'] = $cards;
    $_SESSION['memorama_flipped'] = array_fill(0, count($cards), false);
    $_SESSION['memorama_matched'] = array_fill(0, count($cards), false);
    $_SESSION['memorama_moves'] = 0;
    $_SESSION['memorama_start_time'] = time();
}

// Procesar acciones AJAX
if (isset($_POST['action'])) {
    $response = ['success' => false];
    
    if ($_POST['action'] == 'flip') {
        $index = (int)$_POST['index'];
        
        if (!$_SESSION['memorama_flipped'][$index] && !$_SESSION['memorama_matched'][$index]) {
            $_SESSION['memorama_flipped'][$index] = true;
            
            $flippedCount = 0;
            $flippedIndexes = [];
            foreach ($_SESSION['memorama_flipped'] as $i => $flipped) {
                if ($flipped && !$_SESSION['memorama_matched'][$i]) {
                    $flippedCount++;
                    $flippedIndexes[] = $i;
                }
            }
            
            if ($flippedCount == 2) {
                $_SESSION['memorama_moves']++;
                
                $card1 = $_SESSION['memorama_cards'][$flippedIndexes[0]]['id'];
                $card2 = $_SESSION['memorama_cards'][$flippedIndexes[1]]['id'];
                
                if ($card1 == $card2) {
                    $_SESSION['memorama_matched'][$flippedIndexes[0]] = true;
                    $_SESSION['memorama_matched'][$flippedIndexes[1]] = true;
                    
                    $allMatched = !in_array(false, $_SESSION['memorama_matched']);
                    
                    $response['match'] = true;
                    $response['allMatched'] = $allMatched;
                    
                    if ($allMatched) {
                        $endTime = time();
                        $duration = $endTime - $_SESSION['memorama_start_time'];
                        
                        $points = calculatePoints($_SESSION['memorama_difficulty'], $_SESSION['memorama_pairs'], $_SESSION['memorama_moves'], $duration);
                        $response['points'] = $points;
                        $response['time'] = formatTime($duration);
                        $response['moves'] = $_SESSION['memorama_moves'];
                        
                        if (isset($_SESSION['usuario_id'])) {
                            saveGameResult(true, $duration, $points);
                        }
                    }
                } else {
                    $response['match'] = false;
                    $_SESSION['memorama_flipped'][$flippedIndexes[0]] = false;
                    $_SESSION['memorama_flipped'][$flippedIndexes[1]] = false;
                }
            }
            
            $response['success'] = true;
            $response['card'] = $_SESSION['memorama_cards'][$index];
            $response['moves'] = $_SESSION['memorama_moves'];
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Función para calcular puntos
function calculatePoints($difficulty, $pairs, $moves, $duration) {
    $basePoints = 0;
    if ($difficulty == 'easy') {
        $basePoints = 100;
    } elseif ($difficulty == 'hard') {
        $basePoints = 200;
    } elseif ($difficulty == 'notime') {
        $basePoints = 150;
    }
    
    $pairsBonus = $pairs * 10;
    $movesMultiplier = max(0.5, 1 - (($moves - $pairs) / ($pairs * 3)));
    $timeBonus = 0;
    if ($difficulty != 'notime') {
        $expectedTime = $pairs * 5;
        $timeMultiplier = max(0.5, min(1.5, $expectedTime / max(1, $duration)));
        $timeBonus = $basePoints * 0.5 * $timeMultiplier;
    }
    
    $totalPoints = round(($basePoints + $pairsBonus) * $movesMultiplier + $timeBonus);
    return $totalPoints;
}

// Función para formatear el tiempo
function formatTime($seconds) {
    $minutes = floor($seconds / 60);
    $seconds = $seconds % 60;
    return sprintf('%02d:%02d', $minutes, $seconds);
}

// Función para guardar el resultado del juego
function saveGameResult($won, $duration, $points) {
    $gameData = [
        'usuario_id' => $_SESSION['usuario_id'],
        'duracion' => $duration,
        'fue_ganado' => $won ? 1 : 0
    ];
    // Implementar lógica para guardar en la base de datos si es necesario
}

// Calcular tiempo transcurrido
$elapsedTime = 0;
if (isset($_SESSION['memorama_start_time'])) {
    $elapsedTime = time() - $_SESSION['memorama_start_time'];
}

// Determinar texto de dificultad
$difficultyText = 'Fácil';
if (isset($_SESSION['memorama_difficulty'])) {
    if ($_SESSION['memorama_difficulty'] == 'hard') {
        $difficultyText = 'Difícil';
    } elseif ($_SESSION['memorama_difficulty'] == 'notime') {
        $difficultyText = 'Sin tiempo';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memorama - BCS Flora Games</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/styleGames.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="../img/logoFG.ico">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
        }
        .game-header {
            background-color: #ffffff;
            padding: 10px 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 0 20px;
        }
        h1 {
            color: #1a73e8;
            text-align: center;
            margin-bottom: 20px;
        }
        .game-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .info-item {
            display: flex;
            align-items: center;
        }
        .info-item i {
            margin-right: 8px;
            color: #1a73e8;
        }
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 1rem;
            border-radius: 1rem;
        }
        .cardMemory {
            aspect-ratio: 1;
            background: linear-gradient(15deg, #5ED646, rgb(163, 250, 132));
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: white;
            transition: transform 0.3s ease;
            transform-style: preserve-3d;
            position: relative;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .cardMemory .front,
        .cardMemory .back {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }
        .cardMemory .front {
            background-image: url("/BCS_FLORAGAMES/img/frontalMemorama.png");
            transform: rotateY(0deg);
            background-size: contain;
            background-origin: border-box;
            background-repeat: no-repeat;
            border: 2px solid #436745;
        }
        .cardMemory .back {
            background-color: white;
            transform: rotateY(180deg);
            border: 2px solid #436745;
            overflow: hidden;
        }
        .cardMemory .back img {
            width: 90%;
            height: 90%;
            object-fit: contain;
        }
        .cardMemory.flipped {
            transform: rotateY(180deg);
        }
        .cardMemory.matched .back {
            background-color: #d4edda;
            border-color: #34a853;
        }
        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #1a73e8;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .back-button:hover {
            background-color: #1557b0;
        }
        .reset-btn {
            color: #246741;
            background-color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }
        .reset-btn:hover {
            background-color: #246741;
            color: white;
        }
        .timer {
            font-weight: bold;
        }
        @media (max-width: 768px) {
            .cards-grid {
                grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
                gap: 10px;
            }
            .game-info {
                flex-direction: column;
                gap: 10px;
            }
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
            <h5 style="margin:0">Memorama</h5>
            <div class="level">Modo: <span id="difficulty-display"><?php echo $difficultyText; ?></span></div>
        </div>
        <div style="display:flex; flex-direction:row; gap:10px">
            <div>
                <div class="hd-sec-gm-v2" style="display:flex; flex-direction:row; gap:10px">
                    <div>
                        <h5><span id="matched-pairs">0</span>/<span id="total-pairs"><?php echo isset($_SESSION['memorama_pairs']) ? $_SESSION['memorama_pairs'] : '6'; ?></span></h5>
                    </div>
                    <div class="timer"><h5><i class="fa-solid fa-clock me-2"></i></h5><h5 id="timer">00:00</h5></div>
                </div>
            </div>
        </div>
    </div>

    <div class="contenedor">
        <div class="container">
            <div class="cards-grid">
                <?php
                if (isset($_SESSION['memorama_cards'])) {
                    for ($i = 0; $i < count($_SESSION['memorama_cards']); $i++) {
                        $card = $_SESSION['memorama_cards'][$i];
                        $flipped = $_SESSION['memorama_flipped'][$i] ? 'flipped' : '';
                        $matched = $_SESSION['memorama_matched'][$i] ? 'matched' : '';
                        echo "<div class='cardMemory $flipped $matched' data-index='$i' data-card='{$card['id']}'>";
                        echo "<div class='front'></div>";
                        echo "<div class='back'><img src='../img/plantas/{$card['imagen']}.png' alt='{$card['nombre']}'></div>";
                        echo "</div>";
                    }
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
                    <p><strong>Fácil:</strong> 6 pares de cartas con tiempo</p>
                    <button class="difficulty-btn" data-difficulty="easy">Fácil</button>
                    <p><strong>Difícil:</strong> 8 pares de cartas con tiempo</p>
                    <button class="difficulty-btn" data-difficulty="hard">Difícil</button>
                    <p><strong>Sin tiempo:</strong> 8 pares de cartas</p>
                    <button class="difficulty-btn" data-difficulty="notime">Sin tiempo</button>
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
                    <h5 class="modal-title" id="victoryModalLabel">¡Bien hecho!</h5>
                </div>
                <div class="modal-body text-center">
                    <p>Has completado el juego</p>
                    <div class="victory-stats">
                        <p><i class="fas fa-clock me-2"></i> Tiempo: <span id="victory-time" style="margin-left: 5px;">00:00</span></p>
                        <p><i class="fas fa-trophy me-2"></i> Modo: <span id="victory-mode" style="margin-left: 5px;"><?php echo $difficultyText; ?></span></p>
                        <p><i class="fas fa-sync-alt me-2"></i> Movimientos: <span id="victory-moves" style="margin-left: 5px;">0</span></p>
                        <p><i class="fas fa-star me-2"></i> Puntos: <span id="victory-points" style="margin-left: 5px;">0</span></p>
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
                    <p>1.- Toca dos cartas para voltearlas. Si son iguales, se quedan visibles. Si no coinciden, se voltean de nuevo.</p>
                    <p>2.- El objetivo es encontrar todos los pares con la menor cantidad de movimientos en el menor tiempo posible.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="skipInstructions">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <div id="notificacion-planta" class="notificacion">
        <span id="mensaje-notificacion"></span>
    </div>

    <?php include '../components/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let timer;
        let seconds = 0;
        let flippedCards = [];
        let matchedPairs = 0;
        let moves = <?php echo isset($_SESSION['memorama_moves']) ? $_SESSION['memorama_moves'] : '7'; ?>;
        let totalPairs = <?php echo isset($_SESSION['memorama_pairs']) ? $_SESSION['memorama_pairs'] : '6'; ?>;
        let isProcessing = false;
        let difficultyModal;
        let victoryModal;

        function initGame() {
            flippedCards = [];
            matchedPairs = 0;
            seconds = 0;
            isProcessing = false;

            document.getElementById('timer').textContent = '00:00';
            document.getElementById('matched-pairs').textContent = '0';

            difficultyModal = new bootstrap.Modal(document.getElementById('difficultyModal'));
            victoryModal = new bootstrap.Modal(document.getElementById('victoryModal'));

            fetch('memorama.php?check_difficulty=1')
                .then(response => response.json())
                .then(data => {
                    totalPairs = data.total_pairs;
                    document.getElementById('total-pairs').textContent = totalPairs;
                    document.getElementById('difficulty-display').textContent = data.difficulty ? 
                        (data.difficulty === 'easy' ? 'Fácil' : data.difficulty === 'hard' ? 'Difícil' : 'Sin tiempo') : 'Fácil';
                    
                    if (!data.difficulty_set) {
                        difficultyModal.show();
                    } else {
                        if (data.difficulty !== 'notime') {
                            startTimer();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    difficultyModal.show();
                });

            const cards = document.querySelectorAll('.cardMemory');
            cards.forEach(card => {
                card.addEventListener('click', handleCardClick);
            });
        }

        document.querySelectorAll('.difficulty-btn').forEach(button => {
            button.addEventListener('click', function() {
                const difficulty = this.getAttribute('data-difficulty');
                fetch('memorama.php?set_difficulty=' + difficulty)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            difficultyModal.hide();
                            window.location.href = 'memorama.php?difficulty=' + difficulty;
                        }
                    });
            });
        });

        function handleCardClick(event) {
            if (isProcessing) return;
            const card = event.currentTarget;
            if (card.classList.contains('flipped') || card.classList.contains('matched')) {
                return;
            }

            card.classList.add('flipped');
            flippedCards.push(card);

            if (flippedCards.length === 2) {
                isProcessing = true;
                const card1 = flippedCards[0];
                const card2 = flippedCards[1];

                if (card1.getAttribute('data-card') === card2.getAttribute('data-card')) {
                    setTimeout(() => {
                        card1.classList.add('matched');
                        card2.classList.add('matched');
                        flippedCards = [];
                        isProcessing = false;
                        matchedPairs++;
                        document.getElementById('matched-pairs').textContent = matchedPairs;
                        
                        // Obtener el nombre de la planta del elemento back img alt
                        const plantaName = card1.querySelector('.back img').alt;
                        // Llamar a la función de notificación
                        mostrarNotificacion({card_name: plantaName});
                        
                        if (matchedPairs >= totalPairs) {
                            endGame();
                        }
                    }, 500);
                } else {
                    setTimeout(() => {
                        card1.classList.remove('flipped');
                        card2.classList.remove('flipped');
                        flippedCards = [];
                        isProcessing = false;
                    }, 1000);
                }

                fetch('memorama.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=flip&index=' + card2.getAttribute('data-index')
                });
            }
        }

        function startTimer() {
            clearInterval(timer);
            timer = setInterval(() => {
                seconds++;
                const minutes = Math.floor(seconds / 60);
                const remainingSeconds = seconds % 60;
                document.getElementById('timer').textContent = 
                    (minutes < 10 ? '0' : '') + minutes + ':' + 
                    (remainingSeconds < 10 ? '0' : '') + remainingSeconds;
            }, 1000);
        }

        function endGame() {
            clearInterval(timer);
            document.getElementById('victory-time').textContent = document.getElementById('timer').textContent;
            document.getElementById('victory-points').textContent = calculatePoints();
            document.getElementById('victory-moves').textContent = moves;

            saveGameResult(1,document.getElementById('timer').textContent);
            savePoints(calculatePoints());
            victoryModal.show();
        }

        function calculatePoints() {
            return Math.max(500 - seconds * 5, 100);
        }

        document.getElementById('exit-btn').addEventListener('click', function() {
            window.location.href = '../view/gamesMenu.php';
        });

        document.getElementById('exit-btn2').addEventListener('click', function() {
            window.location.href = '../view/gamesMenu.php';
        });

        document.getElementById('continue-btn').addEventListener('click', function() {
            fetch('memorama.php?reset=1')
                .then(() => {
                    window.location.reload();
                });
        });

        document.getElementById('reset-btn').addEventListener('click', function() {
            fetch('memorama.php?reset=1')
                .then(() => {
                    // En lugar de recargar la página, mostrar el modal de dificultad
                    timer
                    difficultyModal.show();
                });
        });

        document.getElementById('musicToggle').addEventListener('click', function() {
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
        function mostrarNotificacion(cardData) {
                const notificacion = document.getElementById('notificacion-planta');
                const mensaje = document.getElementById('mensaje-notificacion');
                
                // Usar cardData.card_name en lugar de buscar en un objeto local
                mensaje.textContent = `¡Felicidades! Encontraste la planta ${cardData.card_name || 'especial'}`;
                
                notificacion.classList.add('mostrar');
                setTimeout(() => {
                    notificacion.classList.remove('mostrar');
                }, 3000);
            }
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
    </div>
    <script>
        function savePoints(points) {
            console.log('Intentando guardar puntos:', points); // Añadir para depuración
            
            fetch('../config/updatePoints.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `points=${points}&game=sopaDeLetras`
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
        function saveGameResult(won, duration) {
            // Solo guardar si el usuario está autenticado
            if (<?php echo isset($_SESSION['usuario_id']) ? 'true' : 'false'; ?>) {
                const userId = <?php echo isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 'null'; ?>;
                
                // Crear objeto con los datos
                const gameData = {
                    usuario_id: userId,
                    duracion: Math.floor(duration), // Duración en segundos
                    fue_ganado: won ? 1 : 0 // 1 si ganó, 0 si perdió
                };
                
                // Enviar datos al servidor
                fetch('../config/saveGameResult.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(gameData)
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Resultado guardado:', data);
                })
                .catch(error => {
                    console.error('Error al guardar resultado:', error);
                });
            }
        }
    </script>
</body>
</html>