<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../includes/config.php';
header('Content-Type: application/json; charset=utf-8');

function testimonialsResponse($status, array $payload)
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if (empty($_SESSION['alogin'])) testimonialsResponse(401, ['ok' => false, 'message' => 'Sessão administrativa necessária.']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') rejectCrossOriginMutation();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $status = filter_var($_POST['status'] ?? null, FILTER_VALIDATE_INT);
        if (!$id || $id < 1 || !in_array($status, [0, 1], true)) {
            testimonialsResponse(400, ['ok' => false, 'message' => 'Atualização de depoimento inválida.']);
        }
        $query = $dbh->prepare('UPDATE tbltestimonial SET status = :status WHERE id = :id');
        $query->execute(['status' => $status, 'id' => $id]);
        testimonialsResponse(200, ['ok' => true, 'message' => $status ? 'Depoimento ativado/publicado com sucesso.' : 'Depoimento desativado com sucesso.']);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') testimonialsResponse(405, ['ok' => false, 'message' => 'Método não permitido.']);
    $query = $dbh->query(
        'SELECT tbltestimonial.id, tblusers.FullName, tbltestimonial.UserEmail,
                tbltestimonial.Testimonial, tbltestimonial.PostingDate, tbltestimonial.status
         FROM tbltestimonial JOIN tblusers ON tblusers.EmailId = tbltestimonial.UserEmail
         ORDER BY tbltestimonial.id DESC'
    );
    testimonialsResponse(200, ['testimonials' => $query->fetchAll(PDO::FETCH_ASSOC)]);
} catch (Throwable $error) {
    testimonialsResponse(500, ['ok' => false, 'message' => 'Não foi possível carregar os depoimentos.']);
}