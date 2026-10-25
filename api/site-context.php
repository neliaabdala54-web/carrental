<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $user = null;
    if (!empty($_SESSION['login'])) {
        $query = $dbh->prepare('SELECT FullName FROM tblusers WHERE EmailId = :email LIMIT 1');
        $query->execute(['email' => $_SESSION['login']]);
        $user = $query->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    $contactsQuery = $dbh->query('SELECT Address, EmailId, ContactNo FROM tblcontactusinfo');
    echo json_encode([
        'user' => $user,
        'contacts' => $contactsQuery->fetchAll(PDO::FETCH_ASSOC),
        'year' => date('Y'),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $error) {
    http_response_code(500);
    echo json_encode(['error' => 'Não foi possível carregar os dados do site.'], JSON_UNESCAPED_UNICODE);
}