<?php
session_start();
require_once __DIR__ . '/../config/dataPlanta.php';

// Obtener todas las plantas de la base de datos
function obtenerPlantas() {
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT * FROM ficha_planta");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Determinar modo de juego
$modo = isset($_GET['modo']) && $_GET['modo'] === 'dificil' ? 'dificil' : 'facil';
$plantas = obtenerPlantas();
shuffle($plantas);

// Definir tamaño de la carta
$size = $modo === 'dificil' ? 12 : 9;
$carta = array_slice($plantas, 0, $size);

// Barajar el mazo para las cartas que irán saliendo
$mazo = $plantas;
shuffle($mazo);

// Guardar en sesión para mantener el estado del juego
$_SESSION['loteria_modo'] = $modo;
$_SESSION['loteria_carta'] = $carta;
$_SESSION['loteria_mazo'] = $mazo;
$_SESSION['loteria_index'] = 0;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lotería - Flora Games</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/stylesMedia.css">
    <link rel="stylesheet" href="../css/styleGames.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="icon" type="image/x-icon" href="../img/logoFG.ico">
    <style>


.container {
    display: flex;
    justify-content: center;
    gap: 30px;
    align-items: flex-start;
}

/* Contenedor principal del tablero */
#carta {
    width: auto;
    background: #f8f9fa;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    padding: 15px;
    margin-bottom: 20px;
    display: grid;
    gap: 15px;
    justify-items: center;
     background: linear-gradient(135deg, #ebfff3 0%, #d4f3e1 100%);
}

/* Estilos de las celdas de la carta */
/* Estilos para las cartas rectangulares verticales */
.carta-celda {
    position: relative;
    cursor: pointer;
    transition: all 0.3s ease;
    padding: 10px !important;
    border-radius: 8px;
    background: white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    height: 210px; /* Altura fija para todas las cartas */
    width: 120px; /* Ancho fijo para todas las cartas */
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
    margin: 5px;
    overflow: hidden;
}

.card-img {
    height: 80%;
    border-radius: 4px;
    margin-bottom: 8px;
}

.carta-celda div {
    font-size: 12px;
    font-weight: 600;
    color: #246741;
    text-align: center;
    padding: 0 5px;
    display: -webkit-box;
    -webkit-line-clamp: 2; /* Limita a 2 líneas */
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Ajustes para el grid de cartas */
#carta .row {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 10px;
    margin: 0;
}

#carta .row > [class*="col-"] {
    padding: 0.5rem !important;
    flex: 0 0 auto;
}

/* Ajuste para el modo difícil (4 columnas) y fácil (3 columnas) */
@media (min-width: 768px) {
    .modo-dificil .carta-celda {
        width: 140px;
        height: 200px;
    }
    .modo-dificil .card-img {
        width: 100px;
        height: 100px;
    }
}
/* Estilo para las celdas marcadas */
.carta-celda.marcada {
    background-color: #e8f5e9;
}

.hoja-overlay {
    display: none;
    position: absolute;
    top: 20%;
    left: 50%;
    transform: translateX(-50%);
    width: 70% !important;
    pointer-events: none;
    filter: drop-shadow(0 0 0.75rem white);
    z-index: 2;
}

/* Estilos para el área del mazo */
#mazo-actual {
    background: white;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 350px;
    background: linear-gradient(135deg, #ebfff3 0%, #d4f3e1 100%);
}

#planta-actual .card {
    border: none;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

#planta-actual .card-img-top {
    height: 90%;
    object-fit: cover;
}

#planta-actual .card-body {
    padding: 15px;
    background: #f8f9fa;
}

/* Botones de control */
#mazo-actual .btn {
    margin: 0 5px;
    font-weight: 600;
    padding: 8px 15px;
    border-radius: 8px;
    border: none;
}

#prev-btn, #next-btn {
    background-color: #246741;
    color: white;
}

#pause-btn {
    background-color: #ffc107;
    color: #212529;
}


