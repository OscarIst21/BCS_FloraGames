<?php
session_start();

header('Content-Type: application/json');

$answer = $_POST['answer'] ?? '';
$definition = $_POST['definition'] ?? '';
$response = ['correct' => false];

if ($answer && $definition) {
    $index = intval($definition) - 1;
    $words = array_keys($_SESSION['crucigrama_words']);

    if (isset($words[$index]) && $words[$index] === $answer) {
        $_SESSION['crucigrama_solved'][$index] = true;
        $response['correct'] = true;

        // Verificar si el juego está completo
        $allSolved = true;
        foreach ($_SESSION['crucigrama_solved'] as $solved) {
            if (!$solved) {
                $allSolved = false;
                break;
            }
        }
        $response['gameComplete'] = $allSolved;
    }
}

echo json_encode($response);
