<?php
require_once __DIR__.'/../config/init.php';
require_once __DIR__.'/../connection/database.php';

// Elimina la conexión y consulta a la base de datos
//$plantas = json_decode(file_get_contents(__DIR__.'/../plantas.json'), true);
// Obtener todas las plantas de la base de datos
//$db = new Database();
//$conn = $db->getConnection();
//$stmt = $conn->prepare("SELECT * FROM ficha_planta");
//$stmt->execute();
//$plantas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Cargar solo desde el JSON generado
$plantas = json_decode(file_get_contents(__DIR__.'/../config/plantas.json'), true);

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
    <link rel="stylesheet" href="../css/stylesMedia.css">
    <link rel="stylesheet" href="../css/styleLearning.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../img/logoFG.ico">
    <style>
        .badge-endemica {
            background-color: #1d5825;
            color: white;
        }
        .badge-nativa {
            background-color: #6aab78;
            color: white;
        }
        .badge-otra {
            background-color: #bdbdbd;
            color: white;
        }
        .lupa{
                border-radius: 0 10px 10px 0;
                border: 1px solid rgb(222, 226, 230);
                padding: 10px;
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
                    <div class="lupa"  id="buscar">
                        <i class="fas fa-search"></i> <!-- Icono de lupa -->
                    </div>
                </div>
                <button title="Filtrar por" class="btn btn-success filter-btn" type="button" data-bs-toggle="modal" data-bs-target="#filterModal">
                    <i class="fas fa-filter me-2"></i>
                </button>
                
            </div>
        </div>  
    </div>

    <div class="page-container">
        <div class="contenedor">
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
                        data-usos="<?php echo htmlspecialchars($planta['usos']); ?>"
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
                        echo "<span class='page-items' style='font-weight:bold; color:#246741;'>$i</span> ";
                    } else {
                        echo "<a href='?page=$i' class='page-itemsV2' style='color:#246741; text-decoration:underline;'>$i</a> ";
                    }
                }
                ?>
            </div>
        </div>
    </div>
    

    <?php include "../components/filter.php"; ?>
      <?php include '../components/welcomeMessage.php'; ?>
      <div id="btnSubir" title="Volver arriba">
            <a href="#"><i class="fas fa-arrow-up"></i></a>
    </div>
<!-- Modal Info plantas -->
<div class="modal fade" id="plantModal" tabindex="-1" aria-labelledby="plantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h1 class="modal-title fs-5" id="plantModalLabel"></h1>
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
                            <img src="" class="d-block w-100 h-100" alt="...">
                            </div>
                            <div class="carousel-item">
                            <img src="" class="d-block w-100 h-100"  alt="...">
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

