<?php
session_start();

// Inicializar el juego si no existe
if (!isset($_SESSION['memorama_cards'])) {
    $animals = [
        'ballena_gris' => 'Ballena Gris',
        'lobo_marino' => 'Lobo Marino',
        'tortuga_marina' => 'Tortuga Marina',
        'pelicano' => 'Pelícano',
        'delfin' => 'Delfín',
        'mantarraya' => 'Mantarraya',
        'tiburon_ballena' => 'Tiburón Ballena',
        'gaviota' => 'Gaviota'
    ];

    // Duplicar las cartas para crear pares
    $cards = array_merge(array_keys($animals), array_keys($animals));
    shuffle($cards);
    $_SESSION['memorama_cards'] = $cards;
    $_SESSION['memorama_flipped'] = array_fill(0, count($cards), false);
    $_SESSION['memorama_matched'] = array_fill(0, count($cards), false);
    $_SESSION['memorama_moves'] = 0;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memorama - BCS Flora Games</title>
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

        .game-info {
            text-align: center;
            margin-bottom: 20px;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .card {
            aspect-ratio: 1;
            background-color: #1a73e8;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: white;
            transition: transform 0.3s ease;
            transform-style: preserve-3d;
        }

        .card.flipped {
            transform: rotateY(180deg);
            background-color: white;
            color: #202124;
            border: 2px solid #1a73e8;
        }

        .card.matched {
            background-color: #34a853;
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
    </style>
</head>

<body>
    <div class="container">
        <a href="../index.php" class="back-button">← Volver al menú</a>
        <h1>Memorama de Flora BCS</h1>
        <div class="game-info">
            <p>Movimientos: <span id="moves"><?php echo $_SESSION['memorama_moves']; ?></span></p>
        </div>
        <div class="cards-grid">
            <?php
            for ($i = 0; $i < count($_SESSION['memorama_cards']); $i++) {
                $card = $_SESSION['memorama_cards'][$i];
                $flipped = $_SESSION['memorama_flipped'][$i] ? 'flipped' : '';
                $matched = $_SESSION['memorama_matched'][$i] ? 'matched' : '';
                echo "<div class='card $flipped $matched' data-index='$i' data-card='$card'>";
                echo $flipped || $matched ? ucwords(str_replace('_', ' ', $card)) : '';
                echo "</div>";
            }
            ?>
        </div>
    </div>
    <script>
        let flippedCards = [];
        let canFlip = true;

        document.querySelectorAll('.card').forEach(card => {
            card.addEventListener('click', () => {
                if (!canFlip) return;
                if (card.classList.contains('flipped') || card.classList.contains('matched')) return;
                if (flippedCards.length >= 2) return;

                card.classList.add('flipped');
                flippedCards.push(card);

                if (flippedCards.length === 2) {
                    canFlip = false;
                    const [card1, card2] = flippedCards;
                    const match = card1.dataset.card === card2.dataset.card;

                    setTimeout(() => {
                        if (match) {
                            card1.classList.add('matched');
                            card2.classList.add('matched');
                        } else {
                            card1.classList.remove('flipped');
                            card2.classList.remove('flipped');
                        }
                        flippedCards = [];
                        canFlip = true;

                        // Actualizar movimientos
                        const movesElement = document.getElementById('moves');
                        movesElement.textContent = parseInt(movesElement.textContent) + 1;

                        // Verificar victoria
                        const allMatched = document.querySelectorAll('.card.matched').length === document.querySelectorAll('.card').length;
                        if (allMatched) {
                            alert('¡Felicidades! Has completado el juego.');
                            if (confirm('¿Quieres jugar de nuevo?')) {
                                window.location.reload();
                            }
                        }
                    }, 1000);
                }
            });
        });
    </script>
</body>

</html>