<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../includes/config.php';
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['alogin'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Sessão administrativa necessária.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $counts = [];
    foreach ([
        'users' => 'tblusers',
        'vehicles' => 'tblvehicles',
        'bookings' => 'tblbooking',
        'brands' => 'tblbrands',
        'subscribers' => 'tblsubscribers',
        'contactQueries' => 'tblcontactusquery',
        'testimonials' => 'tbltestimonial',
    ] as $key => $table) {
        $counts[$key] = (int) $dbh->query('SELECT COUNT(*) FROM ' . $table)->fetchColumn();
    }
    echo json_encode($counts, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $error) {
    http_response_code(500);
    echo json_encode(['error' => 'Não foi possível carregar o painel.'], JSON_UNESCAPED_UNICODE);
}