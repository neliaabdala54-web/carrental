<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../includes/config.php';
header('Content-Type: application/json; charset=utf-8');

function settingsResponse($status, array $payload)
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if (empty($_SESSION['alogin'])) settingsResponse(401, ['ok' => false, 'message' => 'Sessão administrativa necessária.']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') rejectCrossOriginMutation();

$pageTypes = ['terms', 'privacy', 'aboutus', 'faqs'];
try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $setting = $_GET['setting'] ?? '';
        if ($setting === 'contact') {
            $query = $dbh->query('SELECT Address, EmailId, ContactNo FROM tblcontactusinfo LIMIT 1');
            $contact = $query->fetch(PDO::FETCH_ASSOC);
            if (!$contact) settingsResponse(404, ['ok' => false, 'message' => 'Dados de contato não encontrados.']);
            settingsResponse(200, ['contact' => $contact]);
        }
        if ($setting === 'pages') {
            $type = $_GET['type'] ?? '';
            if (!in_array($type, $pageTypes, true)) settingsResponse(400, ['ok' => false, 'message' => 'Selecione uma página válida.']);
            $query = $dbh->prepare('SELECT type, PageName, detail FROM tblpages WHERE type = :type LIMIT 1');
            $query->execute(['type' => $type]);
            $page = $query->fetch(PDO::FETCH_ASSOC);
            if (!$page) settingsResponse(404, ['ok' => false, 'message' => 'Conteúdo da página não encontrado.']);
            settingsResponse(200, ['page' => $page]);
        }
        settingsResponse(400, ['ok' => false, 'message' => 'Configuração inválida.']);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') settingsResponse(405, ['ok' => false, 'message' => 'Método não permitido.']);
    $setting = $_POST['setting'] ?? '';
    if ($setting === 'contact') {
        $address = trim($_POST['address'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['contactno'] ?? '');
        if ($address === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $phone === '') {
            settingsResponse(400, ['ok' => false, 'message' => 'Preencha os dados de contato corretamente.']);
        }
        $query = $dbh->prepare('UPDATE tblcontactusinfo SET Address = :address, EmailId = :email, ContactNo = :phone');
        $query->execute(['address' => $address, 'email' => $email, 'phone' => $phone]);
        settingsResponse(200, ['ok' => true, 'message' => 'Informações de contacto atualizadas com sucesso.']);
    }
    if ($setting === 'pages') {
        $type = $_POST['type'] ?? '';
        $detail = trim($_POST['detail'] ?? '');
        if (!in_array($type, $pageTypes, true) || $detail === '') {
            settingsResponse(400, ['ok' => false, 'message' => 'Informe uma página e conteúdo válidos.']);
        }
        $query = $dbh->prepare('UPDATE tblpages SET detail = :detail WHERE type = :type');
        $query->execute(['detail' => $detail, 'type' => $type]);
        settingsResponse(200, ['ok' => true, 'message' => 'Página atualizada com sucesso.']);
    }
    settingsResponse(400, ['ok' => false, 'message' => 'Configuração inválida.']);
} catch (Throwable $error) {
    settingsResponse(500, ['ok' => false, 'message' => 'Não foi possível guardar as configurações.']);
}