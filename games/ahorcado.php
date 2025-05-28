<?php
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../connection/database.php';
require_once __DIR__ . '/../config/dataPlanta.php';

// Variables para usuarios no autenticados
$userId = null;
$musicEnabled = true; // Valor predeterminado

// Verificar si el usuario está autenticado
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
function normalizarCaracter($letra) {
    $mapaAcentos = [
        'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
        'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U'
    ];
    return isset($mapaAcentos[$letra]) ? $mapaAcentos[$letra] : $letra;
}
// Obtener nombres de plantas desde la base de datos
function obtenerPalabrasPorDificultad() {
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT nombre_comun, foto FROM ficha_planta");
    $stmt->execute();
    $plantas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $palabras = [
        'facil' => [],
        'dificil' => []
    ];
    foreach ($plantas as $planta) {
        if (mb_strlen($planta['nombre_comun']) <= 8) {
            $palabras['facil'][] = $planta;
        } else if(mb_strlen($planta['nombre_comun']) <= 15){
            $palabras['dificil'][] = $planta;
        }
    }
    return $palabras;
}

$palabras = obtenerPalabrasPorDificultad();

// Procesar solicitud AJAX
if (isset($_POST['ajax']) && $_POST['ajax'] === 'process_letter' && isset($_SESSION['ahorcado_palabra'])) {
    header('Content-Type: application/json');
    
    $letra = strtolower($_POST['letra']);
    $response = [
        'success' => false,
        'word_display' => '',
        'errors' => $_SESSION['ahorcado_errores'] ?? 0,
        'opportunities' => $_SESSION['ahorcado_oportunidades'] ?? 5,
        'completed' => false,
        'won' => false,
        'word' => '',
        'time_elapsed' => 0
    ];
    
    // Calcula el tiempo transcurrido
    if (isset($_SESSION['ahorcado_tiempo_inicio'])) {
        $_SESSION['ahorcado_tiempo_transcurrido'] = time() - $_SESSION['ahorcado_tiempo_inicio'];
        $response['time_elapsed'] = $_SESSION['ahorcado_tiempo_transcurrido'];
    }
    
    if (!in_array($letra, $_SESSION['ahorcado_letras_adivinadas']) && 
        !in_array($letra, $_SESSION['ahorcado_letras_incorrectas'])) {
        
        // Normalizar la palabra y la letra ingresada
        $palabra_minuscula = strtolower($_SESSION['ahorcado_palabra']);
        $letra_normalizada = normalizarCaracter($letra);
        $letras_en_palabra = [];
        
        // Comparar letras normalizadas, ignorando espacios
        for ($i = 0; $i < strlen($palabra_minuscula); $i++) {
            if ($palabra_minuscula[$i] !== ' ' && normalizarCaracter($palabra_minuscula[$i]) === $letra_normalizada) {
                $letras_en_palabra[] = $i; // Guardar posiciones de coincidencias
            }
        }
        
        if (!empty($letras_en_palabra)) {
            $_SESSION['ahorcado_letras_adivinadas'][] = $letra;
            // Verificar si la palabra está completa, ignorando espacios
            $palabra_completa = true;
            for ($i = 0; $i < strlen($palabra_minuscula); $i++) {
                if ($palabra_minuscula[$i] !== ' ' && 
                    !in_array(normalizarCaracter($palabra_minuscula[$i]), 
                             array_map('normalizarCaracter', $_SESSION['ahorcado_letras_adivinadas']))) {
                    $palabra_completa = false;
                    break;
                }
            }
            if ($palabra_completa) {
                $_SESSION['ahorcado_completado'] = true;
                $_SESSION['ahorcado_tiempo_fin'] = time();
                $response['completed'] = true;
                $response['won'] = true;
                $response['word'] = $_SESSION['ahorcado_palabra'];
            }
        } else {
            $_SESSION['ahorcado_letras_incorrectas'][] = $letra;
            $_SESSION['ahorcado_errores']++;
            if ($_SESSION['ahorcado_errores'] >= $_SESSION['ahorcado_oportunidades']) {
                $_SESSION['ahorcado_completado'] = true;
                $_SESSION['ahorcado_tiempo_fin'] = time();
                $response['completed'] = true;
                $response['won'] = false;
                $response['word'] = $_SESSION['ahorcado_palabra'];
            }
        }
        $response['success'] = true;
    }
    
    // Generar la palabra para mostrar, incluyendo espacios
    $word_display = '';
    $palabra = $_SESSION['ahorcado_palabra'];
    for ($i = 0; $i < strlen($palabra); $i++) {
        $letra_palabra = strtolower($palabra[$i]);
        if ($letra_palabra === ' ') {
            $word_display .= "<div class='letter-box' style='border-bottom:none'> </div>"; // Espacio sin línea inferior
        } else {
            $mostrar = in_array(normalizarCaracter($letra_palabra), 
                               array_map('normalizarCaracter', $_SESSION['ahorcado_letras_adivinadas'])) 
                       ? $palabra[$i] : '';
            $word_display .= "<div class='letter-box'>$mostrar</div>";
        }
    }
    $response['word_display'] = $word_display;
    $response['errors'] = $_SESSION['ahorcado_errores'];
    $response['opportunities'] = $_SESSION['ahorcado_oportunidades'];
    
    echo json_encode($response);
    exit;
}

