<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Get (or create) the CSRF token stored in the session.
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Echo a hidden input field with the CSRF token.
 * Use inside HTML forms.
 */
function csrf_field(): void
{
    $token = csrf_token();
    echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

/**
 * Validate the CSRF token on POST requests.
 * Throws an Exception if invalid.
 */
function csrf_validate(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    $sessionToken = $_SESSION['csrf_token'] ?? '';
    $postedToken  = $_POST['csrf_token']     ?? '';

    if (!$sessionToken || !$postedToken || !hash_equals($sessionToken, $postedToken)) {
        throw new Exception('Invalid CSRF token.');
    }
}
