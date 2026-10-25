<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/passwords.php';
header('Content-Type: application/json; charset=utf-8');

function accountResponse($status, array $payload)
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if (empty($_SESSION['login'])) {
    accountResponse(401, ['ok' => false, 'message' => 'Inicie sessão para acessar esta página.']);
}
$email = $_SESSION['login'];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $view = $_GET['view'] ?? 'profile';
        $userQuery = $dbh->prepare('SELECT FullName, EmailId, ContactNo, dob, Address, City, Country, RegDate, UpdationDate FROM tblusers WHERE EmailId = :email LIMIT 1');
        $userQuery->execute(['email' => $email]);
        $user = $userQuery->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            $_SESSION = [];
            accountResponse(401, ['ok' => false, 'message' => 'Sessão inválida.']);
        }

        $data = ['user' => $user];
        if ($view === 'bookings') {
            $query = $dbh->prepare(
                'SELECT tblvehicles.Vimage1, tblvehicles.VehiclesTitle, tblvehicles.id AS vid,
                        tblbrands.BrandName, tblbooking.FromDate, tblbooking.ToDate,
                        tblbooking.message, tblbooking.Status, tblvehicles.PricePerDay,
                        DATEDIFF(tblbooking.ToDate, tblbooking.FromDate) AS totaldays,
                        tblbooking.BookingNumber
                 FROM tblbooking
                 JOIN tblvehicles ON tblbooking.VehicleId = tblvehicles.id
                 JOIN tblbrands ON tblvehicles.VehiclesBrand = tblbrands.id
                 WHERE tblbooking.userEmail = :email ORDER BY tblbooking.id DESC'
            );
            $query->execute(['email' => $email]);
            $data['bookings'] = $query->fetchAll(PDO::FETCH_ASSOC);
        } elseif ($view === 'testimonials') {
            $query = $dbh->prepare('SELECT Testimonial, PostingDate, status FROM tbltestimonial WHERE UserEmail = :email');
            $query->execute(['email' => $email]);
            $data['testimonials'] = $query->fetchAll(PDO::FETCH_ASSOC);
        } elseif (!in_array($view, ['profile', 'password', 'post-testimonial'], true)) {
            accountResponse(400, ['ok' => false, 'message' => 'Página de conta inválida.']);
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        accountResponse(405, ['ok' => false, 'message' => 'Método não permitido.']);
    }
    rejectCrossOriginMutation();

    $action = $_POST['action'] ?? '';
    if ($action === 'update-profile') {
        $name = trim($_POST['fullname'] ?? '');
        $mobile = trim($_POST['mobilenumber'] ?? '');
        if ($name === '' || $mobile === '') {
            accountResponse(400, ['ok' => false, 'message' => 'Nome e telefone são obrigatórios.']);
        }
        $query = $dbh->prepare(
            'UPDATE tblusers SET FullName = :name, ContactNo = :mobile, dob = :dob,
             Address = :address, City = :city, Country = :country WHERE EmailId = :email'
        );
        $query->execute([
            'name' => $name,
            'mobile' => $mobile,
            'dob' => trim($_POST['dob'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'country' => trim($_POST['country'] ?? ''),
            'email' => $email,
        ]);
        $_SESSION['fname'] = $name;
        accountResponse(200, ['ok' => true, 'message' => 'Perfil atualizado com sucesso.']);
    }

    if ($action === 'update-password') {
        $current = $_POST['password'] ?? '';
        $newPassword = $_POST['newpassword'] ?? '';
        if (strlen($newPassword) < 8) {
            accountResponse(400, ['ok' => false, 'message' => 'A nova senha deve ter pelo menos 8 caracteres.']);
        }
        $verify = $dbh->prepare('SELECT Password FROM tblusers WHERE EmailId = :email LIMIT 1');
        $verify->execute(['email' => $email]);
        $storedPassword = $verify->fetchColumn();
        $replacementHash = $storedPassword ? verifyPasswordAndGetUpgrade($current, $storedPassword) : false;
        if (!$storedPassword || $replacementHash === false) {
            accountResponse(400, ['ok' => false, 'message' => 'Sua senha atual está incorreta.']);
        }
        $update = $dbh->prepare('UPDATE tblusers SET Password = :password WHERE EmailId = :email');
        $update->execute(['password' => password_hash($newPassword, PASSWORD_DEFAULT), 'email' => $email]);
        accountResponse(200, ['ok' => true, 'message' => 'Sua senha foi alterada com sucesso.']);
    }

    if ($action === 'testimonial') {
        $testimonial = trim($_POST['testimonial'] ?? '');
        if ($testimonial === '') {
            accountResponse(400, ['ok' => false, 'message' => 'Escreva seu depoimento.']);
        }
        $insert = $dbh->prepare('INSERT INTO tbltestimonial (UserEmail, Testimonial) VALUES (:email, :testimonial)');
        $insert->execute(['email' => $email, 'testimonial' => $testimonial]);
        accountResponse(201, ['ok' => true, 'message' => 'Depoimento submetido com sucesso.']);
    }

    accountResponse(400, ['ok' => false, 'message' => 'Ação inválida.']);
} catch (Throwable $error) {
    accountResponse(500, ['ok' => false, 'message' => 'Não foi possível concluir a solicitação.']);
}