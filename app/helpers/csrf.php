<?php
// CSRF stands for "Cross-Site Request Forgery".
// These functions help protect our forms from being submitted by malicious websites.

// This function creates or gets the CSRF token stored in the session.
// A CSRF token is a random string that we put in our forms to verify
// that the form was actually submitted from our website.
function csrf_token()
{
    // Start the session if it hasn't been started yet
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // If there's no token yet, create a new random one
    if (empty($_SESSION['_csrf_token'])) {
        // Generate 32 random bytes and convert to a hex string
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['_csrf_token'];
}

// This function creates a hidden input field with the CSRF token.
// We put this inside our HTML forms to include the token.
function csrf_field()
{
    $token = csrf_token();
    // Use htmlspecialchars to prevent XSS attacks
    $safeToken = htmlspecialchars($token, ENT_QUOTES, 'UTF-8');
    return '<input type="hidden" name="_csrf_token" value="' . $safeToken . '">';
}

// This function checks if the CSRF token from the form matches
// the one stored in the session. If it doesn't match, stop everything.
function csrf_verify_or_die()
{
    // Start the session if it hasn't been started yet
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Get the token from the session
    $sessionToken = '';
    if (isset($_SESSION['_csrf_token'])) {
        $sessionToken = $_SESSION['_csrf_token'];
    }

    // Get the token that was submitted with the form
    $formToken = '';
    if (isset($_POST['_csrf_token'])) {
        $formToken = $_POST['_csrf_token'];
    }

    // Check if both tokens exist and match
    // hash_equals() is used instead of == to prevent timing attacks
    if ($sessionToken === '' || $formToken === '' || !hash_equals($sessionToken, $formToken)) {
        http_response_code(419);
        exit('419 Page Expired (Invalid CSRF token). Please go back and retry.');
    }
}