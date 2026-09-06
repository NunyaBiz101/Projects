<?php
require_once 'config.php';
$db = getDB();

$products = $db->query("SELECT * FROM products ORDER BY created_at DESC")->fetchAll();

$deals = $db->query("SELECT * FROM deals WHERE active=1 ORDER BY created_at DESC")->fetchAll();

$categories = array_unique(array_column($products, 'category'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Park View Supermarket – Your Local Supermarket</title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="assets/page-styles.css">
</head>
<body>

<nav>
  <div class="nav-inner">
    <a href="#" class="logo">
      <div class="logo-icon"><img src="pics/logo.png" alt="Park View Supermarket logo"></div>
      <span class="logo-text">Park View Supermarket</span>
    </a>
    <ul class="nav-links" id="navLinks">
      <li><a href="#" class="active">Home</a></li>
      <li><a href="about.php">About</a></li>
      <li><a href="#products">Products</a></li>
      <li><a href="#deals">Deals</a></li>
      <li><a href="contact.php">Contact</a></li>
      <li><a href="admin/login.php" class="btn-nav">Admin</a></li>
    </ul>
    <button class="hamburger" id="hamburger" aria-label="Menu">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>

<section class="hero">
  <div class="hero-content">
    <div>
      <div class="hero-badge animate-up">🌱 Fresh &amp; Local Every Day</div>
      <h1 class="animate-up delay-1">The <span>Freshest</span> Groceries, The Best Prices.</h1>
      <p class="animate-up delay-2">Highest quality at supermarket prices. You can find hundreds of fresh products, exclusive deals, and the highest quality you can trust.</p>
      <div class="hero-actions animate-up delay-3">
        <a href="#deals" class="btn btn-outline">View Deals</a>
      </div>
    </div>

    <div class="hero-image">
      <img src="pics/fresh-veggies.jpg" alt="Fresh groceries">
      <div class="hero-float-card top">
        <div class="float-icon">🥦</div>
        <div>
          <div class="float-title">100% Fresh</div>
          <div class="float-sub">Daily Fresh Veggies</div>
        </div>
      </div>
    </div>

  </div>
</section>

<section class="section">
  <div class="section-header">
    <div class="section-tag">Why Park View Supermarket?</div>
    <h2 class="section-title">More Than Just a Supermarket</h2>
    <p class="section-sub">We go above and beyond to make sure that your shopping experience is comfortable.</p>
  </div>
  <div class="features-grid">
    <div class="feature-card">
      <div class="feature-icon">🌿</div>
      <div class="feature-title">Organic Products</div>
      <p class="feature-desc">The best organic products are also available here for you.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">🛒</div>
      <div class="feature-title">Wide Selection</div>
      <p class="feature-desc">A wide variety of products ranging from groceries to household essentials are available, to match your needs.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">💰</div>
      <div class="feature-title">Best Prices Here</div>
      <p class="feature-desc">We match any local price. You can shop here comfortably knowing you get the best deal.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">😊</div>
      <div class="feature-title">Friendly Staff</div>
      <p class="feature-desc">Get the best customer service here, to make you feel right at home.</p>
    </div>
  </div>
</section>

<section class="section" id="products">
  <div class="section-header">
    <div class="section-tag">Our Products</div>
    <h2 class="section-title">Fresh Picks, Every Day</h2>
    <p class="section-sub">High quality products across all your favourite categories.</p>
  </div>

  <div class="search-bar">
    <input type="text" id="searchInput" placeholder="Search products…" oninput="filterProducts()">
    <button onclick="filterProducts()">Search</button>
  </div>

  <div class="categories" id="catFilters">
    <button class="cat-btn active" onclick="filterCategory('all', this)">All</button>
    <?php foreach ($categories as $cat): ?>
    <button class="cat-btn" onclick="filterCategory('<?= htmlspecialchars($cat) ?>', this)"><?= htmlspecialchars($cat) ?></button>
    <?php endforeach; ?>
  </div>

  <div class="products-grid" id="productsGrid">
    <?php foreach ($products as $p): ?>
    <div class="product-card" data-category="<?= htmlspecialchars($p['category']) ?>" data-name="<?= strtolower(htmlspecialchars($p['name'])) ?>">
      <div class="product-img-wrap">
        <img src="<?= htmlspecialchars($p['image_url'] ?: 'https://via.placeholder.com/400x200') ?>"
             alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy">
        <?php if (!$p['in_stock']): ?>
        <span class="out-of-stock-badge">Out of Stock</span>
        <?php endif; ?>
      </div>
      <div class="product-body">
        <div class="product-cat"><?= htmlspecialchars($p['category']) ?></div>
        <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
        <div class="product-desc"><?= htmlspecialchars($p['description']) ?></div>
        <div class="product-footer">
          <div class="product-price">$<?= number_format($p['price'], 2) ?></div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<div class="deals-bg" id="deals">
  <div class="section">
    <div class="section-header">
      <div class="section-tag">Hot Deals</div>
      <h2 class="section-title">This Week's Best Offers</h2>
      <p class="section-sub" style="color:rgba(255,255,255,0.6)">Limited time deals you don't want to miss. Stock up and save big!</p>
    </div>

    <?php if (empty($deals)): ?>
    <p style="text-align:center;color:rgba(255,255,255,0.5);padding:40px">No active deals right now. Check back soon!</p>
    <?php else: ?>
    <div class="deals-grid">
      <?php foreach ($deals as $d): ?>
      <div class="deal-card">
        <img class="deal-img" src="<?= htmlspecialchars($d['image_url'] ?: 'https://via.placeholder.com/400x220') ?>" alt="<?= htmlspecialchars($d['title']) ?>">
        <div class="deal-overlay">
          <?php if ($d['discount_percent']): ?>
          <span class="deal-badge">-<?= $d['discount_percent'] ?>%</span>
          <?php endif; ?>
          <div class="deal-title"><?= htmlspecialchars($d['title']) ?></div>
          <div class="deal-desc"><?= htmlspecialchars($d['description']) ?></div>
          <?php if ($d['original_price'] && $d['sale_price']): ?>
          <div class="deal-prices">
            <span class="deal-original">$<?= number_format($d['original_price'], 2) ?></span>
            <span class="deal-sale">$<?= number_format($d['sale_price'], 2) ?></span>
          </div>
          <?php endif; ?>
          <?php if ($d['valid_until']): ?>
          <div class="deal-expiry">📅 Valid until <?= htmlspecialchars($d['valid_until']) ?></div>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php include 'footer.php'; ?>

<div class="cart-toast" id="cartToast">🛒 <span id="cartToastMsg">Added to cart!</span></div>

<script src="assets/script.js"></script>
</body>
</html>
