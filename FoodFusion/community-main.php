<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once 'app/config/database.php';

$isLoggedIn = isset($_SESSION['user_id']);
$current_user_id = $_SESSION['user_id'] ?? 0;

$post_errors = $_SESSION['post_errors'] ?? [];
$post_old = $_SESSION['post_old'] ?? [];
$post_success = $_SESSION['post_success'] ?? null;
$community_error = $_SESSION['community_error'] ?? null;
$community_success = $_SESSION['community_success'] ?? null;
unset($_SESSION['post_errors'], $_SESSION['post_old'], $_SESSION['post_success'], $_SESSION['community_error'], $_SESSION['community_success']);

$shared_recipes = [];
$community_posts = [];

$recipe_query = "SELECT r.*, 
    u.username as sharer_username,
    u.id as sharer_id,
    (SELECT COUNT(*) FROM recipe_likes WHERE recipe_id = r.id) as like_count,
    (SELECT COUNT(*) FROM recipe_likes WHERE recipe_id = r.id AND user_id = ?) as user_liked
    FROM recipes r
    JOIN users u ON r.shared_by_user_id = u.id
    WHERE r.is_shared = TRUE
    ORDER BY r.created_at DESC";

$stmt = $conn->prepare($recipe_query);
$stmt->bind_param('i', $current_user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $shared_recipes[] = $row;
}
$stmt->close();

$post_query = "SELECT p.*, 
    (SELECT COUNT(*) FROM post_likes WHERE post_id = p.id) as like_count,
    (SELECT COUNT(*) FROM post_likes WHERE post_id = p.id AND user_id = ?) as user_liked
    FROM community_posts p
    ORDER BY p.created_at DESC";

$stmt2 = $conn->prepare($post_query);
$stmt2->bind_param('i', $current_user_id);
$stmt2->execute();
$result2 = $stmt2->get_result();
while ($row = $result2->fetch_assoc()) {
    $community_posts[] = $row;
}
$stmt2->close();
?>

