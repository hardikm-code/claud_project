<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= clean($page_title ?? 'Welcome') ?> – Bella Vista</title>
  <meta name="description" content="Bella Vista – Fine Dining Experience. Exquisite cuisine, impeccable service, unforgettable memories.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- ── Navigation ────────────────────────────────────────────────── -->
<nav class="navbar" id="navbar">
  <div class="nav-container">
    <a href="index.php" class="nav-logo">
      <span class="logo-text">BELLA<span class="logo-accent"> VISTA</span></span>
    </a>

    <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
      <span></span><span></span><span></span>
    </button>

    <ul class="nav-links" id="navLinks">
      <li><a href="index.php" class="<?= ($active_page ?? '') === 'home' ? 'active' : '' ?>">Home</a></li>
      <li><a href="index.php?page=menu" class="<?= ($active_page ?? '') === 'menu' ? 'active' : '' ?>">Menu</a></li>
      <li><a href="index.php?page=reservation" class="<?= ($active_page ?? '') === 'reservation' ? 'active' : '' ?>">Reservations</a></li>
      <li><a href="#contact" class="<?= ($active_page ?? '') === 'contact' ? 'active' : '' ?>">Contact</a></li>
    </ul>

    <a href="index.php?page=reservation" class="btn-book">Book a Table</a>
  </div>
</nav>
