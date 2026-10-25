<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'Método não permitido.']);
    exit;
}

$email = trim($_POST['subscriberemail'] ?? '');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => 'Digite um e-mail válido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $exists = $dbh->prepare('SELECT 1 FROM tblsubscribers WHERE SubscriberEmail = :email LIMIT 1');
    $exists->execute(['email' => $email]);
    if ($exists->fetchColumn()) {
        http_response_code(409);
        echo json_encode(['ok' => false, 'message' => 'Este e-mail já está subscrito.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $insert = $dbh->prepare('INSERT INTO tblsubscribers (SubscriberEmail) VALUES (:email)');
    $insert->execute(['email' => $email]);
    echo json_encode(['ok' => true, 'message' => 'Subscrição realizada com sucesso.'], JSON_UNESCAPED_UNICODE);
} catch (Throwable $error) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'Ocorreu um erro. Por favor tente novamente.'], JSON_UNESCAPED_UNICODE);
}
rejectCrossOriginMutation();