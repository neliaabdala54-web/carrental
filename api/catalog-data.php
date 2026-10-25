<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $conditions = [];
    $parameters = [];
    foreach (['brand' => 'tblvehicles.VehiclesBrand', 'category' => 'tblvehicles.Category', 'fueltype' => 'tblvehicles.FuelType'] as $key => $column) {
        $value = trim($_GET[$key] ?? '');
        if ($value !== '') {
            $conditions[] = $column . ' = :' . $key;
            $parameters[$key] = $value;
        }
    }

    $search = trim($_GET['q'] ?? '');
    if ($search !== '') {
        $searchColumns = [
            'tblvehicles.VehiclesTitle',
            'tblvehicles.FuelType',
            'tblbrands.BrandName',
            'CAST(tblvehicles.ModelYear AS CHAR)',
            'tblvehicles.Category',
            'tblvehicles.Transmission',
            'tblvehicles.SeriesVersion',
        ];
        $searchConditions = [];
        foreach ($searchColumns as $index => $column) {
            $placeholder = 'search' . $index;
            $searchConditions[] = $column . ' LIKE :' . $placeholder;
            $parameters[$placeholder] = '%' . $search . '%';
        }
        $conditions[] = '(' . implode(' OR ', $searchConditions) . ')';
    }

    $where = $conditions ? ' WHERE ' . implode(' AND ', $conditions) : '';
    $vehiclesQuery = $dbh->prepare(
        'SELECT tblvehicles.*, tblbrands.BrandName, tblbrands.id AS bid
         FROM tblvehicles
         JOIN tblbrands ON tblbrands.id = tblvehicles.VehiclesBrand' . $where
    );
    $vehiclesQuery->execute($parameters);
    $vehicles = $vehiclesQuery->fetchAll(PDO::FETCH_ASSOC);
    $brandsQuery = $dbh->query('SELECT id, BrandName FROM tblbrands');
    $recentQuery = $dbh->query(
        'SELECT tblvehicles.*, tblbrands.BrandName, tblbrands.id AS bid
         FROM tblvehicles
         JOIN tblbrands ON tblbrands.id = tblvehicles.VehiclesBrand
         ORDER BY tblvehicles.id DESC
         LIMIT 4'
    );

    echo json_encode([
        'count' => count($vehicles),
        'vehicles' => $vehicles,
        'brands' => $brandsQuery->fetchAll(PDO::FETCH_ASSOC),
        'recent' => $recentQuery->fetchAll(PDO::FETCH_ASSOC),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $error) {
    http_response_code(500);
    echo json_encode(['error' => 'Não foi possível carregar o catálogo.'], JSON_UNESCAPED_UNICODE);
}