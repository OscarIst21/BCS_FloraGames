<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BCS Fauna Games</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f0f2f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            color: #1a73e8;
            text-align: center;
            margin-bottom: 30px;
        }
        .games-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .game-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .game-card:hover {
            transform: translateY(-5px);
        }
        .game-card h2 {
            color: #202124;
            margin-bottom: 15px;
        }
        .game-card p {
            color: #5f6368;
            margin-bottom: 20px;
        }
        .play-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #1a73e8;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .play-button:hover {
            background-color: #1557b0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>BCS Fauna Games</h1>
        <div class="games-grid">
            <div class="game-card">
                <h2>Memorama</h2>
                <p>Pon a prueba tu memoria encontrando pares de cartas iguales.</p>
                <a href="games/memorama.php" class="play-button">Jugar</a>
            </div>
            <div class="game-card">
                <h2>Lotería</h2>
                <p>Juega la tradicional lotería mexicana en versión digital.</p>
                <a href="games/loteria.php" class="play-button">Jugar</a>
            </div>
            <div class="game-card">
                <h2>Crucigrama</h2>
                <p>Resuelve crucigramas con temática de fauna de BCS.</p>
                <a href="games/crucigrama.php" class="play-button">Jugar</a>
            </div>
            <div class="game-card">
                <h2>Ahorcado</h2>
                <p>Adivina palabras relacionadas con la fauna de BCS.</p>
                <a href="games/ahorcado.php" class="play-button">Jugar</a>
            </div>
        </div>
    </div>
</body>
</html>