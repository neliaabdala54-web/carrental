<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json; charset=utf-8');
$vehicleId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$vehicleId || $vehicleId < 1) {
    http_response_code(400);
    echo json_encode(['error' => 'Veículo inválido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $vehicleQuery = $dbh->prepare(
        'SELECT tblvehicles.*, tblbrands.BrandName, tblbrands.id AS bid
         FROM tblvehicles JOIN tblbrands ON tblbrands.id = tblvehicles.VehiclesBrand
         WHERE tblvehicles.id = :id LIMIT 1'
    );
    $vehicleQuery->execute(['id' => $vehicleId]);
    $vehicle = $vehicleQuery->fetch(PDO::FETCH_ASSOC);
    if (!$vehicle) {
        http_response_code(404);
        echo json_encode(['error' => 'Veículo não encontrado.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $similarQuery = $dbh->prepare(
        'SELECT tblvehicles.VehiclesTitle, tblbrands.BrandName, tblvehicles.PricePerDay,
                tblvehicles.FuelType, tblvehicles.ModelYear, tblvehicles.id,
                tblvehicles.SeatingCapacity, tblvehicles.VehiclesOverview, tblvehicles.Vimage1
         FROM tblvehicles JOIN tblbrands ON tblbrands.id = tblvehicles.VehiclesBrand
         WHERE tblvehicles.VehiclesBrand = :brand'
    );
    $similarQuery->execute(['brand' => $vehicle['bid']]);

    $user = null;
    if (!empty($_SESSION['login'])) {
        $userQuery = $dbh->prepare('SELECT FullName FROM tblusers WHERE EmailId = :email LIMIT 1');
        $userQuery->execute(['email' => $_SESSION['login']]);
        $user = $userQuery->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    echo json_encode([
        'vehicle' => $vehicle,
        'similar' => $similarQuery->fetchAll(PDO::FETCH_ASSOC),
        'user' => $user,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $error) {
    http_response_code(500);
    echo json_encode(['error' => 'Não foi possível carregar os dados do veículo.'], JSON_UNESCAPED_UNICODE);
}