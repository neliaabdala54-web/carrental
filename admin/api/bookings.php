<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../includes/config.php';
header('Content-Type: application/json; charset=utf-8');

function bookingsResponse($status, array $payload)
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if (empty($_SESSION['alogin'])) bookingsResponse(401, ['ok' => false, 'message' => 'Sessão administrativa necessária.']);

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        rejectCrossOriginMutation();
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $action = $_POST['action'] ?? '';
        $statuses = ['confirm' => 1, 'cancel' => 2];
        if (!$id || $id < 1 || !isset($statuses[$action])) {
            bookingsResponse(400, ['ok' => false, 'message' => 'Ação de reserva inválida.']);
        }
        $update = $dbh->prepare('UPDATE tblbooking SET Status = :status WHERE id = :id');
        $update->execute(['status' => $statuses[$action], 'id' => $id]);
        bookingsResponse(200, ['ok' => true, 'message' => $action === 'confirm' ? 'Reserva confirmada com sucesso.' : 'Reserva cancelada com sucesso.']);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') bookingsResponse(405, ['ok' => false, 'message' => 'Método não permitido.']);
    $bookingId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $baseQuery = 'SELECT tblusers.FullName, tblusers.EmailId, tblusers.ContactNo, tblusers.Address,
                         tblusers.City, tblusers.Country, tblbooking.LastUpdationDate,
                         tblbrands.BrandName, tblvehicles.VehiclesTitle, tblbooking.FromDate,
                         tblbooking.ToDate, tblbooking.message, tblbooking.VehicleId AS vid,
                         tblbooking.Status, tblbooking.PostingDate, tblbooking.id,
                         tblbooking.BookingNumber, tblvehicles.PricePerDay,
                         DATEDIFF(tblbooking.ToDate, tblbooking.FromDate) AS totaldays
                  FROM tblbooking
                  JOIN tblvehicles ON tblvehicles.id = tblbooking.VehicleId
                  JOIN tblusers ON tblusers.EmailId = tblbooking.userEmail
                  JOIN tblbrands ON tblbrands.id = tblvehicles.VehiclesBrand';

    if ($bookingId) {
        $query = $dbh->prepare($baseQuery . ' WHERE tblbooking.id = :id LIMIT 1');
        $query->execute(['id' => $bookingId]);
        $booking = $query->fetch(PDO::FETCH_ASSOC);
        if (!$booking) bookingsResponse(404, ['ok' => false, 'message' => 'Reserva não encontrada.']);
        bookingsResponse(200, ['booking' => $booking]);
    }

    $status = $_GET['status'] ?? 'pending';
    $statusMap = ['pending' => 0, 'confirmed' => 1, 'cancelled' => 2, 'all' => null];
    if (!array_key_exists($status, $statusMap)) bookingsResponse(400, ['ok' => false, 'message' => 'Filtro de reserva inválido.']);
    $where = $statusMap[$status] === null ? '' : ' WHERE tblbooking.Status = :status';
    $query = $dbh->prepare($baseQuery . $where . ' ORDER BY tblbooking.id DESC');
    $query->execute($statusMap[$status] === null ? [] : ['status' => $statusMap[$status]]);
    bookingsResponse(200, ['bookings' => $query->fetchAll(PDO::FETCH_ASSOC)]);
} catch (Throwable $error) {
    bookingsResponse(500, ['ok' => false, 'message' => 'Não foi possível carregar as reservas.']);
}