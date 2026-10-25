<?php
function verifyPasswordAndGetUpgrade($plainPassword, $storedPassword)
{
    if (password_verify($plainPassword, $storedPassword)) {
        return password_needs_rehash($storedPassword, PASSWORD_DEFAULT)
            ? password_hash($plainPassword, PASSWORD_DEFAULT)
            : null;
    }

    if (preg_match('/^[a-f0-9]{32}$/i', $storedPassword)
        && hash_equals(strtolower($storedPassword), md5($plainPassword))) {
        return password_hash($plainPassword, PASSWORD_DEFAULT);
    }

    return false;
}