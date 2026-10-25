<?php
require_once __DIR__ . '/includes/session.php';
destroyApplicationSession();
header('Location: index.html');
exit;

