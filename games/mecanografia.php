<?php
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../connection/database.php';

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
    <title>Mecanografía - Flora Games</title>
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
            margin: 60px auto 20px
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

        .typing-area {
            background-color: white;
            border-radius: 10px;
            padding: 40px 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            text-align: center;
            position: relative;
            min-height: 150px;
            overflow: hidden;
        }

        .word-display {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 20px;
            letter-spacing: 2px;
            min-height: 60px;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }

        .word-container {
            display: inline-block;
            margin: 0 10px;
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s, transform 0.5s;
        }

        .word-container.active {
            opacity: 1;
            transform: translateY(0);
        }

        .letter {
            position: relative;
            transition: all 0.2s;
            display: inline-block;
        }

        .letter.current {
            text-decoration: underline;
        }

        .letter.correct {
            color: #246741;
        }

        .letter.incorrect {
            color: #ff5252;
        }

        .space-indicator {
            display: inline-block;
            width: 20px;
            height: 5px;
            background-color: #ccc;
            margin: 0 5px;
            position: relative;
            bottom: 10px;
        }

        .space-indicator.current {
            background-color: #246741;
            height: 5px;
            animation: blink 1s infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .stats {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
            font-size: 1.2rem;
            background-color: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .progress-bar {
            height: 10px;
            background-color: #e0e0e0;
            border-radius: 5px;
            margin-top: 20px;
            overflow: hidden;
        }

        .progress {
            height: 100%;
            background-color: #246741;
            width: 0%;
            transition: width 0.3s;
        }

        .keyboard {
            display: grid;
            grid-template-rows: repeat(4, 50px);
            gap: 5px;
            margin-top: 30px;
        }

        .keyboard-row {
            display: flex;
            justify-content: center;
            gap: 5px;
        }

        .key {
            min-width: 40px;
            height: 50px;
            background-color: #f0f0f0;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.1s;
        }

        .key.active {
            background-color: #246741;
            color: white;
            transform: translateY(2px);
        }

        .key.space {
            width: 200px;
        }

        .stats {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
            font-size: 1.2rem;
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

        @media (max-width: 768px) {
            .word-display {
                font-size: 2rem;
            }

            .key {
                min-width: 30px;
                height: 40px;
                font-size: 0.8rem;
            }

            .key.space {
                width: 150px;
            }
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

        #continue-btn {
            background-color: #246741;
            color: white;
            border: none;
        }

        #continue-btn:hover {
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
    </style>
</head>

<body>
    <?php include '../components/header.php'; ?>

    <div class="header-secundary" style="color:#246741; display: flex; align-items: center;">
        <div style="display:flex; flex-direction:row; gap:5px">
            <button class="reset-btn"onclick="window.location.href='../view/gamesMenu.php'"  title="Volver al menú">
                <h5><i class="fas fa-sign-out-alt fa-flip-horizontal"></i></h5>
            </button>
            <button class="reset-btn" id="musicToggle"  title="Música">
                <h5><i class="fa-solid <?php echo $musicEnabled ? 'fa-volume-high' : 'fa-volume-xmark'; ?>"></i></h5>
            </button>
            <audio id="gameMusic" loop <?php echo $musicEnabled ? 'autoplay' : ''; ?>>
                <source src="../assets/musica.mp3" type="audio/mp3">
            </audio>

            <button class="reset-btn btn-success" id="reset-btn"  title="Reiniciar"><h5><i class="fa-solid fa-arrow-rotate-right"></h5></i></button>
        </div>
        <div style="text-align:center">
            <h5>Mecanografía</h5>
            <div class="level">Modo <span id="level-display"></span></div>
        </div>
        <div style="display:flex; flex-direction:row; gap:10px">
            
            <h5><i class="fa-solid fa-clock"></i></h5>
            <div class="timer"> <h5 id="timer"> 00:00</h5></div>
        </div>
    </div>

    <div class="page-container">
        <div class="game-container">

            <div class="typing-area">
                <div class="word-display" id="word-display"></div>
                <div class="progress-bar">
                    <div class="progress" id="progress"></div>
                </div>
            </div>

            <div class="stats">
                <div>Palabras: <span id="words-count">0</span></div>
                <div>Precisión: <span id="accuracy">100%</span></div>
                <div>PPM: <span id="wpm">0</span></div>
            </div>

            <div class="keyboard" id="keyboard">
                <div class="keyboard-row">
                    <div class="key" data-key="q">Q</div>
                    <div class="key" data-key="w">W</div>
                    <div class="key" data-key="e">E</div>
                    <div class="key" data-key="r">R</div>
                    <div class="key" data-key="t">T</div>
                    <div class="key" data-key="y">Y</div>
                    <div class="key" data-key="u">U</div>
                    <div class="key" data-key="i">I</div>
                    <div class="key" data-key="o">O</div>
                    <div class="key" data-key="p">P</div>
                </div>
                <div class="keyboard-row">
                    <div class="key" data-key="a">A</div>
                    <div class="key" data-key="s">S</div>
                    <div class="key" data-key="d">D</div>
                    <div class="key" data-key="f">F</div>
                    <div class="key" data-key="g">G</div>
                    <div class="key" data-key="h">H</div>
                    <div class="key" data-key="j">J</div>
                    <div class="key" data-key="k">K</div>
                    <div class="key" data-key="l">L</div>
                    <div class="key" data-key="ñ">Ñ</div>
                </div>
                <div class="keyboard-row">
                    <div class="key" data-key="z">Z</div>
                    <div class="key" data-key="x">X</div>
                    <div class="key" data-key="c">C</div>
                    <div class="key" data-key="v">V</div>
                    <div class="key" data-key="b">B</div>
                    <div class="key" data-key="n">N</div>
                    <div class="key" data-key="m">M</div>
                </div>
                <div class="keyboard-row">
                    <div class="key space" data-key=" ">ESPACIO</div>
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
                    <p><strong>Fácil:</strong> Nombres de plantas de 8 a 12 letras</p>
                    <button class="difficulty-btn" data-difficulty="easy">Fácil</button>

                    <p><strong>Difícil:</strong> Frases de 6 a 10 palabras</p>
                    <button class="difficulty-btn" data-difficulty="hard">Difícil</button>

                    <p><strong>Sin tiempo:</strong> Combinación de ambos (máximo 8)</p>
                    <button class="difficulty-btn" data-difficulty="notime">Sin tiempo</button>
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
                    <p>1. Escribe las palabras o frases que aparecen en pantalla.</p>
                    <p>2. La palabra avanzará a medida que escribas correctamente.</p>
                    <p>3. Si te equivocas, la letra aparecerá en rojo.</p>
                    <p>4. El teclado en pantalla se iluminará con cada tecla que presiones.</p>
                    <p>5. Intenta escribir lo más rápido y preciso posible.</p>
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
                    <p>Has completado esta ronda</p>
                    <div class="victory-stats">
                        <p><i class="fas fa-clock me-2"></i> Tiempo: <span id="victory-time" style="margin-left: 5px;"> 00:00</span></p>
                        <p><i class="fas fa-trophy me-2"></i> Modo: <span id="victory-mode" style="margin-left: 5px;"> Fácil</span></p>
                        <p><i class="fas fa-keyboard me-2"></i> PPM: <span id="victory-wpm" style="margin-left: 5px;"> 0</span></p>
                        <p><i class="fas fa-bullseye me-2"></i> Precisión: <span id="victory-accuracy" style="margin-left: 5px;"> 100%</span></p>
                        <p><i class="fas fa-star me-2"></i> Puntos: <span id="victory-points" style="margin-left: 5px;"> 0</span></p>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<script>
    document.addEventListener('DOMContentLoaded', function() {
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

        // Function to update music preference
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

        // Palabras y frases para el juego
        const words = {
            easy: [
                'MARGARITA', 'GIRASOL', 'TULIPAN', 'ORQUIDEA', 'CRISANTEMO',
                'BEGONIA', 'GERANIO', 'AZUCENA', 'HORTENSIA', 'JAZMIN',
                'LAVANDA', 'MAGNOLIA', 'NARCISO', 'PETUNIA', 'VIOLETA',
                'AMAPOLA', 'DALIA', 'GARDENIA', 'HIBISCO', 'LIRIO'
            ],
            hard: [
                'Las plantas necesitan luz solar para crecer',
                'El girasol sigue la dirección del sol',
                'Las orquídeas son flores muy delicadas',
                'Los cactus almacenan agua en su tallo',
                'Las rosas tienen espinas para protegerse',
                'Los árboles producen oxígeno para respirar',
                'Las plantas carnívoras atrapan pequeños insectos',
                'El bambú es una de las plantas más rápidas',
                'Las flores atraen a abejas y mariposas',
                'Los helechos crecen mejor en la sombra'
            ],
            notime: [
                'MARGARITA', 'GIRASOL', 'ORQUIDEA', 'JAZMIN',
                'Las plantas necesitan luz solar',
                'El girasol sigue al sol',
                'Las rosas tienen espinas',
                'Los cactus almacenan agua'
            ]
        };

        // Variables del juego
        let currentWord = '';
        let currentWords = [];
        let currentWordIndex = 0;
        let currentLetterIndex = 0;
        let correctLetters = 0;
        let incorrectLetters = 0;
        let totalWords = 0;
        let startTime = null;
        let timerInterval = null;
        let timeElapsed = 0;
        let gameMode = 'easy'; // Por defecto

        // Elementos del DOM
        const wordDisplay = document.getElementById('word-display');
        const progressBar = document.getElementById('progress');
        const timerElement = document.getElementById('timer');
        const resetButton = document.getElementById('reset-btn');
        const wordsCount = document.getElementById('words-count');
        const accuracyElement = document.getElementById('accuracy');
        const wpmElement = document.getElementById('wpm');
        const levelDisplay = document.getElementById('level-display');
        const keys = document.querySelectorAll('.key');

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
                gameMode = this.dataset.difficulty;
                levelDisplay.textContent = this.textContent;

                // Cerrar modal de dificultad
                difficultyModal.hide();

                // Verificar si es la primera vez que se juega
                if (!localStorage.getItem('mecanografiaInstructionsShown')) {
                    instructionsModal.show();
                } else {
                    // Iniciar juego
                    initGame();
                }
            });
        });

        // Configurar botones de instrucciones
        document.getElementById('skipInstructions').addEventListener('click', function() {
            instructionsModal.hide();
            initGame();
        });
        
        // Configurar botones del modal de victoria
        document.getElementById('continue-btn').addEventListener('click', function() {
            const victoryModal = bootstrap.Modal.getInstance(document.getElementById('victoryModal'));
            victoryModal.hide();
            document.querySelector('.modal-backdrop')?.remove();
            resetGame();
        });
        
        document.getElementById('exit-btn').addEventListener('click', function() {
            window.location.href = '../view/gamesMenu.php';
        });

        document.getElementById('dontShowAgain').addEventListener('click', function() {
            localStorage.setItem('mecanografiaInstructionsShown', 'true');
            instructionsModal.hide();
            initGame();
        });

        // Función para inicializar el juego
        function initGame() {
            // Reiniciar variables
            currentIndex = 0;
            correctLetters = 0;
            incorrectLetters = 0;
            timeElapsed = 0;

            // Mostrar nueva palabra
            showNewWord();

            // Iniciar temporizador
            startTimer();

            // Enfocar el juego para capturar teclas
            window.focus();

            // Configurar evento de reinicio
            resetButton.addEventListener('click', resetGame);

            // Configurar evento de teclado
            document.addEventListener('keydown', handleKeyPress);
        }

        // Función para mostrar una nueva palabra o frase
        function showNewWord() {
            // Seleccionar palabra aleatoria según el modo de juego
            const wordList = words[gameMode];
            const randomIndex = Math.floor(Math.random() * wordList.length);
            const selectedText = wordList[randomIndex].toUpperCase();

            // Dividir en palabras si es una frase
            currentWords = selectedText.split(' ');
            currentWordIndex = 0;
            currentLetterIndex = 0;

            // Limpiar display
            wordDisplay.innerHTML = '';

            // Crear contenedores para cada palabra
            for (let i = 0; i < currentWords.length; i++) {
                const wordContainer = document.createElement('div');
                wordContainer.className = 'word-container';

                // Solo la primera palabra está activa al inicio
                if (i === 0) {
                    wordContainer.classList.add('active');
                }

                // Crear elementos para cada letra de la palabra
                for (let j = 0; j < currentWords[i].length; j++) {
                    const letterElement = document.createElement('span');
                    letterElement.className = 'letter';
                    letterElement.textContent = currentWords[i][j];

                    // La primera letra de la primera palabra es la actual
                    if (i === 0 && j === 0) {
                        letterElement.classList.add('current');
                    }

                    wordContainer.appendChild(letterElement);
                }

                // Añadir un espacio visual después de cada palabra (excepto la última)
                if (i < currentWords.length - 1) {
                    const spaceIndicator = document.createElement('span');
                    spaceIndicator.className = 'space-indicator';
                    wordContainer.appendChild(spaceIndicator);
                }

                wordDisplay.appendChild(wordContainer);
            }

            // Reiniciar barra de progreso
            progressBar.style.width = '0%';

            // Actualizar palabra actual
            currentWord = currentWords[0];
        }

        // Función para manejar las pulsaciones de teclas
        function handleKeyPress(e) {
            // Ignorar teclas especiales
            if (e.ctrlKey || e.altKey || e.metaKey) return;

            // Prevenir el comportamiento predeterminado para la tecla de espacio
            if (e.key === ' ' || e.code === 'Space') {
                e.preventDefault();
            }

            // Obtener la tecla presionada
            const key = e.key.toUpperCase();

            // Animar tecla en el teclado virtual
            animateKey(key.toLowerCase());

            // Si ya completamos todas las palabras, mostrar una nueva frase
            if (currentWordIndex >= currentWords.length) {
                totalWords += currentWords.length;
                wordsCount.textContent = totalWords;
                showNewWord();
                return;
            }

            // Obtener los contenedores de palabras y las letras de la palabra actual
            const wordContainers = wordDisplay.querySelectorAll('.word-container');
            const currentWordContainer = wordContainers[currentWordIndex];
            const letterElements = currentWordContainer.querySelectorAll('.letter');
            const spaceIndicator = currentWordContainer.querySelector('.space-indicator');

            // Verificar si hemos completado la palabra actual y necesitamos un espacio
            if (currentLetterIndex >= currentWord.length && currentWordIndex < currentWords.length - 1) {
                // Si hay un indicador de espacio, marcarlo como actual
                if (spaceIndicator && !spaceIndicator.classList.contains('current')) {
                    spaceIndicator.classList.add('current');
                }

                // Si se presiona espacio, avanzar a la siguiente palabra
                if (key === ' ') {
                    // Quitar la marca actual del espacio
                    if (spaceIndicator) {
                        spaceIndicator.classList.remove('current');
                    }

                    currentWordIndex++;
                    currentLetterIndex = 0;
                    currentWord = currentWords[currentWordIndex];

                    // Activar la siguiente palabra
                    wordContainers[currentWordIndex].classList.add('active');

                    // Marcar la primera letra de la siguiente palabra como actual
                    const nextLetterElements = wordContainers[currentWordIndex].querySelectorAll('.letter');
                    if (nextLetterElements.length > 0) {
                        nextLetterElements[0].classList.add('current');
                    }

                    correctLetters++; // Contar el espacio como letra correcta

                    // Actualizar barra de progreso
                    const totalLetters = currentWords.join(' ').length;
                    const completedLetters = currentWords.slice(0, currentWordIndex).join(' ').length + 1; // +1 por el espacio
                    const progress = (completedLetters / totalLetters) * 100;
                    progressBar.style.width = `${progress}%`;

                    // Actualizar estadísticas
                    updateStats();
                    return;
                } else {
                    // Tecla incorrecta cuando se esperaba un espacio
                    incorrectLetters++;
                    // Actualizar estadísticas
                    updateStats();
                    return;
                }
            }

            // Verificar si la tecla es correcta para la letra actual
            const currentLetter = currentWord[currentLetterIndex];

            if (key === currentLetter) {
                // Letra correcta
                letterElements[currentLetterIndex].classList.remove('current');
                letterElements[currentLetterIndex].classList.add('correct');

                currentLetterIndex++;
                correctLetters++;

                // Si completamos la palabra actual
                if (currentLetterIndex >= currentWord.length) {
                    // Si no es la última palabra, mostrar el indicador de espacio
                    if (currentWordIndex < currentWords.length - 1 && spaceIndicator) {
                        spaceIndicator.classList.add('current');
                    } else if (currentWordIndex === currentWords.length - 1) {
                        // Si es la última palabra y la completamos, mostrar nueva frase
                        setTimeout(() => {
                            totalWords += currentWords.length;
                            wordsCount.textContent = totalWords;
                            showNewWord();
                        }, 500);
                    }
                } else {
                    // Marcar la siguiente letra como actual
                    letterElements[currentLetterIndex].classList.add('current');
                }

                // Actualizar barra de progreso
                const totalLetters = currentWords.join(' ').length;
                const completedLetters = currentWords.slice(0, currentWordIndex).join(' ').length + currentLetterIndex;
                const progress = (completedLetters / totalLetters) * 100;
                progressBar.style.width = `${progress}%`;
            } else {
                // Letra incorrecta
                letterElements[currentLetterIndex].classList.add('incorrect');
                incorrectLetters++;
            }

            // Actualizar estadísticas
            updateStats();
        }

        // Función para animar tecla en el teclado virtual
        function animateKey(key) {
            // Normalizar la tecla (para espacio y otros caracteres especiales)
            if (key === ' ') key = ' ';

            // Buscar la tecla en el teclado virtual
            const keyElement = document.querySelector(`.key[data-key="${key}"]`);

            if (keyElement) {
                // Añadir clase activa
                keyElement.classList.add('active');

                // Quitar clase después de un breve tiempo
                setTimeout(() => {
                    keyElement.classList.remove('active');
                }, 100);
            }
        }

        // Función para actualizar estadísticas
        function updateStats() {
            // Calcular precisión
            const totalLetters = correctLetters + incorrectLetters;
            const accuracy = totalLetters > 0 ? Math.round((correctLetters / totalLetters) * 100) : 100;
            accuracyElement.textContent = `${accuracy}%`;

            // Calcular palabras por minuto (PPM)
            if (startTime) {
                const minutes = (Date.now() - startTime) / 60000;
                const wpm = minutes > 0 ? Math.round(totalWords / minutes) : 0;
                wpmElement.textContent = wpm;
            }
            
            // Verificar si se ha completado el juego (10 palabras)
            if (totalWords >= 10 && gameMode !== 'notime') {
                handleVictory();
            }
        }
        
        // Función para calcular precisión actual
        function calculateAccuracy() {
            const totalLetters = correctLetters + incorrectLetters;
            return totalLetters > 0 ? Math.round((correctLetters / totalLetters) * 100) : 100;
        }
        
        // Función para calcular PPM actual
        function calculateWPM() {
            if (startTime) {
                const minutes = (Date.now() - startTime) / 60000;
                return minutes > 0 ? Math.round(totalWords / minutes) : 0;
            }
            return 0;
        }
        
        // Función para formatear el tiempo
        function formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = seconds % 60;
            return `${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`;
        }

        // Función para iniciar el temporizador
        function startTimer() {
            // Registrar tiempo de inicio
            startTime = Date.now();

            // Detener temporizador anterior si existe
            if (timerInterval) {
                clearInterval(timerInterval);
            }

            // Si es modo sin tiempo, mostrar un guión
            if (gameMode === 'notime') {
                timerElement.textContent = '--:--';
                return;
            }

            // Actualizar temporizador cada segundo
            timerInterval = setInterval(() => {
                timeElapsed++;
                updateTimerDisplay();
            }, 1000);
        }

        // Función para actualizar la visualización del temporizador
        function updateTimerDisplay() {
            const minutes = Math.floor(timeElapsed / 60);
            const seconds = timeElapsed % 60;
            timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }

        // Función para reiniciar el juego
        function resetGame() {
            // Detener temporizador
            if (timerInterval) {
                clearInterval(timerInterval);
            }

            // Reiniciar estadísticas
            totalWords = 0;
            wordsCount.textContent = totalWords;
            accuracyElement.textContent = '100%';
            wpmElement.textContent = '0';

            // Reiniciar juego
            initGame();
        }
        
        // Función para manejar la victoria
        function handleVictory() {
            // Detener el temporizador
            clearInterval(timerInterval);
            
            // Calcular estadísticas finales
            const finalWpm = calculateWPM();
            const finalAccuracy = calculateAccuracy();
            
            // Calcular puntos basados en precisión y velocidad
            const points = calculatePoints(finalWpm, finalAccuracy, gameMode);
            
            // Actualizar estadísticas en el modal de victoria
            document.getElementById('victory-time').textContent = formatTime(timeElapsed);
            document.getElementById('victory-mode').textContent = gameMode === 'easy' ? 'Fácil' : 
                                                                 gameMode === 'hard' ? 'Difícil' : 'Sin tiempo';
            document.getElementById('victory-wpm').textContent = finalWpm;
            document.getElementById('victory-accuracy').textContent = finalAccuracy + '%';
            document.getElementById('victory-points').textContent = points;
            
            // Mostrar modal de victoria
            const victoryModal = new bootstrap.Modal(document.getElementById('victoryModal'));
            victoryModal.show();
            
            // Si el usuario está autenticado, guardar puntos
            <?php if (isset($_SESSION['usuario_id'])): ?>
            savePoints(points);
            <?php endif; ?>
        }
        
        // Función para calcular puntos basados en precisión y velocidad
        function calculatePoints(wpm, accuracy, gameMode) {
            // Base de puntos según el modo de juego
            let basePoints = 100;
            if (gameMode === 'hard') {
                basePoints = 200;
            } else if (gameMode === 'notime') {
                basePoints = 150;
            }
            
            // Multiplicador de precisión (0.5 a 1.5)
            const accuracyMultiplier = Math.max(0.5, Math.min(1.5, accuracy / 100 * 1.5));
            
            // Multiplicador de velocidad (0.5 a 2.0)
            const wpmMultiplier = Math.max(0.5, Math.min(2.0, wpm / 50));
            
            // Calcular puntos totales
            return Math.round(basePoints * accuracyMultiplier * wpmMultiplier);
        }
        
        // Función para guardar puntos en la base de datos
        function savePoints(points) {
            console.log('Intentando guardar puntos:', points);
            
            fetch('../config/updatePoints.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `points=${points}&game=mecanografia`
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
    });
</script>
</body>

</html>