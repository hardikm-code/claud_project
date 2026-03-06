<aside class="admin-sidebar" id="adminSidebar">
  <div class="sidebar-logo">
    <span>BELLA<span class="accent"> VISTA</span></span>
    <small>Admin Panel</small>
  </div>

  <?php $current = $_GET['action'] ?? 'dashboard'; ?>

  <nav class="sidebar-nav">
    <div class="nav-section-label">Main</div>
    <a href="admin.php?action=dashboard"
       class="sidebar-link <?= $current === 'dashboard' ? 'active' : '' ?>">
      <span class="sl-icon">⊞</span> Dashboard
    </a>

    <div class="nav-section-label">Menu</div>
    <a href="admin.php?action=menu_list"
       class="sidebar-link <?= in_array($current, ['menu_list','menu_add','menu_edit']) ? 'active' : '' ?>">
      <span class="sl-icon">🍽</span> Menu Items
    </a>
    <a href="admin.php?action=menu_add"
       class="sidebar-link <?= $current === 'menu_add' ? 'active' : '' ?>">
      <span class="sl-icon">+</span> Add New Item
    </a>

    <div class="nav-section-label">Bookings</div>
    <a href="admin.php?action=reservations"
       class="sidebar-link <?= $current === 'reservations' ? 'active' : '' ?>">
      <span class="sl-icon">📋</span> All Reservations
    </a>
    <a href="admin.php?action=reservations&status=pending"
       class="sidebar-link">
      <span class="sl-icon">⏳</span> Pending
    </a>
    <a href="admin.php?action=reservations&status=confirmed"
       class="sidebar-link">
      <span class="sl-icon">✓</span> Confirmed
    </a>

    <div class="nav-section-label">System</div>
    <a href="index.php" target="_blank" class="sidebar-link">
      <span class="sl-icon">🌐</span> View Website
    </a>
    <a href="admin.php?action=logout" class="sidebar-link sidebar-logout">
      <span class="sl-icon">⏏</span> Logout
    </a>
  </nav>
</aside>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