/* Animación para feedback */
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    20%, 60% { transform: translateX(-5px); }
    40%, 80% { transform: translateX(5px); }
}


    </style>
</head>
<body>
    <?php include '../components/header.php'; ?>
    
    <div class="header-secundary" style="color:#246741; display: flex; align-items: center;">
        <div style="display:flex; flex-direction:row; gap:10px">
            <button class="reset-btn" onclick="window.location.href='../view/gamesMenu.php'" title="Volver al menú">
                <h5><i class="fas fa-sign-out-alt fa-flip-horizontal"></i></h5>
            </button>
            <button class="reset-btn" id="musicToggle" title="Música">
                <h5><i class="fa-solid <?php echo $musicEnabled ? 'fa-volume-high' : 'fa-volume-xmark'; ?>"></i></h5>
            </button>
            <audio id="gameMusic" loop <?php echo $musicEnabled ? 'autoplay' : ''; ?>>
                <source src="../assets/musica.mp3" type="audio/mp3">
            </audio>
            <button class="reset-btn" id="reset-btn" title="Reiniciar" type="button">
                <h5><i class="fa-solid fa-arrow-rotate-right"></i></h5>
            </button>
        </div>
        <div style="text-align:center">
            <h5 style="margin:0">Loteria</h5>
            <div class="level">
                Modo: <span id="level-display">
                   <?php echo ucfirst($modo); ?>
                </span>
            </div>
        </div>
        <div style="display:flex; flex-direction:row; gap:10px">
            <h5><i class="fa-solid fa-clock"></i></h5>
            <div class="timer">
                <h5 id="timer">00:00</h5>
            </div>
        </div>
    </div>
<script>
// Make seconds globally available
let seconds = 0;

