<?php
session_start();
include_once '../app/config/database.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['community_error'] = 'You must be logged in.';
    header('Location: /FoodFusion/community.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = intval($_POST['post_id'] ?? 0);
    $user_id = $_SESSION['user_id'];

    if ($post_id <= 0) {
        $_SESSION['community_error'] = 'Invalid post ID.';
        header('Location: /FoodFusion/community.php');
        exit;
    }

    $check = $conn->prepare('SELECT user_id FROM community_posts WHERE id = ? AND user_id = ?');
    $check->bind_param('ii', $post_id, $user_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['community_error'] = 'You can only delete your own posts.';
        $check->close();
        header('Location: /FoodFusion/community.php');
        exit;
    }

    $check->close();

    $delete = $conn->prepare('DELETE FROM community_posts WHERE id = ? AND user_id = ?');
    $delete->bind_param('ii', $post_id, $user_id);

    if ($delete->execute()) {
        $_SESSION['community_success'] = 'Post deleted successfully!';
    } else {
        $_SESSION['community_error'] = 'Failed to delete post. Please try again.';
    }

    $delete->close();
    header('Location: /FoodFusion/community.php');
    exit;
}
?>