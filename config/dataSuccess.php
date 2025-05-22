<?php
require_once __DIR__.'/../config/init.php';
require_once __DIR__.'/../connection/database.php'; // Añadir esta línea

// Obtener datos del ranking
$rankingData = [];
$userRank = null;
$currentUserId = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;

// Variables para la información del nivel y las insignias
$nivelInfo = null;
$insignias = [];
$userPoints = 0;
$userGames = 0;
$nextLevelProgress = 0;
$userRanking = 0;

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Consultar el top 5 del ranking
   $stmt = $conn->prepare("
    SELECT 
        r.usuario_id, 
        r.posicion, 
        r.puntos_ganados, 
        u.nombre, 
        u.nivel_de_usuario_id,
        n.nombre AS nivel_nombre,
        (
            SELECT MAX(ui.insignia_id)
            FROM usuario_insignias ui
            WHERE ui.usuario_id = r.usuario_id
        ) AS insignia_id
    FROM ranking r
    JOIN usuarios u ON r.usuario_id = u.id
    JOIN nivel_de_usuario n ON u.nivel_de_usuario_id = n.id
    ORDER BY r.posicion ASC
    LIMIT 5
");
$stmt->execute();
$rankingData = $stmt->fetchAll(PDO::FETCH_ASSOC);


    
    // Consultar la posición del usuario actual si está autenticado
    if ($currentUserId) {
        $stmt = $conn->prepare("
            SELECT r.posicion, r.puntos_ganados
            FROM ranking r
            WHERE r.usuario_id = ?
        ");
        $stmt->execute([$currentUserId]);
        $userRank = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Obtener información del nivel del usuario
        $stmt = $conn->prepare("
            SELECT n.*, u.puntos_ganados, u.juegos_ganados 
            FROM nivel_de_usuario n
            JOIN usuarios u ON u.nivel_de_usuario_id = n.id
            WHERE u.id = ?
        ");
        $stmt->execute([$currentUserId]);
        $nivelInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($nivelInfo) {
            $userPoints = $nivelInfo['puntos_ganados'];
            $userGames = $nivelInfo['juegos_ganados'];
            $userRanking = $userRank ? $userRank['posicion'] : 0;
            
            // Calcular progreso para el siguiente nivel
            // Suponiendo que cada 20 juegos ganados se sube de nivel hasta el nivel 15
            $juegosParaNivel = 20;
            $nivelActual = $nivelInfo['id'];
            
            if ($nivelActual < 15) { // Si no es el último nivel
                $juegosRestantes = $juegosParaNivel - ($userGames % $juegosParaNivel);
                $nextLevelProgress = 100 - (($juegosRestantes / $juegosParaNivel) * 100);
            } else {
                $nextLevelProgress = 100; // Ya está en el nivel máximo
            }
            
            // Obtener insignias del usuario
            $stmt = $conn->prepare("
                SELECT i.*, ui.fecha_obtenida 
                FROM insignias i
                JOIN usuario_insignias ui ON i.id = ui.insignia_id
                WHERE ui.usuario_id = ?
                ORDER BY ui.fecha_obtenida DESC
            ");
            $stmt->execute([$currentUserId]);
            $insignias = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
} catch (PDOException $e) {
    // Manejar error de base de datos
    error_log("Error al obtener datos: " . $e->getMessage());
}
?>

<?php
            // Obtener estadísticas del usuario
            $estadisticas = [];
            $porcentajeGanados = 0;
            $tiempoPromedio = '0 min 0s';
            $plantasAprendidas = 0;
            $juegosJugados = 0;
            $fechaRegistro = '';
            
            if ($currentUserId) {
                try {
                    // Obtener juegos jugados y porcentaje de ganados
                    $stmt = $conn->prepare("
                        SELECT 
                            COUNT(*) as total_juegos,
                            SUM(CASE WHEN fue_ganado = 1 THEN 1 ELSE 0 END) as juegos_ganados
                        FROM juego_usuario
                        WHERE usuario_id = ?
                    ");
                    $stmt->execute([$currentUserId]);
                    $estadisticas = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($estadisticas && $estadisticas['total_juegos'] > 0) {
                        $juegosJugados = $estadisticas['total_juegos'];
                        $porcentajeGanados = round(($estadisticas['juegos_ganados'] / $estadisticas['total_juegos']) * 100);
                        
                        // Obtener tiempo promedio (duracion es de tipo TIME)
                        $stmt = $conn->prepare("
                            SELECT SEC_TO_TIME(AVG(TIME_TO_SEC(duracion))) as duracion_promedio
                            FROM juego_usuario
                            WHERE usuario_id = ?
                        ");
                        $stmt->execute([$currentUserId]);
                        $tiempoResult = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($tiempoResult && $tiempoResult['duracion_promedio']) {
                            // Formatear el tiempo TIME a minutos y segundos
                            $tiempo = explode(':', $tiempoResult['duracion_promedio']);
                            $horas = $tiempo[0];
                            $minutos = $tiempo[1];
                            $segundos = $tiempo[2];
                            
                            // Convertir todo a minutos y segundos
                            $totalMinutos = ($horas * 60) + $minutos;
                            $tiempoPromedio = $totalMinutos . ' min ' . $segundos . 's';
                        }
                        
                        // Obtener plantas aprendidas (de la tabla usuarios)
                        $stmt = $conn->prepare("
                            SELECT plantas_aprendidas 
                            FROM usuarios 
                            WHERE id = ?
                        ");
                        $stmt->execute([$currentUserId]);
                        $plantasResult = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($plantasResult) {
                            $plantasAprendidas = $plantasResult['plantas_aprendidas'];
                        }

                        // Obtener fecha de registro
                        $stmt = $conn->prepare("
                            SELECT fecha_registro 
                            FROM usuarios 
                            WHERE id = ?
                        ");
                        $stmt->execute([$currentUserId]);
                        $fechaRegistro = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($fechaRegistro) {
                            // Crear objeto DateTime
                            $fecha = new DateTime($fechaRegistro['fecha_registro']);

                            // Formatear la fecha
                            $fechaFormateada = $fecha->format('d-m-Y'); 
                        }

                        // Obtener fecha de registro
                        $stmt = $conn->prepare("
                            SELECT nivel_de_usuario_id 
                            FROM usuarios 
                            WHERE id = ?
                        ");
                        $stmt->execute([$currentUserId]);
                        $nivel = $stmt->fetch(PDO::FETCH_ASSOC);

                        $nivelusuario = ($nivel['nivel_de_usuario_id'] / 15) * 100;

                    }
                } catch (PDOException $e) {
                    error_log("Error al obtener estadísticas: " . $e->getMessage());
                }
            }
            ?>