<?php
require_once 'auth.php';
require_once '../config.php';
$db = getDB();

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $db->prepare("DELETE FROM contacts WHERE id=?")->execute([intval($_POST['delete_id'])]);
    $msg = "Message deleted.";
}

$contacts = $db->query("SELECT * FROM contacts ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Messages – Park View Supermarket Admin</title>
  <link rel="stylesheet" href="../assets/admin.css">
</head>
<body>
<div class="admin-layout">
  <?php include 'sidebar.php'; ?>
  <div class="main-content">
    <?php $pageTitle = 'Customer Messages'; include 'topbar.php'; ?>
    <div class="content-area">

      <?php if ($msg): ?>
      <div class="alert alert-success">✅ <?= htmlspecialchars($msg) ?></div>
      <?php endif; ?>

      <div class="page-header">
        <div>
          <h1>Customer Messages</h1>
          <p><?= count($contacts) ?> total message<?= count($contacts) != 1 ? 's' : '' ?> received</p>
        </div>
      </div>

      <?php if (empty($contacts)): ?>
      <div style="text-align:center;padding:80px;background:white;border-radius:12px;box-shadow:var(--shadow)">
        <div style="font-size:48px;margin-bottom:16px">✉️</div>
        <h3 style="margin-bottom:8px">No messages yet</h3>
        <p style="color:var(--text-muted)">Customer messages from the contact form will appear here.</p>
      </div>
      <?php else: ?>
      <div class="table-wrap">
        <div style="overflow-x:auto">
          <table>
            <thead>
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Message</th>
                <th>Date</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($contacts as $i => $c): ?>
              <tr>
                <td style="color:var(--text-muted)"><?= $i+1 ?></td>
                <td><strong><?= htmlspecialchars($c['name']) ?></strong></td>
                <td><a href="mailto:<?= htmlspecialchars($c['email']) ?>" style="color:var(--green);text-decoration:none"><?= htmlspecialchars($c['email']) ?></a></td>
                <td style="max-width:400px">
                  <div style="cursor:pointer" onclick="this.classList.toggle('expanded')" title="Click to expand">
                    <div class="msg-preview" style="overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical"><?= htmlspecialchars($c['message']) ?></div>
                  </div>
                </td>
                <td style="color:var(--text-muted);font-size:0.8rem;white-space:nowrap"><?= htmlspecialchars(substr($c['created_at'], 0, 16)) ?></td>
                <td>
                  <form method="POST" onsubmit="return confirm('Delete this message?')">
                    <input type="hidden" name="delete_id" value="<?= $c['id'] ?>">
                    <button type="submit" class="btn btn-danger btn-sm">🗑 Delete</button>
                  </form>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <?php endif; ?>

    </div>
  </div>
</div>
</body>
</html>
