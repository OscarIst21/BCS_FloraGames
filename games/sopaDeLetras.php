<?php
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../connection/database.php';
require_once __DIR__ . '/../config/dataPlanta.php';

function obtenerPalabras() {
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT nombre_comun FROM ficha_planta");
    $stmt->execute();
    $nombres = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Dividir nombres por coma y limpiar espacios
    $todosNombres = [];
    foreach ($nombres as $nombre) {
        $partes = explode(',', $nombre);
        foreach ($partes as $parte) {
            $nombreLimpio = trim($parte);
            if ($nombreLimpio !== '') {
                $todosNombres[] = $nombreLimpio;
            }
        }
    }

    // Mezclar todos los nombres
    shuffle($todosNombres);

    // Seleccionar palabras para cada modo, pero no más de las que existan
    $palabras = [
        'facil' => array_slice($todosNombres, 0, min(4, count($todosNombres))),
        'dificil' => array_slice($todosNombres, 0, min(6, count($todosNombres)))
    ];

    return $palabras;
}

// Variables para usuarios no autenticados
$userId = null;
$musicEnabled = true; // Valor predeterminado

// Verificar si el usuario está autenticado
if (isset($_SESSION['user'])) {
    // Obtener preferencia de música de la base de datos
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

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sopa de Letras - Flora Games</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/stylesMedia.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../img/logoFG.ico">
    
    <style>
        .game-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .timer {
            font-size: 1.5rem;
            font-weight: bold;
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

        .level {
            font-size: 1.2rem;
        }

        .board-container {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .letter-board {
            display: grid;
            grid-template-columns: repeat(15, 1fr);
            gap: 5px;
            margin: 0 auto;
            max-width: 600px;
        }

        .letter-cell {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f0f0f0;
            border-radius: 5px;
            font-weight: bold;
            font-size: 1.1rem;
            cursor: pointer;
            user-select: none;
            transition: all 0.2s;
        }

        .letter-cell:hover {
            background-color: #e0e0e0;
        }

        .letter-cell.selected {
            background-color: #c8e6c9;
        }

        .letter-cell.found {
            background-color: #246741;
            color: white;
        }

        .word-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .word-item {
            background-color: #f0f0f0;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .word-item.found {
            background-color: #246741;
            color: white;
            text-decoration: line-through;
        }

        .modal-header {
            background-color: #246741;
            color: white;
        }

        .difficulty-btn {
            width: 100%;
            margin-bottom: 10px;
            padding: 15px;
            font-size: 1.2rem;
            background-color: #f0f0f0;
            border: none;
            border-radius: 10px;
            transition: all 0.3s;
        }

        .difficulty-btn:hover {
            background-color: #c8e6c9;
        }

        .difficulty-btn.selected {
            background-color: #246741;
            color: white;
        }

        .header-box {
            background-color: #246741;
            color: white;
            text-align: center;
            padding: 0.5rem;
            border-radius: 10px 10px 0px 0px;
        }

        .box {
            background-color: white;
            min-height: 50px;
            border-radius: 0px 0px 10px 10px;
            padding: 1rem;
        }

        .victory-stats {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin: 20px 0;
        }

        .victory-stats p {
            font-size: 1.1rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-header.bg-success {
            background-color: #246741 !important;
        }

        #retry-btn {
            background-color: #246741;
            color: white;
            border: none;
        }

        #retry-btn:hover {
            background-color: #1a4d30;
        }

        #exit-btn {
            background-color: #6c757d;
            color: white;
            border: none;
        }

        #exit-btn:hover {
            background-color: #5a6268;
        }

        @media (max-width: 576px) {
            .letter-cell {
                width: 18px;
                height: 18px;
                font-size: 0.7rem;
            }

            .letter-board {
                grid-template-columns: repeat(15, 1fr);
                gap: 2px;
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
            <h5>Sopa de letras</h5>
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

    <div class="page-container">
        <div class="game-container">
            <div class="board-container">
                <div class="letter-board" id="letter-board">
                    <!-- Las letras se generarán con JavaScript -->
                </div>
            </div>

            <div class="box-list">
                <div class="header-box">Palabras</div>
                <div class="box">
                    <div class="word-list" id="word-list">
                        <!-- Las palabras se generarán con JavaScript -->
                    </div>
                </div>

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
                    <button class="difficulty-btn" data-difficulty="notime">Sin tiempo</button>
                    <button class="difficulty-btn" data-difficulty="easy">Fácil</button>
                    <button class="difficulty-btn" data-difficulty="hard">Difícil</button>
                    <button type="button" class="btn btn-primary" id="exit-btn">Salir</button>
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
                    <p>1.- Busca y marca las palabras escondidas en una cuadrícula de letras.</p>
                    <p>2.- Las palabras pueden estar en cualquier dirección.</p>
                    <p>3.- El juego termina cuando se encuentran todas o se termine el tiempo.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="skipInstructions">Omitir</button>
                    <button type="button" class="btn btn-primary" id="dontShowAgain">No volver a mostrar</button>
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
                    <p>Has completado esta partida</p>
                    <div class="victory-stats">
                        <p><i class="fas fa-clock me-2"></i> Tiempo: <span id="victory-time" style="margin-left: 5px;">00:00</span></p>
                        <p><i class="fas fa-trophy me-2"></i> Nivel: <span id="victory-level" style="margin-left: 5px;">1</span></p>
                        <p><i class="fas fa-star me-2"></i> Puntos: <span id="victory-points" style="margin-left: 5px;">100</span></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="retry-btn">Continuar</button>
                    <button type="button" class="btn btn-primary" id="exit-btn">Salir</button>
                </div>
            </div>
        </div>
    </div>

    <?php include '../components/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Obtener palabras desde PHP según la dificultad
            <?php
                $palabras = obtenerPalabras();
                echo "const palabrasPorDificultad = {";
                echo "easy: " . json_encode(array_map('mb_strtoupper', $palabras['facil']), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ",";
                echo "hard: " . json_encode(array_map('mb_strtoupper', $palabras['dificil']), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                echo "};";
            ?>

            // DEBUG: Mostrar en consola las palabras que se traen de PHP
            console.log("Palabras modo fácil:", palabrasPorDificultad.easy);
            console.log("Palabras modo difícil:", palabrasPorDificultad.hard);

            let boardSize = 15;
            let board = [];
            let words = [];
            let foundWords = [];
            let selectedCells = [];
            let timeElapsed = 0;
            let timerInterval;
            let gameMode = 'easy'; // Por defecto
            let timeLimit = 180; // Por defecto, puedes ajustar según dificultad

            const letterBoardElement = document.getElementById('letter-board');
            const wordListElement = document.getElementById('word-list');
            const timerElement = document.getElementById('timer');
            const resetButton = document.getElementById('reset-btn');
            const levelDisplay = document.getElementById('level-display');

            // Modales
            const difficultyModal = new bootstrap.Modal(document.getElementById('difficultyModal'));
            const instructionsModal = new bootstrap.Modal(document.getElementById('instructionsModal'));

            // Mostrar modal de dificultad al cargar la página
            difficultyModal.show();

            // Configurar botones de dificultad
            const difficultyButtons = document.querySelectorAll('.difficulty-btn');
            difficultyButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Quitar selección anterior
                    difficultyButtons.forEach(btn => btn.classList.remove('selected'));
                    // Añadir selección actual
                    this.classList.add('selected');

                    // Establecer modo de juego
                    if (this.dataset.difficulty === 'easy') {
                        gameMode = 'easy';
                    } else if (this.dataset.difficulty === 'hard') {
                        gameMode = 'hard';
                    } else {
                        gameMode = 'notime';
                    }

                    // Cerrar modal de dificultad
                    difficultyModal.hide();

                    // Verificar si es la primera vez que se juega
                    if (!localStorage.getItem('sopaLetrasInstructionsShown')) {
                        instructionsModal.show();
                    } else {
                        // Iniciar juego
                        initGame();
                    }
                });
            });

            // Agregar manejador para el botón "Salir" del modal de dificultad
            document.getElementById('exit-btn').addEventListener('click', function() {
                window.location.href = '../view/gamesMenu.php';
            });

            // Configurar botones de instrucciones
            document.getElementById('skipInstructions').addEventListener('click', function() {
                instructionsModal.hide();
                initGame();
            });

            document.getElementById('dontShowAgain').addEventListener('click', function() {
                localStorage.setItem('sopaLetrasInstructionsShown', 'true');
                instructionsModal.hide();
                initGame();
            });

            // Función para inicializar el juego
            function initGame() {
                // Reiniciar variables
                foundWords = [];
                selectedCells = [];
                timeElapsed = 0;
                words = palabrasPorDificultad[gameMode] || [];

                // Actualizar contador de palabras
                document.getElementById('total-words-count').textContent = words.length;
                document.getElementById('found-words-count').textContent = '0';

                // Establecer límite de tiempo según dificultad
                if (gameMode === 'easy') {
                    timeLimit = 180;
                } else if (gameMode === 'hard') {
                    timeLimit = 120;
                } else {
                    timeLimit = 0;
                }

                // Crear tablero vacío
                board = [];
                for (let i = 0; i < boardSize; i++) {
                    board[i] = [];
                    for (let j = 0; j < boardSize; j++) {
                        board[i][j] = {
                            letter: '',
                            words: []
                        };
                    }
                }

                // Colocar palabras en el tablero
                placeWords();

                // Llenar espacios vacíos con letras aleatorias
                fillEmptySpaces();

                // Renderizar tablero
                renderBoard();

                // Renderizar lista de palabras
                renderWordList();

                // Iniciar temporizador
                startTimer();

                // Configurar evento de reinicio
                resetButton.onclick = resetGame;
            }

            // Función para colocar palabras en el tablero
            function placeWords() {
                for (const word of words) {
                    let placed = false;
                    let attempts = 0;

                    while (!placed && attempts < 100) {
                        attempts++;

                        // Elegir dirección aleatoria (0: horizontal, 1: vertical, 2: diagonal)
                        const direction = Math.floor(Math.random() * 3);

                        // Validación: si la palabra es más larga que el tablero, saltar intento
                        if (word.length > boardSize) continue;

                        // Elegir posición inicial aleatoria
                        let row, col;

                        if (direction === 0) { // Horizontal
                            row = Math.floor(Math.random() * boardSize);
                            col = Math.floor(Math.random() * (boardSize - word.length + 1));
                        } else if (direction === 1) { // Vertical
                            row = Math.floor(Math.random() * (boardSize - word.length + 1));
                            col = Math.floor(Math.random() * boardSize);
                        } else { // Diagonal
                            row = Math.floor(Math.random() * (boardSize - word.length + 1));
                            col = Math.floor(Math.random() * (boardSize - word.length + 1));
                        }

                        // Verificar si la palabra cabe en la posición elegida
                        let canPlace = true;
                        const positions = [];

                        for (let i = 0; i < word.length; i++) {
                            let r, c;

                            if (direction === 0) { // Horizontal
                                r = row;
                                c = col + i;
                            } else if (direction === 1) { // Vertical
                                r = row + i;
                                c = col;
                            } else { // Diagonal
                                r = row + i;
                                c = col + i;
                            }

                            // Validación extra: asegurarse de que r y c estén dentro de los límites
                            if (r < 0 || r >= boardSize || c < 0 || c >= boardSize) {
                                canPlace = false;
                                break;
                            }

                            // Verificar si la celda está vacía o tiene la misma letra
                            if (board[r][c].letter !== '' && board[r][c].letter !== word[i]) {
                                canPlace = false;
                                break;
                            }

                            positions.push({
                                row: r,
                                col: c
                            });
                        }

                        // Colocar la palabra si es posible
                        if (canPlace) {
                            for (let i = 0; i < word.length; i++) {
                                const pos = positions[i];
                                board[pos.row][pos.col].letter = word[i];
                                board[pos.row][pos.col].words.push(word);
                            }
                            placed = true;
                        }
                    }

                    // Si no se pudo colocar después de varios intentos, intentar con otra dirección
                    if (!placed) {
                        console.warn(`No se pudo colocar la palabra: ${word}`);
                    }
                }
            }

            // Función para llenar espacios vacíos con letras aleatorias
            function fillEmptySpaces() {
                const letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

                for (let i = 0; i < boardSize; i++) {
                    for (let j = 0; j < boardSize; j++) {
                        if (board[i][j].letter === '') {
                            const randomIndex = Math.floor(Math.random() * letters.length);
                            board[i][j].letter = letters[randomIndex];
                        }
                    }
                }
            }

            // Función para renderizar el tablero
            function renderBoard() {
                letterBoardElement.innerHTML = '';

                for (let i = 0; i < boardSize; i++) {
                    for (let j = 0; j < boardSize; j++) {
                        const cell = document.createElement('div');
                        cell.className = 'letter-cell';
                        cell.textContent = board[i][j].letter;
                        cell.dataset.row = i;
                        cell.dataset.col = j;

                        // Agregar eventos de interacción
                        cell.addEventListener('mousedown', startSelection);
                        cell.addEventListener('mouseover', continueSelection);
                        cell.addEventListener('touchstart', startSelection);
                        cell.addEventListener('touchmove', handleTouchMove);

                        letterBoardElement.appendChild(cell);
                    }
                }

                // Agregar evento para finalizar selección
                document.addEventListener('mouseup', endSelection);
                document.addEventListener('touchend', endSelection);
            }

            // Función para manejar eventos táctiles
            function handleTouchMove(e) {
                e.preventDefault();

                const touch = e.touches[0];
                const element = document.elementFromPoint(touch.clientX, touch.clientY);

                if (element && element.classList.contains('letter-cell')) {
                    continueSelection({
                        target: element
                    });
                }
            }

            // Función para renderizar la lista de palabras
            function renderWordList() {
                wordListElement.innerHTML = '';

                for (const word of words) {
                    const wordItem = document.createElement('div');
                    wordItem.className = 'word-item';
                    if (foundWords.includes(word)) {
                        wordItem.classList.add('found');
                    }
                    wordItem.textContent = word;
                    wordListElement.appendChild(wordItem);
                }
            }

            // Función para iniciar la selección
            function startSelection(e) {
                if (!e.target.classList.contains('letter-cell')) return;

                // Limpiar selección anterior
                clearSelection();

                // Agregar celda a la selección
                const row = parseInt(e.target.dataset.row);
                const col = parseInt(e.target.dataset.col);

                selectedCells.push({
                    row,
                    col,
                    element: e.target
                });
                e.target.classList.add('selected');
            }

            // Función para continuar la selección
            function continueSelection(e) {
                if (!e.target.classList.contains('letter-cell') || selectedCells.length === 0) return;

                const row = parseInt(e.target.dataset.row);
                const col = parseInt(e.target.dataset.col);

                // Verificar si la celda ya está seleccionada
                const isAlreadySelected = selectedCells.some(cell => cell.row === row && cell.col === col);
                if (isAlreadySelected) return;

                // Verificar si la celda es adyacente a la última seleccionada
                const lastCell = selectedCells[selectedCells.length - 1];
                const rowDiff = Math.abs(row - lastCell.row);
                const colDiff = Math.abs(col - lastCell.col);

                // Solo permitir selección en línea recta (horizontal, vertical o diagonal)
                if (rowDiff <= 1 && colDiff <= 1) {
                    // Verificar si la dirección es consistente con la selección actual
                    if (selectedCells.length >= 2) {
                        const prevCell = selectedCells[selectedCells.length - 2];
                        const currentDirection = getDirection(prevCell, lastCell);
                        const newDirection = getDirection(lastCell, {
                            row,
                            col
                        });

                        // Si la dirección cambia, no permitir la selección
                        if (currentDirection !== newDirection) return;
                    }

                    selectedCells.push({
                        row,
                        col,
                        element: e.target
                    });
                    e.target.classList.add('selected');
                }
            }

            // Función para obtener la dirección entre dos celdas
            function getDirection(cell1, cell2) {
                const rowDiff = cell2.row - cell1.row;
                const colDiff = cell2.col - cell1.col;

                if (rowDiff === 0 && colDiff !== 0) return 'horizontal';
                if (rowDiff !== 0 && colDiff === 0) return 'vertical';
                if (rowDiff === colDiff) return 'diagonal-1';
                if (rowDiff === -colDiff) return 'diagonal-2';
                return 'none';
            }

            // Función para finalizar la selección
            function endSelection() {
                if (selectedCells.length < 2) {
                    clearSelection();
                    return;
                }

                // Obtener palabra seleccionada
                const selectedWord = selectedCells.map(cell => board[cell.row][cell.col].letter).join('');
                const reversedWord = selectedWord.split('').reverse().join('');

                // Verificar si la palabra está en la lista (normal o invertida)
                const wordFound = words.includes(selectedWord) || words.includes(reversedWord);
                const wordToAdd = words.includes(selectedWord) ? selectedWord :
                    words.includes(reversedWord) ? reversedWord : null;

                if (wordFound && !foundWords.includes(wordToAdd)) {
                    // Marcar palabra como encontrada
                    foundWords.push(wordToAdd);

                    // Update found words counter
                    document.getElementById('found-words-count').textContent = foundWords.length;

                    // Marcar celdas como encontradas
                    selectedCells.forEach(cell => {
                        cell.element.classList.remove('selected');
                        cell.element.classList.add('found');
                    });

                    // Actualizar lista de palabras
                    renderWordList();

                    // Verificar si se han encontrado todas las palabras
                    if (foundWords.length === words.length) {
                        showVictoryModal();
                    }
                } else {
                    // Limpiar selección
                    clearSelection();
                }
            }

            // Función para limpiar la selección
            function clearSelection() {
                selectedCells.forEach(cell => {
                    cell.element.classList.remove('selected');
                });
                selectedCells = [];
            }

            // Función para iniciar el temporizador
            function startTimer() {
                // Detener temporizador anterior si existe
                if (timerInterval) {
                    clearInterval(timerInterval);
                }

                updateTimerDisplay();

                // Si no hay límite de tiempo, no iniciar el temporizador
                if (gameMode === 'notime') {
                    timerElement.textContent = '--:--';
                    return;
                }

                timerInterval = setInterval(() => {
                    timeElapsed++;
                    updateTimerDisplay();

                    // Verificar si se ha alcanzado el límite de tiempo
                    if (timeLimit > 0 && timeElapsed >= timeLimit) {
                        clearInterval(timerInterval);
                        gameOver();
                    }
                }, 1000);
            }

            // Función para actualizar la visualización del temporizador
            function updateTimerDisplay() {
                if (gameMode === 'notime') {
                    timerElement.textContent = '--:--';
                    return;
                }

                const minutes = Math.floor(timeElapsed / 60);
                const seconds = timeElapsed % 60;
                timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

                // Cambiar color cuando queda poco tiempo
                if (timeLimit > 0 && timeLimit - timeElapsed <= 30) {
                    timerElement.style.color = '#ff5252';
                } else {
                    timerElement.style.color = '#246741';
                }
            }

            // Función para mostrar el modal de victoria
            function showVictoryModal() {
                clearInterval(timerInterval);

                // Calcular puntos (puedes ajustar la fórmula si lo deseas)
                const points = Math.max(100, 500 - timeElapsed * 2);

                // Actualizar información en el modal
                document.getElementById('victory-time').textContent = timerElement.textContent;
                document.getElementById('victory-level').textContent = '1';
                document.getElementById('victory-points').textContent = points;

                // Guardar resultado en la base de datos (victoria)
                saveGameResult(true, timeElapsed);

                // Save points to database if user is logged in
                savePoints(points);

                // Mostrar modal de victoria
                const victoryModal = new bootstrap.Modal(document.getElementById('victoryModal'));
                victoryModal.show();

                // Configurar botones del modal de victoria
                document.getElementById('retry-btn').onclick = function() {
                    bootstrap.Modal.getInstance(document.getElementById('victoryModal')).hide();
                    initGame();
                };
                document.getElementById('exit-btn').onclick = function() {
                    window.location.href = '../view/gamesMenu.php';
                };
            }

            // Función para cuando se acaba el tiempo
            function gameOver() {
                clearInterval(timerInterval);
                
                // Guardar resultado en la base de datos (derrota)
                saveGameResult(false, timeElapsed);
                
                alert('¡Se acabó el tiempo! Inténtalo de nuevo.');
                resetGame();
            }

            // Función para reiniciar el juego
            function resetGame() {
                clearInterval(timerInterval);
                initGame();
            }
        });

        // Music control
        const musicToggle = document.getElementById('musicToggle');
        const musicIcon = musicToggle.querySelector('i');
        const gameMusic = document.getElementById('gameMusic');

        // Set initial volume
        gameMusic.volume = 0.5;

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

        // Function to update music preference in database
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

        // Función para guardar puntos
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
        
        // Función para guardar el resultado del juego
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

        // Función para calcular puntos
        function calculatePoints() {
            // Base de puntos según el nivel
            const basePoints = currentLevel  + 100;
            
            // Bonificación por tiempo restante (solo en modos con tiempo)
            let timeBonus = 0;
            if (gameMode !== 'notime' && timeLimit > 0) {
                // Porcentaje de tiempo restante
                const timePercentage = 1 - (timeElapsed / timeLimit);
                timeBonus = Math.round(basePoints * timePercentage * 0.5);
            }
            
            // Bonificación por dificultad
            let difficultyMultiplier = 1;
            if (gameMode === 'hard') {
                difficultyMultiplier = 1.5;
            }
            
            return Math.round((basePoints + timeBonus) * difficultyMultiplier);
        }

        // Función para manejar la victoria
        function handleVictory() {
            // Detener el temporizador
            clearInterval(timerInterval);
            
            // Actualizar estadísticas de victoria
            document.getElementById('victory-time').textContent = formatTime(timeElapsed);
            document.getElementById('victory-level').textContent = levels[currentLevel].level;

            // Calcular puntos basados en el tiempo y nivel
            let points = calculatePoints();
            document.getElementById('victory-points').textContent = points;

            // Mostrar modal de victoria
            const victoryModal = new bootstrap.Modal(document.getElementById('victoryModal'));
            victoryModal.show();

            // Si el usuario está autenticado, guardar puntos
            <?php if (isset($_SESSION['user'])): ?>
            savePoints(points);
            <?php endif; ?>
        }

        // Función para guardar el resultado del juego
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

        // Modificar la función showVictoryModal para guardar el resultado
        function showVictoryModal() {
            // Actualizar estadísticas en el modal
            document.getElementById('victory-time').textContent = formatTime(timeElapsed);
            document.getElementById('victory-level').textContent = levels[currentLevel].level;
            
            // Calcular puntos según el tiempo y nivel
            let points = 100 * levels[currentLevel].level;
            if (gameMode !== 'notime') {
                const timeBonus = Math.max(0, timeLimit - timeElapsed);
                points += timeBonus;
            }
            document.getElementById('victory-points').textContent = points;
            
            // Guardar resultado en la base de datos (victoria)
            saveGameResult(true, timeElapsed);
            
            // Mostrar modal
            const victoryModal = new bootstrap.Modal(document.getElementById('victoryModal'));
            victoryModal.show();
        }

        // Modificar la función gameOver para guardar el resultado
        function gameOver() {
            clearInterval(timerInterval);
            
            // Guardar resultado en la base de datos (derrota)
            saveGameResult(false, timeElapsed);
            
            // Mostrar alerta
            alert('¡Se acabó el tiempo! Inténtalo de nuevo.');
            
            // Reiniciar juego
            resetGame();
        }
        </script>
</body>
</html>