<main>
    <div class="community-header">
        <h1 class="cookbook-title">Community</h1>
        <p class="cookbook-subtitle">Share tips, recipes, and connect with fellow food enthusiasts!</p>
        
        <?php if ($isLoggedIn): ?>
            <div class="community-actions">
                <button type="button" class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#postModal">
                    <i class="bi bi-pencil"></i> Create Post
                </button>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($post_success): ?>
        <div class="container">
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <?= htmlspecialchars($post_success) ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($community_error): ?>
        <div class="container">
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <?= htmlspecialchars($community_error) ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($community_success): ?>
        <div class="container">
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <?= htmlspecialchars($community_success) ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="modal fade" id="postModal" tabindex="-1" aria-labelledby="postModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title" id="postModalLabel">Create a Post</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if (!$isLoggedIn): ?>
                        <div class="alert alert-warning">
                            <p>You must be logged in to create a post.</p>
                        </div>
                    <?php else: ?>
                        <?php if (!empty($post_errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($post_errors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form action="public/post_handler.php" method="post" class="post-form">
                            <div class="mb-3">
                                <label for="post_title" class="form-label">Title *</label>
                                <input type="text" id="post_title" name="post_title" class="form-control" required 
                                       placeholder="What's your post about?" 
                                       value="<?= htmlspecialchars($post_old['title'] ?? '') ?>">
                            </div>

                            <div class="mb-3">
                                <label for="post_type" class="form-label">Post Type</label>
                                <select id="post_type" name="post_type" class="form-control">
                                    <option value="tip" <?= ($post_old['post_type'] ?? 'tip') === 'tip' ? 'selected' : '' ?>>Cooking Tip</option>
                                    <option value="question" <?= ($post_old['post_type'] ?? '') === 'question' ? 'selected' : '' ?>>Question</option>
                                    <option value="discussion" <?= ($post_old['post_type'] ?? '') === 'discussion' ? 'selected' : '' ?>>Discussion</option>
                                    <option value="story" <?= ($post_old['post_type'] ?? '') === 'story' ? 'selected' : '' ?>>Story</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="post_content" class="form-label">Content *</label>
                                <textarea id="post_content" name="post_content" class="form-control" rows="6" required 
                                          placeholder="Share your thoughts, tips, or ask a question..."><?= htmlspecialchars($post_old['content'] ?? '') ?></textarea>
                            </div>

                            <div class="text-end">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-custom">Publish Post</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="community-feed-centered">
        <div class="feed-container">
            <?php if (!empty($community_posts)): ?>
                <div class="feed-section">
                    <h2 class="feed-section-title">💬 Community Discussions</h2>
                    <?php foreach ($community_posts as $post): ?>
                        <div class="community-card post-card">
                            <div class="post-header">
                                <div class="post-user">
                                    <span class="user-avatar">👤</span>
                                    <div>
                                        <strong><?= htmlspecialchars($post['username']) ?></strong>
                                        <span class="post-type-badge"><?= htmlspecialchars(ucfirst($post['post_type'])) ?></span>
                                    </div>
                                </div>
                                <div class="post-header-right">
                                    <span class="post-time"><?= date('M j, Y', strtotime($post['created_at'])) ?></span>
                                    <?php if ($isLoggedIn && $post['user_id'] == $current_user_id): ?>
                                        <form method="post" action="public/delete_post.php" style="display: inline;" 
                                              onsubmit="return confirm('Are you sure you want to delete this post?');">
                                            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                            <button type="submit" class="btn-delete-mini" title="Delete post">🗑️</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="post-content">
                                <h3 class="post-title"><?= htmlspecialchars($post['title']) ?></h3>
                                <p class="post-text"><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                            </div>
                            <div class="post-footer">
                                <?php if ($isLoggedIn): ?>
                                    <button class="btn-social-minimal <?= $post['user_liked'] > 0 ? 'liked' : '' ?>" 
                                            onclick="likePost(<?= $post['id'] ?>)"
                                            data-post-id="<?= $post['id'] ?>">
                                        ❤️ <span class="like-count"><?= $post['like_count'] ?></span>
                                    </button>
                                <?php else: ?>
                                    <span class="like-display">❤️ <?= $post['like_count'] ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($shared_recipes)): ?>
                <div class="feed-section">
                    <h2 class="feed-section-title">🍽️ Shared Recipes</h2>
                    <?php foreach ($shared_recipes as $recipe): ?>
                        <div class="community-card recipe-card">
                            <div class="share-header">
                                <span class="user-avatar">👤</span>
                                <div class="share-info">
                                    <strong><?= htmlspecialchars($recipe['sharer_username']) ?></strong> shared 
                                    <strong><?= htmlspecialchars($recipe['username']) ?></strong>'s recipe
                                    <br>
                                    <span class="post-time"><?= date('M j, Y', strtotime($recipe['created_at'])) ?></span>
                                </div>
                                <?php if ($isLoggedIn && $recipe['sharer_id'] == $current_user_id): ?>
                                    <form method="post" action="public/delete_shared.php" style="margin-left: auto;" 
                                          onsubmit="return confirm('Remove this recipe from the community feed?');">
                                        <input type="hidden" name="shared_id" value="<?= $recipe['id'] ?>">
                                        <button type="submit" class="btn-delete-mini" title="Remove from community">🗑️</button>
                                    </form>
                                <?php endif; ?>
                            </div>

                            <div class="shared-recipe-content">
                                <div class="recipe-image-container">
                                    <?php if (!empty($recipe['image_path'])): ?>
                                        <img src="<?= htmlspecialchars($recipe['image_path']) ?>" alt="<?= htmlspecialchars($recipe['title']) ?>" class="recipe-image">
                                    <?php else: ?>
                                        <img src="pics/default-recipe.jpg" alt="No image" class="recipe-image">
                                    <?php endif; ?>
                                    <?php if (!empty($recipe['category'])): ?>
                                        <span class="recipe-category-badge"><?= htmlspecialchars($recipe['category']) ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="recipe-content">
                                    <h3 class="recipe-title"><?= htmlspecialchars($recipe['title']) ?></h3>
                                    <p class="recipe-author">Original recipe by <?= htmlspecialchars($recipe['username']) ?></p>
                                    
                                    <div class="recipe-info">
                                        <?php if (!empty($recipe['cook_time'])): ?>
                                            <span class="info-item">⏱️ <?= htmlspecialchars($recipe['cook_time']) ?> mins</span>
                                        <?php endif; ?>
                                        <?php if (!empty($recipe['difficulty_level'])): ?>
                                            <span class="info-item">📊 <?= htmlspecialchars($recipe['difficulty_level']) ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <button class="btn btn-sm btn-outline" onclick="toggleRecipe(<?= $recipe['id'] ?>)">
                                        View Full Recipe
                                    </button>

                                    <div id="recipe-details-<?= $recipe['id'] ?>" class="recipe-details" style="display: none;">
                                        <div class="recipe-section">
                                            <h4>Ingredients</h4>
                                            <pre class="recipe-text"><?= htmlspecialchars($recipe['ingredients']) ?></pre>
                                        </div>

                                        <div class="recipe-section">
                                            <h4>Instructions</h4>
                                            <pre class="recipe-text"><?= htmlspecialchars($recipe['instructions']) ?></pre>
                                        </div>
                                    </div>

                                    <div class="post-footer">
                                        <?php if ($isLoggedIn): ?>
                                            <button class="btn-social-minimal <?= $recipe['user_liked'] > 0 ? 'liked' : '' ?>" 
                                                    onclick="likeRecipe(<?= $recipe['id'] ?>)"
                                                    data-recipe-id="<?= $recipe['id'] ?>">
                                                ❤️ <span class="like-count"><?= $recipe['like_count'] ?></span>
                                            </button>
                                        <?php else: ?>
                                            <span class="like-display">❤️ <?= $recipe['like_count'] ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (empty($community_posts) && empty($shared_recipes)): ?>
                <div class="empty-state">
                    <p>🌟 The community is just getting started!</p>
                    <?php if ($isLoggedIn): ?>
                        <p>Be the first to share something!</p>
                        <button type="button" class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#postModal">Create Post</button>
                    <?php else: ?>
                        <p>Please <a href="home.php" data-bs-toggle="modal" data-bs-target="#loginModal">login</a> to post and interact with content.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<script>
function toggleRecipe(id) {
    var details = $('#recipe-details-' + id);
    details.slideToggle(300);
}

function likeRecipe(recipeId) {
    $.ajax({
        url: 'public/like_recipe.php',
        method: 'POST',
        data: { recipe_id: recipeId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const btn = $('[data-recipe-id="' + recipeId + '"]');
                btn.find('.like-count').text(response.like_count);
                
                if (response.liked) {
                    btn.addClass('liked');
                } else {
                    btn.removeClass('liked');
                }
            } else {
                alert(response.message || 'Failed to like recipe');
            }
        }
    });
}

function likePost(postId) {
    $.ajax({
        url: 'public/like_post.php',
        method: 'POST',
        data: { post_id: postId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const btn = $('[data-post-id="' + postId + '"]');
                btn.find('.like-count').text(response.like_count);
                
                if (response.liked) {
                    btn.addClass('liked');
                } else {
                    btn.removeClass('liked');
                }
            }
        }
    });
}

<?php if (!empty($post_errors)): ?>
    $(document).ready(function() {
        $('#postModal').modal('show');
    });
<?php endif; ?>
</script>

<style>
.community-feed-centered {
    display: flex;
    justify-content: center;
    padding: 40px 20px;
    width: 100%;
}

.feed-container {
    max-width: 900px;
    width: 100%;
}

.feed-section {
    margin-bottom: 50px;
}

.feed-section-title {
    font-family: 'Playfair Display', serif;
    font-size: 28px;
    color: #865029;
    margin-bottom: 25px;
    padding-bottom: 10px;
    border-bottom: 3px solid #865029;
}

.post-header-right {
    display: flex;
    align-items: center;
    gap: 10px;
}

.btn-delete-mini {
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
    opacity: 0.6;
    transition: all 0.2s ease;
    padding: 5px;
}

.btn-delete-mini:hover {
    opacity: 1;
    transform: scale(1.2);
}

.like-display {
    color: #666;
    font-size: 14px;
    padding: 5px 10px;
}

@media (max-width: 768px) {
    .feed-container {
        max-width: 100%;
        padding: 0 10px;
    }
}
</style>