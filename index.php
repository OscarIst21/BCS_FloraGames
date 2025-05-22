<?php
require_once __DIR__ . '/config/init.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Flora Games</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/styleIndex.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="img/logoFG.ico">

    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Ancizar+Sans:ital,wght@0,100..1000;1,100..1000&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'components/header.php'; ?>

    <div class="contenedor">
        <!-- Sección Hero -->
        <section class="hero-section">
            <i class="fas fa-seedling plant-decoration plant-1"></i>
            <i class="fas fa-leaf plant-decoration plant-2"></i>
            <div class="hero-content">
                <h1 class="hero-title">Descubre la Flora de Baja California Sur</h1>
                <p class="hero-subtitle">Explora más de 30 plantas nativas y endémicas de la región a través de fichas informativas interactivas y juegos educativos diseñados para aprender mientras te diviertes.</p>
                <div>
                    <a href="view/learning.php" class="btn-hero"><i class="fa-solid fa-leaf me-2"></i>Comenzar a aprender</a>
                    <a href="view/gamesMenu.php" class="btn-hero btn-hero-secondary"><i class="fa-solid fa-gamepad me-2"></i>Explorar juegos</a>
                </div>
            </div>
        </section>
        
        <!-- Sección de características -->
        <section class="features-section">
            <h2 class="section-title">¿Qué ofrecemos?</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <h3 class="feature-title">Fichas Informativas</h3>
                        <p>Información detallada sobre cada especie, incluyendo características botánicas, hábitat natural, usos tradicionales y estado de conservación, acompañada de galerías fotográficas.</p>
                    </div>
                </div>
                 <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-gamepad"></i>
                        </div>
                        <h3 class="feature-title">Juegos Educativos</h3>
                        <p>Diversos juegos interactivos como quizzes, memoramas y desafíos de identificación que refuerzan tu conocimiento sobre la flora local de manera entretenida.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 class="feature-title">Seguimiento de Progreso</h3>
                        <p>Sistema de logros, insignias y estadísticas detalladas que te permiten monitorear tu aprendizaje y comparar tus resultados con otros usuarios.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección de desafío -->
        <section class="challenge-section">
            <i class="fas fa-seedling plant-decoration plant-1"></i>
            <i class="fas fa-leaf plant-decoration plant-2"></i>
            <div class="challenge-container">
                <h2 class="challenge-title">¿Listo para poner a prueba tus conocimientos?</h2>
                <p class="challenge-description">
                    Gana insignias exclusivas y asciende en el ranking global. Cada planta que aprendes te acerca a convertirte en un experto en la flora de BCS.
                </p>
                
                <div class="challenge-divider"></div>
                
                <div class="challenge-buttons">
                    <a href="view/myProfile.php" class="btn-challenge">
                        <i class="fas fa-user-circle me-2"></i>Ver Mi Perfil
                    </a>
                    <a href="view/mySuccesses.php" class="btn-challenge btn-outline-challenge">
                        <i class="fas fa-trophy me-2"></i>Ranking Global
                    </a>
                </div>
            </div>
        </section>
    </div>

    <?php include 'components/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>
</html>