<?php
include_once '../app/config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $errors = [];

    if (empty($name)) $errors[] = 'Name is required.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if (empty($subject)) $errors[] = 'Subject is required.';
    if (empty($message)) $errors[] = 'Message is required.';

    if (empty($errors)) {
        $stmt = $conn->prepare('INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $name, $email, $subject, $message);
        $stmt->execute();
        $stmt->close();

        $to = 'camryncelineram@gmail.com'; 
        $headers = "From: $email\r\nReply-To: $email\r\nContent-Type: text/plain; charset=UTF-8";
        $mailBody = "Name: $name\nEmail: $email\nSubject: $subject\nMessage:\n$message";
        mail($to, "Contact Form: $subject", $mailBody, $headers);

        header('Location: ../home.php?section=contact&success=1');
        exit;
    } else {
        session_start();
        $_SESSION['contact_errors'] = $errors;
        $_SESSION['contact_old'] = compact('name', 'email', 'subject', 'message');
        header('Location: ../home.php?section=contact');
        exit;
    }
}
