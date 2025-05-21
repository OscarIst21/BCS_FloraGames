<?php
require_once __DIR__ . '/../connection/database.php';
if (basename(__FILE__) != basename($_SERVER['SCRIPT_FILENAME'])) {
    // Si este archivo es incluido desde otro, no hacer nada
    return;
}
require_once __DIR__ . '/../config/dataPlanta.php';
// Método para obtener todos los datos de las plantas
function getAllPlantas() {
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT * FROM ficha_planta");
    $stmt->execute();
    $plantas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($plantas);
}

// Método para obtener solo los nombres científicos
function getNombresCientificos() {
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT id, nombre_cientifico FROM ficha_planta");
    $stmt->execute();
    $nombres = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($nombres);
}

// Método para obtener la distribución
function getDistribuciones() {
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT id, distribucion FROM ficha_planta");
    $stmt->execute();
    $distribuciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($distribuciones);
}

// Método para obtener las fotos
function getFotos() {
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT id, foto FROM ficha_planta");
    $stmt->execute();
    $fotos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($fotos);
}

// Método para obtener los dibujos animados
function getDibujosAnimados() {
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT id, dibujo_animado FROM ficha_planta");
    $stmt->execute();
    $dibujos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($dibujos);
}

// Método para obtener curiosidades
function getCuriosidades() {
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT id, curiosidad FROM ficha_planta");
    $stmt->execute();
    $curiosidades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($curiosidades);
}

// Método para obtener audios
function getAudios() {
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT id, audio FROM ficha_planta");
    $stmt->execute();
    $audios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($audios);
}

// Método para obtener hábitats
function getHabitats() {
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT id, habitat FROM ficha_planta");
    $stmt->execute();
    $habitats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($habitats);
}

// Método para obtener características
function getCaracteristicas() {
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT id, caracteristicas FROM ficha_planta");
    $stmt->execute();
    $caracteristicas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($caracteristicas);
}

// Método para obtener situación
function getSituaciones() {
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT id, situación FROM ficha_planta");
    $stmt->execute();
    $situaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($situaciones);
}

function getNombresPlantas() {
    $db = new Database();
    $conn = $db->getConnection();
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
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT id, nombre_comun, curiosidad FROM ficha_planta");
    $stmt->execute();
    $curiosidades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($curiosidades);
}

function getImagenesPlantas() {
    $db = new Database();
    $conn = $db->getConnection();
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
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Tipo no válido']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'No se especificó el tipo de datos']);
}

