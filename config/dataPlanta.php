<?php
require_once __DIR__ . '/../connection/database.php';
// Método para obtener todos los datos de las plantas
$db = new Database();
$conn = $db->getConnection();
function getAllPlantas() {
    $stmt = $conn->prepare("SELECT * FROM ficha_planta");
    $stmt->execute();
    $plantas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($plantas);
}

// Método para obtener solo los nombres científicos
function getNombresCientificos() {
    $stmt = $conn->prepare("SELECT id, nombre_cientifico FROM ficha_planta");
    $stmt->execute();
    $nombres = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($nombres);
}

// Método para obtener la distribución
function getDistribuciones() {
    $stmt = $conn->prepare("SELECT id, distribucion FROM ficha_planta");
    $stmt->execute();
    $distribuciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($distribuciones);
}

// Método para obtener las fotos
function getFotos() {
    $stmt = $conn->prepare("SELECT id, foto FROM ficha_planta");
    $stmt->execute();
    $fotos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($fotos);
}

// Método para obtener los dibujos animados
function getDibujosAnimados() {
    $stmt = $conn->prepare("SELECT id, dibujo_animado FROM ficha_planta");
    $stmt->execute();
    $dibujos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($dibujos);
}

// Método para obtener curiosidades
function getCuriosidades() {
    $stmt = $conn->prepare("SELECT id, curiosidad FROM ficha_planta");
    $stmt->execute();
    $curiosidades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($curiosidades);
}

function getUsos() {
    $stmt = $conn->prepare("SELECT id, usos FROM ficha_planta");
    $stmt->execute();
    $usos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($usos);
}

// Método para obtener audios
function getAudios() {
    $stmt = $conn->prepare("SELECT id, audio FROM ficha_planta");
    $stmt->execute();
    $audios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($audios);
}

// Método para obtener hábitats
function getHabitats() {
    $stmt = $conn->prepare("SELECT id, habitat FROM ficha_planta");
    $stmt->execute();
    $habitats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($habitats);
}

// Método para obtener características
function getCaracteristicas() {
    $stmt = $conn->prepare("SELECT id, caracteristicas FROM ficha_planta");
    $stmt->execute();
    $caracteristicas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($caracteristicas);
}

// Método para obtener situación
function getSituaciones() {
    $stmt = $conn->prepare("SELECT id, situación FROM ficha_planta");
    $stmt->execute();
    $situaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($situaciones);
}

function getNombresPlantas() {
    $stmt = $conn->prepare("SELECT id, nombre_comun FROM ficha_planta");
    $stmt->execute();
    $nombres = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $nombres_finales = [];
    foreach ($nombres as $row) {
        // Separar por coma y limpiar espacios
        $nombres_divididos = array_map('trim', explode(',', $row['nombre_comun']));
        foreach ($nombres_divididos as $nombre) {
            if ($nombre !== '') {
                $nombres_finales[] = [
                    'id' => $row['id'],
                    'nombre_comun' => $nombre
                ];
            }
        }
    }
    header('Content-Type: application/json');
    echo json_encode($nombres_finales);
}

function getCuriosidadesPlantas() {
    $stmt = $conn->prepare("SELECT id, nombre_comun, curiosidad FROM ficha_planta");
    $stmt->execute();
    $curiosidades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($curiosidades);
}

function getImagenesPlantas() {
    $stmt = $conn->prepare("SELECT id, nombre_comun, foto, dibujo_animado FROM ficha_planta");
    $stmt->execute();
    $imagenes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($imagenes);
}

// Ejemplo de uso según parámetro GET
if (isset($_GET['tipo'])) {
    switch ($_GET['tipo']) {
        case 'nombres':
            getNombresPlantas();
            break;
        case 'curiosidades':
            getCuriosidadesPlantas();
            break;
        case 'imagenes':
            getImagenesPlantas();
            break;
        case 'todas':
            getAllPlantas();
            break;
        case 'usos':
                getUsos();
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Tipo no válido']);
    }
} else {
  /*  http_response_code(400);
   echo json_encode(['error' => 'No se especificó el tipo de datos']);*/
}
