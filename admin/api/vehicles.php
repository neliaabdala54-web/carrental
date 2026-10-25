<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../includes/config.php';
header('Content-Type: application/json; charset=utf-8');

function vehiclesResponse($status, array $payload)
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if (empty($_SESSION['alogin'])) vehiclesResponse(401, ['ok' => false, 'message' => 'Sessão administrativa necessária.']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') rejectCrossOriginMutation();

function saveVehicleImage($file)
{
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) return null;
    if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 8 * 1024 * 1024) {
        throw new RuntimeException('Cada imagem deve ter até 8 MB.');
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($extensions[$mime])) throw new RuntimeException('Use imagens JPG, PNG ou WEBP.');

    $directory = __DIR__ . '/../img/vehicleimages';
    if (!is_dir($directory) || !is_writable($directory)) {
        throw new RuntimeException('A pasta de imagens não está disponível para gravação.');
    }
    $filename = bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
    if (!move_uploaded_file($file['tmp_name'], $directory . DIRECTORY_SEPARATOR . $filename)) {
        throw new RuntimeException('Não foi possível guardar uma das imagens.');
    }
    return $filename;
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $brandsQuery = $dbh->query('SELECT id, BrandName FROM tblbrands ORDER BY BrandName');
        $response = ['brands' => $brandsQuery->fetchAll(PDO::FETCH_ASSOC)];
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            $query = $dbh->prepare('SELECT * FROM tblvehicles WHERE id = :id LIMIT 1');
            $query->execute(['id' => $id]);
            $vehicle = $query->fetch(PDO::FETCH_ASSOC);
            if (!$vehicle) vehiclesResponse(404, ['ok' => false, 'message' => 'Veículo não encontrado.']);
            $response['vehicle'] = $vehicle;
        } else {
            $query = $dbh->query(
                'SELECT tblvehicles.id, tblvehicles.VehiclesTitle, tblvehicles.SeriesVersion,
                        tblvehicles.LicensePlate, tblvehicles.Category, tblvehicles.Transmission,
                        tblvehicles.FuelType, tblvehicles.PricePerDay, tblvehicles.StatusAvailability,
                        tblbrands.BrandName
                 FROM tblvehicles JOIN tblbrands ON tblbrands.id = tblvehicles.VehiclesBrand
                 ORDER BY tblvehicles.id DESC'
            );
            $response['vehicles'] = $query->fetchAll(PDO::FETCH_ASSOC);
        }
        vehiclesResponse(200, $response);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        if ($action === 'delete' && $id && $id > 0) {
            $delete = $dbh->prepare('DELETE FROM tblvehicles WHERE id = :id');
            $delete->execute(['id' => $id]);
            vehiclesResponse(200, ['ok' => true, 'message' => 'Veículo removido com sucesso.']);
        }
        if ($action !== 'save') {
            vehiclesResponse(400, ['ok' => false, 'message' => 'Ação de veículo inválida.']);
        }

        $isUpdate = $id && $id > 0;
        $textFields = [
            'vehicletitle' => 'VehiclesTitle',
            'brandname' => 'VehiclesBrand',
            'vehicalorcview' => 'VehiclesOverview',
            'priceperday' => 'PricePerDay',
            'fueltype' => 'FuelType',
            'modelyear' => 'ModelYear',
            'seatingcapacity' => 'SeatingCapacity',
            'licenseplate' => 'LicensePlate',
            'seriesversion' => 'SeriesVersion',
            'category' => 'Category',
            'transmission' => 'Transmission',
            'statusavailability' => 'StatusAvailability',
        ];
        $values = [];
        foreach ($textFields as $input => $column) $values[$column] = trim($_POST[$input] ?? '');
        if ($values['VehiclesTitle'] === '' || !filter_var($values['VehiclesBrand'], FILTER_VALIDATE_INT)
            || $values['VehiclesOverview'] === '' || !is_numeric($values['PricePerDay']) || (float) $values['PricePerDay'] <= 0
            || !preg_match('/^\d{4}$/', $values['ModelYear']) || !filter_var($values['SeatingCapacity'], FILTER_VALIDATE_INT)
            || $values['LicensePlate'] === '' || $values['Category'] === '' || $values['FuelType'] === ''
            || $values['Transmission'] === '' || !in_array($values['StatusAvailability'], ['Disponível', 'Não Disponível'], true)) {
            vehiclesResponse(400, ['ok' => false, 'message' => 'Preencha todos os campos obrigatórios corretamente.']);
        }

        $featureFields = [
            'airconditioner' => 'AirConditioner', 'powerdoorlocks' => 'PowerDoorLocks',
            'antilockbrakingsys' => 'AntiLockBrakingSystem', 'brakeassist' => 'BrakeAssist',
            'powersteering' => 'PowerSteering', 'driverairbag' => 'DriverAirbag',
            'passengerairbag' => 'PassengerAirbag', 'powerwindow' => 'PowerWindows',
            'cdplayer' => 'CDPlayer', 'centrallocking' => 'CentralLocking',
            'crashcensor' => 'CrashSensor', 'leatherseats' => 'LeatherSeats',
        ];
        foreach ($featureFields as $input => $column) $values[$column] = isset($_POST[$input]) ? 1 : 0;

        if (!$isUpdate) {
            for ($imageNumber = 1; $imageNumber <= 4; $imageNumber++) {
                $file = $_FILES['img' . $imageNumber] ?? null;
                if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
                    vehiclesResponse(400, ['ok' => false, 'message' => 'As quatro primeiras imagens são obrigatórias.']);
                }
            }
        }

        $uploaded = [];
        for ($imageNumber = 1; $imageNumber <= 5; $imageNumber++) {
            $key = 'img' . $imageNumber;
            $filename = saveVehicleImage($_FILES[$key] ?? null);
            if ($filename !== null) {
                $uploaded[] = $filename;
                $values['Vimage' . $imageNumber] = $filename;
            } elseif (!$isUpdate) {
                $values['Vimage' . $imageNumber] = '';
            }
        }

        if ($isUpdate) {
            $set = [];
            foreach ($values as $column => $value) $set[] = $column . ' = :' . $column;
            $values['id'] = $id;
            $query = $dbh->prepare('UPDATE tblvehicles SET ' . implode(', ', $set) . ' WHERE id = :id');
            $query->execute($values);
            vehiclesResponse(200, ['ok' => true, 'id' => $id, 'message' => 'Dados do veículo atualizados com sucesso.']);
        }

        $columns = array_keys($values);
        $placeholders = array_map(function ($column) { return ':' . $column; }, $columns);
        $query = $dbh->prepare('INSERT INTO tblvehicles (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $placeholders) . ')');
        $query->execute($values);
        vehiclesResponse(201, ['ok' => true, 'id' => (int) $dbh->lastInsertId(), 'message' => 'Veículo cadastrado com sucesso.']);
    }

    vehiclesResponse(405, ['ok' => false, 'message' => 'Método não permitido.']);
} catch (Throwable $error) {
    if (!empty($uploaded)) {
        foreach ($uploaded as $filename) {
            $path = __DIR__ . '/../img/vehicleimages/' . $filename;
            if (is_file($path)) unlink($path);
        }
    }
    if ($error instanceof RuntimeException) vehiclesResponse(400, ['ok' => false, 'message' => $error->getMessage()]);
    vehiclesResponse(500, ['ok' => false, 'message' => 'Não foi possível carregar os veículos.']);
}