<?php
require_once __DIR__.'/../config/init.php';
require_once __DIR__.'/../connection/database.php';

// Obtener todas las plantas de la base de datos
$db = new Database();
$conn = $db->getConnection();
$stmt = $conn->prepare("SELECT * FROM ficha_planta");
$stmt->execute();
$plantas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ORDENAR ALFABÉTICAMENTE POR NOMBRE COMÚN
usort($plantas, function($a, $b) {
    return strcmp(mb_strtolower($a['nombre_comun']), mb_strtolower($b['nombre_comun']));
});

// Lógica de paginación
$plantasPorPagina = 12;
$paginaActual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$totalPlantas = count($plantas);
$inicio = ($paginaActual - 1) * $plantasPorPagina;
$plantasPagina = array_slice($plantas, $inicio, $plantasPorPagina);
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
    <style>
        .badge-endemica {
            background-color: #e57373;
            color: white;
        }
        .badge-nativa {
            background-color: #64b5f6;
            color: white;
        }
        .badge-otra {
            background-color: #bdbdbd;
            color: white;
        }
        .plant-card .plant-image img,
        .plant-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 8px 8px 0 0;
            display: block;
        }
    </style>
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
            <?php foreach ($plantasPagina as $planta): ?>
                <div class="plant-card"
                    data-bs-toggle="modal"
                    data-bs-target="#plantModal"
                    data-nombre="<?php echo htmlspecialchars($planta['nombre_comun']); ?>"
                    data-cientifico="<?php echo htmlspecialchars($planta['nombre_cientifico']); ?>"
                    data-foto="<?php echo htmlspecialchars($planta['foto']); ?>"
                    data-dibujo="<?php echo htmlspecialchars($planta['dibujo_animado']); ?>"
                    data-caracteristicas="<?php echo htmlspecialchars($planta['caracteristicas']); ?>"
                    data-habitat="<?php echo htmlspecialchars($planta['habitat']); ?>"
                    data-distribucion="<?php echo htmlspecialchars($planta['distribucion']); ?>"
                    data-curiosidad="<?php echo htmlspecialchars($planta['curiosidad']); ?>"
                    data-audio="<?php echo htmlspecialchars($planta['audio']); ?>"
                    data-situacion="<?php echo htmlspecialchars($planta['situación']); ?>"
                >
                    <div class="plant-image">
                        <img src="../img/plantas/<?php echo htmlspecialchars($planta['foto']); ?>" alt="<?php echo htmlspecialchars($planta['nombre_comun']); ?>">
                    </div>
                    <div class="plant-content">
                        <h3 class="plant-title"><?php echo htmlspecialchars($planta['nombre_comun']); ?></h3>
                        <hr style="margin: 0 10px">
                        <p class="plant-sci"><?php echo htmlspecialchars($planta['nombre_cientifico']); ?></p>
                        <?php
                            $situacion = strtolower($planta['situación']);
                            $badgeClass = '';
                            if ($situacion === 'endémica' || $situacion === 'endemica') {
                                $badgeClass = 'badge-endemica';
                            } elseif ($situacion === 'nativa') {
                                $badgeClass = 'badge-nativa';
                            } else {
                                $badgeClass = 'badge-otra';
                            }
                        ?>
                        <p class="plant-badge <?php echo $badgeClass; ?>">
                            <?php echo htmlspecialchars($planta['situación']); ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Paginación -->
        <div class="pagination" style="text-align:center; margin:1rem 0;">
            <?php
            $totalPaginas = ceil($totalPlantas / $plantasPorPagina);
            for ($i = 1; $i <= $totalPaginas; $i++) {
                if ($i == $paginaActual) {
                    echo "<span style='font-weight:bold; color:#246741;'>$i</span> ";
                } else {
                    echo "<a href='?page=$i' style='color:#246741; text-decoration:underline;'>$i</a> ";
                }
            }
            ?>
        </div>
        
        
    </div>
    
</div>
    <?php include "../components/filter.php"; ?>
    <?php include '../components/footer.php'; ?>
      <?php include '../components/welcomeMessage.php'; ?>
      <div id="btnSubir" title="Volver arriba">
    <a href="#"><i class="fas fa-arrow-up"></i></a>
