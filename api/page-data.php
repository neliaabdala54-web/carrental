<?php
require_once __DIR__ . '/../includes/config.php';
header('Content-Type: application/json; charset=utf-8');

$type = $_GET['type'] ?? 'aboutus';
$pages = [
    'aboutus' => [
        'title' => 'Sobre Nós',
        'detail' => 'Somos uma empresa líder no segmento de aluguer de automóveis em Moçambique, comprometida em oferecer aos nossos clientes soluções de mobilidade modernas, seguras e acessíveis.<br><br>A nossa frota conta com veículos de alta qualidade, inspecionados e prontos para atender tanto a viagens de negócios como a passeios em família. Garantimos um atendimento personalizado, transparência total e as melhores tarifas do mercado.',
    ],
    'privacy' => [
        'title' => 'Política de Privacidade',
        'detail' => 'A sua privacidade é fundamental para nós. Esta política descreve como recolhemos, usamos e protegemos as suas informações pessoais quando utiliza os nossos serviços de aluguer de carros.<br><br>Todos os dados fornecidos são armazenados com total segurança e utilizados exclusivamente para o processamento de reservas e melhoria contínua dos nossos serviços.',
    ],
    'terms' => [
        'title' => 'Termos e Condições',
        'detail' => 'Ao utilizar o nosso portal e efetuar uma reserva de veículo, concorda com os nossos termos e condições de serviço.<br><br>O condutor deve possuir uma licença de condução válida e idade mínima legal. Os pagamentos e cauções são informados com total transparência antes da confirmação final da reserva.',
    ],
    'faqs' => [
        'title' => 'Perguntas Frequentes (FAQs)',
        'detail' => '',
    ],
];

if (!isset($pages[$type])) {
    http_response_code(404);
    echo json_encode(['error' => 'Página não encontrada.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$page = $pages[$type];
$pageQuery = $dbh->prepare('SELECT PageName, detail FROM tblpages WHERE type = :type LIMIT 1');
$pageQuery->execute(['type' => $type]);
$storedPage = $pageQuery->fetch(PDO::FETCH_ASSOC);
if ($storedPage) {
    if (!empty($storedPage['PageName'])) $page['title'] = $storedPage['PageName'];
    $page['detail'] = $storedPage['detail'];
}
if ($type === 'aboutus') {
    $query = $dbh->query('SELECT Address FROM tblcontactusinfo LIMIT 1');
    $address = $query->fetchColumn();
    if ($address) {
        $page['detail'] .= '<br><br><strong>Localização da Empresa:</strong> ' . htmlspecialchars($address, ENT_QUOTES, 'UTF-8') . '.';
    }
}

echo json_encode($page, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);