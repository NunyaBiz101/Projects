<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$errors = $_SESSION['contact_errors'] ?? [];
$old = $_SESSION['contact_old'] ?? [];
unset($_SESSION['contact_errors'], $_SESSION['contact_old']);
?>

<main class="main-content">
    <section class="hero-section">
        <div class="container">
            <h1 class="hero-title">Get in Touch with Us</h1>
            <p class="hero-subtitle">We'd love to hear from you! Share your feedback, questions, or recipe ideas.</p>
        </div>
    </section>

    <section class="contact-section">
        <div class="container">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php elseif (isset($_GET['success'])): ?>
                <div class="alert alert-success">Thank you for contacting us! We will get back to you soon.</div>
            <?php endif; ?>

            <form action="public/contactus_handler.php" method="post" class="contact-form">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" class="form-control" required value="<?= htmlspecialchars($old['name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" class="form-control" required value="<?= htmlspecialchars($old['subject'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" class="form-control" rows="5" required><?= htmlspecialchars($old['message'] ?? '') ?></textarea>
                </div>
                <button type="submit" class="btn btn-custom">Send Message</button>
            </form>
        </div>
    </section>
</main>