<?php
session_start();
require_once '../app/config/database.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username_or_email = trim($_POST['username_or_email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $errors = [];

    if (empty($username_or_email)) $errors[] = "Username or email is required.";
    if (empty($password)) $errors[] = "Password is required.";

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id, username, email, password, first_name, last_name FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username_or_email, $username_or_email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                
                header('Location: ../home.php?login=success');
                exit;
            } else {
                $errors[] = "Invalid password.";
            }
        } else {
            $errors[] = "No account found with that username or email.";
        }
        $stmt->close();
    }

    $_SESSION['login_errors'] = $errors;
    $_SESSION['login_old'] = ['username_or_email' => $username_or_email];
    header('Location: ../home.php?login=failed');
    exit;
}
?>