document.addEventListener('DOMContentLoaded', function() {
    // --- Temporizador ---
    let timerInterval;
    function startTimer() {
        timerInterval = setInterval(function() {
            seconds++;
            let min = Math.floor(seconds / 60);
            let seg = seconds % 60;
            document.getElementById('timer').textContent =
                (min < 10 ? '0' : '') + min + ':' + (seg < 10 ? '0' : '') + seg;
        }, 1000);
    }
    function resetTimer() {
        clearInterval(timerInterval);
        seconds = 0;
        document.getElementById('timer').textContent = '00:00';
        startTimer();
    }
    startTimer();

    // --- Música ---
    const musicToggle = document.getElementById('musicToggle');
    const gameMusic = document.getElementById('gameMusic');
    musicToggle.addEventListener('click', function() {
        const isPlaying = !gameMusic.paused;
        // Cambiar icono
        const icon = musicToggle.querySelector('i');
        if (isPlaying) {
            gameMusic.pause();
            icon.classList.remove('fa-volume-high');
            icon.classList.add('fa-volume-xmark');
            updateMusicPreference(0);
        } else {
            gameMusic.play();
            icon.classList.remove('fa-volume-xmark');
            icon.classList.add('fa-volume-high');
            updateMusicPreference(1);
        }
    });
    function updateMusicPreference(enabled) {
        // AJAX para guardar preferencia
        fetch('../config/updateMusicPreference.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'music_enabled=' + enabled
        });
    }

    // --- Reset: mostrar modal de dificultad y reiniciar timer ---
    const resetBtn = document.getElementById('reset-btn');
    resetBtn.addEventListener('click', function() {
        // Reiniciar timer
        resetTimer();
        // Mostrar modal de dificultad
        const difficultyModal = new bootstrap.Modal(document.getElementById('difficultyModal'));
        difficultyModal.show();
    });
});
</script>

    <div class="contenedor">
    <div class="container mt-4">
        <!-- Tablero de lotería -->
        <div id="carta" class="mb-4">
            <div class="row g-3">
                <?php
                $cols = $modo === 'dificil' ? 4 : 3;
                foreach ($carta as $i => $planta):
                    if ($i % $cols === 0 && $i > 0) echo '</div><div class="row g-3">';
                ?>
                    <div class="col-md-<?php echo 12/$cols; ?> card-info p-2 text-center carta-celda" data-index="<?php echo $i; ?>">
                        <img class="card-img" src="../img/plantas/<?php echo htmlspecialchars($planta['foto']); ?>" alt="<?php echo htmlspecialchars($planta['nombre_comun']); ?>">
                        <div><?php echo htmlspecialchars($planta['nombre_comun']); ?></div>
                        <img src="../img/hoja.png" class="hoja-overlay">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Mazo y controles -->
        <div id="mazo-actual">
            <h5 class="mb-3 text-center" style="color: #246741;">Planta Actual</h5>
            <div id="planta-actual" class="mb-3"></div>
            <div class="controls text-center">
                <button id="prev-btn" class="btn btn-secondary"><i class="fas fa-arrow-left"></i></button>
                <button id="pause-btn" class="btn btn-warning"><i class="fas fa-pause"></i> </button>
                <button id="next-btn" class="btn btn-secondary" style="display:none"><i class="fas fa-arrow-right"></i></button>
            </div>
            <div class="mt-3 text-center">
                <small class="text-muted">Cartas restantes: <span id="cartas-restantes"><?php echo count($mazo); ?></span></small>
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
                        <p><i class="fas fa-star me-2"></i> Puntos: <span id="victory-points" style="margin-left: 5px;">0</span></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="continue-btn">Volver a jugar</button>
                    <button type="button" class="btn btn-secondary" id="exit-btn2">Salir</button>
                </div>
            </div>
        </div>
    </div>

    <?php include '../components/footer.php'; ?>    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mostrar el modal al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            const difficultyModal = new bootstrap.Modal(document.getElementById('difficultyModal'));
            // Mostrar el modal solo si no hay modo en la URL
            if (!window.location.search.includes('modo')) {
                difficultyModal.show();
            }

            // Lógica para seleccionar dificultad y recargar la página con el modo seleccionado
            document.querySelectorAll('.difficulty-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    let modo = this.dataset.difficulty;
                    if (modo === 'easy') window.location.search = '?modo=facil';
                    else if (modo === 'hard') window.location.search = '?modo=dificil';
                    else window.location.search = '?modo=notime';
                });
            });

            document.getElementById('exit-btn2').addEventListener('click', function() {
                window.location.href = '../view/gamesMenu.php';
            });
            document.getElementById('exit-btn').addEventListener('click', function() {
                window.location.href = '../view/gamesMenu.php';
            });
        });

        // Lógica para mostrar las cartas del mazo con control manual
        let mazo = <?php echo json_encode(array_map(function($p) {
            return [
                'nombre_comun' => $p['nombre_comun'],
                'foto' => $p['foto']
            ];
        }, $mazo)); ?>;
        let index = 0;
        let intervalo = null;
        let pausado = false;

        // Guardar nombres de cartas que ya han salido
        let cartasSalidas = [];

        function mostrarCarta(idx) {
            if (idx < 0 || idx >= mazo.length) return;
            let planta = mazo[idx];
            document.getElementById('planta-actual').innerHTML =
                `<div class="card mx-auto" style="width: 18rem;">
                    <img src="../img/plantas/${planta.foto}" class="card-img-top" alt="${planta.nombre_comun}">
                    <div class="card-body">
                        <h5 class="card-title">${planta.nombre_comun}</h5>
                    </div>
                </div>`;
        }

        // Variables para puntos y modo
        let puntos = <?php echo $modo === 'dificil' ? 200 : 150; ?>;
        let modoTexto = "<?php echo ucfirst($modo); ?>";
        
        // Mostrar modal de victoria
        function mostrarModalVictoria() {
            clearInterval(intervalo);
            // Mostrar tiempo
            document.getElementById('victory-time').textContent = document.getElementById('timer').textContent;
            // Mostrar modo
            document.getElementById('victory-mode').textContent = modoTexto;
            // Mostrar puntos
            document.getElementById('victory-points').textContent = puntos;
            // Guardar puntos y resultado si hay sesión
            savePoints(puntos);
            // Mostrar modal
            const victoryModal = new bootstrap.Modal(document.getElementById('victoryModal'));
            victoryModal.show();
        }
        function savePoints(points) {
            console.log('Intentando guardar puntos:', points); // Añadir para depuración
        
            fetch('../config/updatePoints.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `points=${points}&game=loteria`
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
                    // Solo llamar a saveGameResult si ganó
                    saveGameResult(true, seconds);
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
        function mostrarSiguiente() {
            if (index >= mazo.length) {
                clearInterval(intervalo);
                document.getElementById('planta-actual').innerHTML = "<h4>¡Fin del mazo!</h4>";
                // Solo mostrar el modal de victoria si todas las cartas están marcadas
                if (todasLasCartasMarcadas()) {
                    mostrarModalVictoria();
                }
                return;
            }
            mostrarCarta(index);
            cartasSalidas.push(mazo[index].nombre_comun);
            index++;
        }

        function mostrarAnterior() {
            if (index > 1) {
                index -= 2;
                cartasSalidas.pop(); // Quitar la última carta salida
                mostrarSiguiente();
            }
        }

        // Iniciar el pase automático al cargar la página (después de seleccionar dificultad)
        window.onload = function() {
            if (window.location.search.includes('modo')) {
                mostrarSiguiente();
                intervalo = setInterval(function() {
                    if (!pausado) mostrarSiguiente();
                }, 100);
            }
        };

        document.getElementById('pause-btn').onclick = function() {
            pausado = !pausado;
            this.textContent = pausado ? 'Reanudar' : 'Pausa';
            if (!pausado) {
                // Si se reanuda y no hay intervalo, lo creamos de nuevo
                if (!intervalo) {
                    intervalo = setInterval(function() {
                        if (!pausado) mostrarSiguiente();
                    }, 3500);
                }
            } else {
                // Si se pausa, detenemos el intervalo
                clearInterval(intervalo);
                intervalo = null;
            }
        };
        document.getElementById('next-btn').onclick = function() {
            if (index < mazo.length) mostrarSiguiente();
        };
        document.getElementById('prev-btn').onclick = function() {
            mostrarAnterior();
        };

        // Selección de cartas y superposición de hoja SOLO si ya salió y solo una vez
        document.querySelectorAll('.carta-celda').forEach(function(celda) {
            celda.addEventListener('click', function() {
                const nombreCarta = this.querySelector('div').innerText.trim();
                const hoja = this.querySelector('.hoja-overlay');
                // Solo permitir si la carta ya salió y la hoja no está puesta
                if (cartasSalidas.includes(nombreCarta) && hoja.style.display !== 'block') {
                    hoja.style.display = 'block';
                    this.classList.add('marcada');
                    // Verificar si todas las cartas tienen hoja
                    if (todasLasCartasMarcadas()) {
                        setTimeout(() => {
                            mostrarModalVictoria();
                        }, 500);
                    }
                } else if (!cartasSalidas.includes(nombreCarta)) {
                    // Feedback visual si no ha salido
                    this.style.animation = 'shake 0.3s';
                    setTimeout(() => { this.style.animation = ''; }, 300);
                }
                // Si la hoja ya está puesta, no se puede quitar
            });
        });

        // Función para verificar si todas las cartas están marcadas
        function todasLasCartasMarcadas() {
            return Array.from(document.querySelectorAll('.carta-celda .hoja-overlay'))
                .every(hoja => hoja.style.display === 'block');
        }

        // Botón "Volver a jugar" en el modal de victoria
        document.getElementById('continue-btn').addEventListener('click', function() {
            // Ocultar modal de victoria y mostrar modal de dificultad
            const victoryModal = bootstrap.Modal.getInstance(document.getElementById('victoryModal'));
            victoryModal.hide();
            setTimeout(() => {
                const difficultyModal = new bootstrap.Modal(document.getElementById('difficultyModal'));
                difficultyModal.show();
            }, 500);
        });
    </script>
</body>
</html>