<?php
session_start();
require_once '../app/config/database.php';
require_once '../app/models/user.php';

$UserModel = new User($conn);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $errors = [];

    if (empty($first_name)) $errors[] = "First name is required.";
    if (empty($last_name)) $errors[] = "Last name is required.";
    if (empty($username)) $errors[] = "Username is required.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if (empty($errors)) {
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check_stmt->bind_param("ss", $username, $email);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            $errors[] = "Username or email already exists.";
        }
        $check_stmt->close();
    }

    if (empty($errors)) {
        if ($UserModel->create($first_name, $last_name, $username, $password, $email)) {
            $stmt = $conn->prepare("SELECT id, username, email, first_name, last_name FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            
            echo "<!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Account Created Successfully</title>
                <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css'>
                <style>
                    body { 
                        background: linear-gradient(135deg, #865029 0%, #6F4E37 100%);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        min-height: 100vh;
                        margin: 0;
                        font-family: Arial, sans-serif;
                    }
                    .success-container {
                        background: white;
                        padding: 40px;
                        border-radius: 12px;
                        box-shadow: 0 8px 30px rgba(0,0,0,0.3);
                        text-align: center;
                        max-width: 500px;
                    }
                    .success-icon {
                        font-size: 64px;
                        color: #27ae60;
                        margin-bottom: 20px;
                    }
                    h1 { 
                        color: #865029;
                        font-size: 28px;
                        margin-bottom: 15px;
                    }
                    p { 
                        color: #555;
                        font-size: 16px;
                        margin-bottom: 30px;
                    }
                    .btn-custom {
                        background-color: #865029;
                        color: white;
                        padding: 12px 30px;
                        border: none;
                        border-radius: 6px;
                        font-size: 16px;
                        text-decoration: none;
                        display: inline-block;
                        transition: all 0.3s ease;
                    }
                    .btn-custom:hover {
                        background-color: #6F4E37;
                        color: white;
                        text-decoration: none;
                    }
                </style>
            </head>
            <body>
                <div class='success-container'>
                    <div class='success-icon'>✓</div>
                    <h1>Account Created Successfully!</h1>
                    <p>Welcome to FoodFusion, <strong>" . htmlspecialchars($first_name) . "</strong>! You are now logged in.</p>
                    <p>You can now share recipes and join our community.</p>
                    <a href='../home.php' class='btn-custom'>Go to Home Page</a>
                </div>
            </body>
            </html>";
            exit;
        } else {
            $errors[] = "Error during registration. Please try again.";
        }
    }

    if (!empty($errors)) {
        $_SESSION['signup_errors'] = $errors;
        $_SESSION['signup_old'] = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'username' => $username,
            'email' => $email
        ];
        header('Location: ../home.php?signup=failed');
        exit;
    }
}
?>