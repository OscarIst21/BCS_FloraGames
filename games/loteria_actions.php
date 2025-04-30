<?php
session_start();

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$response = ['success' => false];

switch ($action) {
    case 'draw':
        if (empty($_SESSION['loteria_used_cards'])) {
            $_SESSION['loteria_used_cards'] = array_keys($_SESSION['loteria_board']);
            shuffle($_SESSION['loteria_used_cards']);
        }

        if (!empty($_SESSION['loteria_used_cards'])) {
            $card = array_pop($_SESSION['loteria_used_cards']);
            $_SESSION['loteria_current_card'] = $card;
            $response['card'] = $card;
            $response['success'] = true;

            // Verificar si hay victoria
            $allMarked = true;
            foreach ($_SESSION['loteria_marked'] as $marked) {
                if (!$marked) {
                    $allMarked = false;
                    break;
                }
            }
            $response['gameOver'] = $allMarked;
        }
        break;

    case 'mark':
        $card = $_GET['card'] ?? '';
        if ($card === $_SESSION['loteria_current_card']) {
            foreach ($_SESSION['loteria_board'] as $index => $boardCard) {
                if ($boardCard === $card) {
                    $_SESSION['loteria_marked'][$index] = true;
                    $response['marked'] = true;
                    $response['success'] = true;
                    break;
                }
            }
        }
        break;

    case 'new':
        unset($_SESSION['loteria_board']);
        unset($_SESSION['loteria_marked']);
        unset($_SESSION['loteria_current_card']);
        unset($_SESSION['loteria_used_cards']);
        $response['success'] = true;
        break;
}

echo json_encode($response);
