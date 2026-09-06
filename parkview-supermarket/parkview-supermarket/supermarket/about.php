<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us – Park View Supermarket</title>
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
      <li><a href="index.php">Home</a></li>
      <li><a href="about.php" class="active">About</a></li>
      <li><a href="index.php#products">Products</a></li>
      <li><a href="index.php#deals">Deals</a></li>
      <li><a href="contact.php">Contact</a></li>
      <li><a href="admin/login.php" class="btn-nav">Admin</a></li>
    </ul>
    <button class="hamburger" id="hamburger" aria-label="Menu">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>

<section class="about-hero">
  <h1>About Park View Supermarket</h1>
  <p>We are your local community supermarket aiming for wide variety, better pricing and friendly service. Our employees work every day to make shopping easy and enjoyable for families and individuals across Trinidad.</p>
</section>

<div class="about-main">

    <div class="about-story">

      <h2>Our Story</h2>
      <div class="team-card">
        <div class="team-photo">
          <img src="pics/team.jpg" alt="Park View Supermarket employees">
        </div>
        <div class="team-details">
          <h3>Meet Our Team</h3>
          <p>Our employees are the heart of Park View Supermarket that bring optimism and a dedication to helping customers.</p>
        </div>
      </div>

      <p>Park View Supermarket was founded by Mr. Hanuman Ramanand and Mrs. Indra Ramanand. 
        Their journey began with a small supermarket in Diamond Village, just ten minutes from the Palmiste area. 
        They later expanded by renting and operating Sunkist Supermarket in Sunkist, Phillipine. </p>
      <p>During this time, they recognized the growing need for a larger, more modern supermarket to serve the Palmiste 
        community. Acting on that vision, the Ramanands acquired land opposite Palmiste Park and, in 2003, established 
        Park View Supermarket - the very first supermarket in the Palmiste community.</p>
      
        <div class="about-values">
        <div class="about-value">
          <h3>Best Quality</h3>
          <p>Delivering fresh and high quality grocery items every day, with prices you can afford.</p>
        </div>
        <div class="about-value">
          <h3>Local and imported products</h3>
          <p>Supporting local and international suppliers to offer a wide range of products.</p>
        </div>
        <div class="about-value">
          <h3>Friendly Service</h3>
          <p>Our employees help provide an experience that makes shopping enjoyable, quick, and stress-free.</p>
        </div>
      </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script src="assets/script.js"></script>
</body>
</html>
