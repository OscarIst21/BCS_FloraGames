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
    <!--<link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/stylesMedia.css"> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../img/logoFG.ico">
    <style>
        /* Agrega aquí tus estilos para la carta y el tablero */
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2>Lotería - Nivel <?php echo ucfirst($modo); ?></h2>
        <div id="carta" class="mb-4">
            <div class="row">
                <?php
                $cols = $modo === 'dificil' ? 4 : 3;
                foreach ($carta as $i => $planta):
                    if ($i % $cols === 0 && $i > 0) echo '</div><div class="row">';
                ?>
                    <div class="col border p-2 text-center carta-celda" data-index="<?php echo $i; ?>" style="position:relative; cursor:pointer;">
                        <img src="../img/plantas/<?php echo htmlspecialchars($planta['foto']); ?>" alt="<?php echo htmlspecialchars($planta['nombre_comun']); ?>" style="width:100px;height:100px;">
                        <div><?php echo htmlspecialchars($planta['nombre_comun']); ?></div>
                        <img src="../img/hoja.png" class="hoja-overlay" style="display:none; position:absolute; top:10px; left:50%; transform:translateX(-50%); width:60px; pointer-events:none; z-index:2;">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div id="mazo-actual" class="mb-4 text-center">
            <div id="planta-actual" style="margin-top:20px;"></div>
            <div class="mt-3">
                <button id="prev-btn" class="btn btn-secondary">Antes</button>
                <button id="pause-btn" class="btn btn-warning">Pausa</button>
                <button id="next-btn" class="btn btn-secondary">Después</button>
            </div>
        </div>
        <a href="../view/gamesMenu.php" class="btn btn-secondary">Volver al menú</a>
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

        function mostrarSiguiente() {
            if (index >= mazo.length) {
                clearInterval(intervalo);
                document.getElementById('planta-actual').innerHTML = "<h4>¡Fin del mazo!</h4>";
                return;
            }
            mostrarCarta(index);
            // Registrar la carta que acaba de salir
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
                }, 2500);
            }
        };

        document.getElementById('pause-btn').onclick = function() {
            pausado = !pausado;
            this.textContent = pausado ? 'Reanudar' : 'Pausa';
        };
        document.getElementById('next-btn').onclick = function() {
            if (index < mazo.length) mostrarSiguiente();
        };
        document.getElementById('prev-btn').onclick = function() {
            mostrarAnterior();
        };

        // Selección de cartas y superposición de hoja SOLO si ya salió
        document.querySelectorAll('.carta-celda').forEach(function(celda) {
            celda.addEventListener('click', function() {
                const nombreCarta = this.querySelector('div').innerText.trim();
                const hoja = this.querySelector('.hoja-overlay');
                // Solo permitir si la carta ya salió
                if (cartasSalidas.includes(nombreCarta)) {
                    hoja.style.display = hoja.style.display === 'none' ? 'block' : 'none';
                } else {
                    // Opcional: feedback visual
                    this.style.animation = 'shake 0.3s';
                    setTimeout(() => { this.style.animation = ''; }, 300);
                }
            });
        });
    </script>
</body>
</html>