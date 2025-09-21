<?php
// College Canteen Management System Homepage
require_once '../includes/header.php';
require_once '../includes/db_connect.php';
// Fetch featured menu items (top 4)
$featured = [];
$res = $conn->query('SELECT * FROM menu WHERE availability="yes" ORDER BY RAND() LIMIT 4');
while ($row = $res->fetch_assoc()) {
    $featured[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="College Canteen Management System - Order food online, manage menu, and track orders. Modern, responsive, and secure.">
  <meta name="author" content="[Your Name]">
  <meta name="keywords" content="canteen, food, college, menu, order, PHP, Bootstrap, project">
  <link rel="icon" type="image/x-icon" href="../assets/img/favicon.ico">
  <title>College Canteen | Home</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <!-- Project Version: v1.0.0 -->
  <style>
    .hero-bg {background: linear-gradient(90deg,#ff7043 60%,#fff3e0 100%);}
    .hero-img {max-width: 350px; border-radius: 1rem; box-shadow: 0 4px 24px rgba(0,0,0,0.12);}
    .featured-card {transition: box-shadow .2s;}
    .featured-card:hover {box-shadow: 0 4px 24px rgba(255,112,67,0.18);}
  </style>
</head>
<body>
<!-- Header/Navbar -->
<!--
  Welcome to the College Canteen Management System!
  This project was created as a college mini project by [Your Name].
  Enjoy browsing the menu, placing orders, and managing the canteen with a modern, user-friendly interface.
-->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">College Canteen</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
        <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Hero Section -->
<div class="container-fluid hero-bg py-5 mb-5">
  <div class="row align-items-center">
    <div class="col-md-6 text-center text-md-start">
  <h1 class="display-4 fw-bold mb-3">Welcome to Your College Canteen!</h1>
  <p class="lead mb-4">Hi there! This is your friendly online canteen. Browse the menu, place orders, and enjoy delicious meals with just a few clicks. Created with care for students and staff.</p>
      <a href="login.php" class="btn btn-lg btn-warning me-2">Get Started</a>
      <a href="view_menu.php" class="btn btn-lg btn-outline-primary">Browse Menu</a>
    </div>
    <div class="col-md-6 text-center">
      <img src="../assets/img/hero_food.png" alt="Canteen Food" class="hero-img">
    </div>
  </div>
</div>

<!-- Featured Menu -->
<div class="container mb-5">
  <h2 class="text-center mb-4">Featured Items</h2>
  <div class="row justify-content-center">
    <?php foreach ($featured as $item): ?>
    <?php $img = $item['image'] && file_exists("../assets/uploads/{$item['image']}") ? "../assets/uploads/{$item['image']}" : "../assets/img/placeholder_food.png"; ?>
    <div class="col-md-3 mb-4">
      <div class="card featured-card h-100">
        <img src="<?= $img ?>" class="card-img-top" alt="<?= htmlspecialchars($item['item_name']) ?>" style="height:180px;object-fit:cover;">
        <div class="card-body text-center">
          <h5 class="card-title mb-2"><?= htmlspecialchars($item['item_name']) ?></h5>
          <p class="card-text text-muted mb-2">₹<?= number_format($item['price'],2) ?></p>
          <p class="card-text small mb-3"><?= htmlspecialchars($item['description']) ?></p>
          <a href="login.php" class="btn btn-sm btn-success">Order Now</a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- Call to Action -->
<div class="container mb-5">
  <div class="row justify-content-center">
    <div class="col-md-8 text-center p-4 bg-light rounded shadow">
  <h3 class="mb-3">Why wait in line?</h3>
  <p class="mb-4">Order online, pick up your food when it's ready, and spend more time enjoying your break. We hope you love the experience!</p>
      <a href="register.php" class="btn btn-lg btn-primary">Create Account</a>
    </div>
  </div>
</div>

<?php require_once '../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>