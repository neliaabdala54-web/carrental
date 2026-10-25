<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/config.php';
header('Content-Type: application/json; charset=utf-8');

function contactResponse($status, array $payload)
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    contactResponse(405, ['ok' => false, 'message' => 'Método não permitido.']);
}

$name = trim($_POST['fullname'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['contactno'] ?? '');
$message = trim($_POST['message'] ?? '');
if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $phone === '' || $message === '') {
    contactResponse(400, ['ok' => false, 'message' => 'Preencha todos os campos corretamente.']);
}

try {
    $insert = $dbh->prepare(
        'INSERT INTO tblcontactusquery (name, EmailId, ContactNumber, Message) VALUES (:name, :email, :phone, :message)'
    );
    $insert->execute(['name' => $name, 'email' => $email, 'phone' => $phone, 'message' => $message]);

    $adminEmail = 'neliaabdala54@gmail.com';
    $contactQuery = $dbh->query('SELECT EmailId FROM tblcontactusinfo LIMIT 1');
    $configuredEmail = trim((string) $contactQuery->fetchColumn());
    if ($configuredEmail !== '') {
        $adminEmail = $configuredEmail;
    }

    $subject = 'Novo Contacto Recebido - Portal de Aluguer de Carros';
    $body = "Recebeu uma nova mensagem do formulário de contacto do site:\n\n";
    $body .= "Nome do Cliente: {$name}\nE-mail do Cliente: {$email}\nContacto Telefónico: {$phone}\n\n";
    $body .= "Mensagem:\n{$message}\n\n----------------------------------------\n";
    $body .= 'Enviado automaticamente a partir do formulário do Portal de Aluguer de Carros.';
    $headers = "From: {$email}\r\nReply-To: {$email}\r\nX-Mailer: PHP/" . phpversion();
    @mail($adminEmail, $subject, $body, $headers);

    contactResponse(201, ['ok' => true, 'message' => 'Mensagem enviada com sucesso! Entraremos em contacto brevemente.']);
} catch (Throwable $error) {
    contactResponse(500, ['ok' => false, 'message' => 'Ocorreu um erro ao enviar a mensagem. Por favor tente novamente.']);
}
rejectCrossOriginMutation();