  </div><!-- /.admin-content -->
</div><!-- /.admin-main -->

<script>
// Sidebar toggle for mobile
document.getElementById('sidebarToggle').addEventListener('click', function() {
  document.getElementById('adminSidebar').classList.toggle('open');
});
// Confirm delete
document.querySelectorAll('[data-confirm]').forEach(function(el) {
  el.addEventListener('click', function(e) {
    if (!confirm(this.dataset.confirm || 'Are you sure?')) {
      e.preventDefault();
    }
  });
});
</script>
</body>
</html>
