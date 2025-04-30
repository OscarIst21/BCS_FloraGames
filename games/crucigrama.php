<?php
session_start();

// Inicializar el juego si no existe
if (!isset($_SESSION['crucigrama_board'])) {
    $words = [
        'BALLENA' => 'Mamífero marino de gran tamaño que migra a las aguas de BCS',
        'DELFIN' => 'Cetáceo inteligente conocido por su comportamiento juguetón',
        'TORTUGA' => 'Reptil marino que anida en las playas de BCS',
        'PELICANO' => 'Ave marina con un gran pico que usa como red de pesca',
        'MANTARRAYA' => 'Pez cartilaginoso que parece volar bajo el agua',
        'TIBURON' => 'Depredador marino con aletas dorsales prominentes',
        'LOBO' => 'Mamífero marino que forma colonias en las islas',
        'GAVIOTA' => 'Ave costera que se alimenta de peces pequeños'
    ];

    $_SESSION['crucigrama_words'] = $words;
    $_SESSION['crucigrama_solved'] = array_fill(0, count($words), false);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crucigrama - BCS Fauna Games</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f0f2f5;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        h1 {
            color: #1a73e8;
            text-align: center;
        }

        .game-board {
            display: flex;
            gap: 20px;
            margin: 20px 0;
        }

        .definitions {
            flex: 1;
        }

        .definition-item {
            background: white;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .definition-item.solved {
            background-color: #34a853;
            color: white;
        }

        .answer-section {
            flex: 1;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .answer-input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 2px solid #1a73e8;
            border-radius: 5px;
            font-size: 16px;
        }

        .submit-answer {
            width: 100%;
            padding: 10px;
            background-color: #1a73e8;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .submit-answer:hover {
            background-color: #1557b0;
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
    </style>
</head>

<body>
    <div class="container">
        <a href="../index.php" class="back-button">← Volver al menú</a>
        <h1>Crucigrama de Fauna BCS</h1>

        <div class="game-board">
            <div class="definitions">
                <?php
                $index = 1;
                foreach ($_SESSION['crucigrama_words'] as $word => $definition) {
                    $solved = $_SESSION['crucigrama_solved'][$index - 1] ? 'solved' : '';
                    echo "<div class='definition-item $solved'>";
                    echo "<strong>$index.</strong> $definition";
                    echo "</div>";
                    $index++;
                }
                ?>
            </div>

            <div class="answer-section">
                <input type="text" id="answerInput" class="answer-input" placeholder="Escribe tu respuesta...">
                <input type="number" id="definitionNumber" class="answer-input" placeholder="Número de definición" min="1" max="<?php echo count($_SESSION['crucigrama_words']); ?>">
                <button class="submit-answer" onclick="checkAnswer()">Comprobar Respuesta</button>
            </div>
        </div>
    </div>

    <script>
        function checkAnswer() {
            const answer = document.getElementById('answerInput').value.toUpperCase();
            const defNumber = document.getElementById('definitionNumber').value;

            if (!answer || !defNumber) {
                alert('Por favor, ingresa una respuesta y el número de definición.');
                return;
            }

            fetch('crucigrama_actions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `answer=${answer}&definition=${defNumber}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.correct) {
                        document.querySelectorAll('.definition-item')[defNumber - 1].classList.add('solved');
                        document.getElementById('answerInput').value = '';
                        document.getElementById('definitionNumber').value = '';
                        alert('¡Correcto!');

                        if (data.gameComplete) {
                            alert('¡Felicidades! Has completado el crucigrama.');
                            if (confirm('¿Quieres jugar de nuevo?')) {
                                window.location.reload();
                            }
                        }
                    } else {
                        alert('Intenta de nuevo.');
                    }
                });
        }
    </script>
</body>

</html>