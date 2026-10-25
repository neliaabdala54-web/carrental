<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');

    $scriptDirectory = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
    $applicationPath = preg_replace('~/(?:admin/)?api$~', '', $scriptDirectory);
    $applicationPath = preg_replace('~/admin$~', '', $applicationPath);
    if (!$applicationPath || $applicationPath === '.') {
        $applicationPath = '/';
    } elseif (substr($applicationPath, -1) !== '/') {
        $applicationPath .= '/';
    }

    $isSecure = !empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off';
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => $applicationPath,
        'secure' => $isSecure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function isSameOriginMutation()
{
    $fetchSite = strtolower($_SERVER['HTTP_SEC_FETCH_SITE'] ?? '');
    if ($fetchSite !== '' && $fetchSite !== 'same-origin') {
        return false;
    }

    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    if ($origin === '') {
        return true;
    }

    $isSecure = !empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off';
    $requestUrl = ($isSecure ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? '');
    $originParts = parse_url($origin);
    $requestParts = parse_url($requestUrl);
    if (!$originParts || !$requestParts || empty($originParts['host']) || empty($requestParts['host'])) {
        return false;
    }

    $originPort = $originParts['port'] ?? (($originParts['scheme'] ?? '') === 'https' ? 443 : 80);
    $requestPort = $requestParts['port'] ?? (($requestParts['scheme'] ?? '') === 'https' ? 443 : 80);
    return strtolower($originParts['host']) === strtolower($requestParts['host'])
        && $originPort === $requestPort
        && strtolower($originParts['scheme'] ?? '') === strtolower($requestParts['scheme'] ?? '');
}

function rejectCrossOriginMutation()
{
    if (!isSameOriginMutation()) {
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'message' => 'Origem da solicitação não autorizada.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

function destroyApplicationSession()
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return;
    }
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 42000,
            'path' => $params['path'],
            'domain' => $params['domain'],
            'secure' => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => $params['samesite'] ?? 'Lax',
        ]);
    }
    session_destroy();
}