// Inicializar o reiniciar el juego
if (!isset($_SESSION['ahorcado_iniciado']) || isset($_GET['reset'])) {
    unset($_SESSION['ahorcado_dificultad']);
    unset($_SESSION['ahorcado_palabra']);
    unset($_SESSION['ahorcado_letras_adivinadas']);
    unset($_SESSION['ahorcado_letras_incorrectas']);
    unset($_SESSION['ahorcado_errores']);
    unset($_SESSION['ahorcado_tiempo_inicio']);
    unset($_SESSION['ahorcado_tiempo_transcurrido']);
    unset($_SESSION['ahorcado_tiempo_fin']);
    unset($_SESSION['ahorcado_completado']);
    unset($_SESSION['ahorcado_iniciado']);
    unset($_SESSION['ahorcado_oportunidades']);
    
    if (isset($_GET['reset'])) {
        header('Location: ahorcado.php');
        exit;
    }
}

// Establecer dificultad
if (isset($_GET['difficulty'])) {
    $_SESSION['ahorcado_dificultad'] = $_GET['difficulty'];
    $_SESSION['ahorcado_oportunidades'] = ($_GET['difficulty'] == 'facil') ? 5 : 3;
    
    $lista_palabras = $palabras[$_GET['difficulty']];
    $palabra_seleccionada = $lista_palabras[array_rand($lista_palabras)];
    
    $_SESSION['ahorcado_palabra'] = $palabra_seleccionada['nombre_comun'];
    $_SESSION['ahorcado_imagen'] = $palabra_seleccionada['foto'];
    $_SESSION['ahorcado_letras_adivinadas'] = [];
    $_SESSION['ahorcado_letras_incorrectas'] = [];
    $_SESSION['ahorcado_errores'] = 0;
    $_SESSION['ahorcado_tiempo_inicio'] = time();
    $_SESSION['ahorcado_tiempo_transcurrido'] = 0;
    $_SESSION['ahorcado_completado'] = false;
    $_SESSION['ahorcado_iniciado'] = true;
    
    header('Location: ahorcado.php');
    exit;
}

