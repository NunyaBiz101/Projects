<header class="topbar">
  <div style="display:flex;align-items:center;gap:12px;">
    <button onclick="document.getElementById('sidebar').classList.toggle('open')" style="background:none;border:none;cursor:pointer;font-size:20px;display:none;padding:4px" id="sidebarToggle">☰</button>
    <span class="topbar-title"><?= htmlspecialchars($pageTitle ?? 'Admin') ?></span>
  </div>
  <div class="topbar-right">
    <div class="admin-avatar">A</div>
    <div>
      <div class="admin-name"><?= htmlspecialchars($_SESSION['admin_user'] ?? 'Admin') ?></div>
      <div class="admin-role">Administrator</div>
    </div>
  </div>
</header>
<style>@media(max-width:768px){#sidebarToggle{display:block!important}}</style>
