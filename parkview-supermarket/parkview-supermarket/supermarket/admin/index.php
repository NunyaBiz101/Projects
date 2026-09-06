<?php
require_once 'auth.php';
require_once '../config.php';
$db = getDB();

$productCount  = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();
$dealsCount    = $db->query("SELECT COUNT(*) FROM deals WHERE active=1")->fetchColumn();
$contactCount  = $db->query("SELECT COUNT(*) FROM contacts")->fetchColumn();
$outOfStock    = $db->query("SELECT COUNT(*) FROM products WHERE in_stock=0")->fetchColumn();

$recentProducts = $db->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 5")->fetchAll();
$recentContacts = $db->query("SELECT * FROM contacts ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard – Park View Supermarket Admin</title>
  <link rel="stylesheet" href="../assets/admin.css">
</head>
<body>
<div class="admin-layout">
  <?php include 'sidebar.php'; ?>
  <div class="main-content">
    <?php $pageTitle = 'Dashboard'; include 'topbar.php'; ?>
    <div class="content-area">

      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon green">🥦</div>
          <div>
            <div class="stat-value"><?= $productCount ?></div>
            <div class="stat-label">Total Products</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon gold">🔥</div>
          <div>
            <div class="stat-value"><?= $dealsCount ?></div>
            <div class="stat-label">Active Deals</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon blue">✉️</div>
          <div>
            <div class="stat-value"><?= $contactCount ?></div>
            <div class="stat-label">Messages</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon red">📦</div>
          <div>
            <div class="stat-value"><?= $outOfStock ?></div>
            <div class="stat-label">Out of Stock</div>
          </div>
        </div>
      </div>

      <div class="table-wrap" style="margin-bottom:28px">
        <div class="table-header">
          <h3>Recent Products</h3>
          <a href="products.php" class="btn btn-outline btn-sm">View All →</a>
        </div>
        <table>
          <thead>
            <tr>
              <th>Image</th>
              <th>Name</th>
              <th>Category</th>
              <th>Price</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recentProducts as $p): ?>
            <tr>
              <td><img class="td-img" src="<?= htmlspecialchars($p['image_url'] ?: 'https://via.placeholder.com/52x40') ?>" alt=""></td>
              <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
              <td><?= htmlspecialchars($p['category']) ?></td>
              <td>$<?= number_format($p['price'], 2) ?></td>
              <td><span class="badge <?= $p['in_stock'] ? 'badge-green' : 'badge-red' ?>"><?= $p['in_stock'] ? 'In Stock' : 'Out of Stock' ?></span></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="table-wrap">
        <div class="table-header">
          <h3>Recent Messages</h3>
          <a href="contacts.php" class="btn btn-outline btn-sm">View All →</a>
        </div>
        <?php if (empty($recentContacts)): ?>
        <p style="padding:24px;color:var(--text-muted);text-align:center;">No messages yet.</p>
        <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Message</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recentContacts as $c): ?>
            <tr>
              <td><strong><?= htmlspecialchars($c['name']) ?></strong></td>
              <td><?= htmlspecialchars($c['email']) ?></td>
              <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars($c['message']) ?></td>
              <td style="color:var(--text-muted);font-size:0.8rem"><?= htmlspecialchars($c['created_at']) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>
</body>
</html>
