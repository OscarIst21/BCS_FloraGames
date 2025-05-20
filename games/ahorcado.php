<?php
session_start();
require_once __DIR__ . '/../connection/database.php';

// Lista de palabras (nombres de plantas comunes)
$palabras = [
    'facil' => [
        'rosa', 'lirio', 'tulipan', 'girasol', 'cactus', 'orquidea', 'margarita', 'jazmin',
        'lavanda', 'menta', 'bambu', 'helecho', 'aloe', 'pino', 'roble', 'cedro', 'palma',
        'hiedra', 'musgo', 'tomillo', 'romero', 'salvia', 'petunia', 'dalia', 'begonia'
    ],
    'dificil' => [
        'buganvilla', 'crisantemo', 'hortensia', 'azalea', 'magnolia', 'bromelia', 'geranio',
        'kalanchoe', 'adelfa', 'hibisco', 'jacaranda', 'bonsai', 'eucalipto', 'ciclamen',
        'gardenia', 'agapanto', 'amarilis', 'anturio', 'caladio', 'camelia', 'celosia',
        'dracaena', 'freesia', 'gerbera', 'gloxinia'
    ]
];

// Inicializar o reiniciar el juego
if (!isset($_SESSION['ahorcado_iniciado']) || isset($_GET['reset']) || isset($_GET['difficulty'])) {
    // Establecer dificultad
    if (isset($_GET['difficulty'])) {
        $_SESSION['ahorcado_dificultad'] = $_GET['difficulty'];
    } else if (!isset($_SESSION['ahorcado_dificultad'])) {
        $_SESSION['ahorcado_dificultad'] = 'facil'; // Dificultad por defecto
    }
    
    // Establecer número de oportunidades según dificultad
    $_SESSION['ahorcado_oportunidades'] = ($_SESSION['ahorcado_dificultad'] == 'facil') ? 5 : 3;
    
    // Seleccionar palabra aleatoria según dificultad
    $lista_palabras = $palabras[$_SESSION['ahorcado_dificultad']];
    $palabra_seleccionada = $lista_palabras[array_rand($lista_palabras)];
    
    // Guardar en sesión
    $_SESSION['ahorcado_palabra'] = $palabra_seleccionada;
    $_SESSION['ahorcado_letras_adivinadas'] = [];
    $_SESSION['ahorcado_letras_incorrectas'] = [];
    $_SESSION['ahorcado_errores'] = 0;
    $_SESSION['ahorcado_iniciado'] = true;
    $_SESSION['ahorcado_tiempo_inicio'] = time();
    $_SESSION['ahorcado_completado'] = false;
    
    // Redireccionar para limpiar la URL
    if (isset($_GET['reset']) || isset($_GET['difficulty'])) {
        header('Location: ahorcado.php');
        exit;
    }
}

