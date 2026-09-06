<?php
session_start();
include_once '../app/config/database.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['delete_error'] = 'You must be logged in to delete recipes.';
    header('Location: /FoodFusion/recipe.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipe_id = intval($_POST['recipe_id'] ?? 0);
    $user_id = $_SESSION['user_id'];

    if ($recipe_id <= 0) {
        $_SESSION['delete_error'] = 'Invalid recipe ID.';
        header('Location: /FoodFusion/recipe.php');
        exit;
    }

    $check = $conn->prepare('SELECT user_id, image_path FROM recipes WHERE id = ? AND user_id = ? AND is_shared = FALSE');
    $check->bind_param('ii', $recipe_id, $user_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['delete_error'] = 'You can only delete your own recipes.';
        $check->close();
        header('Location: /FoodFusion/recipe.php');
        exit;
    }

    $recipe = $result->fetch_assoc();
    $check->close();

    // Delete the image file if it exists
    if (!empty($recipe['image_path']) && file_exists('../' . $recipe['image_path'])) {
        unlink('../' . $recipe['image_path']);
    }

    // Delete the recipe (this will cascade delete likes and shares)
    $delete = $conn->prepare('DELETE FROM recipes WHERE id = ? AND user_id = ?');
    $delete->bind_param('ii', $recipe_id, $user_id);

    if ($delete->execute()) {
        $_SESSION['delete_success'] = 'Recipe deleted successfully!';
    } else {
        $_SESSION['delete_error'] = 'Failed to delete recipe. Please try again.';
    }

    $delete->close();
    header('Location: /FoodFusion/recipe.php');
    exit;
}
?>