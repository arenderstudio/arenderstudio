<?php

require '/Applications/XAMPP/xamppfiles/htdocs/src/Exception.php';
require '/Applications/XAMPP/xamppfiles/htdocs/src/PHPMailer.php';
require '/Applications/XAMPP/xamppfiles/htdocs/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


header('Content-Type: application/json');

// Function to sanitize input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Optional: CSRF token validation (uncomment and implement if needed)
    /*
    session_start();
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token']);
        exit;
    }
    */

    // Get and sanitize form inputs
    $name = isset($_POST['name']) ? sanitize($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize($_POST['email']) : '';
    $message = isset($_POST['message']) ? sanitize($_POST['message']) : '';

    // Validate inputs
    $errors = [];
    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required';
    }
    if (empty($message)) {
        $errors[] = 'Message is required';
    }

    if (empty($errors)) {
        try {
            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'aangadranjitsingh@gmail.com';
            $mail->Password = 'rgzn qxbs oibz hrgo';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom($email, $name);
            $mail->addAddress('aangadranjitsingh@gmail.com');
            $mail->Subject = 'New Contact Form Submission from A Render Studio';
            $mail->Body    = "Name: $name\nEmail: $email\nMessage:\n$message";

            $mail->send();
            http_response_code(200);
            echo json_encode(['status' => 'success', 'message' => 'Thank you! Your message has been sent.']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Mailer Error: ' . $mail->ErrorInfo]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => implode(', ', $errors)]);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
?>
