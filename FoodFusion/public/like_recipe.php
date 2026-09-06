<?php
session_start();
include_once '../app/config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to like recipes.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipe_id = intval($_POST['recipe_id'] ?? 0);
    $user_id = $_SESSION['user_id'];

    if ($recipe_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid recipe ID.']);
        exit;
    }

    $check = $conn->prepare('SELECT id FROM recipe_likes WHERE recipe_id = ? AND user_id = ?');
    $check->bind_param('ii', $recipe_id, $user_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $delete = $conn->prepare('DELETE FROM recipe_likes WHERE recipe_id = ? AND user_id = ?');
        $delete->bind_param('ii', $recipe_id, $user_id);
        $delete->execute();
        $delete->close();
        $liked = false;
    } else {
        $insert = $conn->prepare('INSERT INTO recipe_likes (recipe_id, user_id) VALUES (?, ?)');
        $insert->bind_param('ii', $recipe_id, $user_id);
        $insert->execute();
        $insert->close();
        $liked = true;
    }

    $check->close();

    $count_stmt = $conn->prepare('SELECT COUNT(*) as count FROM recipe_likes WHERE recipe_id = ?');
    $count_stmt->bind_param('i', $recipe_id);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $count_row = $count_result->fetch_assoc();
    $like_count = $count_row['count'];
    $count_stmt->close();

    echo json_encode([
        'success' => true, 
        'liked' => $liked, 
        'like_count' => $like_count
    ]);
}
?>