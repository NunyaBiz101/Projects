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
        $title            = trim($_POST['title'] ?? '');
        $description      = trim($_POST['description'] ?? '');
        $discount         = intval($_POST['discount_percent'] ?? 0);
        $origPrice        = floatval($_POST['original_price'] ?? 0);
        $salePrice        = floatval($_POST['sale_price'] ?? 0);
        $imageUrl         = '';
        $existingImageUrl = trim($_POST['existing_image_url'] ?? '');
        $validUntil       = trim($_POST['valid_until'] ?? '');
        $active           = isset($_POST['active']) ? 1 : 0;

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
                $newName = uniqid('deal_', true) . '.' . $uploadedExt;
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
            if ($title && ($action === 'edit' || $imageUrl)) {
                if ($action === 'add') {
                    $stmt = $db->prepare("INSERT INTO deals (title, description, discount_percent, original_price, sale_price, image_url, valid_until, active) VALUES (?,?,?,?,?,?,?,?)");
                    $stmt->execute([$title, $description, $discount, $origPrice ?: null, $salePrice ?: null, $imageUrl, $validUntil ?: null, $active]);
                    $msg = "Deal \"$title\" added!";
                } else {
                    $id = intval($_POST['id']);
                    $stmt = $db->prepare("UPDATE deals SET title=?, description=?, discount_percent=?, original_price=?, sale_price=?, image_url=?, valid_until=?, active=? WHERE id=?");
                    $stmt->execute([$title, $description, $discount, $origPrice ?: null, $salePrice ?: null, $imageUrl, $validUntil ?: null, $active, $id]);
                    $msg = "Deal \"$title\" updated!";
                }
            } else {
                $msg = 'Please fill in all required fields and upload a deal image.';
                $msgType = 'error';
            }
        }
    }

    if ($action === 'delete') {
        $id = intval($_POST['id']);
        $db->prepare("DELETE FROM deals WHERE id=?")->execute([$id]);
        $msg = "Deal deleted.";
    }

    if ($action === 'toggle') {
        $id = intval($_POST['id']);
        $db->prepare("UPDATE deals SET active = CASE WHEN active=1 THEN 0 ELSE 1 END WHERE id=?")->execute([$id]);
        $msg = "Deal status toggled.";
    }
}

