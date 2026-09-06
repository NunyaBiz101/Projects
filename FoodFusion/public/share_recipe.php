<?php
session_start();
include_once '../app/config/database.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['share_error'] = 'You must be logged in to share recipes.';
    header('Location: /FoodFusion/recipe.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipe_id = intval($_POST['recipe_id'] ?? 0);
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];
    $email = $_SESSION['email'];

    if ($recipe_id <= 0) {
        $_SESSION['share_error'] = 'Invalid recipe ID.';
        header('Location: /FoodFusion/recipe.php');
        exit;
    }

    // original recipe details
    $stmt = $conn->prepare('SELECT * FROM recipes WHERE id = ? AND is_shared = FALSE');
    $stmt->bind_param('i', $recipe_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['share_error'] = 'Recipe not found or already shared.';
        header('Location: /FoodFusion/recipe.php');
        exit;
    }

    $original_recipe = $result->fetch_assoc();
    $stmt->close();

    $check = $conn->prepare('SELECT id FROM recipes WHERE original_recipe_id = ? AND shared_by_user_id = ? AND is_shared = TRUE');
    $check->bind_param('ii', $recipe_id, $user_id);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result->num_rows > 0) {
        $_SESSION['share_error'] = 'You have already shared this recipe to the community.';
        $check->close();
        header('Location: /FoodFusion/recipe.php');
        exit;
    }
    $check->close();

    $share_stmt = $conn->prepare('INSERT INTO recipes (user_id, username, email, title, category, ingredients, instructions, cook_time, difficulty_level, image_path, visibility, is_shared, original_recipe_id, shared_by_user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "public", TRUE, ?, ?)');
    
    $share_stmt->bind_param(
        'issssssissii',
        $original_recipe['user_id'],
        $original_recipe['username'],
        $original_recipe['email'],
        $original_recipe['title'],
        $original_recipe['category'],
        $original_recipe['ingredients'],
        $original_recipe['instructions'],
        $original_recipe['cook_time'],
        $original_recipe['difficulty_level'],
        $original_recipe['image_path'],
        $recipe_id,
        $user_id
    );

    if ($share_stmt->execute()) {
        $_SESSION['share_success'] = 'Recipe shared to community successfully!';
        $share_stmt->close();
        header('Location: /FoodFusion/community.php');
        exit;
    } else {
        $_SESSION['share_error'] = 'Failed to share recipe. Please try again.';
        $share_stmt->close();
        header('Location: /FoodFusion/recipe.php');
        exit;
    }
}
?>