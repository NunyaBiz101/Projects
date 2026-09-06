<?php
require_once 'auth.php';
require_once '../config.php';
$db = getDB();

$msg = '';
$msgType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $uploadsDir = __DIR__ . '/../uploads/';

    if ($action === 'add' || $action === 'edit') {
        $name             = trim($_POST['name'] ?? '');
        $category         = trim($_POST['category'] ?? '');
        $price            = floatval($_POST['price'] ?? 0);
        $desc             = trim($_POST['description'] ?? '');
        $imageUrl         = '';
        $existingImageUrl = trim($_POST['existing_image_url'] ?? '');
        $inStock          = isset($_POST['in_stock']) ? 1 : 0;

        if (!empty($_FILES['image_file']['name']) && $_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadedName = $_FILES['image_file']['name'];
            $uploadedTmp  = $_FILES['image_file']['tmp_name'];
            $uploadedExt  = strtolower(pathinfo($uploadedName, PATHINFO_EXTENSION));
            $allowedExts  = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (!in_array($uploadedExt, $allowedExts, true)) {
                $msg = 'Invalid image format. Use JPG, PNG, GIF or WEBP.';
                $msgType = 'error';
            } elseif ($_FILES['image_file']['error'] !== UPLOAD_ERR_OK) {
                $msg = 'Image upload failed. Please try again.';
                $msgType = 'error';
            } elseif ($_FILES['image_file']['size'] > 5 * 1024 * 1024) {
                $msg = 'Image file is too large. Maximum size is 5MB.';
                $msgType = 'error';
            } else {
                if (!is_dir($uploadsDir)) {
                    mkdir($uploadsDir, 0755, true);
                }
                $newName = uniqid('prod_', true) . '.' . $uploadedExt;
                $target  = $uploadsDir . $newName;

                if (move_uploaded_file($uploadedTmp, $target)) {
                    $imageUrl = 'uploads/' . $newName;
                    if ($action === 'edit' && $existingImageUrl && str_starts_with($existingImageUrl, 'uploads/')) {
                        $oldFile = __DIR__ . '/../' . $existingImageUrl;
                        if (is_file($oldFile)) {
                            @unlink($oldFile);
                        }
                    }
                } else {
                    $msg = 'Unable to save uploaded image. Please try again.';
                    $msgType = 'error';
                }
            }
        } elseif ($action === 'edit') {
            $imageUrl = $existingImageUrl;
        }

        if (!$msg) {
            if ($name && $category && $price > 0 && ($action === 'edit' || $imageUrl)) {
                if ($action === 'add') {
                    $stmt = $db->prepare("INSERT INTO products (name, category, price, description, image_url, in_stock) VALUES (?,?,?,?,?,?)");
                    $stmt->execute([$name, $category, $price, $desc, $imageUrl, $inStock]);
                    $msg = "Product \"$name\" added successfully!";
                } else {
                    $id = intval($_POST['id']);
                    $stmt = $db->prepare("UPDATE products SET name=?, category=?, price=?, description=?, image_url=?, in_stock=? WHERE id=?");
                    $stmt->execute([$name, $category, $price, $desc, $imageUrl, $inStock, $id]);
                    $msg = "Product \"$name\" updated successfully!";
                }
            } else {
                $msg = 'Please fill in all required fields and upload a product image.';
                $msgType = 'error';
            }
        }
    }

    if ($action === 'delete') {
        $id = intval($_POST['id']);
        $stmt = $db->prepare("DELETE FROM products WHERE id=?");
        $stmt->execute([$id]);
        $msg = "Product deleted.";
    }

    if ($action === 'toggle_stock') {
        $id = intval($_POST['id']);
        $db->prepare("UPDATE products SET in_stock = CASE WHEN in_stock=1 THEN 0 ELSE 1 END WHERE id=?")->execute([$id]);
        $msg = "Stock status updated.";
    }
}

