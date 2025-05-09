<?php
session_start();

// Inicializar el juego si no existe
if (!isset($_SESSION['loteria_board'])) {
    $animals = [
        'ballena_gris' => 'Ballena Gris',
        'lobo_marino' => 'Lobo Marino',
        'tortuga_marina' => 'Tortuga Marina',
        'pelicano' => 'Pelícano',
        'delfin' => 'Delfín',
        'mantarraya' => 'Mantarraya',
        'tiburon_ballena' => 'Tiburón Ballena',
        'gaviota' => 'Gaviota',
        'caracol' => 'Caracol',
        'cangrejo' => 'Cangrejo',
        'estrella_mar' => 'Estrella de Mar',
        'pulpo' => 'Pulpo',
        'medusa' => 'Medusa',
        'caballito_mar' => 'Caballito de Mar',
        'pez_angel' => 'Pez Ángel',
        'morena' => 'Morena'
    ];

    // Crear tablero aleatorio
    $board = array_rand($animals, 16);
    shuffle($board);
    $_SESSION['loteria_board'] = $board;
    $_SESSION['loteria_marked'] = array_fill(0, 16, false);
    $_SESSION['loteria_current_card'] = '';
    $_SESSION['loteria_used_cards'] = [];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lotería - BCS Flora Games</title>
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

        .game-controls {
            text-align: center;
            margin: 20px 0;
        }

        .board {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin: 20px 0;
        }

        .card {
            aspect-ratio: 1;
            background: white;
            border: 2px solid #1a73e8;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            text-align: center;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .card.marked {
            background-color: #34a853;
            color: white;
            border-color: #34a853;
        }

        .current-card {
            background: #1a73e8;
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #1a73e8;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            margin: 0 10px;
        }

        .button:hover {
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
        <h1>Lotería de Flora BCS</h1>

        <div class="game-controls">
            <button id="drawCard" class="button">Sacar Carta</button>
            <button id="newGame" class="button">Nuevo Juego</button>
        </div>

        <div id="currentCard" class="current-card">
            <?php
            if (!empty($_SESSION['loteria_current_card'])) {
                echo ucwords(str_replace('_', ' ', $_SESSION['loteria_current_card']));
            } else {
                echo "Presiona 'Sacar Carta' para comenzar";
            }
            ?>
        </div>

        <div class="board">
            <?php
            for ($i = 0; $i < 16; $i++) {
                $card = $_SESSION['loteria_board'][$i];
                $marked = $_SESSION['loteria_marked'][$i] ? 'marked' : '';
                echo "<div class='card $marked' data-card='$card'>";
                echo ucwords(str_replace('_', ' ', $card));
                echo "</div>";
            }
            ?>
        </div>
    </div>

    <script>
        document.getElementById('drawCard').addEventListener('click', () => {
            fetch('loteria_actions.php?action=draw')
                .then(response => response.json())
                .then(data => {
                    if (data.card) {
                        document.getElementById('currentCard').textContent =
                            data.card.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                    }
                    if (data.gameOver) {
                        alert('¡LOTERÍA! Has ganado el juego.');
                        if (confirm('¿Quieres jugar de nuevo?')) {
                            window.location.reload();
                        }
                    }
                });
        });

        document.getElementById('newGame').addEventListener('click', () => {
            if (confirm('¿Estás seguro de que quieres comenzar un nuevo juego?')) {
                fetch('loteria_actions.php?action=new')
                    .then(() => window.location.reload());
            }
        });

        document.querySelectorAll('.card').forEach(card => {
            card.addEventListener('click', () => {
                const cardValue = card.dataset.card;
                fetch(`loteria_actions.php?action=mark&card=${cardValue}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.marked) {
                            card.classList.add('marked');
                        }
                    });
            });
        });
    </script>
</body>

</html>