// Procesar intento de letra
if (isset($_POST['letra'])) {
    $letra = strtolower($_POST['letra']);
    
    // Verificar si la letra ya fue adivinada
    if (!in_array($letra, $_SESSION['ahorcado_letras_adivinadas']) && 
        !in_array($letra, $_SESSION['ahorcado_letras_incorrectas'])) {
        
        // Verificar si la letra está en la palabra
        if (strpos($_SESSION['ahorcado_palabra'], $letra) !== false) {
            $_SESSION['ahorcado_letras_adivinadas'][] = $letra;
            
            // Verificar si se completó la palabra
            $palabra_completa = true;
            for ($i = 0; $i < strlen($_SESSION['ahorcado_palabra']); $i++) {
                if (!in_array($_SESSION['ahorcado_palabra'][$i], $_SESSION['ahorcado_letras_adivinadas'])) {
                    $palabra_completa = false;
                    break;
                }
            }
            
            if ($palabra_completa && !$_SESSION['ahorcado_completado']) {
                $_SESSION['ahorcado_completado'] = true;
                $_SESSION['ahorcado_tiempo_fin'] = time();
                $duracion = $_SESSION['ahorcado_tiempo_fin'] - $_SESSION['ahorcado_tiempo_inicio'];
                $puntos = calcularPuntos($duracion, $_SESSION['ahorcado_errores'], $_SESSION['ahorcado_dificultad']);
                
                // Guardar resultado si el usuario está autenticado
                if (isset($_SESSION['usuario_id'])) {
                    guardarResultado(true, $duracion, $puntos);
                }
            }
        } else {
            $_SESSION['ahorcado_letras_incorrectas'][] = $letra;
            $_SESSION['ahorcado_errores']++;
            
            // Verificar si se agotaron las oportunidades
            if ($_SESSION['ahorcado_errores'] >= $_SESSION['ahorcado_oportunidades'] && !$_SESSION['ahorcado_completado']) {
                $_SESSION['ahorcado_completado'] = true;
                $_SESSION['ahorcado_tiempo_fin'] = time();
                $duracion = $_SESSION['ahorcado_tiempo_fin'] - $_SESSION['ahorcado_tiempo_inicio'];
                
                // Guardar resultado si el usuario está autenticado
                if (isset($_SESSION['usuario_id'])) {
                    guardarResultado(false, $duracion, 0);
                }
            }
        }
    }
    
    // Redireccionar para evitar reenvío del formulario
    header('Location: ahorcado.php');
    exit;
}

// Función para calcular puntos
function calcularPuntos($duracion, $errores, $dificultad) {
    // Puntos base
    $puntos_base = 100;
    
    // Multiplicador por dificultad
    $multiplicador_dificultad = ($dificultad == 'facil') ? 1 : 1.5;
    
    // Penalización por errores
    $factor_errores = max(0.5, 1 - ($errores / 10));
    
    // Penalización por tiempo
    $factor_tiempo = max(0.5, 1 - ($duracion / 300));
    
    // Calcular puntos totales
    return round($puntos_base * $multiplicador_dificultad * $factor_errores * $factor_tiempo);
}

// Función para formatear tiempo
function formatearTiempo($segundos) {
    $minutos = floor($segundos / 60);
    $segundos = $segundos % 60;
    return sprintf('%02d:%02d', $minutos, $segundos);
}

// Función para guardar resultado
function guardarResultado($ganado, $duracion, $puntos) {
    // Preparar datos
    $datos = [
        'usuario_id' => $_SESSION['usuario_id'],
        'duracion' => $duracion,
        'fue_ganado' => $ganado ? 1 : 0,
        'puntos' => $puntos
    ];
    
    // Convertir a JSON
    $json_datos = json_encode($datos);
    
    // Configurar solicitud
    $ch = curl_init('../config/saveGameResult.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_datos);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($json_datos)
    ]);
    
    // Ejecutar solicitud
    $resultado = curl_exec($ch);
    curl_close($ch);
    
    return $resultado;
}

// Obtener estado actual del juego
$palabra = $_SESSION['ahorcado_palabra'];
$letras_adivinadas = $_SESSION['ahorcado_letras_adivinadas'];
$letras_incorrectas = $_SESSION['ahorcado_letras_incorrectas'];
$errores = $_SESSION['ahorcado_errores'];
$oportunidades = $_SESSION['ahorcado_oportunidades'];
$dificultad = $_SESSION['ahorcado_dificultad'];
$completado = $_SESSION['ahorcado_completado'];
$ganado = $completado && $errores < $oportunidades;

// Calcular tiempo transcurrido
$tiempo_inicio = $_SESSION['ahorcado_tiempo_inicio'];
$tiempo_actual = $completado ? $_SESSION['ahorcado_tiempo_fin'] : time();
$tiempo_transcurrido = $tiempo_actual - $tiempo_inicio;

