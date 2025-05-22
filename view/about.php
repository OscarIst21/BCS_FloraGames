<?php
require_once __DIR__.'/../config/init.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acerca de - Flora Games</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/styleAbout.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../img/logoFG.ico">

</head>
<body>
    <?php include '../components/header.php'; ?>

    <div class="page-container">
        <div class="contenedor">

         <section class="hero-section">
            <div class="hero-content">
                <h1 class="hero-title">Acerca de nosotros</h1>
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#" data-bs-toggle="tab" data-bs-target="#creditos"><i class="fa-solid fa-image me-2"></i>Créditos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="tab" data-bs-target="#licencias"><i class="fa-solid fa-id-badge me-2"></i>Licencias</a>
                    </li>
                </ul>

                <div class="tab-content p-3 border border-top-0 rounded-bottom">
                <div class="tab-pane fade show active" id="creditos">
                    <div class="cd-info">
                        <h5 class="cd-title"><i class="fa-solid fa-camera me-2"></i>Créditos de imágenes</h5>
                        <p class="fw-bold  mb-0">Fotografías de las plantas: </p>
                        <div class="d-flex justify-content-between ms-2">
                            <p>Esli Mayer </p>
                            <p class="fst-italic">Uso autorizado para Flora Games</p>
                        </div>
                        <p class="fw-bold mb-0">Iconos de insignias y niveles: </p>
                         <div class="d-flex justify-content-between ms-2">
                            <p>Colaboradores de Flora Games </p>
                            <p class="fst-italic">Uso exclusivo para Flora Games</p>
                        </div>
                    </div>
                    <hr>
                    <div class="cd-info">
                        <h5 class="cd-title"><i class="fa-solid fa-music me-2"></i>Créditos de audio</h5>
                        <p class="fw-bold mb-0">Música de fondo: </p>
                        <div class="d-flex justify-content-between ms-2">
                            <p> </p>
                            <p class="fst-italic">Uso autorizado para Flora Games</p>
                        </div>
                        <p class="fw-bold mb-0">Audios informativos de plantas: </p>
                         <div class="d-flex justify-content-between ms-2">
                            <p>Colaboradores de Flora Games </p>
                            <p class="fst-italic">Uso exclusivo para Flora Games</p>
                        </div>
                    </div>    
                    <hr>
                    <div class="cd-info">
                        <h5 class="cd-title"><i class="fa-solid fa-book-journal-whills me-2"></i>Créditos de contenido</h5>
                        <p class="fw-bold mb-0">Información botánica: </p>
                        <div class="d-flex justify-content-between ms-2">
                            <p>Flora Iconográfica de Baja California Sur</p>
                            <p class="fst-italic">CiB, 2015</p>
                        </div>
                        
                    </div>    
                </div>
                
                <div class="tab-pane fade" id="licencias">
                    <div class="cd-info">
                        <h5 class="cd-title">Licencias y Derechos de Autor</h5>
                        <p class="fw-bold mb-0">Flora Games © 2025 </p>
                        <p>Todos los derechos reservados. El contenido original de Flora Games, incluyendo textos, diseño, código y estructura de la aplicación están protegidos por leyes de derecho de autor</p>
                        <p class="fw-bold mb-0">Uso educativo </p>
                         <p>Flora Games está diseñado con fines educativos. Se permite el uso de la plataforma en entornos educativos sin fines comerciales, siempre que se mantenga la atribución adecuada.</p>
                         <p class="fw-bold mb-0">Términos de uso </p>
                         <p>Al utilizar Flora Games, aceptas respetar los derechos de autor y las licencias de todos los materiales presentados. No se permite la redistribución, modificación o uso comercial sin autorización expresa.</p>
                    </div>
                </div>
                </div>
            </div>
        </section>
            
        </div>
    </div>
    <?php include '../components/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>

</html>