<!-- Modal de Filtros -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <!-- Encabezado del Modal -->
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="filterModalLabel">
                    <i class="bi bi-funnel-fill me-2"></i>Filtrar especies
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            
            <!-- Cuerpo del Modal -->
            <div class="modal-body">
                <div class="filter-options">
                    <div class="filter-tabs">
                        <button type="button" class="filter-tab active" data-filter="all">
                            <i class="bi bi-collection me-1"></i> Todos
                        </button>
                        <button type="button" class="filter-tab" data-filter="Nativa">
                            <i class="bi bi-tree me-1"></i> Nativa
                        </button>
                        <button type="button" class="filter-tab" data-filter="endemica">
                            <i class="bi bi-globe-americas me-1"></i> Endémica
                        </button>
                        <hr>
                        <button type="button" class="filter-tab" data-filter="medicinal">
                            <i class="bi bi-capsule-pill me-1"></i> Uso medicinal
                        </button>
                        <button type="button" class="filter-tab" data-filter="ornamental">
                            <i class="bi bi-flower1 me-1"></i> Uso ornamental
                        </button>
                        <button type="button" class="filter-tab" data-filter="forraje">
                            <i class="bi bi-droplet-half me-1"></i> Uso forraje
                        </button>
                        <button type="button" class="filter-tab" data-filter="alimenticio">
                            <i class="bi bi-egg-fried me-1"></i> Uso alimenticio
                        </button>
                        <button type="button" class="filter-tab" data-filter="combustible">
                            <i class="bi bi-fire me-1"></i> Uso combustible
                        </button>
                        <button type="button" class="filter-tab" data-filter="maderable">
                            <i class="bi bi-box me-1"></i> Uso maderable
                        </button>
                        <button type="button" class="filter-tab" data-filter="tinte">
                            <i class="bi bi-palette2 me-1"></i> Tinte natural
                        </button>
                        <button type="button" class="filter-tab" data-filter="artesanal">
                            <i class="bi bi-scissors me-1"></i> Uso artesanal
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Pie del Modal -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-success" id="applyFilter">Aplicar Filtros
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Datos de las plantas desde PHP
    const plantas = <?php echo json_encode($plantas); ?>;
    let plantasFiltradas = [...plantas];
    const plantasPorPagina = 12;
    let paginaActual = 1;

    // Elementos del DOM
    const grid = document.querySelector('.plants-grid');
    const paginacion = document.querySelector('.pagination');
    const inputBusqueda = document.getElementById('inputBusqueda');
    const plantModal = new bootstrap.Modal(document.getElementById('plantModal'));

    // Función para determinar la clase del badge
    function getBadgeClass(situacion) {
        situacion = situacion.toLowerCase();
        if (situacion.includes('endémica') || situacion.includes('endemica')) return 'badge-endemica';
        if (situacion === 'nativa') return 'badge-nativa';
        return 'badge-otra';
    }

    // Función principal de renderizado
    function renderPlantas() {
        grid.innerHTML = '';
        const inicio = (paginaActual - 1) * plantasPorPagina;
        const plantasPagina = plantasFiltradas.slice(inicio, inicio + plantasPorPagina);

        plantasPagina.forEach(planta => {
            const card = document.createElement('div');
            card.className = 'plant-card';
            
            // Asignar todos los atributos data
            card.dataset.nombre = planta.nombre_comun;
            card.dataset.cientifico = planta.nombre_cientifico;
            card.dataset.foto = planta.foto;
            card.dataset.dibujo = planta.dibujo_animado;
            card.dataset.caracteristicas = planta.caracteristicas;
            card.dataset.habitat = planta.habitat;
            card.dataset.distribucion = planta.distribucion;
            card.dataset.curiosidad = planta.curiosidad;
            card.dataset.audio = planta.audio;
            card.dataset.situacion = planta.situación;
            card.dataset.usos = planta.usos;

            card.innerHTML = `
                <div class="plant-image">
                    <img loading="lazy" src="../img/plantas/${planta.foto}" alt="${planta.nombre_comun}">
                </div>
                <div class="plant-content">
                    <h3 class="plant-title">${planta.nombre_comun}</h3>
                    <hr style="margin: 0 10px">
                    <p class="plant-sci">${planta.nombre_cientifico}</p>
                    <p class="plant-badge ${getBadgeClass(planta.situación)}">${planta.situación}</p>
                </div>
            `;
            grid.appendChild(card);
        });

        renderPaginacion();
    }

    // Función para renderizar la paginación
    function renderPaginacion() {
        paginacion.innerHTML = '';
        const totalPaginas = Math.ceil(plantasFiltradas.length / plantasPorPagina);
        
        // Botón Anterior
        if (paginaActual > 1) {
            const prevLink = document.createElement('a');
            prevLink.href = '#';
            prevLink.className = 'page-itemsV2';
            prevLink.innerHTML = '<i class="fas fa-chevron-left"></i>';
            prevLink.addEventListener('click', (e) => {
                e.preventDefault();
                paginaActual--;
                renderPlantas();
            });
            paginacion.appendChild(prevLink);
            paginacion.appendChild(document.createTextNode(' '));
        }
        
        // Páginas numeradas
        const inicioPag = Math.max(1, paginaActual - 2);
        const finPag = Math.min(totalPaginas, paginaActual + 2);
        
        for (let i = inicioPag; i <= finPag; i++) {
            if (i === paginaActual) {
                const span = document.createElement('span');
                span.className = 'page-items';
                span.textContent = i;
                paginacion.appendChild(span);
            } else {
                const link = document.createElement('a');
                link.href = '#';
                link.className = 'page-itemsV2';
                link.textContent = i;
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    paginaActual = i;
                    renderPlantas();
                });
                paginacion.appendChild(link);
            }
            paginacion.appendChild(document.createTextNode(' '));
        }
        
        // Botón Siguiente
        if (paginaActual < totalPaginas) {
            const nextLink = document.createElement('a');
            nextLink.href = '#';
            nextLink.className = 'page-itemsV2';
            nextLink.innerHTML = '<i class="fas fa-chevron-right"></i>';
            nextLink.addEventListener('click', (e) => {
                e.preventDefault();
                paginaActual++;
                renderPlantas();
            });
            paginacion.appendChild(nextLink);
        }
    }

    // Configurar el modal con los datos de la planta
    function setupModal(card) {
        document.getElementById('plantModalLabel').textContent = card.dataset.nombre;
        document.querySelector('#plantModal [data-field="nombre"]').textContent = card.dataset.nombre;
        document.querySelector('#plantModal [data-field="cientifico"]').innerHTML = `<em>${card.dataset.cientifico}</em>`;
        document.querySelector('#plantModal [data-field="caracteristicas"]').textContent = card.dataset.caracteristicas;
        document.querySelector('#plantModal [data-field="habitat"]').textContent = card.dataset.habitat;
        document.querySelector('#plantModal [data-field="distribucion"]').textContent = card.dataset.distribucion;
        document.querySelector('#plantModal [data-field="curiosidad"]').textContent = card.dataset.curiosidad;
        document.querySelector('#plantModal [data-field="situacion"]').textContent = card.dataset.situacion;
        document.querySelector('#plantModal [data-field="usos"]').textContent = card.dataset.usos;
        
        // Imágenes
        document.querySelector('#plantModal .carousel-item.active img').src = `../img/plantas/${card.dataset.foto}`;
        document.querySelectorAll('#plantModal .carousel-item img')[1].src = `../img/plantas/${card.dataset.dibujo}`;
        
        // Audio
        const audioBtn = document.querySelector('#plantModal .btn-audio');
        audioBtn.href = card.dataset.audio ? `../audio/${card.dataset.audio}` : '#';
        if (!card.dataset.audio) {
            audioBtn.classList.add('disabled');
        } else {
            audioBtn.classList.remove('disabled');
        }
    }

    // Event Delegation para las tarjetas
    grid.addEventListener('click', (e) => {
        const card = e.target.closest('.plant-card');
        if (card) {
            setupModal(card);
            plantModal.show();
        }
    });

    // Función debounce para mejorar rendimiento en búsqueda
    function debounce(func, wait) {
        let timeout;
        return function() {
            const context = this, args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), wait);
        };
    }

    // Búsqueda con debounce
    inputBusqueda.addEventListener('input', debounce(function() {
        const filtro = this.value.toLowerCase();
        plantasFiltradas = plantas.filter(planta =>
            planta.nombre_comun.toLowerCase().includes(filtro) ||
            (planta.nombre_cientifico && planta.nombre_cientifico.toLowerCase().includes(filtro))
        );
        paginaActual = 1;
        renderPlantas();
    }, 300));

    // Inicializar
    renderPlantas();
});
</script>
   

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>

</html>