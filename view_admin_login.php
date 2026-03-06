<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login – Bella Vista</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-login-page">

<div class="login-wrapper">
  <div class="login-card">
    <div class="login-logo">
      <span>BELLA<span class="accent"> VISTA</span></span>
      <small>Admin Panel</small>
    </div>

    <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= clean($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="admin.php?action=login">
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" placeholder="Enter username"
               value="<?= clean($_POST['username'] ?? '') ?>" required autofocus>
      </div>
      <div class="form-group">
        <label>Password</label>
        <div class="input-icon-wrap">
          <input type="password" name="password" id="passwordField" placeholder="Enter password" required>
          <button type="button" class="toggle-pass" onclick="togglePass()">👁</button>
        </div>
      </div>
      <button type="submit" class="btn-admin-primary btn-block">Sign In</button>
    </form>

    <div class="login-footer">
      <a href="index.php">&larr; Back to Website</a>
    </div>
  </div>
</div>

<script>
function togglePass() {
  const f = document.getElementById('passwordField');
  f.type = f.type === 'password' ? 'text' : 'password';
}
</script>
</body>
</html>
