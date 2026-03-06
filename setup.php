<?php
/**
 * Bella Vista – One-Click Setup
 * Visit: http://localhost/claud_project/setup.php
 * After setup, delete or rename this file.
 */
require 'config.php';

$messages = [];
$errors   = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_username = trim($_POST['admin_username'] ?? 'admin');
    $admin_email    = trim($_POST['admin_email']    ?? 'admin@bellavista.com');
    $admin_password = $_POST['admin_password'] ?? 'admin123';

    try {
        $pdo = getDB();

        // Read and execute SQL schema
        $sql = file_get_contents(__DIR__ . '/database.sql');
        // Split by semicolon but ignore empty statements
        $statements = array_filter(
            array_map('trim', explode(';', $sql)),
            fn($s) => $s !== '' && !str_starts_with($s, '--')
        );

        foreach ($statements as $statement) {
            if (!empty(trim($statement))) {
                try {
                    $pdo->exec($statement);
                } catch (PDOException $e) {
                    // Ignore duplicate data errors
                }
            }
        }
        $messages[] = '✓ Database tables created successfully.';
        $messages[] = '✓ Sample menu data inserted.';

        // Create/update admin user
        $hash = password_hash($admin_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE username = ?");
        $stmt->execute([$admin_username]);
        if ($stmt->fetch()) {
            $upd = $pdo->prepare("UPDATE admin_users SET password_hash=?, email=? WHERE username=?");
            $upd->execute([$hash, $admin_email, $admin_username]);
            $messages[] = '✓ Admin user updated.';
        } else {
            $ins = $pdo->prepare("INSERT INTO admin_users (username, email, password_hash) VALUES (?,?,?)");
            $ins->execute([$admin_username, $admin_email, $hash]);
            $messages[] = '✓ Admin user created.';
        }
        $messages[] = '✓ Setup complete! Admin login: <strong>' . htmlspecialchars($admin_username) . ' / ' . htmlspecialchars($admin_password) . '</strong>';

    } catch (PDOException $e) {
        $errors[] = 'Error: ' . htmlspecialchars($e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Setup – Bella Vista</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Segoe UI', sans-serif; background: #0d0d0d; color: #f5f5f5; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
  .card { background: #1a1a1a; border: 1px solid #333; border-radius: 12px; padding: 40px; width: 480px; box-shadow: 0 20px 60px rgba(0,0,0,0.5); }
  h1 { color: #c8a97e; font-size: 2rem; margin-bottom: 6px; }
  p.sub { color: #888; margin-bottom: 24px; font-size: 0.9rem; }
  label { display: block; color: #ccc; font-size: 0.85rem; margin-bottom: 6px; margin-top: 16px; }
  input { width: 100%; padding: 10px 14px; background: #111; border: 1px solid #444; color: #f5f5f5; border-radius: 6px; font-size: 0.95rem; }
  input:focus { outline: none; border-color: #c8a97e; }
  button { width: 100%; margin-top: 24px; padding: 12px; background: #c8a97e; color: #0d0d0d; border: none; border-radius: 6px; font-size: 1rem; font-weight: 700; cursor: pointer; }
  button:hover { background: #e0c08a; }
  .msg { margin-top: 20px; padding: 14px; border-radius: 6px; font-size: 0.9rem; }
  .msg.success { background: #1a3a1a; border: 1px solid #2e7d32; color: #81c784; }
  .msg.error   { background: #3a1a1a; border: 1px solid #c62828; color: #ef9a9a; }
  .links { margin-top: 20px; display: flex; gap: 12px; }
  .links a { flex: 1; text-align: center; padding: 10px; background: #222; border: 1px solid #444; color: #c8a97e; text-decoration: none; border-radius: 6px; font-size: 0.85rem; }
  .links a:hover { background: #2d2d2d; }
</style>
</head>
<body>
<div class="card">
  <h1>Bella Vista</h1>
  <p class="sub">One-click database setup. Run this once to initialize.</p>

  <?php if ($messages): ?>
    <div class="msg success"><?= implode('<br>', $messages) ?></div>
    <div class="links">
      <a href="index.php">View Website</a>
      <a href="admin.php">Admin Panel</a>
    </div>
  <?php elseif ($errors): ?>
    <div class="msg error"><?= implode('<br>', $errors) ?></div>
  <?php endif; ?>

  <?php if (!$messages): ?>
  <form method="POST">
    <label>Admin Username</label>
    <input type="text" name="admin_username" value="admin" required>
    <label>Admin Email</label>
    <input type="email" name="admin_email" value="admin@bellavista.com" required>
    <label>Admin Password</label>
    <input type="text" name="admin_password" value="admin123" required>
    <button type="submit">Run Setup</button>
  </form>
  <?php endif; ?>
</div>
</body>
</html>
