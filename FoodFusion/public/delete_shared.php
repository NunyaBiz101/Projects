<?php
session_start();
include_once '../app/config/database.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['community_error'] = 'You must be logged in.';
    header('Location: /FoodFusion/community.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shared_id = intval($_POST['shared_id'] ?? 0);
    $user_id = $_SESSION['user_id'];

    if ($shared_id <= 0) {
        $_SESSION['community_error'] = 'Invalid ID.';
        header('Location: /FoodFusion/community.php');
        exit;
    }

    $check = $conn->prepare('SELECT shared_by_user_id FROM recipes WHERE id = ? AND is_shared = TRUE');
    $check->bind_param('i', $shared_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['community_error'] = 'Shared item not found.';
        $check->close();
        header('Location: /FoodFusion/community.php');
        exit;
    }

    $shared = $result->fetch_assoc();
    $check->close();

    if ($shared['shared_by_user_id'] != $user_id) {
        $_SESSION['community_error'] = 'You can only delete items you shared.';
        header('Location: /FoodFusion/community.php');
        exit;
    }

    $delete = $conn->prepare('DELETE FROM recipes WHERE id = ? AND shared_by_user_id = ? AND is_shared = TRUE');
    $delete->bind_param('ii', $shared_id, $user_id);

    if ($delete->execute()) {
        $_SESSION['community_success'] = 'Removed from community successfully!';
    } else {
        $_SESSION['community_error'] = 'Failed to remove. Please try again.';
    }

    $delete->close();
    header('Location: /FoodFusion/community.php');
    exit;
}
?>