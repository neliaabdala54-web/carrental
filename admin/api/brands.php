<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../includes/config.php';
header('Content-Type: application/json; charset=utf-8');

function brandsResponse($status, array $payload)
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') rejectCrossOriginMutation();

if (empty($_SESSION['alogin'])) {
    brandsResponse(401, ['ok' => false, 'message' => 'Sessão administrativa necessária.']);
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            $query = $dbh->prepare('SELECT id, BrandName FROM tblbrands WHERE id = :id LIMIT 1');
            $query->execute(['id' => $id]);
            $brand = $query->fetch(PDO::FETCH_ASSOC);
            if (!$brand) brandsResponse(404, ['ok' => false, 'message' => 'Marca não encontrada.']);
            brandsResponse(200, ['brand' => $brand]);
        }
        $query = $dbh->query('SELECT id, BrandName, CreationDate, UpdationDate FROM tblbrands ORDER BY id DESC');
        brandsResponse(200, ['brands' => $query->fetchAll(PDO::FETCH_ASSOC)]);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        brandsResponse(405, ['ok' => false, 'message' => 'Método não permitido.']);
    }

    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $name = trim($_POST['brand'] ?? '');
        if ($name === '') brandsResponse(400, ['ok' => false, 'message' => 'Informe o nome da marca.']);
        $query = $dbh->prepare('INSERT INTO tblbrands (BrandName) VALUES (:name)');
        $query->execute(['name' => $name]);
        brandsResponse(201, ['ok' => true, 'message' => 'Marca criada com sucesso.']);
    }

    if ($action === 'update') {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $name = trim($_POST['brand'] ?? '');
        if (!$id || $id < 1 || $name === '') brandsResponse(400, ['ok' => false, 'message' => 'Informe uma marca válida.']);
        $query = $dbh->prepare('UPDATE tblbrands SET BrandName = :name WHERE id = :id');
        $query->execute(['name' => $name, 'id' => $id]);
        brandsResponse(200, ['ok' => true, 'message' => 'Marca atualizada com sucesso.']);
    }

    if ($action === 'delete') {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        if (!$id || $id < 1) brandsResponse(400, ['ok' => false, 'message' => 'Marca inválida.']);
        $query = $dbh->prepare('DELETE FROM tblbrands WHERE id = :id');
        $query->execute(['id' => $id]);
        brandsResponse(200, ['ok' => true, 'message' => 'Marca removida com sucesso.']);
    }

    brandsResponse(400, ['ok' => false, 'message' => 'Ação inválida.']);
} catch (Throwable $error) {
    brandsResponse(500, ['ok' => false, 'message' => 'Não foi possível concluir a operação.']);
}