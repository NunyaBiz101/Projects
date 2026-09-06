<?php
session_start();
include_once '../app/config/database.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['recipe_error'] = 'You must be logged in to post a recipe.';
    header('Location: /FoodFusion/community.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];
    $email = $_SESSION['email'];
    
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $ingredients = trim($_POST['ingredients'] ?? '');
    $instructions = trim($_POST['instructions'] ?? '');
    $cook_time = intval($_POST['cook_time'] ?? 0);
    $difficulty_level = trim($_POST['difficulty_level'] ?? 'Medium');
    $image_path = null;
    $errors = [];

    if (empty($title)) $errors[] = 'Recipe title is required.';
    if (empty($ingredients)) $errors[] = 'Ingredients are required.';
    if (empty($instructions)) $errors[] = 'Instructions are required.';

    if (!empty($_FILES['cookbook_image']['name'])) {
        $file = $_FILES['cookbook_image'];
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = basename($file['name']);
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $errors[] = 'Only JPG, PNG, and GIF images are allowed.';
        } elseif ($file['size'] > 5000000) { 
            $errors[] = 'Image must be less than 5MB.';
        } else {
            $upload_dir = '../pics/';
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $new_filename = 'cookbook_' . time() . '.' . $ext;
            $upload_path = $upload_dir . $new_filename;

            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                $image_path = 'pics/' . $new_filename;
            } else {
                $errors[] = 'Failed to upload image. Check folder permissions.';
            }
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare('INSERT INTO recipes (user_id, username, email, title, category, ingredients, instructions, cook_time, difficulty_level, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('issssssiss', $user_id, $username, $email, $title, $category, $ingredients, $instructions, $cook_time, $difficulty_level, $image_path);
        
        if ($stmt->execute()) {
            $stmt->close();
            header('Location: /FoodFusion/community.php?success=1');
            exit;
        } else {
            $errors[] = 'Failed to submit recipe. Please try again.';
        }
    }

    $_SESSION['cookbook_errors'] = $errors;
    $_SESSION['cookbook_old'] = compact('title', 'category', 'ingredients', 'instructions', 'cook_time', 'difficulty_level');
    header('Location: /FoodFusion/community.php');
    exit;
}
?>