$deals = $db->query("SELECT * FROM deals ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Deals – Park View Supermarket Admin</title>
  <link rel="stylesheet" href="../assets/admin.css">
</head>
<body>
<div class="admin-layout">
  <?php include 'sidebar.php'; ?>
  <div class="main-content">
    <?php $pageTitle = 'Deals & Offers'; include 'topbar.php'; ?>
    <div class="content-area">

      <?php if ($msg): ?>
      <div class="alert alert-<?= $msgType ?>"><?= $msgType==='success'?'✅':'❌' ?> <?= htmlspecialchars($msg) ?></div>
      <?php endif; ?>

      <div class="page-header">
        <div>
          <h1>Deals & Offers</h1>
          <p>Create and manage promotional deals for your customers</p>
        </div>
        <button class="btn btn-primary" onclick="openModal()">+ Add Deal</button>
      </div>

      <div class="table-wrap">
        <div class="table-header">
          <h3>All Deals (<?= count($deals) ?>)</h3>
        </div>
        <div style="overflow-x:auto">
          <table>
            <thead>
              <tr>
                <th>Image</th>
                <th>Title</th>
                <th>Discount</th>
                <th>Original</th>
                <th>Sale Price</th>
                <th>Valid Until</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($deals as $d): ?>
              <tr>
                <td>
                  <?php
                  $imgSrc = '';
                  if ($d['image_url']) {
                      $imgSrc = strpos($d['image_url'], 'uploads/') === 0 ? '../' . $d['image_url'] : $d['image_url'];
                  }
                  ?>
                  <?php if ($imgSrc): ?>
                  <img class="td-img" src="<?= htmlspecialchars($imgSrc) ?>" alt="">
                  <?php else: ?>
                  <div style="width:52px;height:40px;background:#f3f4f6;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:20px">🔥</div>
                  <?php endif; ?>
                </td>
                <td>
                  <strong><?= htmlspecialchars($d['title']) ?></strong>
                  <?php if ($d['description']): ?>
                  <br><small style="color:var(--text-muted)"><?= htmlspecialchars(substr($d['description'], 0, 60)) ?>…</small>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($d['discount_percent']): ?>
                  <span class="badge badge-red">-<?= $d['discount_percent'] ?>%</span>
                  <?php else: ?><span style="color:var(--text-muted)">–</span><?php endif; ?>
                </td>
                <td><?= $d['original_price'] ? '$'.number_format($d['original_price'],2) : '–' ?></td>
                <td><strong style="color:var(--green)"><?= $d['sale_price'] ? '$'.number_format($d['sale_price'],2) : '–' ?></strong></td>
                <td style="font-size:0.85rem;color:var(--text-muted)"><?= $d['valid_until'] ?: '–' ?></td>
                <td>
                  <form method="POST" style="display:inline">
                    <input type="hidden" name="action" value="toggle">
                    <input type="hidden" name="id" value="<?= $d['id'] ?>">
                    <button type="submit" class="badge <?= $d['active'] ? 'badge-green' : 'badge-gold' ?>" style="border:none;cursor:pointer;font-size:0.75rem;padding:4px 10px">
                      <?= $d['active'] ? '● Active' : '○ Inactive' ?>
                    </button>
                  </form>
                </td>
                <td>
                  <div class="actions">
                    <button class="btn btn-outline btn-sm" onclick="openEditModal(<?= htmlspecialchars(json_encode($d)) ?>)">✏️ Edit</button>
                    <form method="POST" onsubmit="return confirm('Delete this deal?')" style="display:inline">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?= $d['id'] ?>">
                      <button type="submit" class="btn btn-danger btn-sm">🗑</button>
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

<div class="modal-backdrop" id="dealModal">
  <div class="modal">
    <div class="modal-header">
      <h2 id="modalTitle">Add Deal</h2>
      <button class="modal-close" onclick="closeModal()">×</button>
    </div>
    <div class="modal-body">
      <form method="POST" id="dealForm" enctype="multipart/form-data">
        <input type="hidden" name="action" id="formAction" value="add">
        <input type="hidden" name="id" id="formId">
        <input type="hidden" name="existing_image_url" id="fExistingImage">
        <div class="form-group">
          <label>Deal Title *</label>
          <input type="text" name="title" id="fTitle" required placeholder="e.g. Weekend Fresh Deal">
        </div>

        <div class="form-group">
          <label>Description</label>
          <textarea name="description" id="fDesc" placeholder="Describe the offer…"></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Discount %</label>
            <input type="number" name="discount_percent" id="fDiscount" min="0" max="100" placeholder="25">
          </div>
          <div class="form-group">
            <label>Valid Until</label>
            <input type="date" name="valid_until" id="fValid">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Original Price ($)</label>
            <input type="number" name="original_price" id="fOriginal" step="0.01" min="0" placeholder="0.00">
          </div>
          <div class="form-group">
            <label>Sale Price ($)</label>
            <input type="number" name="sale_price" id="fSale" step="0.01" min="0" placeholder="0.00">
          </div>
        </div>

        <div class="form-group">
          <label>Deal Image</label>
          <input type="file" name="image_file" id="fImageFile" accept="image/*">
          <small style="display:block;color:var(--text-muted);margin-top:6px">Upload a JPG, PNG, GIF or WEBP file. Leave blank during edit to keep the current image.</small>
        </div>

        <div class="form-group">
          <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-weight:500">
            <input type="checkbox" name="active" id="fActive" checked style="width:18px;height:18px;accent-color:var(--green)">
            Active (visible on store)
          </label>
        </div>

        <div class="form-actions">
          <button type="button" class="btn btn-outline" onclick="closeModal()">Cancel</button>
          <button type="submit" class="btn btn-primary" id="submitBtn">Add Deal</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function openModal() {
  document.getElementById('modalTitle').textContent = 'Add Deal';
  document.getElementById('formAction').value = 'add';
  document.getElementById('dealForm').reset();
  document.getElementById('fExistingImage').value = '';
  document.getElementById('fImageFile').required = true;
  document.getElementById('fActive').checked = true;
  document.getElementById('submitBtn').textContent = 'Add Deal';
  document.getElementById('dealModal').classList.add('open');
}

function openEditModal(d) {
  document.getElementById('modalTitle').textContent = 'Edit Deal';
  document.getElementById('formAction').value = 'edit';
  document.getElementById('formId').value = d.id;
  document.getElementById('fTitle').value = d.title;
  document.getElementById('fDesc').value = d.description || '';
  document.getElementById('fDiscount').value = d.discount_percent || '';
  document.getElementById('fOriginal').value = d.original_price || '';
  document.getElementById('fSale').value = d.sale_price || '';
  document.getElementById('fExistingImage').value = d.image_url || '';
  document.getElementById('fImageFile').value = '';
  document.getElementById('fImageFile').required = false;
  document.getElementById('fValid').value = d.valid_until || '';
  document.getElementById('fActive').checked = d.active == 1;
  document.getElementById('submitBtn').textContent = 'Save Changes';
  document.getElementById('dealModal').classList.add('open');
}

function closeModal() {
  document.getElementById('dealModal').classList.remove('open');
}

document.getElementById('dealModal').addEventListener('click', function(e) {
  if (e.target === this) closeModal();
});
</script>
</body>
</html>
