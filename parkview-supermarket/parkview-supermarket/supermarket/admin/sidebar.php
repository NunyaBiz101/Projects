<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
<aside class="sidebar" id="sidebar">
  <div class="sidebar-logo">
    <div class="logo-icon">🛒</div>
    <div>
      <div class="sidebar-logo-text">Park View Supermarket</div>
      <span class="sidebar-subtitle">Admin Panel</span>
    </div>
  </div>
  <ul class="sidebar-nav">
    <li><a href="index.php" class="<?= $currentPage=='index.php'?'active':'' ?>">
      <span class="nav-icon"></span> Dashboard
    </a></li>
    <li><a href="products.php" class="<?= $currentPage=='products.php'?'active':'' ?>">
      <span class="nav-icon"></span> Products
    </a></li>
    <li><a href="deals.php" class="<?= $currentPage=='deals.php'?'active':'' ?>">
      <span class="nav-icon"></span> Deals & Offers
    </a></li>
    <li><a href="contacts.php" class="<?= $currentPage=='contacts.php'?'active':'' ?>">
      <span class="nav-icon"></span> Messages
    </a></li>
    <li style="margin-top:12px;padding-top:12px;border-top:1px solid rgba(255,255,255,0.08)">
      <a href="../index.php" target="_blank">
        <span class="nav-icon"></span> View Store
      </a>
    </li>
  </ul>
  <div class="sidebar-footer">
    <a href="logout.php">Sign Out</a>
  </div>
</aside>
