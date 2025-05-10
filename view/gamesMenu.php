<?php
require_once __DIR__.'/../config/init.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juegos - Flora Games</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/stylesMedia.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../img/logoFG.ico">

</head>
<body>
    <?php include '../components/header.php'; ?>

    <div class="contenedor">
        <div class="games-grid">
            <div class="card">
                <div class="card-head"><img src="../img/logosJuegos/memorama.png" alt="Memorama"></div>
                <div class="card-body">
                    <h3 class="card-title">Memorama</h3>
                    <form action="../games/memorama.php" method="get">
                        <p class="card-content">
                            <button type="submit" class="btnActions">Jugar</button>
                        </p>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-head"><img src="../img/logosJuegos/sopaLetras.png" alt="Sopa de letras"></div>
                <div class="card-body">
                    <h3 class="card-title">Sopa de letras</h3>
                    <p class="card-content">
                        <button type="" class="btnActions">Jugar</button>
                    </p>
                </div>
            </div>
            <div class="card">
                <div class="card-head"><img src="../img/logosJuegos/puzzle.png" alt="Puzzle deslizante"></div>
                <div class="card-body">
                    <h3 class="card-title">Puzzle deslizante</h3>
                    <p class="card-content">
                        <button type="" class="btnActions">Jugar</button>
                    </p>
                </div>
            </div>
            <div class="card">
                <div class="card-head"><img src="../img/logosJuegos/memorama.png" alt="Ahorcado"></div>
                <div class="card-body">
                    <h3 class="card-title">Ahorcado</h3>
                    <form action="../games/ahorcado.php" method="get">
                        <p class="card-content">
                            <button type="submit" class="btnActions">Jugar</button>
                        </p>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-head"><img src="../img/logosJuegos/sopaLetras.png" alt="Loteria"></div>
                <div class="card-body">
                    <h3 class="card-title">Loteria</h3>
                    <form action="../games/loteria.php" method="get">
                        <p class="card-content">
                            <button type="submit" class="btnActions">Jugar</button>
                        </p>
                    </form>
                </div>
            </div>
            <div class="card">
            <div class="card-head"><img src="../img/logosJuegos/puzzle.png" alt="Mecanografia"></div>
            <div class="card-body">
                <h3 class="card-title">Mecanografía</h3>
                <p class="card-content">
                    <button type="" class="btnActions">Jugar</button>
                </p>
            </div>
            </div> 
        </div>
    </div>

    <?php include '../components/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>

</html>