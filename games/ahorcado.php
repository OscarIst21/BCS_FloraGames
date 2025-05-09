<?php
session_start();

// Inicializar el juego si no existe
if (!isset($_SESSION['ahorcado_word'])) {
    $animals = [
        'BALLENA' => 'Mamífero marino de gran tamaño',
        'DELFIN' => 'Cetáceo inteligente y juguetón',
        'TORTUGA' => 'Reptil que anida en las playas',
        'PELICANO' => 'Ave con gran pico',
        'MANTARRAYA' => 'Pez que parece volar',
        'TIBURON' => 'Depredador con aletas dorsales',
        'LOBO' => 'Mamífero que forma colonias',
        'GAVIOTA' => 'Ave costera pescadora'
    ];

    $randomIndex = array_rand($animals);
    $_SESSION['ahorcado_word'] = $randomIndex;
    $_SESSION['ahorcado_hint'] = $animals[$randomIndex];
    $_SESSION['ahorcado_guessed'] = [];
    $_SESSION['ahorcado_mistakes'] = 0;
    $_SESSION['ahorcado_max_mistakes'] = 6;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ahorcado - BCS Flora Games</title>
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

        .game-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
        }

        .word-display {
            text-align: center;
            font-size: 2em;
            margin: 20px 0;
            letter-spacing: 5px;
        }

        .hint {
            text-align: center;
            color: #5f6368;
            margin: 10px 0;
        }

        .letters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(40px, 1fr));
            gap: 5px;
            margin: 20px 0;
        }

        .letter-button {
            padding: 10px;
            background: #1a73e8;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .letter-button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .hangman-display {
            text-align: center;
            font-size: 1.2em;
            margin: 20px 0;
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
        <h1>Ahorcado de Flora BCS</h1>

        <div class="game-container">
            <div class="hangman-display">
                Intentos restantes: <?php echo $_SESSION['ahorcado_max_mistakes'] - $_SESSION['ahorcado_mistakes']; ?>
            </div>

            <div class="hint">
                Pista: <?php echo $_SESSION['ahorcado_hint']; ?>
            </div>

            <div class="word-display">
                <?php
                $word = $_SESSION['ahorcado_word'];
                for ($i = 0; $i < strlen($word); $i++) {
                    $letter = $word[$i];
                    if (in_array($letter, $_SESSION['ahorcado_guessed'])) {
                        echo $letter;
                    } else {
                        echo '_';
                    }
                    echo ' ';
                }
                ?>
            </div>

            <div class="letters-grid">
                <?php
                for ($i = 65; $i <= 90; $i++) {
                    $letter = chr($i);
                    $disabled = in_array($letter, $_SESSION['ahorcado_guessed']) ? 'disabled' : '';
                    echo "<button class='letter-button' $disabled onclick='guessLetter(\"$letter\")'>" . $letter . "</button>";
                }
                ?>
            </div>
        </div>
    </div>

    <script>
        function guessLetter(letter) {
            fetch('ahorcado_actions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `letter=${letter}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.gameOver) {
                        alert(data.message);
                        if (confirm('¿Quieres jugar de nuevo?')) {
                            window.location.reload();
                        }
                    } else {
                        window.location.reload();
                    }
                });
        }
    </script>
</body>

</html>