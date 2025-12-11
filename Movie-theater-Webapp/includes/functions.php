<?php

/**
 * Redirect to a relative URL and stop.
 */
function redirect(string $path): void
{
    header("Location: {$path}");
    exit;
}

/**
 * Get an integer from $_GET safely, or null if invalid/missing.
 */
function get_int_from_get(string $key): ?int
{
    if (!isset($_GET[$key])) {
        return null;
    }
    if (!ctype_digit($_GET[$key])) {  // only allow digits
        return null;
    }
    return (int) $_GET[$key];
}

/**
 * Get a date (YYYY-MM-DD) from $_GET safely, or null if invalid/missing.
 */
function get_date_from_get(string $key): ?string
{
    if (!isset($_GET[$key])) {
        return null;
    }
    $value = $_GET[$key];
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
        return null;
    }
    return $value;
}
