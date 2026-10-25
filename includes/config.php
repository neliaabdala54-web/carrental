
<?php

/*
|--------------------------------------------------------------------------
| Configuração central da base de dados — Car Rental
|--------------------------------------------------------------------------
| Ambiente local: XAMPP
| Base de dados: MySQL
| Ligação: PDO
|--------------------------------------------------------------------------
*/

// Evitar redefinir as constantes caso a configuração já tenha sido carregada.
if (!defined('DB_HOST')) {

    $environment = strtolower(
        trim((string) (getenv('CARRENTAL_ENV') ?: 'development'))
    );

    $configuredDbUser = getenv('CARRENTAL_DB_USER');
    $configuredDbPass = getenv('CARRENTAL_DB_PASS');

    $remoteAddress = $_SERVER['REMOTE_ADDR'] ?? '';
    $httpHost = $_SERVER['HTTP_HOST'] ?? '';

    $requestHost = strtolower(
        (string) parse_url('http://' . $httpHost, PHP_URL_HOST)
    );

    $isLoopbackRequest =
        in_array($remoteAddress, ['127.0.0.1', '::1'], true)
        && in_array($requestHost, ['localhost', '127.0.0.1', '::1'], true);

    $isLocalEnvironment =
        PHP_SAPI === 'cli'
        || (
            $environment !== 'production'
            && $isLoopbackRequest
        );

    // Em produção, exigir credenciais configuradas.
    if (
        !$isLocalEnvironment
        && (
            $configuredDbUser === false
            || $configuredDbUser === ''
            || $configuredDbPass === false
        )
    ) {
        error_log(
            'Car Rental: database credentials are not configured.'
        );

        http_response_code(500);
        exit('As credenciais da base de dados não estão configuradas.');
    }

    // Parâmetros da ligação.
    define(
        'DB_HOST',
        getenv('CARRENTAL_DB_HOST') ?: '127.0.0.1'
    );

    // Mantida a porta original; confirma a configuração do teu MySQL.
    define(
        'DB_PORT',
        getenv('CARRENTAL_DB_PORT') ?: '3307'
    );

    define(
        'DB_NAME',
        getenv('CARRENTAL_DB_NAME') ?: 'carrental'
    );

    define(
        'DB_USER',
        ($configuredDbUser !== false && $configuredDbUser !== '')
            ? $configuredDbUser
            : 'root'
    );

    define(
        'DB_PASS',
        $configuredDbPass !== false
            ? $configuredDbPass
            : ''
    );
}

// Criar a ligação PDO se ainda não existir.
if (!isset($dbh) || !($dbh instanceof PDO)) {

    try {

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            DB_HOST,
            DB_PORT,
            DB_NAME
        );

        $dbh = new PDO(
            $dsn,
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false,
            ]
        );

    } catch (PDOException $error) {

        // Registar detalhes técnicos sem os mostrar aos visitantes.
        error_log(
            'Car Rental: database connection failed. '
            . $error->getMessage()
        );

        http_response_code(500);
        exit('Não foi possível conectar à base de dados.');
    }
}
