<?php
declare(strict_types=1);

session_start();

header('Content-Type: application/json; charset=UTF-8');

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

echo json_encode(['csrf_token' => $_SESSION['csrf_token']], JSON_UNESCAPED_UNICODE);
