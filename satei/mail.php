<?php
/**
 * Mail Processing Script
 * 
 * This file is kept for backward compatibility but redirects to the new system
 */

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Start session securely
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);
session_start();

// Redirect to the new form handler
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Process through the new system
    require_once __DIR__ . '/../src/classes/InquiryHandler.php';
    
    try {
        $handler = new InquiryHandler();
        $result = $handler->processInquiry($_POST, $_SESSION);
        
        if ($result['success']) {
            header('Location: success.html');
            exit;
        } else {
            // Redirect back to form with error
            $_SESSION['form_error'] = $result['message'];
            header('Location: index.php');
            exit;
        }
        
    } catch (Exception $e) {
        error_log("Mail processing error: " . $e->getMessage());
        $_SESSION['form_error'] = 'システムエラーが発生しました。しばらく後にもう一度お試しください。';
        header('Location: index.php');
        exit;
    }
} else {
    // Redirect to form for GET requests
    header('Location: index.php');
    exit;
}