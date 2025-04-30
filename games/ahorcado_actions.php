<?php
session_start();

header('Content-Type: application/json');

$letter = $_POST['letter'] ?? '';
$response = ['gameOver' => false];

if ($letter) {
    // Agregar la letra a las letras adivinadas
    $_SESSION['ahorcado_guessed'][] = $letter;

    // Verificar si la letra está en la palabra
    $word = $_SESSION['ahorcado_word'];
    if (strpos($word, $letter) === false) {
        $_SESSION['ahorcado_mistakes']++;
    }

    // Verificar si se perdió el juego
    if ($_SESSION['ahorcado_mistakes'] >= $_SESSION['ahorcado_max_mistakes']) {
        $response['gameOver'] = true;
        $response['message'] = '¡Game Over! La palabra era: ' . $word;
        unset($_SESSION['ahorcado_word']);
    } else {
        // Verificar si se ganó el juego
        $won = true;
        for ($i = 0; $i < strlen($word); $i++) {
            if (!in_array($word[$i], $_SESSION['ahorcado_guessed'])) {
                $won = false;
                break;
            }
        }

        if ($won) {
            $response['gameOver'] = true;
            $response['message'] = '¡Felicidades! Has adivinado la palabra: ' . $word;
            unset($_SESSION['ahorcado_word']);
        }
    }
}

echo json_encode($response);
