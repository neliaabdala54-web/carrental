<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/config.php';
header('Content-Type: application/json; charset=utf-8');

function respond($status, array $payload)
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, ['ok' => false, 'message' => 'Método não permitido.']);
}
rejectCrossOriginMutation();
if (empty($_SESSION['login'])) {
    respond(401, ['ok' => false, 'message' => 'Inicie sessão para confirmar a reserva.']);
}

$vehicleId = filter_var($_POST['vehicleId'] ?? null, FILTER_VALIDATE_INT);
$fromDate = trim($_POST['fromdate'] ?? '');
$toDate = trim($_POST['todate'] ?? '');
$message = trim($_POST['message'] ?? '');
$start = DateTimeImmutable::createFromFormat('!Y-m-d', $fromDate);
$end = DateTimeImmutable::createFromFormat('!Y-m-d', $toDate);
$today = new DateTimeImmutable('today');

if (!$vehicleId || $vehicleId < 1 || !$start || !$end || $start->format('Y-m-d') !== $fromDate || $end->format('Y-m-d') !== $toDate || $message === '') {
    respond(400, ['ok' => false, 'message' => 'Preencha os dados da reserva corretamente.']);
}
if ($start < $today || $end < $today) {
    respond(400, ['ok' => false, 'message' => 'A data da reserva não pode ser anterior à data atual.']);
}
if ($end < $start) {
    respond(400, ['ok' => false, 'message' => 'A data de fim não pode ser anterior à data de início.']);
}

try {
    $dbh->beginTransaction();
    $vehicleQuery = $dbh->prepare('SELECT id FROM tblvehicles WHERE id = :id LIMIT 1 FOR UPDATE');
    $vehicleQuery->execute(['id' => $vehicleId]);
    if (!$vehicleQuery->fetchColumn()) {
        respond(404, ['ok' => false, 'message' => 'Veículo não encontrado.']);
    }

    $overlapQuery = $dbh->prepare(
        'SELECT 1 FROM tblbooking WHERE VehicleId = :vehicle
         AND DATE(FromDate) <= :end_date AND DATE(ToDate) >= :start_date LIMIT 1'
    );
    $overlapQuery->execute(['vehicle' => $vehicleId, 'end_date' => $toDate, 'start_date' => $fromDate]);
    if ($overlapQuery->fetchColumn()) {
        respond(409, ['ok' => false, 'message' => 'O carro já se encontra reservado para este período.']);
    }

    $insert = $dbh->prepare(
        'INSERT INTO tblbooking (BookingNumber, userEmail, VehicleId, FromDate, ToDate, message, Status)
         VALUES (:number, :email, :vehicle, :start_date, :end_date, :message, 0)'
    );
    $insert->execute([
        'number' => random_int(100000000, 999999999),
        'email' => $_SESSION['login'],
        'vehicle' => $vehicleId,
        'start_date' => $fromDate,
        'end_date' => $toDate,
        'message' => $message,
    ]);
    $dbh->commit();
    respond(201, ['ok' => true, 'message' => 'Reserva efetuada com sucesso.']);
} catch (Throwable $error) {
    if ($dbh->inTransaction()) $dbh->rollBack();
    respond(500, ['ok' => false, 'message' => 'Ocorreu um erro. Por favor tente novamente.']);
}