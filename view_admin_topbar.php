<header class="admin-topbar">
  <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Toggle sidebar">&#9776;</button>
  <div class="topbar-title"><?= clean($page_title ?? 'Admin') ?></div>
  <div class="topbar-right">
    <span class="topbar-user">👤 <?= clean($_SESSION['admin_username'] ?? 'Admin') ?></span>
    <a href="admin.php?action=logout" class="topbar-logout">Logout</a>
  </div>
</header>

<script>
function toggleSidebar() {
  document.getElementById('adminSidebar').classList.toggle('open');
  document.getElementById('sidebarOverlay').classList.toggle('visible');
}
</script>
