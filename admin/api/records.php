<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../includes/config.php';
header('Content-Type: application/json; charset=utf-8');

function recordsResponse($status, array $payload)
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if (empty($_SESSION['alogin'])) {
    recordsResponse(401, ['ok' => false, 'message' => 'Sessão administrativa necessária.']);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') rejectCrossOriginMutation();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        if (!$id || $id < 1) recordsResponse(400, ['ok' => false, 'message' => 'Registro inválido.']);

        if ($action === 'delete-subscriber') {
            $query = $dbh->prepare('DELETE FROM tblsubscribers WHERE id = :id');
            $query->execute(['id' => $id]);
            recordsResponse(200, ['ok' => true, 'message' => 'Subscritor removido com sucesso.']);
        }
        if ($action === 'read-contact') {
            $query = $dbh->prepare('UPDATE tblcontactusquery SET status = 1 WHERE id = :id');
            $query->execute(['id' => $id]);
            recordsResponse(200, ['ok' => true, 'message' => 'Mensagem marcada como lida.']);
        }
        recordsResponse(400, ['ok' => false, 'message' => 'Ação inválida.']);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') recordsResponse(405, ['ok' => false, 'message' => 'Método não permitido.']);
    $type = $_GET['type'] ?? '';
    $queries = [
        'subscribers' => 'SELECT id, SubscriberEmail, PostingDate FROM tblsubscribers ORDER BY id DESC',
        'contacts' => 'SELECT id, name, EmailId, ContactNumber, Message, PostingDate, status FROM tblcontactusquery ORDER BY id DESC',
        'users' => 'SELECT FullName, EmailId, ContactNo, dob, Address, City, Country, RegDate FROM tblusers ORDER BY id DESC',
    ];
    if (!isset($queries[$type])) recordsResponse(400, ['ok' => false, 'message' => 'Tipo de tabela inválido.']);
    $query = $dbh->query($queries[$type]);
    recordsResponse(200, ['records' => $query->fetchAll(PDO::FETCH_ASSOC)]);
} catch (Throwable $error) {
    recordsResponse(500, ['ok' => false, 'message' => 'Não foi possível carregar os registos.']);
}