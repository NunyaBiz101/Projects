<?php
require_once 'config.php';
$db = getDB();

$contactMsg = '';
$contactError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($name && $email && $message && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $db->prepare("INSERT INTO contacts (name, email, message) VALUES (?,?,?)");
        $stmt->execute([$name, $email, $message]);
        $contactMsg = "Thanks, $name! Your message has been received. We'll get back to you shortly.";
    } else {
        $contactError = "Please fill in all fields with a valid email address.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us – Park View Supermarket</title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="assets/page-styles.css">
</head>
<body>

<nav>
  <div class="nav-inner">
    <a href="index.php" class="logo">
      <div class="logo-icon"><img src="pics/logo.png" alt="Park View Supermarket logo"></div>
      <span class="logo-text">Park View Supermarket</span>
    </a>
    <ul class="nav-links" id="navLinks">
      <li><a href="index.php">Home</a></li>      <li><a href="about.php">About</a></li>      <li><a href="index.php#products">Products</a></li>
      <li><a href="index.php#deals">Deals</a></li>
      <li><a href="contact.php" class="active">Contact</a></li>
      <li><a href="admin/login.php" class="btn-nav">Admin</a></li>
    </ul>
    <button class="hamburger" id="hamburger" aria-label="Menu">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>

<div class="contact-hero">
  <h1>Get In Touch</h1>
  <p>We'd love to hear from you! Have questions or feedback? Contact us today and we'll get back to you shortly.</p>
</div>

<div class="contact-main">
  <div class="contact-grid-main">
    <!-- Contact Information -->
    <div class="contact-section">
      <h2>Contact Information</h2>
      
      <div class="contact-item">
        <div class="contact-item-icon">📍</div>
        <div class="contact-item-text">
          <strong>Visit Us</strong>
          <span>Lot B SS Erin Road<br>Philippine, San Fernando</span>
        </div>
      </div>

      <div class="contact-item">
        <div class="contact-item-icon">📞</div>
        <div class="contact-item-text">
          <strong>Phone</strong>
          <span><a href="tel:+18682234235">+1 (868) 223-4235</a></span>
        </div>
      </div>

      <div class="contact-item">
        <div class="contact-item-icon">✉️</div>
        <div class="contact-item-text">
          <strong>Email</strong>
          <span><a href="mailto:parkviewsupermarket@gmail.com">parkviewsupermarket@gmail.com</a></span>
        </div>
      </div>

      <div class="contact-item">
        <div class="contact-item-icon">🕐</div>
        <div class="contact-item-text">
          <strong>Hours</strong>
          <span>Mon–Sat: 8am–10:30pm<br>Sun: 8am–10:30pm</span>
        </div>
      </div>
    </div>

    <div class="contact-form-wrap">
      <h2>Send Us a Message</h2>
      
      <?php if ($contactMsg): ?>
      <div class="alert alert-success"> <?= htmlspecialchars($contactMsg) ?></div>
      <?php endif; ?>
      <?php if ($contactError): ?>
      <div class="alert alert-error"> <?= htmlspecialchars($contactError) ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="form-row">
          <div class="form-group">
            <label>Your Name</label>
            <input type="text" name="name" placeholder="John Doe" required>
          </div>
          <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="example@email.com" required>
          </div>
        </div>
        <div class="form-group">
          <label>Subject (Optional)</label>
          <input type="text" name="subject" placeholder="What is this about?">
        </div>
        <div class="form-group">
          <label>Message</label>
          <textarea name="message" placeholder="How can we help you? Tell us more..." required></textarea>
        </div>
        <button type="submit" name="contact_submit" class="btn-submit">Send Message →</button>
      </form>
    </div>
  </div>

  <div class="maps-section">
    <h2 style="padding: 30px 30px 0; margin: 0; font-family: 'Playfair Display', serif;">Our Location</h2>
    <iframe class="maps-container" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3926.198482154953!2d-61.4561623!3d10.2455702!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8c358c2fe39923d9%3A0x58f807da1df74794!2sPark%20View%20Supermarket!5e0!3m2!1sen!2stt!4v1777310449971!5m2!1sen!2stt" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
  </div>
</div>

<div class="hours-section">
  <h2 style="text-align: center; font-family: 'Playfair Display', serif; font-size: 2rem; margin-bottom: 50px;">Opening Hours</h2>
  <div class="hours-grid">
    <div class="hours-card">
      <h3>Store Hours</h3>
      <div class="hours-list">
        <div class="hours-row">
          <span class="hours-day">Monday</span>
          <span class="hours-time">8:00 AM – 10:30 PM</span>
        </div>
        <div class="hours-row">
          <span class="hours-day">Tuesday</span>
          <span class="hours-time">8:00 AM – 10:30 PM</span>
        </div>
        <div class="hours-row">
          <span class="hours-day">Wednesday</span>
          <span class="hours-time">8:00 AM – 10:30 PM</span>
        </div>
        <div class="hours-row">
          <span class="hours-day">Thursday</span>
          <span class="hours-time">8:00 AM – 10:30 PM</span>
        </div>
        <div class="hours-row">
          <span class="hours-day">Friday</span>
          <span class="hours-time">8:00 AM – 11:00 PM</span>
        </div>
        <div class="hours-row">
          <span class="hours-day">Saturday</span>
          <span class="hours-time">8:00 AM – 11:00 PM</span>
        </div>
        <div class="hours-row">
          <span class="hours-day">Sunday</span>
          <span class="hours-time">8:00 AM – 10:30 PM</span>
        </div>
      </div>
    </div>

    <div class="hours-card">
      <h3>Customer Service</h3>
      <div class="hours-list">
        <div class="hours-row">
          <span class="hours-day">Phone Support</span>
          <span class="hours-time">Mon–Sat: 7am–9pm</span>
        </div>
        <div class="hours-row">
          <span class="hours-day"></span>
          <span class="hours-time">Sun: 8am–7pm</span>
        </div>
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border);">
          <div class="hours-row">
            <span class="hours-day">Email</span>
            <span class="hours-time">24/7</span>
          </div>
          <div class="hours-row">
            <span class="hours-day">Response Time</span>
            <span class="hours-time">Within 24h</span>
          </div>
        </div>
      </div>
    </div>

    <div class="hours-card">
      <h3>Quick Links</h3>
      <div class="hours-list" style="text-align: center;">
        <div style="margin-bottom: 15px;">
          <a href="index.php#products" style="color: var(--green); text-decoration: none; font-weight: 500; display: block; margin-bottom: 10px;">Browse Products</a>
          <a href="index.php#deals" style="color: var(--green); text-decoration: none; font-weight: 500; display: block; margin-bottom: 10px;">View Deals</a>
          <a href="mailto:parkviewsupermarket@gmail.com" style="color: var(--green); text-decoration: none; font-weight: 500; display: block; margin-bottom: 10px;">Email Us</a>
          <a href="tel:+18682234235" style="color: var(--green); text-decoration: none; font-weight: 500; display: block;">Call Us</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>

<script src="assets/script.js"></script>
</body>
</html>
