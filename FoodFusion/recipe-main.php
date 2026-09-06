<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once 'app/config/database.php';

$isLoggedIn = isset($_SESSION['user_id']);
$current_user_id = $_SESSION['user_id'] ?? 0;

$recipe_errors = $_SESSION['recipe_errors'] ?? [];
$recipe_old = $_SESSION['recipe_old'] ?? [];
$share_error = $_SESSION['share_error'] ?? null;
$share_success = $_SESSION['share_success'] ?? null;
$delete_error = $_SESSION['delete_error'] ?? null;
$delete_success = $_SESSION['delete_success'] ?? null;
unset($_SESSION['recipe_errors'], $_SESSION['recipe_old'], $_SESSION['share_error'], $_SESSION['share_success'], $_SESSION['delete_error'], $_SESSION['delete_success']);

$recipes = [];
$search = trim($_GET['q'] ?? '');
$searchParam = '%' . $search . '%';

if ($isLoggedIn) {
    if ($search !== '') {
        $stmt = $conn->prepare('SELECT r.*, 
            (SELECT COUNT(*) FROM recipe_likes WHERE recipe_id = r.id) as like_count,
            (SELECT COUNT(*) FROM recipe_likes WHERE recipe_id = r.id AND user_id = ?) as user_liked,
            (SELECT COUNT(*) FROM recipes WHERE original_recipe_id = r.id AND is_shared = TRUE) as share_count
            FROM recipes r 
            WHERE r.is_shared = FALSE
              AND (r.visibility = "public" OR r.user_id = ?)
              AND (r.title LIKE ? OR r.ingredients LIKE ? OR r.category LIKE ? OR r.instructions LIKE ?)
            ORDER BY r.created_at DESC');
        $stmt->bind_param('iissss', $current_user_id, $current_user_id, $searchParam, $searchParam, $searchParam, $searchParam);
    } else {
        $stmt = $conn->prepare('SELECT r.*, 
            (SELECT COUNT(*) FROM recipe_likes WHERE recipe_id = r.id) as like_count,
            (SELECT COUNT(*) FROM recipe_likes WHERE recipe_id = r.id AND user_id = ?) as user_liked,
            (SELECT COUNT(*) FROM recipes WHERE original_recipe_id = r.id AND is_shared = TRUE) as share_count
            FROM recipes r 
            WHERE r.user_id = ? AND r.is_shared = FALSE
            ORDER BY r.created_at DESC');
        $stmt->bind_param('ii', $current_user_id, $current_user_id);
    }
} else {
    if ($search !== '') {
        $stmt = $conn->prepare('SELECT r.*, 
            (SELECT COUNT(*) FROM recipe_likes WHERE recipe_id = r.id) as like_count,
            0 as user_liked,
            (SELECT COUNT(*) FROM recipes WHERE original_recipe_id = r.id AND is_shared = TRUE) as share_count
            FROM recipes r 
            WHERE r.visibility = "public" AND r.is_shared = FALSE
              AND (r.title LIKE ? OR r.ingredients LIKE ? OR r.category LIKE ? OR r.instructions LIKE ?)
            ORDER BY r.created_at DESC');
        $stmt->bind_param('ssss', $searchParam, $searchParam, $searchParam, $searchParam);
    } else {
        $stmt = $conn->prepare('SELECT r.*, 
            (SELECT COUNT(*) FROM recipe_likes WHERE recipe_id = r.id) as like_count,
            0 as user_liked,
            (SELECT COUNT(*) FROM recipes WHERE original_recipe_id = r.id AND is_shared = TRUE) as share_count
            FROM recipes r 
            WHERE r.visibility = "public" AND r.is_shared = FALSE
            ORDER BY r.created_at DESC');
    }
}

$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $recipes[] = $row;
}
$stmt->close();
?>

<main>
    
    <?php if ($share_error): ?>
        <div class="container">
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <?= htmlspecialchars($share_error) ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($share_success): ?>
        <div class="container">
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <?= htmlspecialchars($share_success) ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($delete_error): ?>
        <div class="container">
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <?= htmlspecialchars($delete_error) ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($delete_success): ?>
        <div class="container">
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <?= htmlspecialchars($delete_success) ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
        <div class="container">
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                Your recipe has been posted successfully!
            </div>
        </div>
    <?php endif; ?>

    <div class="modal fade" id="recipeModal" tabindex="-1" aria-labelledby="recipeModalLabel" aria-hidden="true"
         data-auto-open="<?= !empty($recipe_errors) ? 'true' : 'false' ?>">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title" id="recipeModalLabel">Post Your Recipe</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if (!$isLoggedIn): ?>
                        <div class="alert alert-warning">
                            <p>You must be logged in to post a recipe.</p>
                            <a href="home.php" class="btn btn-custom btn-sm">Go to Home to Login</a>
                        </div>
                    <?php else: ?>
                        <?php if (!empty($recipe_errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($recipe_errors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form action="public/recipe_handler.php" method="post" enctype="multipart/form-data" class="cookbook-form">
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="title" class="form-label">Recipe Title *</label>
                                    <input type="text" id="title" name="title" class="form-control" required value="<?= htmlspecialchars($recipe_old['title'] ?? '') ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="visibility" class="form-label">Visibility</label>
                                    <select id="visibility" name="visibility" class="form-control">
                                        <option value="private" <?= ($recipe_old['visibility'] ?? 'private') === 'private' ? 'selected' : '' ?>>Private (Only You)</option>
                                        <option value="public" <?= ($recipe_old['visibility'] ?? '') === 'public' ? 'selected' : '' ?>>Public (Can be shared)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="category" class="form-label">Category</label>
                                    <select id="category" name="category" class="form-control">
                                        <option value="">Select a category</option>
                                        <option value="Appetizer" <?= ($recipe_old['category'] ?? '') === 'Appetizer' ? 'selected' : '' ?>>Appetizer</option>
                                        <option value="Main Course" <?= ($recipe_old['category'] ?? '') === 'Main Course' ? 'selected' : '' ?>>Main Course</option>
                                        <option value="Dessert" <?= ($recipe_old['category'] ?? '') === 'Dessert' ? 'selected' : '' ?>>Dessert</option>
                                        <option value="Beverage" <?= ($recipe_old['category'] ?? '') === 'Beverage' ? 'selected' : '' ?>>Beverage</option>
                                        <option value="Snack" <?= ($recipe_old['category'] ?? '') === 'Snack' ? 'selected' : '' ?>>Snack</option>
                                        <option value="Side Dish" <?= ($recipe_old['category'] ?? '') === 'Side Dish' ? 'selected' : '' ?>>Side Dish</option>
                                        <option value="Other" <?= ($recipe_old['category'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="cook_time" class="form-label">Cook Time (minutes)</label>
                                    <input type="number" id="cook_time" name="cook_time" class="form-control" value="<?= htmlspecialchars($recipe_old['cook_time'] ?? '') ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="difficulty_level" class="form-label">Difficulty Level</label>
                                    <select id="difficulty_level" name="difficulty_level" class="form-control">
                                        <option value="Easy" <?= ($recipe_old['difficulty_level'] ?? '') === 'Easy' ? 'selected' : '' ?>>Easy</option>
                                        <option value="Medium" <?= ($recipe_old['difficulty_level'] ?? 'Medium') === 'Medium' ? 'selected' : '' ?>>Medium</option>
                                        <option value="Hard" <?= ($recipe_old['difficulty_level'] ?? '') === 'Hard' ? 'selected' : '' ?>>Hard</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="recipe_image" class="form-label">Recipe Image</label>
                                <input type="file" id="recipe_image" name="recipe_image" class="form-control" accept="image/*">
                            </div>

                            <div class="mb-3">
                                <label for="ingredients" class="form-label">Ingredients *</label>
                                <textarea id="ingredients" name="ingredients" class="form-control" rows="4" required placeholder="List each ingredient on a new line"><?= htmlspecialchars($recipe_old['ingredients'] ?? '') ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="instructions" class="form-label">Instructions *</label>
                                <textarea id="instructions" name="instructions" class="form-control" rows="5" required placeholder="Step-by-step cooking instructions"><?= htmlspecialchars($recipe_old['instructions'] ?? '') ?></textarea>
                            </div>

                            <div class="text-end">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-custom">Post Recipe</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <section class="recipes-section">
        <h1 class="section-heading">
            <?= $isLoggedIn ? 'Your Recipe Collection' : 'Browse Recipes' ?>
        </h1>
        
        <div class="row justify-content-center mb-4">
            <div class="col-md-8">
                <form class="d-flex" role="search" method="get" action="recipe.php">
                    <input class="form-control me-2" type="search" placeholder="Search recipes..." name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" aria-label="Search">
                    <button class="btn btn-custom" type="submit">Search</button>
                </form>
            </div>
        </div>
        
        <?php if ($isLoggedIn): ?>
            <div class="section-actions text-center" style="margin-bottom: 15px;">
                <button type="button" class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#recipeModal">Post Your Recipe</button>
            </div>
        <?php endif; ?>

        <?php if (!empty($search)): ?>
            <p class="recipe-subtitle">Showing results for "<?= htmlspecialchars($search) ?>"</p>
        <?php endif; ?>
        
        <?php if (!$isLoggedIn && empty($recipes)): ?>
            <div class="empty-state">
                <p>🍳 No public recipes available yet!</p>
                <a href="home.php" class="btn btn-custom">Login to Post Recipes</a>
            </div>
        <?php elseif ($isLoggedIn && empty($recipes)): ?>
            <div class="empty-state">
                <p>🍳 You haven't posted any recipes yet!</p>
                <button type="button" class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#recipeModal">
                    Post Your First Recipe
                </button>
            </div>
        <?php else: ?>
            <div class="recipe-grid">
                <?php foreach ($recipes as $recipe): ?>
                    <div class="recipe-card">
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
                            <p class="recipe-author">by <?= htmlspecialchars($recipe['username']) ?></p>
                            
                            <div class="recipe-info">
                                <?php if (!empty($recipe['cook_time'])): ?>
                                    <span class="info-item">⏱️ <?= htmlspecialchars($recipe['cook_time']) ?> mins</span>
                                <?php endif; ?>
                                <?php if (!empty($recipe['difficulty_level'])): ?>
                                    <span class="info-item">📊 <?= htmlspecialchars($recipe['difficulty_level']) ?></span>
                                <?php endif; ?>
                                <?php if ($isLoggedIn): ?>
                                    <span class="info-item visibility-badge <?= $recipe['visibility'] === 'public' ? 'public' : 'private' ?>">
                                        <?= $recipe['visibility'] === 'public' ? '🌍 Public' : '🔒 Private' ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <button class="btn btn-sm btn-outline" onclick="toggleRecipe(<?= $recipe['id'] ?>)">
                                View Recipe
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

                                <?php if ($isLoggedIn && $recipe['user_id'] == $current_user_id): ?>
                                    <div class="recipe-social-actions">
                                        <button class="btn-social like-btn <?= $recipe['user_liked'] > 0 ? 'liked' : '' ?>" 
                                                onclick="likeRecipe(<?= $recipe['id'] ?>)" 
                                                data-recipe-id="<?= $recipe['id'] ?>">
                                            <span class="icon">❤️</span>
                                            <span class="count"><?= $recipe['like_count'] ?></span>
                                        </button>

                                        <?php if ($recipe['visibility'] === 'public'): ?>
                                            <form method="post" action="public/share_recipe.php" style="display: inline;">
                                                <input type="hidden" name="recipe_id" value="<?= $recipe['id'] ?>">
                                                <button type="submit" class="btn-social share-btn">
                                                    <span class="icon">🔄</span>
                                                    <span class="count"><?= $recipe['share_count'] ?></span>
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <form method="post" action="public/delete_recipe.php" style="display: inline;" 
                                              onsubmit="return confirm('Are you sure you want to delete this recipe? This cannot be undone.');">
                                            <input type="hidden" name="recipe_id" value="<?= $recipe['id'] ?>">
                                            <button type="submit" class="btn-social delete-btn">
                                                <span class="icon">🗑️</span>
                                            </button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <div class="recipe-social-actions">
                                        <span class="like-display">❤️ <?= $recipe['like_count'] ?></span>
                                    </div>
                                <?php endif; ?>

                                <p class="recipe-date">Posted on <?= date('F j, Y', strtotime($recipe['created_at'])) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>


<style>
.recipe-subtitle {
    text-align: center;
    font-size: 16px;
    color: #666;
    margin: -10px auto 30px auto;
    max-width: 700px;
}

.visibility-badge {
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.visibility-badge.public {
    background: #e8f5e9;
    color: #2e7d32;
}

.visibility-badge.private {
    background: #fff3e0;
    color: #e65100;
}

.btn-social.delete-btn {
    border-color: #e74c3c;
    color: #e74c3c;
}

.btn-social.delete-btn:hover {
    background: #e74c3c;
    color: white;
}

.like-display {
    color: #666;
    font-size: 14px;
    padding: 8px 16px;
}
</style>