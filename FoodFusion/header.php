<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']);
$username = $_SESSION['username'] ?? '';
$firstName = $_SESSION['first_name'] ?? '';

$login_errors = $_SESSION['login_errors'] ?? [];
$login_old = $_SESSION['login_old'] ?? [];
unset($_SESSION['login_errors'], $_SESSION['login_old']);

$signup_errors = $_SESSION['signup_errors'] ?? [];
$signup_old = $_SESSION['signup_old'] ?? [];
unset($_SESSION['signup_errors'], $_SESSION['signup_old']);
?>
<header class="site-header">
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-collapse" aria-controls="navbar-collapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="home.php">FoodFusion</a>

            <div class="collapse navbar-collapse" id="navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="aboutus.php">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="recipe.php">Recipes</a></li>
                    <li class="nav-item"><a class="nav-link" href="community.php">Community</a></li>
                    <li class="nav-item"><a class="nav-link" href="contactus.php">Contact Us</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="resourcesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Resources
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="resourcesDropdown">
                            <li><a class="dropdown-item" href="resources.php#culinary">Culinary Resources</a></li>
                            <li><a class="dropdown-item" href="resources.php#educational">Educational Resources</a></li>
                        </ul>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item"><a href="public/logout.php" class="btn btn-logout">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><button class="btn btn-custom header-login-btn" data-bs-toggle="modal" data-bs-target="#loginModal">Login</button></li>
                        <li class="nav-item"><button class="btn btn-custom header-signup-btn" data-bs-toggle="modal" data-bs-target="#signupModal">Sign Up</button></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true"
         data-auto-open="<?= (!empty($login_errors) || (isset($_GET['login']) && $_GET['login'] === 'failed')) ? 'true' : 'false' ?>">
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
                        
                        <?php if (isset($_GET['login']) && $_GET['login'] === 'success'): ?>
                            <div class="alert alert-success">
                                Welcome back! You've successfully logged in.
                            </div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="login-username" class="form-label">Username or Email</label>
                            <input type="text" class="form-control" id="login-username" name="username_or_email" 
                                   placeholder="Enter your username or email" 
                                   value="<?= htmlspecialchars($login_old['username_or_email'] ?? '') ?>" required>
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
    
    <div class="modal fade" id="signupModal" tabindex="-1" aria-labelledby="signupModalLabel" aria-hidden="true"
         data-auto-open="<?= (!empty($signup_errors) || (isset($_GET['signup']) && $_GET['signup'] === 'failed')) ? 'true' : 'false' ?>">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="signupModalLabel">Sign Up</h5>
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
                        
                        <?php if (isset($_GET['signup']) && $_GET['signup'] === 'success'): ?>
                            <div class="alert alert-success">
                                Account created successfully! Please log in.
                            </div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="modal-firstname" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="modal-firstname" name="first_name" 
                                   placeholder="First Name" 
                                   value="<?= htmlspecialchars($signup_old['first_name'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="modal-lastname" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="modal-lastname" name="last_name" 
                                   placeholder="Last Name" 
                                   value="<?= htmlspecialchars($signup_old['last_name'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="modal-username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="modal-username" name="username" 
                                   placeholder="Choose a username" 
                                   value="<?= htmlspecialchars($signup_old['username'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="modal-email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="modal-email" name="email" 
                                   placeholder="you@example.com" 
                                   value="<?= htmlspecialchars($signup_old['email'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="modal-password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="modal-password" name="password" 
                                   placeholder="Enter a strong password (min 6 characters)" required>
                        </div>
                        <p class="text-muted small">By signing up, you agree to our terms and conditions.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-custom">Sign Up</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</header>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>