
<?php

/*
|--------------------------------------------------------------------------
| Configuração central da base de dados — Car Rental
|--------------------------------------------------------------------------
| Backend: PHP + PDO
| Alojamento: Render
| Base de dados: MySQL na Aiven
|--------------------------------------------------------------------------
*/

if (!defined('DB_HOST')) {

    $environment = strtolower(
        trim((string) (getenv('CARRENTAL_ENV') ?: 'development'))
    );

    $configuredDbUser = getenv('CARRENTAL_DB_USER');
    $configuredDbPass = getenv('CARRENTAL_DB_PASS');

    $isProduction = $environment === 'production';

    // Exigir credenciais em produção.
    if (
        $isProduction
        && (
            $configuredDbUser === false
            || $configuredDbUser === ''
            || $configuredDbPass === false
            || $configuredDbPass === ''
        )
    ) {
        error_log('Car Rental: database credentials are not configured.');

        http_response_code(500);
        exit('As credenciais da base de dados não estão configuradas.');
    }

    define(
        'DB_HOST',
        getenv('CARRENTAL_DB_HOST') ?: '127.0.0.1'
    );

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

// Criar a ligação à base de dados.
if (!isset($dbh) || !($dbh instanceof PDO)) {

    try {

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            DB_HOST,
            DB_PORT,
            DB_NAME
        );

        $pdoOptions = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false,
        ];

        // Configurar SSL na Aiven em produção.
        if ($isProduction) {

            $caFile = getenv('CARRENTAL_DB_SSL_CA');

            if (
                $caFile === false
                || $caFile === ''
                || !is_file($caFile)
                || !is_readable($caFile)
            ) {
                error_log(
                    'Car Rental: SSL CA certificate is missing or unreadable.'
                );

                http_response_code(500);
                exit('A configuração segura da base de dados está incompleta.');
            }

            if (!defined('PDO::MYSQL_ATTR_SSL_CA')) {
                error_log('Car Rental: PDO MySQL SSL support is unavailable.');

                http_response_code(500);
                exit('O suporte SSL da base de dados não está disponível.');
            }

            // Definir o certificado CA para a ligação MySQL.
            $pdoOptions[PDO::MYSQL_ATTR_SSL_CA] = $caFile;
        }

        $dbh = new PDO(
            $dsn,
            DB_USER,
            DB_PASS,
            $pdoOptions
        );

    } catch (PDOException $error) {

        // Registar o erro no servidor sem expor detalhes ao visitante.
        error_log(
            'Car Rental: database connection failed. '
            . $error->getMessage()
        );

        http_response_code(500);
        exit('Não foi possível conectar à base de dados.');
    }
}
