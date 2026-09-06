<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include_once '../app/config/database.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['recipe_error'] = 'You must be logged in to post a recipe.';
    header('Location: /FoodFusion/recipe.php');
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
    $visibility = trim($_POST['visibility'] ?? 'private');
    $image_path = null;
    $errors = [];

    if (empty($title)) $errors[] = 'Recipe title is required.';
    if (empty($ingredients)) $errors[] = 'Ingredients are required.';
    if (empty($instructions)) $errors[] = 'Instructions are required.';

    if (!empty($_FILES['recipe_image']['name'])) {
        $file = $_FILES['recipe_image'];
        
        error_log("File upload debug:");
        error_log("Name: " . $file['name']);
        error_log("Size: " . $file['size']);
        error_log("Error: " . $file['error']);
        error_log("Tmp: " . $file['tmp_name']);
        
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = basename($file['name']);
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $upload_errors = [
                UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize in php.ini',
                UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE in HTML form',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the upload'
            ];
            $errors[] = 'Upload error: ' . ($upload_errors[$file['error']] ?? 'Unknown error');
        } elseif (!in_array($ext, $allowed)) {
            $errors[] = 'Only JPG, PNG, and GIF images are allowed.';
        } elseif ($file['size'] > 10000000) { 
            $errors[] = 'Image must be less than 10MB.';
        } else {
            $upload_dir = '../pics/';
            
            if (!is_dir($upload_dir)) {
                error_log("Directory doesn't exist, creating...");
                mkdir($upload_dir, 0777, true);
                chmod($upload_dir, 0777);
            }
            
            if (!is_writable($upload_dir)) {
                error_log("Directory not writable, attempting to fix...");
                chmod($upload_dir, 0777);
            }
            
            $new_filename = 'recipe_' . time() . '_' . $user_id . '.' . $ext;
            $upload_path = $upload_dir . $new_filename;
            
            error_log("Upload path: " . $upload_path);
            error_log("Real path: " . realpath(dirname($upload_path)));
            error_log("Directory writable: " . (is_writable($upload_dir) ? 'YES' : 'NO'));

            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                $image_path = 'pics/' . $new_filename;
                chmod($upload_path, 0644);
                error_log("Upload successful: " . $image_path);
            } else {
                $last_error = error_get_last();
                $error_msg = 'Failed to upload image.';
                $error_msg .= ' Path: ' . $upload_path;
                $error_msg .= ' | Temp file exists: ' . (file_exists($file['tmp_name']) ? 'YES' : 'NO');
                $error_msg .= ' | Dir writable: ' . (is_writable($upload_dir) ? 'YES' : 'NO');
                if ($last_error) {
                    $error_msg .= ' | PHP Error: ' . $last_error['message'];
                }
                $errors[] = $error_msg;
                error_log($error_msg);
            }
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare('INSERT INTO recipes (user_id, username, email, title, category, ingredients, instructions, cook_time, difficulty_level, image_path, visibility, is_shared) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, FALSE)');
        $stmt->bind_param('issssssisss', $user_id, $username, $email, $title, $category, $ingredients, $instructions, $cook_time, $difficulty_level, $image_path, $visibility);
        
        if ($stmt->execute()) {
            $stmt->close();
            header('Location: /FoodFusion/recipe.php?success=1');
            exit;
        } else {
            $errors[] = 'Failed to submit recipe. Please try again.';
        }
    }

    $_SESSION['recipe_errors'] = $errors;
    $_SESSION['recipe_old'] = compact('title', 'category', 'ingredients', 'instructions', 'cook_time', 'difficulty_level', 'visibility');
    header('Location: /FoodFusion/recipe.php');
    exit;
}
?>