$categories = ['Fruits & Veg', 'Dairy & Eggs', 'Meat & Fish', 'Bakery', 'Beverages', 'Pantry', 'Snacks', 'Frozen', 'Other'];
$products = $db->query("SELECT * FROM products ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Products – Park View Supermarket Admin</title>
  <link rel="stylesheet" href="../assets/admin.css">
</head>
<body>
<div class="admin-layout">
  <?php include 'sidebar.php'; ?>
  <div class="main-content">
    <?php $pageTitle = 'Products'; include 'topbar.php'; ?>
    <div class="content-area">

      <?php if ($msg): ?>
      <div class="alert alert-<?= $msgType ?>"><?= $msgType==='success'?'✅':'❌' ?> <?= htmlspecialchars($msg) ?></div>
      <?php endif; ?>

      <div class="page-header">
        <div>
          <h1>Products</h1>
          <p>Manage your store's product catalogue</p>
        </div>
        <button class="btn btn-primary" onclick="openModal('add')">+ Add Product</button>
      </div>

      <div class="table-wrap">
        <div class="table-header">
          <h3>All Products (<?= count($products) ?>)</h3>
          <div class="table-search">
            <input type="text" id="searchInput" placeholder="Search products…" oninput="filterTable()">
          </div>
        </div>
        <div style="overflow-x:auto">
          <table id="productsTable">
            <thead>
              <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($products as $p): ?>
              <tr data-search="<?= strtolower(htmlspecialchars($p['name'] . ' ' . $p['category'])) ?>">
                <td>
                  <?php
                  $imgSrc = '';
                  if ($p['image_url']) {
                      $imgSrc = strpos($p['image_url'], 'uploads/') === 0 ? '../' . $p['image_url'] : $p['image_url'];
                  }
                  ?>
                  <?php if ($imgSrc): ?>
                  <img class="td-img" src="<?= htmlspecialchars($imgSrc) ?>" alt="">
                  <?php else: ?>
                  <div style="width:52px;height:40px;background:#f3f4f6;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:20px">🥦</div>
                  <?php endif; ?>
                </td>
                <td>
                  <strong><?= htmlspecialchars($p['name']) ?></strong>
                  <?php if ($p['description']): ?>
                  <br><small style="color:var(--text-muted)"><?= htmlspecialchars(substr($p['description'], 0, 60)) ?>…</small>
                  <?php endif; ?>
                </td>
                <td><span class="badge badge-green"><?= htmlspecialchars($p['category']) ?></span></td>
                <td><strong>$<?= number_format($p['price'], 2) ?></strong></td>
                <td>
                  <form method="POST" style="display:inline">
                    <input type="hidden" name="action" value="toggle_stock">
                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                    <button type="submit" class="badge <?= $p['in_stock'] ? 'badge-green' : 'badge-red' ?>" style="border:none;cursor:pointer;font-size:0.75rem;padding:4px 10px">
                      <?= $p['in_stock'] ? '✓ In Stock' : '✗ Out of Stock' ?>
                    </button>
                  </form>
                </td>
                <td>
                  <div class="actions">
                    <button type="button" class="btn btn-outline btn-sm"
                      data-id="<?= htmlspecialchars($p['id'], ENT_QUOTES) ?>"
                      data-name="<?= htmlspecialchars($p['name'] ?? '', ENT_QUOTES) ?>"
                      data-category="<?= htmlspecialchars($p['category'] ?? '', ENT_QUOTES) ?>"
                      data-price="<?= htmlspecialchars($p['price'], ENT_QUOTES) ?>"
                      data-description="<?= htmlspecialchars($p['description'] ?? '', ENT_QUOTES) ?>"
                      data-in_stock="<?= htmlspecialchars($p['in_stock'], ENT_QUOTES) ?>"
                      data-image_url="<?= htmlspecialchars($p['image_url'] ?? '', ENT_QUOTES) ?>"
                      onclick="openEditModalFromButton(this)">✏️ Edit</button>
                    <form method="POST" onsubmit="return confirm('Delete this product?')" style="display:inline">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?= $p['id'] ?>">
                      <button type="submit" class="btn btn-danger btn-sm">🗑 Delete</button>
                    </form>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>

<div class="modal-backdrop" id="productModal">
  <div class="modal">
    <div class="modal-header">
      <h2 id="modalTitle">Add Product</h2>
      <button class="modal-close" onclick="closeModal()">×</button>
    </div>
    <div class="modal-body">
      <form method="POST" id="productForm" enctype="multipart/form-data">
        <input type="hidden" name="action" id="formAction" value="add">
        <input type="hidden" name="id" id="formId">

        <div class="form-row">
          <div class="form-group">
            <label>Product Name *</label>
            <input type="text" name="name" id="fName" required placeholder="e.g. Organic Avocados">
          </div>
          <div class="form-group">
            <label>Category *</label>
            <select name="category" id="fCategory" required>
              <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat ?>"><?= $cat ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Price ($) *</label>
            <input type="number" name="price" id="fPrice" step="0.01" min="0.01" required placeholder="0.00">
          </div>
          <div class="form-group" style="display:flex;align-items:flex-end;padding-bottom:2px">
            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-weight:500">
              <input type="checkbox" name="in_stock" id="fStock" checked style="width:18px;height:18px;accent-color:var(--green)">
              In Stock
            </label>
          </div>
        </div>

        <input type="hidden" name="existing_image_url" id="fExistingImage">
        <div class="form-group">
          <label>Product Image</label>
          <input type="file" name="image_file" id="fImageFile" accept="image/*">
          <small style="display:block;color:var(--text-muted);margin-top:6px">Upload a JPG, PNG, GIF or WEBP file. Leave blank during edit to keep the current image.</small>
        </div>

        <div class="form-group">
          <label>Description</label>
          <textarea name="description" id="fDesc" placeholder="Brief product description…"></textarea>
        </div>

        <div class="form-actions">
          <button type="button" class="btn btn-outline" onclick="closeModal()">Cancel</button>
          <button type="submit" class="btn btn-primary" id="submitBtn">Add Product</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="products.js"></script>
</body>
</html>
