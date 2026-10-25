<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $vehiclesQuery = $dbh->prepare(
        'SELECT tblvehicles.*, tblbrands.BrandName
         FROM tblvehicles
         JOIN tblbrands ON tblbrands.id = tblvehicles.VehiclesBrand
         ORDER BY tblvehicles.id DESC
         LIMIT 9'
    );
    $vehiclesQuery->execute();

    $testimonialsQuery = $dbh->prepare(
        'SELECT tbltestimonial.Testimonial, tblusers.FullName
         FROM tbltestimonial
         JOIN tblusers ON tbltestimonial.UserEmail = tblusers.EmailId
         WHERE tbltestimonial.status = :status
         LIMIT 4'
    );
    $testimonialsQuery->execute(['status' => 1]);

    echo json_encode(
        [
            'vehicles' => $vehiclesQuery->fetchAll(PDO::FETCH_ASSOC),
            'testimonials' => $testimonialsQuery->fetchAll(PDO::FETCH_ASSOC),
        ],
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
} catch (Throwable $error) {
    http_response_code(500);
    echo json_encode(['error' => 'Não foi possível carregar os dados da página.'], JSON_UNESCAPED_UNICODE);
}
