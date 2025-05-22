<?php
session_start();

// Reiniciar el juego si se solicita
if (isset($_GET['reset']) || !isset($_SESSION['memorama_cards'])) {
    unset($_SESSION['memorama_cards']);
    unset($_SESSION['memorama_flipped']);
    unset($_SESSION['memorama_matched']);
    unset($_SESSION['memorama_moves']);
    unset($_SESSION['memorama_start_time']);
    unset($_SESSION['memorama_difficulty']);
    unset($_SESSION['memorama_pairs']);
}

// Inicializar variables de sesión si no existen
if (!isset($_SESSION['memorama_difficulty'])) {
    $_SESSION['memorama_difficulty'] = isset($_GET['difficulty']) ? $_GET['difficulty'] : '';
}

// Inicializar el juego si no existe o si se cambió la dificultad
if (!isset($_SESSION['memorama_cards']) || (isset($_GET['difficulty']) && $_GET['difficulty'] != $_SESSION['memorama_difficulty'])) {
    // Actualizar dificultad si se proporciona
    if (isset($_GET['difficulty'])) {
        $_SESSION['memorama_difficulty'] = $_GET['difficulty'];
    }
    
    // Determinar número de pares según la dificultad
    $numPairs = 6; // Valor predeterminado
    
    if ($_SESSION['memorama_difficulty'] == 'easy') {
        $numPairs = rand(6, 10);
    } elseif ($_SESSION['memorama_difficulty'] == 'hard') {
        $numPairs = rand(10, 15);
    } elseif ($_SESSION['memorama_difficulty'] == 'notime') {
        $numPairs = rand(6, 10);
    }
    
    $_SESSION['memorama_pairs'] = $numPairs;
    
    // Crear array con números del 1 al 15 (imágenes disponibles)
    $availableImages = range(1, 15);
    shuffle($availableImages);
    
    // Seleccionar los primeros N pares según la dificultad
    $selectedImages = array_slice($availableImages, 0, $numPairs);
    
    // Duplicar las imágenes para crear pares
    $cards = array_merge($selectedImages, $selectedImages);
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
        
        // Verificar que la carta no esté ya volteada o emparejada
        if (!$_SESSION['memorama_flipped'][$index] && !$_SESSION['memorama_matched'][$index]) {
            $_SESSION['memorama_flipped'][$index] = true;
            
            // Contar cartas volteadas actualmente
            $flippedCount = 0;
            $flippedIndexes = [];
            
            foreach ($_SESSION['memorama_flipped'] as $i => $flipped) {
                if ($flipped && !$_SESSION['memorama_matched'][$i]) {
                    $flippedCount++;
                    $flippedIndexes[] = $i;
                }
            }
            
            // Si hay dos cartas volteadas, verificar si son pares
            if ($flippedCount == 2) {
                $_SESSION['memorama_moves']++;
                
                $card1 = $_SESSION['memorama_cards'][$flippedIndexes[0]];
                $card2 = $_SESSION['memorama_cards'][$flippedIndexes[1]];
                
                // Si son pares, marcarlas como emparejadas
                if ($card1 == $card2) {
                    $_SESSION['memorama_matched'][$flippedIndexes[0]] = true;
                    $_SESSION['memorama_matched'][$flippedIndexes[1]] = true;
                    
                    // Verificar si todas las cartas están emparejadas
                    $allMatched = !in_array(false, $_SESSION['memorama_matched']);
                    
                    $response['match'] = true;
                    $response['allMatched'] = $allMatched;
                    
                    if ($allMatched) {
                        $endTime = time();
                        $duration = $endTime - $_SESSION['memorama_start_time'];
                        
                        // Calcular puntos
                        $points = calculatePoints($_SESSION['memorama_difficulty'], $_SESSION['memorama_pairs'], $_SESSION['memorama_moves'], $duration);
                        $response['points'] = $points;
                        $response['time'] = formatTime($duration);
                        $response['moves'] = $_SESSION['memorama_moves'];
                        
                        // Guardar resultado en la base de datos si el usuario está autenticado
                        if (isset($_SESSION['usuario_id'])) {
                            saveGameResult(true, $duration, $points);
                        }
                    }
                } else {
                    // Si no son pares, voltearlas de nuevo después de un tiempo
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
    // Puntos base según dificultad
    $basePoints = 0;
    
    if ($difficulty == 'easy') {
        $basePoints = 100;
    } elseif ($difficulty == 'hard') {
        $basePoints = 200;
    } elseif ($difficulty == 'notime') {
        $basePoints = 150;
    }
    
    // Bonificación por pares
    $pairsBonus = $pairs * 10;
    
    // Penalización por movimientos (menos movimientos = más puntos)
    $movesMultiplier = max(0.5, 1 - (($moves - $pairs) / ($pairs * 3)));
    
    // Bonificación por tiempo (solo para modos con tiempo)
    $timeBonus = 0;
    if ($difficulty != 'notime') {
        // Tiempo esperado: 5 segundos por par
        $expectedTime = $pairs * 5;
        $timeMultiplier = max(0.5, min(1.5, $expectedTime / max(1, $duration)));
        $timeBonus = $basePoints * 0.5 * $timeMultiplier;
    }
    
    // Calcular puntos totales
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
    // Guardar resultado en la tabla juego_usuario
    $gameData = [
        'usuario_id' => $_SESSION['usuario_id'],
        'duracion' => $duration,
        'fue_ganado' => $won ? 1 : 0
    ];
    
    // También actualizar los puntos del usuario
    updatePoints($points);
}

// Función para actualizar los puntos del usuario
function updatePoints($points) {
    // Esta función se implementará en el lado del cliente con JavaScript
}

// Calcular tiempo transcurrido
$elapsedTime = 0;
if (isset($_SESSION['memorama_start_time'])) {
    $elapsedTime = time() - $_SESSION['memorama_start_time'];
}

// Determinar texto de dificultad
$difficultyText = 'Fácil';
if ($_SESSION['memorama_difficulty'] == 'hard') {
    $difficultyText = 'Difícil';
} elseif ($_SESSION['memorama_difficulty'] == 'notime') {
    $difficultyText = 'Sin tiempo';
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
            background: linear-gradient(15deg, #5ED646,rgb(163, 250, 132));

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
            color:white;
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
            <h5 style="margin:0">Memorama</h5>
            <div class="level">Modo facil - Nivel: <span id="level-display">1</span></div>
        </div>
        <div style="display:flex; flex-direction:row; gap:10px">
            <div>
                <div style="display:flex; flex-direction:row; gap:10px">
                    <div>
                        <h5><span id="found-words-count">0</span>/<span id="total-words-count">0</span></h5>
                    </div>
                    <h5><i class="fa-solid fa-clock"></i></h5>
            <div class="timer"> <h5 id="timer"> 00:00</h5></div>
                </div>
            </div>
        </div>
    </div>


   <div class="contenedor">
         <div class="container">

        <div class="cards-grid">
            <?php
            for ($i = 0; $i < count($_SESSION['memorama_cards']); $i++) {
                $card = $_SESSION['memorama_cards'][$i];
                $flipped = $_SESSION['memorama_flipped'][$i] ? 'flipped' : '';
                $matched = $_SESSION['memorama_matched'][$i] ? 'matched' : '';
                echo "<div class='cardMemory $flipped $matched' data-index='$i' data-card='$card'>";
                echo "<div class='front'></div>";
                echo "<div class='back'><img src='../img/niveles/$card.png' alt='Imagen $card'></div>";
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
                    <p><strong>Fácil:</strong> 6 a 10 pares de cartas con tiempo</p>
                    <button class="difficulty-btn" data-difficulty="easy">Fácil</button>

                    <p><strong>Difícil:</strong> 10 a 15 pares de cartas con tiempo</p>
                    <button class="difficulty-btn" data-difficulty="hard">Difícil</button>

                    <p><strong>Sin tiempo:</strong> 6 a 10 pares de cartas</p>
                    <button class="difficulty-btn" data-difficulty="notime">Sin tiempo</button>
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
                    <button type="button" class="btn btn-secondary" id="continue-btn">Continuar</button>
                    <button type="button" class="btn btn-primary" id="exit-btn">Salir</button>
                </div>
            </div>
        </div>
    </div>

     <?php include '../components/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>

        
        document.addEventListener('DOMContentLoaded', function() {
            // Elementos del DOM
        // Botones de selección de dificultad
        document.querySelectorAll('.difficulty-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const diff = btn.dataset.difficulty;         // easy | hard | notime
                window.location.href = `?difficulty=${diff}`; // recarga con el parámetro
            });
        });


            // Music control initialization
        const musicToggle = document.getElementById('musicToggle');
        const musicIcon = musicToggle.querySelector('i');
        const gameMusic = document.getElementById('gameMusic');
        gameMusic.volume = 0.5;

        // Music control event listener
        musicToggle.addEventListener('click', function() {
            if (gameMusic.paused) {
                gameMusic.play();
                musicIcon.classList.remove('fa-volume-xmark');
                musicIcon.classList.add('fa-volume-high');
                updateMusicPreference(1);
            } else {
                gameMusic.pause();
                musicIcon.classList.remove('fa-volume-high');
                musicIcon.classList.add('fa-volume-xmark');
                updateMusicPreference(0);
            }
        });

        function updateMusicPreference(enabled) {
            <?php if (isset($_SESSION['user'])): ?>
            fetch('../config/updateMusicPreference.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `music_enabled=${enabled}`
            })
            .catch(error => console.error('Error updating music preference:', error));
            <?php endif; ?>
        }


            const cards = document.querySelectorAll('.cardMemory');
            const timerElement = document.getElementById('timer');
            const resetButton = document.getElementById('reset-btn');
            
            // Variables del juego
            let flippedCards = [];
            let canFlip = true;
            let timerInterval;
            let startTime = <?php echo $_SESSION['memorama_start_time'] ?? 'Date.now() / 1000'; ?>;
            let elapsedTime = <?php echo $elapsedTime; ?>;
            let gameMode = '<?php echo $_SESSION['memorama_difficulty']; ?>';
            
            // Modales
            const difficultyModal = new bootstrap.Modal(document.getElementById('difficultyModal'));
            const victoryModal = new bootstrap.Modal(document.getElementById('victoryModal'));
            
            // Mostrar modal de dificultad si no se ha seleccionado
            if (!gameMode) {
                difficultyModal.show();
            } else {
                // Iniciar temporizador si el juego ya está en progreso
                startTimer();
            }
            
            // Configurar eventos de las cartas
            cards.forEach(card => {
                card.addEventListener('click', () => flipCard(card));
            });
            
            // Configurar botón de reinicio
            resetButton.addEventListener('click', () => {
                window.location.href = '?reset=1';
            });
            
            // Configurar botones del modal de victoria
            document.getElementById('continue-btn').addEventListener('click', function() {
                window.location.href = '?reset=1';
            });
            
            document.getElementById('exit-btn').addEventListener('click', function() {
                window.location.href = '../view/gamesMenu.php';
            });
            
            // Función para voltear una carta
            function flipCard(card) {
                // Verificar si se puede voltear
                if (!canFlip) return;
                if (card.classList.contains('flipped') || card.classList.contains('matched')) return;
                
                const index = card.dataset.index;
                
                // Enviar solicitud AJAX para voltear la carta
                fetch('memorama.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=flip&index=${index}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Voltear la carta en la interfaz
                        card.classList.add('flipped');
                        flippedCards.push(card);
                        
                        
                        // Si hay dos cartas volteadas
                        if (flippedCards.length === 2) {
                            canFlip = false;
                            
                            setTimeout(() => {
                                if (data.match) {
                                    // Si son pares, marcarlas como emparejadas
                                    flippedCards.forEach(c => c.classList.add('matched'));
                                    
                                    // Verificar si todas las cartas están emparejadas
                                    if (data.allMatched) {
                                        handleVictory(data);
                                    }
                                } else {
                                    // Si no son pares, voltearlas de nuevo
                                    flippedCards.forEach(c => c.classList.remove('flipped'));
                                }
                                
                                flippedCards = [];
                                canFlip = true;
                            }, 1000);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
            
            // Función para iniciar el temporizador
            function startTimer() {
                // Si es modo sin tiempo, no mostrar temporizador
                if (gameMode === 'notime') {
                    timerElement.textContent = '--:--';
                    return;
                }
                
                // Actualizar temporizador cada segundo
                timerInterval = setInterval(() => {
                    elapsedTime++;
                    updateTimerDisplay();
                }, 1000);
            }
            
            // Función para actualizar la visualización del temporizador
            function updateTimerDisplay() {
                const minutes = Math.floor(elapsedTime / 60);
                const seconds = elapsedTime % 60;
                timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }
            
            // Función para manejar la victoria
            function handleVictory(data) {
                // Detener el temporizador
                clearInterval(timerInterval);
                
                // Actualizar estadísticas en el modal de victoria
                document.getElementById('victory-time').textContent = data.time;
                document.getElementById('victory-moves').textContent = data.moves;
                document.getElementById('victory-points').textContent = data.points;
                
                // Guardar puntos en la base de datos
                savePoints(data.points);
                
                // Mostrar modal de victoria
                setTimeout(() => {
                    victoryModal.show();
                }, 500);
            }
            
            // Función para guardar puntos
            function savePoints(points) {
                // Solo guardar si el usuario está autenticado
                if (<?php echo isset($_SESSION['usuario_id']) ? 'true' : 'false'; ?>) {
                    fetch('../config/updatePoints.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `points=${points}&game=memorama`
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
            }
        });
    </script>
</body>

</html>