// Calcular puntos si el juego está completado y ganado
$puntos = 0;
if ($completado && $ganado) {
    $puntos = calcularPuntos($tiempo_transcurrido, $errores, $dificultad);
}

// Texto de dificultad para mostrar
$texto_dificultad = ($dificultad == 'facil') ? 'Fácil' : 'Difícil';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ahorcado - Plantas</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background-image: url('../img/fondo.jpg');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            padding-top: 80px; /* Espacio para el navbar */
        }
        
        .game-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding: 0.5rem;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
        }
        
        .game-container {
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .word-display {
            display: flex;
            justify-content: center;
            margin: 1.5rem 0;
            gap: 0.5rem;
        }
        
        .letter-box {
            width: 40px;
            height: 40px;
            border-bottom: 2px solid #333;
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
            gap: 0.5rem;
            margin-top: 1.5rem;
        }
        
        .key {
            width: 40px;
            height: 40px;
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            border-radius: 5px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .key:hover {
            background-color: #e0e0e0;
        }
        
        .key.used {
            background-color: #ccc;
            cursor: not-allowed;
            opacity: 0.7;
        }
        
        .key.correct {
            background-color: #a3cfbb;
        }
        
        .key.incorrect {
            background-color: #f8d7da;
        }
        
        .hangman-image {
            text-align: center;
            margin-bottom: 1rem;
        }
        
        .hangman-image img {
            max-height: 200px;
        }
        
        .game-info {
            display: flex;
            justify-content: space-around;
            margin-bottom: 1rem;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .difficulty-btn {
            display: block;
            width: 100%;
            padding: 1rem;
            margin-bottom: 1rem;
            text-align: center;
            background-color: #f8f9fa;
            border-radius: 10px;
            text-decoration: none;
            color: #212529;
            font-weight: bold;
            transition: all 0.3s;
        }
        
        .difficulty-btn:hover {
            background-color: #e9ecef;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <?php include '../components/header.php'; ?>
    
    <div class="game-header container">
        <div>
            <button id="reset-btn" class="btn btn-outline-secondary">
                <i class="fas fa-sync-alt"></i> Reiniciar
            </button>
        </div>
        <div style="text-align:center">
            <h5>Ahorcado - Plantas</h5>
            <div class="level">Modo <span id="level-display"><?php echo $texto_dificultad; ?></span></div>
        </div>
        <div style="display:flex; flex-direction:row; gap:10px">
            <h5><i class="fa-solid fa-clock"></i></h5>
            <div class="timer">
                <h5 id="timer"><?php echo formatearTiempo($tiempo_transcurrido); ?></h5>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="game-info">
            <div class="info-item">
                <i class="fas fa-heart"></i>
                <span>Oportunidades: <span id="chances"><?php echo ($oportunidades - $errores); ?></span> / <?php echo $oportunidades; ?></span>
            </div>
            <div class="info-item">
                <i class="fas fa-times"></i>
                <span>Errores: <span id="errors"><?php echo $errores; ?></span></span>
            </div>
        </div>

        <div class="game-container">
            <div class="hangman-image">
                <?php 
                $imagen_num = min($errores + 1, 6);
                echo "<img src='../img/ahorcado/{$imagen_num}.png' alt='Ahorcado'>";
                ?>
            </div>
            
            <div class="word-display">
                <?php
                for ($i = 0; $i < strlen($palabra); $i++) {
                    $letra = $palabra[$i];
                    $mostrar = in_array($letra, $letras_adivinadas) ? $letra : '';
                    echo "<div class='letter-box'>$mostrar</div>";
                }
                ?>
            </div>
            
            <?php if (!$completado): ?>
                <div class="keyboard">
                    <?php
                    $letras = range('a', 'z');
                    foreach ($letras as $letra) {
                        $clase = 'key';
                        if (in_array($letra, $letras_adivinadas)) {
                            $clase .= ' used correct';
                        } else if (in_array($letra, $letras_incorrectas)) {
                            $clase .= ' used incorrect';
                        }
                        
                        echo "<form method='post' style='margin:0;'>";
                        echo "<input type='hidden' name='letra' value='$letra'>";
                        echo "<button type='submit' class='$clase' " . (($completado || in_array($letra, $letras_adivinadas) || in_array($letra, $letras_incorrectas)) ? 'disabled' : '') . ">$letra</button>";
                        echo "</form>";
                    }
                    ?>
                </div>
            <?php else: ?>
                <div class="text-center mt-4">
                    <h4><?php echo $ganado ? '¡Felicidades! Has ganado' : '¡Has perdido!'; ?></h4>
                    <p>La palabra era: <strong><?php echo strtoupper($palabra); ?></strong></p>
                    <?php if ($ganado): ?>
                        <p>Tiempo: <?php echo formatearTiempo($tiempo_transcurrido); ?></p>
                        <p>Puntos: <?php echo $puntos; ?></p>
                    <?php endif; ?>
                    <a href="?reset=1" class="btn btn-primary mt-3">Jugar de nuevo</a>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if (!$completado): ?>
            <div class="text-center mt-3">
                <a href="?reset=1" class="btn btn-outline-secondary">Reiniciar juego</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal de selección de dificultad -->
    <div class="modal fade" id="difficultyModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="difficultyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="difficultyModalLabel">Selecciona la dificultad</h5>
                </div>
                <div class="modal-body">
                    <a href="?difficulty=facil" class="difficulty-btn">Fácil (5 oportunidades)</a>
                    <a href="?difficulty=dificil" class="difficulty-btn">Difícil (3 oportunidades)</a>
                    <a href="../index.php" class="difficulty-btn" style="background-color: #f8d7da;">Salir del juego</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elementos del DOM
            const timerElement = document.getElementById('timer');
            const resetButton = document.getElementById('reset-btn');
            
            // Variables del juego
            let timerInterval;
            let startTime = <?php echo $tiempo_inicio; ?>;
            let elapsedTime = <?php echo $tiempo_transcurrido; ?>;
            let gameCompleted = <?php echo $completado ? 'true' : 'false'; ?>;
            
            // Modales
            const difficultyModalElement = document.getElementById('difficultyModal');
            const difficultyModal = new bootstrap.Modal(difficultyModalElement, {
                backdrop: 'static',
                keyboard: false
            });
            
            // Mostrar modal de dificultad solo si no hay dificultad establecida
            <?php if (!isset($_SESSION['ahorcado_dificultad'])): ?>
            difficultyModal.show();
            <?php endif; ?>
            
            // Función para actualizar el temporizador
            function updateTimer() {
                const now = Math.floor(Date.now() / 1000);
                elapsedTime = now - startTime;
                
                // Formatear el tiempo (minutos:segundos)
                const minutes = Math.floor(elapsedTime / 60);
                const seconds = elapsedTime % 60;
                timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }
            
            // Iniciar el temporizador si el juego no está completado
            if (!gameCompleted) {
                timerInterval = setInterval(updateTimer, 1000);
                updateTimer(); // Actualizar inmediatamente
            }
            
            // Evento para el botón de reinicio
            resetButton.addEventListener('click', function() {
                window.location.href = 'ahorcado.php?reset=1';
            });
            
            // Función para guardar puntos en la base de datos
            function savePoints(points) {
                fetch('../config/updatePoints.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `points=${points}&game=ahorcado`
                })
                .then(response => response.text())
                .then(data => {
                    console.log('Puntos actualizados:', data);
                })
                .catch(error => {
                    console.error('Error al actualizar puntos:', error);
                });
            }
            
            // Guardar puntos si el juego está completado y ganado
            <?php if ($completado && $ganado && isset($_SESSION['usuario_id'])): ?>
            savePoints(<?php echo $puntos; ?>);
            <?php endif; ?>
        });
    </script>
</body>
</html>