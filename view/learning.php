<?php
require_once __DIR__.'/../config/init.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aprendizaje - Flora Games</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/styleLearning.css">
    <link rel="stylesheet" href="../css/styleInfoPlants.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="img/logoFG.ico">

</head>
<body>
    <?php include '../components/header.php'; ?>
    <div class="header-secundary">
        <h3 style="margin: 0;">Aprendizaje</h3>

        <div class="search">
            <div class="container search">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Buscar..." aria-label="Buscar" aria-describedby="button-addon2" id="inputBusqueda">
                    <button class="btn btn-success" type="button" id="buscar">
                        <i class="fas fa-search"></i> <!-- Icono de lupa -->
                    </button>
                </div>
                <button title="Filtrar por" class="btn btn-success filter-btn" type="button" data-bs-toggle="modal" data-bs-target="#filterModal">
                    <i class="fas fa-filter me-2"></i>
                </button>
                
            </div>
        </div>  
    </div>

    <div class="contenedor" >

        <div class="plants-grid">
            <div class="plant-card" data-bs-toggle="modal" data-bs-target="#plantModal">
                <div class="plant-image">
                    <img src="../img/plantas/biznaga.png" alt="Biznaga">
                </div>
                <div class="plant-content">
                    <h3 class="plant-title">Biznaga</h3>
                    <hr style="margin: 0 10px">
                    <p class="plant-sci">Ferocactus townsendianus (Engelm.)Britton & Rose var. townsendianus</p>
                    <p class="plant-badge">Endémica</p>
                </div>
            </div>

        </div>  
        
        
    </div>
    
</div>
    <?php include "../components/filter.php"; ?>
    <?php include '../components/footer.php'; ?>
     <?php include '../components/info-plants.php'; ?>
      <?php include '../components/welcomeMessage.php'; ?>
      <div id="btnSubir" title="Volver arriba">
    <a href="#"><i class="fas fa-arrow-up"></i></a>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>

</html>