</div>
<!-- Modal Info plantas -->
<div class="modal fade" id="plantModal" tabindex="-1" aria-labelledby="plantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h1 class="modal-title fs-5" id="plantModalLabel">Alfilerillo</h1>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <!-- Imagen de la planta -->
                        <div id="carouselExampleIndicators" class="carousel slide">
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                        </div>
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                            <img src="../img/plantas/biznaga.png" class="d-block w-100 h-100" alt="...">
                            </div>
                            <div class="carousel-item">
                            <img src="../img/plantas/biznaga1.png" class="d-block w-100 h-100"  alt="...">
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                        </div> 
                        
                        <a href="" class="btn-audio"><i class="fa-solid fa-volume-high me-2"></i></i>Escuchar audio</a>
                    </div>
                    <div class="col-md-6">
                        <!-- Información de la planta -->
                        <div class="plant-info">
                            <p><i class="fa-solid fa-circle-info me-2"></i><strong>Nombre:</strong> <span data-field="nombre"></span></p>
                            <p><i class="fa-solid fa-atom  me-2"></i><strong>Nombre científico:</strong> <span data-field="cientifico"></span></p>   
                            <hr>
                            <div class="plant-section">
                                <p><i class="fa-solid fa-leaf  me-2"></i><strong>Características:</strong> <span data-field="caracteristicas"></span></p>
                            </div>
                            
                            <div class="plant-section">
                                <p><i class="fa-solid fa-location-dot  me-2"></i><strong>Hábitat:</strong> <span data-field="habitat"></span></p>
                                <p><i class="fa-solid fa-map  me-2"></i><strong>Distribución:</strong> <span data-field="distribucion"></span></p>
                            </div>
                            
                            <div class="plant-section">
                                <p><i class="fa-solid fa-seedling  me-2"></i><strong>Curiosidades:</strong> <span data-field="curiosidad"></span></p>
                            </div>

                            <div class="plant-section">
                                <p><i class="fa-solid fa-wand-magic-sparkles me-2"></i><strong>Usos:</strong> <span data-field="usos"></span></p>
                            </div>

                             <div class="plant-section">
                                <p><i class="fa-solid fa-thumbtack me-2"></i><strong>Situación actual:</strong> <span data-field="situacion"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Buscador en tiempo real
    const inputBusqueda = document.getElementById('inputBusqueda');
    const plantCards = document.querySelectorAll('.plant-card');

    inputBusqueda.addEventListener('input', function() {
        const filtro = inputBusqueda.value.toLowerCase();
        plantCards.forEach(card => {
            const nombre = card.dataset.nombre.toLowerCase();
            const cientifico = card.dataset.cientifico.toLowerCase();
            if (nombre.includes(filtro) || cientifico.includes(filtro)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });

    // Modal dinámico (ya existente)
    plantCards.forEach(card => {
        card.addEventListener('click', function() {
            document.getElementById('plantModalLabel').textContent = card.dataset.nombre;
            document.querySelector('#plantModal .plant-info [data-field="nombre"]').textContent = card.dataset.nombre;
            document.querySelector('#plantModal .plant-info [data-field="cientifico"]').innerHTML = `<em>${card.dataset.cientifico}</em>`;
            document.querySelector('#plantModal .plant-info [data-field="caracteristicas"]').textContent = card.dataset.caracteristicas;
            document.querySelector('#plantModal .plant-info [data-field="habitat"]').textContent = card.dataset.habitat;
            document.querySelector('#plantModal .plant-info [data-field="distribucion"]').textContent = card.dataset.distribucion;
            document.querySelector('#plantModal .plant-info [data-field="curiosidad"]').textContent = card.dataset.curiosidad;
            document.querySelector('#plantModal .plant-info [data-field="situacion"]').textContent = card.dataset.situacion;
            // Imagen principal
            document.querySelector('#plantModal .carousel-item.active img').src = "../img/plantas/" + card.dataset.foto;
            // Imagen secundaria (dibujo animado)
            document.querySelectorAll('#plantModal .carousel-item img')[1].src = "../img/plantas/" + card.dataset.dibujo;
            // Audio
            document.querySelector('#plantModal .btn-audio').href = card.dataset.audio ? ("../audio/" + card.dataset.audio) : "#";
        });
    });
});
</script>
    <script>
        // Pasar todas las plantas a JavaScript
        const todasLasPlantas = <?php echo json_encode($plantas); ?>;
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>

</html>
