<?php
session_start();
include_once '../app/config/database.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['post_error'] = 'You must be logged in to create a post.';
    header('Location: /FoodFusion/community.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];
    
    $title = trim($_POST['post_title'] ?? '');
    $content = trim($_POST['post_content'] ?? '');
    $post_type = trim($_POST['post_type'] ?? 'tip');
    $errors = [];

    if (empty($title)) $errors[] = 'Post title is required.';
    if (empty($content)) $errors[] = 'Post content is required.';

    if (empty($errors)) {
        $stmt = $conn->prepare('INSERT INTO community_posts (user_id, username, title, content, post_type) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('issss', $user_id, $username, $title, $content, $post_type);
        
        if ($stmt->execute()) {
            $stmt->close();
            $_SESSION['post_success'] = 'Your post has been published!';
            header('Location: /FoodFusion/community.php?success=1');
            exit;
        } else {
            $errors[] = 'Failed to create post. Please try again.';
        }
    }

    $_SESSION['post_errors'] = $errors;
    $_SESSION['post_old'] = compact('title', 'content', 'post_type');
    header('Location: /FoodFusion/community.php');
    exit;
}
?>