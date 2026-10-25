<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/passwords.php';

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

$input = $_POST;
if (empty($input) && strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
    $decoded = json_decode(file_get_contents('php://input'), true);
    $input = is_array($decoded) ? $decoded : [];
}

$action = $input['action'] ?? '';
$email = trim($input['email'] ?? $input['emailid'] ?? '');

try {
    if ($action === 'availability') {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            respond(400, ['ok' => false, 'available' => false, 'message' => 'E-mail inválido.']);
        }

        $query = $dbh->prepare('SELECT 1 FROM tblusers WHERE EmailId = :email LIMIT 1');
        $query->execute(['email' => $email]);
        respond(200, ['ok' => true, 'available' => !$query->fetchColumn()]);
    }

    if ($action === 'login') {
        $password = $input['password'] ?? '';
        $query = $dbh->prepare('SELECT EmailId, FullName, Password FROM tblusers WHERE EmailId = :email LIMIT 1');
        $query->execute(['email' => $email]);
        $user = $query->fetch(PDO::FETCH_ASSOC);
        $replacementHash = $user ? verifyPasswordAndGetUpgrade($password, $user['Password'] ?? '') : false;
        if (!$user || $replacementHash === false) {
            respond(401, ['ok' => false, 'message' => 'E-mail ou senha incorretos.']);
        }

        if (is_string($replacementHash)) {
            $upgrade = $dbh->prepare('UPDATE tblusers SET Password = :password WHERE EmailId = :email');
            $upgrade->execute(['password' => $replacementHash, 'email' => $user['EmailId']]);
        }
        session_regenerate_id(true);
        $_SESSION['login'] = $user['EmailId'];
        $_SESSION['fname'] = $user['FullName'];
        respond(200, ['ok' => true, 'message' => 'Sessão iniciada.']);
    }

    if ($action === 'signup') {
        $fullName = trim($input['fullname'] ?? '');
        $mobile = trim($input['mobileno'] ?? '');
        $password = $input['password'] ?? '';

        if ($fullName === '' || $mobile === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
            respond(400, ['ok' => false, 'message' => 'Preencha todos os campos corretamente.']);
        }

        $exists = $dbh->prepare('SELECT 1 FROM tblusers WHERE EmailId = :email LIMIT 1');
        $exists->execute(['email' => $email]);
        if ($exists->fetchColumn()) {
            respond(409, ['ok' => false, 'message' => 'Este e-mail já está cadastrado.']);
        }

        $insert = $dbh->prepare(
            'INSERT INTO tblusers (FullName, EmailId, ContactNo, Password) VALUES (:name, :email, :mobile, :password)'
        );
        $insert->execute([
            'name' => $fullName,
            'email' => $email,
            'mobile' => $mobile,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);
        respond(201, ['ok' => true, 'message' => 'Cadastro realizado com sucesso. Agora pode iniciar sessão.']);
    }

    if ($action === 'reset') {
        $mobile = trim($input['mobile'] ?? '');
        $password = $input['newpassword'] ?? '';
        if (strlen($password) < 8) {
            respond(400, ['ok' => false, 'message' => 'A nova senha deve ter pelo menos 8 caracteres.']);
        }
        $update = $dbh->prepare(
            'UPDATE tblusers SET Password = :password WHERE EmailId = :email AND ContactNo = :mobile'
        );
        $update->execute([
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'email' => $email,
            'mobile' => $mobile,
        ]);

        if ($update->rowCount() === 0) {
            $exists = $dbh->prepare('SELECT 1 FROM tblusers WHERE EmailId = :email AND ContactNo = :mobile LIMIT 1');
            $exists->execute(['email' => $email, 'mobile' => $mobile]);
            if (!$exists->fetchColumn()) {
                respond(404, ['ok' => false, 'message' => 'E-mail ou número de celular inválido.']);
            }
        }

        respond(200, ['ok' => true, 'message' => 'Sua senha foi alterada com sucesso.']);
    }

    respond(400, ['ok' => false, 'message' => 'Ação inválida.']);
} catch (Throwable $error) {
    respond(500, ['ok' => false, 'message' => 'Não foi possível concluir a solicitação.']);
}