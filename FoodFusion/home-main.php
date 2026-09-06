<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$login_errors = $_SESSION['login_errors'] ?? [];
$login_old = $_SESSION['login_old'] ?? [];
unset($_SESSION['login_errors'], $_SESSION['login_old']);

$signup_errors = $_SESSION['signup_errors'] ?? [];
$signup_old = $_SESSION['signup_old'] ?? [];
unset($_SESSION['signup_errors'], $_SESSION['signup_old']);
?>
<main>            
    <div class="container heading">
        <h1>Home of Many Recipes and Dreams.</h1>

        <?php if (!isset($_SESSION['user_id'])): ?>
            <button type="button" class="btn btn-custom btn-lg" data-bs-toggle="modal" data-bs-target="#exampleModal">
                Sign Up Now
            </button>
        <?php else: ?>
            <a href="community.php" class="btn btn-custom btn-lg">
                Share Your Recipes
            </a>
        <?php endif; ?>
    </div>

    <?php if (isset($_GET['login']) && $_GET['login'] === 'success'): ?>
        <div class="container">
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <strong>Success!</strong> You have been logged in successfully.
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['logout']) && $_GET['logout'] === 'success'): ?>
        <div class="container">
            <div class="alert alert-info alert-dismissible" role="alert">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                You have been logged out successfully.
            </div>
        </div>
    <?php endif; ?>

    <div class="modal fade" id="exampleModal" data-auto-open="<?= (isset($_GET['signup']) && $_GET['signup'] === 'failed') || !empty($signup_errors) ? 'true' : 'false' ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Sign Up</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form method="post" action="public/signup.php">
                    <div class="modal-body">
                        <?php if (!empty($signup_errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($signup_errors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="modal-firstname" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="modal-firstname" name="first_name" placeholder="First Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="modal-lastname" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="modal-lastname" name="last_name" placeholder="Last Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="modal-username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="modal-username" name="username" placeholder="Choose a username" required value="<?= htmlspecialchars($signup_old['username'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="modal-email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="modal-email" name="email" placeholder="you@example.com" required>
                        </div>
                        <div class="mb-3">
                            <label for="modal-password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="modal-password" name="password" placeholder="Enter a strong password (min 6 characters)" required>
                        </div>
                        <p class="text-muted">By signing up, you agree to our terms and conditions.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-custom">Sign Up</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true"
         data-auto-open="<?= !empty($login_errors) ? 'true' : 'false' ?>">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Login</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form method="post" action="public/login.php">
                    <div class="modal-body">
                        <?php if (!empty($login_errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($login_errors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="login-username" class="form-label">Username or Email</label>
                            <input type="text" class="form-control" id="login-username" name="username_or_email" 
                                   placeholder="Enter your username or email" required 
                                   value="<?= htmlspecialchars($login_old['username_or_email'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="login-password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="login-password" name="password" 
                                   placeholder="Enter your password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-custom">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container featured-recipes">
        <h2 class="featured-title">Featured Recipes</h2>
                
        <div id="recipesCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#recipesCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#recipesCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#recipesCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
            </div>

            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="row">
                        <div class="col-md-6">
                            <img src="pics/pastacabonara.jpg" alt="Pasta Carbonara" class="d-block w-100">
                        </div>
                        <div class="col-md-6">
                            <h3>Pasta Carbonara</h3>
                            <p>A classic Italian dish made with eggs, cheese, pancetta, and black pepper. This creamy and delicious pasta is perfect for a quick weeknight dinner or a special occasion.</p>
                            <p><strong>Prep Time:</strong> 10 mins | <strong>Cook Time:</strong> 20 mins</p>
                            <a href="recipe.php" class="btn btn-custom">View Recipe</a>
                        </div>
                    </div>
                </div>

                <div class="carousel-item">
                    <div class="row">
                        <div class="col-md-6">
                            <img src="pics/chocolatecake.jpg" alt="Chocolate Cake" class="d-block w-100">
                        </div>
                        <div class="col-md-6">
                            <h3>Moist Chocolate Cake</h3>
                            <p>Indulge in this rich and fudgy chocolate cake with layers of chocolate frosting. Perfect for chocolate lovers and special celebrations with a moist crumb texture.</p>
                            <p><strong>Prep Time:</strong> 15 mins | <strong>Cook Time:</strong> 35 mins</p>
                            <a href="recipe.php" class="btn btn-custom">View Recipe</a>
                        </div>
                    </div>
                </div>

                <div class="carousel-item">
                    <div class="row">
                        <div class="col-md-6">
                            <img src="pics/stirfry.jpg" alt="Asian Stir Fry" class="d-block w-100">
                        </div>
                        <div class="col-md-6">
                            <h3>Asian Stir Fry</h3>
                            <p>A vibrant and colorful mix of fresh vegetables, tender chicken, and a savory soy-based sauce. Quick to prepare and packed with flavors from the East.</p>
                            <p><strong>Prep Time:</strong> 15 mins | <strong>Cook Time:</strong> 15 mins</p>
                            <a href="recipe.php" class="btn btn-custom">View Recipe</a>
                        </div>
                    </div>
                </div>
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#recipesCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#recipesCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>

    <div class="container upcoming-events-section">
        <h2 class="events-title">Upcoming Cooking Events</h2>
        
        <div class="row">
            <div class="col-md-4">
                <div class="event-card">
                    <img src="pics/cookingclass.jpg" alt="Italian Cooking Class" class="img-responsive event-image">
                    <div class="event-content">
                        <h3>Italian Cooking Class</h3>
                        <p class="event-date"><strong>Date:</strong> December 15, 2025</p>
                        <p class="event-description">Join us for an authentic Italian cooking class where you'll learn to make fresh pasta, risotto, and traditional desserts. Perfect for beginners and enthusiasts alike.</p>
                        <a href="community.php" class="btn btn-custom btn-sm">Learn More</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="event-card">
                    <img src="pics/asianfusion.jpg" alt="Asian Fusion Workshop" class="img-responsive event-image">
                    <div class="event-content">
                        <h3>Asian Fusion Workshop</h3>
                        <p class="event-date"><strong>Date:</strong> December 22, 2025</p>
                        <p class="event-description">Explore the vibrant flavors of Asia in this hands-on workshop. Learn techniques for stir-frying, steaming, and creating mouth-watering Asian fusion dishes.</p>
                        <a href="community.php" class="btn btn-custom btn-sm">Learn More</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="event-card">
                    <img src="pics/bakingclass.jpg" alt="Baking Masterclass" class="img-responsive event-image">
                    <div class="event-content">
                        <h3>Baking Masterclass</h3>
                        <p class="event-date"><strong>Date:</strong> January 10, 2026</p>
                        <p class="event-description">Master the art of baking with our expert pastry chefs. Learn to make croissants, soufflés, and artisan breads from scratch with professional tips and tricks.</p>
                        <a href="community.php" class="btn btn-custom btn-sm">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="cookiePopup" class="cookie-popup" style="display: none;">
        <div class="cookie-content">
            <p class="cookie-text">We use cookies to improve your experience. By continuing to use our site, you agree to our use of cookies.</p>
            <div class="cookie-buttons">
                <button class="btn btn-sm btn-default" id="rejectCookies">Reject</button>
                <button class="btn btn-sm btn-custom" id="acceptCookies">Accept</button>
            </div>
        </div>
    </div>
            
</main>