if (isset($_POST['ajax']) && $_POST['ajax'] === 'reset_session') {
    header('Content-Type: application/json');
    
    // Limpiar todas las variables de sesión relacionadas con el juego
    unset($_SESSION['ahorcado_iniciado']);
    unset($_SESSION['ahorcado_dificultad']);
    unset($_SESSION['ahorcado_palabra']);
    unset($_SESSION['ahorcado_imagen']);
    unset($_SESSION['ahorcado_letras_adivinadas']);
    unset($_SESSION['ahorcado_letras_incorrectas']);
    unset($_SESSION['ahorcado_errores']);
    unset($_SESSION['ahorcado_tiempo_inicio']);
    unset($_SESSION['ahorcado_tiempo_transcurrido']);
    unset($_SESSION['ahorcado_tiempo_fin']);
    unset($_SESSION['ahorcado_completado']);
    unset($_SESSION['ahorcado_oportunidades']);
    
    echo json_encode(['success' => true]);
    exit;
}
?>

<!-- El resto del HTML y JavaScript se encuentra más abajo -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ahorcado - Flora Games</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/styleGames.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../img/logoFG.ico">
    <style>
        .game-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .container-img {
            display: flex;
            justify-content: space-around;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
        }

        .hangman-image, .plant-image {
            flex: 1;
            min-width: 250px;
            text-align: center;
        }
        .hangman-image{
            filter: drop-shadow(0 0 0.75rem white);
        }
        .plant-image img:hover{
            transform: translateY(-5px);
        }

        .plant-image img {
            max-height: 250px;
            max-width: 100%;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        @media (max-width: 768px) {
            .container-img {
                flex-direction: column;
            }
            
            .hangman-image, .plant-image {
                width: 100%;
            }
        }
        
        .hangman-image img {
            max-height: 200px;
        }
        
        .word-display {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 2rem;
        }
        
        .letter-box {
            width: 40px;
            height: 40px;
            border-bottom: 2px solid white;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.5rem;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .keyboard {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 5px;
            margin-top: 2rem;
        }
        
        .key {
            width: 40px;
            height: 40px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .key:hover:not(:disabled) {
            background-color: #e9ecef;
        }
        
        .key.used {
            opacity: 0.7;
            cursor: not-allowed;
        }
        
        .key.correct {
            background-color:rgb(118, 124, 122);
            color: white;
        }
        
        .key.incorrect {
            background-color: rgb(118, 124, 122);
            color: white;
        }
        
        .game-info {
            display: flex;
            justify-content: space-around;
            margin-bottom: 1rem;
            background-color: #f8f9fa;
            border-radius: 10px;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin:0;
        }
        
        @media (max-width: 576px) {
            .letter-box {
                width: 30px;
                height: 30px;
                font-size: 1.2rem;
            }
            
            .key {
                width: 30px;
                height: 30px;
                font-size: 0.9rem;
            }
        }

        .hearts-container {
            display: flex;
            gap: 5px;
        }

        .heart-full {
            color: #dc3545; /* Rojo para corazones llenos */
            font-size: 1.2rem;
        }

        .heart-empty {
            color: #ccc; /* Gris para corazones vacíos */
            font-size: 1.2rem;
        }

        /* Opcional: animación al perder una vida */
        @keyframes heartLost {
            0% { transform: scale(1); }
            50% { transform: scale(1.3); }
            100% { transform: scale(1); }
        }

        .heart-lost {
            animation: heartLost 0.5s ease-in-out;
        }
    </style>
</head>
<body>
    <?php include '../components/header.php'; ?>
    
    <div class="header-secundary" style="color:#246741; display: flex; align-items: center;">
        <div class="hd-sec-gm" style="display:flex; flex-direction:row; gap:10px">
            <button class="reset-btn" id="exit" title="Volver al menú">
                <h5><i class="fas fa-sign-out-alt fa-flip-horizontal"></i></h5>
            </button>
            <button class="reset-btn" id="musicToggle" title="Música">
                <h5><i class="fa-solid <?php echo $musicEnabled ? 'fa-volume-high' : 'fa-volume-xmark'; ?>"></i></h5>
            </button>
            <audio id="gameMusic" loop <?php echo $musicEnabled ? 'autoplay' : ''; ?>>
                <source src="../assets/musica.mp3" type="audio/mp3">
            </audio>
            <button class="reset-btn" id="reset-btn" title="Reiniciar">
                <h5><i class="fa-solid fa-arrow-rotate-right"></i></h5>
            </button>
        </div>
        <div style="text-align:center">
            <h5 style="margin:0">Ahorcado - Plantas</h5>
            <div class="level">
                Modo: <span id="level-display">
                    <?php echo isset($_SESSION['ahorcado_dificultad']) ? 
                        ($_SESSION['ahorcado_dificultad'] == 'facil' ? 'Fácil' : 'Difícil') : 
                        'No seleccionado'; ?>
                </span>
            </div>
        </div>
        <div  class="hd-sec-gm-v2 v2"style="display:flex; flex-direction:row; gap:10px">
            <div class="hearts-container me-2">
                            <?php
                            $oportunidades = $_SESSION['ahorcado_oportunidades'] ?? 5;
                            $errores = $_SESSION['ahorcado_errores'] ?? 0;
                            $vidas_restantes = $oportunidades - $errores;
                            
                            // Mostrar corazones llenos por vidas restantes
                            for ($i = 0; $i < $vidas_restantes; $i++) {
                                echo '<i class="fas fa-heart heart-full me-2"></i>';
                            }
                            
                            // Mostrar corazones vacíos por vidas perdidas
                            for ($i = 0; $i < $errores; $i++) {
                                echo '<i class="fas fa-heart heart-empty me-2"></i>';
                            }
                            ?>
                        </div>
            
             <div class="timer"> <h5><i class="fa-solid fa-clock me-2"></i></h5><h5 id="timer"> 00:00</h5></div>
        </div>
    </div>

    <div class="page-container">
        <div class="contenedor">
            <div class="container mt-4">
            <?php if (isset($_SESSION['ahorcado_palabra'])): ?>
                <div class="game-container">
                    <div class="game-info">
                        <div class="info-item" style="display:none">
                        
                        </div>
                        
                    </div>

                    <div class="container-img">
                        <div class="plant-image">
                            <?php ?>
                                <img src="../img/plantas/<?php echo $_SESSION['ahorcado_imagen']; ?>" alt="Planta a adivinar" class="img-fluid">
                            <?php ?>
                        </div>
                        <div class="hangman-image">
                            <img src="../img/ahorcado/<?php echo min($_SESSION['ahorcado_errores'] + 1, 6); ?>.png" alt="Ahorcado">
                        
                            <div class="word-display" style="color:white">
                        <?php
                        $palabra = $_SESSION['ahorcado_palabra'];
                        for ($i = 0; $i < strlen($palabra); $i++) {
                            $letra = strtolower($palabra[$i]);
                            if ($letra === ' ') {
                                $mostrar = ' '; // Mostrar espacio directamente
                            } else {
                                $mostrar = in_array(normalizarCaracter($letra), 
                                                array_map('normalizarCaracter', $_SESSION['ahorcado_letras_adivinadas'] ?? [])) 
                                        ? $palabra[$i] : '';
                            }
                            echo "<div class='letter-box'>$mostrar</div>";
                        }
                        ?>
                    </div>
                        </div>
                        </div>
                        
                    </div>
                    

                    <?php if (!$_SESSION['ahorcado_completado']): ?>
                    <div class="keyboard">
                            <?php
                            foreach (range('a', 'z') as $letra) {
                                $clase = 'key';
                                if (in_array($letra, $_SESSION['ahorcado_letras_adivinadas'] ?? [])) {
                                    $clase .= ' used correct';
                                } elseif (in_array($letra, $_SESSION['ahorcado_letras_incorrectas'] ?? [])) {
                                    $clase .= ' used incorrect';
                                }
                                echo "<button type='button' class='$clase' data-letra='$letra'" . 
                                    (in_array($letra, array_merge($_SESSION['ahorcado_letras_adivinadas'] ?? [], $_SESSION['ahorcado_letras_incorrectas'] ?? [])) ? 
                                    ' disabled' : '') . ">$letra</button>";
                            }
                            ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center mt-4" style="color:white">
                            <h4><?php echo $_SESSION['ahorcado_errores'] < $_SESSION['ahorcado_oportunidades'] ? 
                                '¡Felicidades! Has ganado' : '¡Game Over!'; ?></h4>
                            <p>La palabra era: <strong><?php echo strtoupper($_SESSION['ahorcado_palabra']); ?></strong></p>
                            <a href="?reset=1" class="btn btn-success mt-3">Jugar de nuevo</a>
                            
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
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
                    <p><strong>Fácil:</strong> (5 oportunidades)</p>
                    <button class="difficulty-btn" data-difficulty="facil">Fácil</button>

                    <p><strong>Difícil:</strong> (3 oportunidades)</p>
                    <button class="difficulty-btn" data-difficulty="dificil">Difícil</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="comoJugar">¿Cómo jugar?</button>
                    <a href="/BCS_FloraGames/view/gamesMenu.php" class="btn btn-secondary" id="exit-btn">Salir</a>
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
                        <p><i class="fas fa-clock me-2"></i> Tiempo: <span id="victory-time">00:00</span></p>
                        <p><i class="fas fa-trophy me-2"></i> Nivel: <span id="victory-level">1</span></p>
                        <p><i class="fas fa-star me-2"></i> Puntos: <span id="victory-points">0</span></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="?reset=1" class="btn btn-success">Jugar de nuevo</a>
                    <a href="/BCS_FloraGames/view/gamesMenu.php" class="btn btn-secondary" id="exit-btn2">Salir</a>
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
                    <p>1.- Trata de adivinar palabra secreta de la imagen y debes adivinarla letra por letra.</p>
                    <p>2.- Se dibujan rayas para representar las letras.</p>
                    <p>3.- Por cada acierto, se coloca la letra en su posición; por cada error, se dibuja una parte del ahorcado.</p>
                    <p>4.- El juego termina cuando se adivina la palabra o se completa el dibujo del hombrecillo.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="skipInstructions">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <?php include '../components/footer.php'; ?>
    <?php include '../components/modalInsignia.php'; ?>
    <?php include '../components/modalNivel.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elementos del DOM
            const resetButton = document.getElementById('reset-btn');
            const musicToggle = document.getElementById('musicToggle');
            const gameMusic = document.getElementById('gameMusic');
            const timerElement = document.getElementById('timer');
            const victoryTimeElement = document.getElementById('victory-time');
            const wordDisplay = document.querySelector('.word-display');
            const hangmanImage = document.querySelector('.hangman-image img');
            const heartsContainer = document.querySelector('.hearts-container');
            const gameContainer = document.querySelector('.game-container');
            
            // Inicializar modales
            const difficultyModal = new bootstrap.Modal(document.getElementById('difficultyModal'));
            const victoryModal = new bootstrap.Modal(document.getElementById('victoryModal'));
            
            // Mostrar modal si no hay dificultad seleccionada
            <?php if (!isset($_SESSION['ahorcado_dificultad'])): ?>
            difficultyModal.show();
            <?php endif; ?>
            
            // Control de música
            musicToggle.addEventListener('click', function() {
                const icon = this.querySelector('i');
                if (gameMusic.paused) {
                    gameMusic.play();
                    icon.classList.remove('fa-volume-xmark');
                    icon.classList.add('fa-volume-high');
                } else {
                    gameMusic.pause();
                    icon.classList.remove('fa-volume-high');
                    icon.classList.add('fa-volume-xmark');
                }
            });
            
            // Reiniciar juego
            resetButton.addEventListener('click', function() {
                window.location.href = '?reset=1';
            });
            
            // Temporizador
            let timeElapsed = <?php echo isset($_SESSION['ahorcado_tiempo_transcurrido']) ? $_SESSION['ahorcado_tiempo_transcurrido'] : 0; ?>;
            let timerInterval = null;
            
            function formatTime(seconds) {
                const min = Math.floor(seconds / 60);
                const sec = seconds % 60;
                return min.toString().padStart(2, '0') + ':' + sec.toString().padStart(2, '0');
            }
            
            function startTimer() {
                timerInterval = setInterval(() => {
                    timeElapsed++;
                    timerElement.textContent = formatTime(timeElapsed);
                }, 1000);
            }
            
            function stopTimer() {
                clearInterval(timerInterval);
                if (timerInterval) {
                    clearInterval(timerInterval);
                    timerInterval = null;
                }
            }
            
            timerElement.textContent = formatTime(timeElapsed);
          
            
            // Manejar clics en el teclado
            document.querySelectorAll('.key').forEach(button => {
                button.addEventListener('click', function() {
                    const letra = this.getAttribute('data-letra');
                    processLetter(letra);
                });
            });
            
            // Manejar entrada de teclado
            document.addEventListener('keydown', function(event) {
                if (event.key.match(/^[a-z]$/i)) {
                    const letra = event.key.toLowerCase();
                    const boton = document.querySelector(`button[data-letra="${letra}"]:not(:disabled)`);
                    if (boton) processLetter(letra);
                }
            });
            
            // Procesar letra mediante AJAX
            function processLetter(letra) {
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `ajax=process_letter&letra=${letra}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Actualizar palabra mostrada
                        wordDisplay.innerHTML = data.word_display;
                        
                        // Actualizar estado del botón
                        const button = document.querySelector(`button[data-letra="${letra}"]`);
                        if (button) {
                            button.disabled = true;
                            button.classList.add(data.word.includes(letra.toLowerCase()) ? 'correct' : 'incorrect');
                            button.classList.add('used');
                        }
                        
                        // Actualizar imagen del ahorcado
                        hangmanImage.src = `../img/ahorcado/${Math.min(data.errors + 1, 6)}.png`;
                        
                        // Actualizar corazones
                        updateHearts(data.opportunities, data.errors);
                        
                        // Actualizar temporizador
                        timeElapsed = data.time_elapsed;
                        timerElement.textContent = formatTime(timeElapsed);
                        
                        // Mostrar modal si el juego terminó
                        if (data.completed) {
                            stopTimer();
                            const modalHeader = document.querySelector('#victoryModal .modal-header');
                            const modalTitle = document.querySelector('#victoryModal .modal-title');
                            const modalBody = document.querySelector('#victoryModal .modal-body p');
                            const victoryLevel = document.getElementById('victory-level');
                            const victoryPoints = document.getElementById('victory-points');
                            
                            if (data.won) {
                                modalHeader.className = 'modal-header bg-success text-white';
                                modalTitle.textContent = '¡Bien hecho!';
                                modalBody.textContent = 'Has completado esta partida';
                                const points = calculatePoints(timeElapsed, '<?php echo isset($_SESSION['ahorcado_dificultad']) ? $_SESSION['ahorcado_dificultad'] : 'facil'; ?>');
                                victoryPoints.textContent = points;
                                <?php if (isset($_SESSION['usuario_id'])): ?>
                                saveGameResult(true, timeElapsed);
                                savePoints(points);
                                <?php endif; ?>
                            } else {
                                modalHeader.className = 'modal-header bg-danger text-white';
                                modalTitle.textContent = '¡Más suerte para la próxima!';
                                modalBody.textContent = `No has adivinado la palabra esta vez, la respuesta correcta era ${data.word}, inténtalo de nuevo`;
                                victoryPoints.textContent = '0';
                                <?php if (isset($_SESSION['usuario_id'])): ?>
                                saveGameResult(false, timeElapsed);
                                <?php endif; ?>
                            }
                            
                            victoryTimeElement.textContent = formatTime(timeElapsed);
                            victoryLevel.textContent = '<?php echo isset($_SESSION['ahorcado_dificultad']) ? ($_SESSION['ahorcado_dificultad'] == 'facil' ? 'Fácil' : 'Difícil') : 'Fácil'; ?>';
                            victoryModal.show();
                            
                            // Desactivar teclado
                            timerElement.textContent = '00:00';
                            gameContainer.innerHTML = `
                                <div class="text-center mt-4">
                                    <h4>${data.won ? '¡Felicidades! Has ganado' : '¡Game Over!'}</h4>
                                    <p>La palabra era: <strong>${data.word.toUpperCase()}</strong></p>
                                    <a href="?reset=1" class="btn btn-success mt-3">Jugar de nuevo</a>
                                </div>
                            `;
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
            }
            
             //startTimer();

             if (!document.querySelector('.text-center.mt-4')) {
                startTimer();
            }

            // Función para actualizar corazones
            function updateHearts(opportunities, errors) {
                const vidasRestantes = opportunities - errors;
                heartsContainer.innerHTML = '';
                
                for (let i = 0; i < vidasRestantes; i++) {
                    const heart = document.createElement('i');
                    heart.className = 'fas fa-heart heart-full me-2';
                    heartsContainer.appendChild(heart);
                }
                
                for (let i = 0; i < errors; i++) {
                    const heart = document.createElement('i');
                    heart.className = 'fas fa-heart heart-empty me-2';
                    if (i === errors - 1 && errors > 0) {
                        heart.classList.add('heart-lost');
                    }
                    heartsContainer.appendChild(heart);
                }
            }
            
            // Función para calcular puntos
            function calculatePoints(timeElapsed, dificultad) {
                const basePoints = 100;
                const timeBonus = Math.max(0, 300 - timeElapsed);
                const difficultyMultiplier = dificultad === 'dificil' ? 1.5 : 1;
                return Math.round((basePoints + timeBonus) * difficultyMultiplier);
            }
            
            // Configurar botones del modal
            document.querySelectorAll('.difficulty-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const difficulty = this.getAttribute('data-difficulty');
                    window.location.href = `?difficulty=${difficulty}`;
                });
            });
            
            document.querySelector('#victoryModal button[href="?reset=1"]').addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = '?reset=1';
            });
            
            document.getElementById('exit-btn2').addEventListener('click', function() {
                victoryModal.hide();
                setTimeout(() => {
                    window.location.href = '/BCS_FloraGames/view/gamesMenu.php';
                }, 300);
            });
        });

        // Manejar clic en el botón Exit
        document.getElementById('exit').addEventListener('click', function() {
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'ajax=reset_session'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '../view/gamesMenu.php';
                }
            })
            .catch(error => {
                console.error('Error al reiniciar sesión:', error);
                // Redirigir incluso si hay un error para no bloquear al usuario
                window.location.href = '../view/gamesMenu.php';
            });
        });

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
        function savePoints(points) {
    console.log('Intentando guardar puntos:', points);
    
    fetch('../config/updatePoints.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `points=${points}&game=memorama`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Puntos actualizados correctamente:', data);
            
            // Mostrar modal de nuevo nivel si hubo subida
            if (data.levelUp) {
                showLevelUpModal(data.newLevel, data.levelName, data.levelImage);
            }
        } else {
            console.error('Error al actualizar puntos:', data.message);
        }
    })
    .catch(error => {
        console.error('Error completo:', error);
    });
}

function showLevelUpModal(newLevel, levelName, levelImage) {
            // Actualizar el contenido del modal con el nuevo nivel
            document.getElementById('newLevelDisplay').textContent = newLevel;
            document.getElementById('levelNumberDisplay').textContent = newLevel;
            
            // Actualizar nombre del nivel e imagen
            document.getElementById('levelNameDisplay').textContent = levelName;
            document.getElementById('levelImageDisplay').src = `../img/niveles/${levelImage}`;
            
            // Mostrar el modal
            const levelUpModal = new bootstrap.Modal(document.getElementById('levelUpModal'));
            createConfetti();
            levelUpModal.show();
            
        }
        // Función para crear efecto de confeti (opcional)
        function createConfetti() {
            const colors = ['#246741', '#ff5252', '#ffab00', '#00bcd4', '#673ab7'];
            const container = document.querySelector('.modal-content');
            
            for (let i = 0; i < 50; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.left = Math.random() * 100 + '%';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.animation = `confettiFall ${Math.random() * 3 + 2}s linear forwards`;
                confetti.style.animationDelay = Math.random() * 0.5 + 's';
                container.appendChild(confetti);
                
                // Eliminar después de la animación
                setTimeout(() => {
                    confetti.remove();
                }, 5000);
            }
        }

 function saveGameResult(won, duration, accuracy, wpm) {
    if (<?php echo isset($_SESSION['usuario_id']) ? 'true' : 'false'; ?>) {
        const userId = <?php echo isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 'null'; ?>;
        const gameData = {
            usuario_id: userId,
            duracion: Math.floor(duration),
            fue_ganado: won ? 1 : 0
        };
        console.log('Enviando datos al servidor:', gameData);
        fetch('../config/saveGameResult.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(gameData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Respuesta del servidor:', data);
            if (data.success && data.insignia) {
                console.log('Insignia recibida:', data.insignia);
                try {
                    const modalElement = document.getElementById('badgeModal');
                    if (!modalElement) {
                        console.error('Modal con id="badgeModal" no encontrado en el DOM');
                        return;
                    }
                    const nombreElement = document.getElementById('badgeName');
                    const descripcionElement = document.getElementById('badgeDescription');
                    const iconoElement = document.getElementById('badgeIcon');
                    if (!nombreElement || !descripcionElement || !iconoElement) {
                        console.error('Elementos del modal no encontrados:', {
                            nombre: !!nombreElement,
                            descripcion: !!descripcionElement,
                            icono: !!iconoElement
                        });
                        return;
                    }
                    // Cerrar el modal de victoria si está abierto
                    const victoryModal = bootstrap.Modal.getInstance(document.getElementById('victoryModal'));
                    if (victoryModal) {
                        victoryModal.hide();
                        console.log('Modal de victoria cerrado');
                        // Esperar un breve momento para evitar conflictos de Bootstrap
                        setTimeout(() => {
                            nombreElement.textContent = 'Insignia: ' + data.insignia.nombre;
                            descripcionElement.textContent = data.insignia.descripcion;
                            iconoElement.src = '../img/insignias/' + data.insignia.icono_url;
                            // Manejar errores de carga de imagen
                            iconoElement.onerror = () => {
                                console.error('Error al cargar la imagen:', iconoElement.src);
                                iconoElement.src = '../img/insignias/default.png'; // Imagen por defecto
                            };
                            const modal = new bootstrap.Modal(modalElement);
                            modal.show();
                            console.log('Modal de insignia mostrado');
                        }, 500);
                    } else {
                        // Si no hay modal de victoria, abrir directamente
                        nombreElement.textContent = 'Insignia: ' + data.insignia.nombre;
                        descripcionElement.textContent = data.insignia.descripcion;
                        iconoElement.src = '../img/insignias/' + data.insignia.icono_url;
                        iconoElement.onerror = () => {
                            console.error('Error al cargar la imagen:', iconoElement.src);
                            iconoElement.src = '../img/insignias/default.png';
                        };
                        const modal = new bootstrap.Modal(modalElement);
                        modal.show();
                        console.log('Modal de insignia mostrado');
                    }
                } catch (error) {
                    console.error('Error al mostrar el modal:', error);
                }
            } else {
                console.log('No se asignó ninguna insignia o la respuesta no fue exitosa:', data);
            }
        })
        .catch(error => {
            console.error('Error al guardar resultado:', error);
        });
    }}
    </script>
</body>
</html>
