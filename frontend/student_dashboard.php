<?php
// Student dashboard page
session_start();
require_once '../includes/header.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit;
}
require_once '../includes/db_connect.php';
$user_id = $_SESSION['user_id'];
// Recent orders (secure: use prepared statement)
$orders = [];
$stmt = $conn->prepare("SELECT order_id, order_date, total_amount, status FROM orders WHERE user_id = ? ORDER BY order_date DESC LIMIT 5");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($order_id, $order_date, $total_amount, $status);
while ($stmt->fetch()) {
    $orders[] = [
        'order_id' => $order_id,
        'order_date' => $order_date,
        'total_amount' => $total_amount,
        'status' => $status
    ];
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | College Canteen</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Canteen</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" href="student_dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="view_menu.php">View Menu</a></li>
        <li class="nav-item"><a class="nav-link" href="order_history.php">Order History</a></li>
        <li class="nav-item"><a class="nav-link" href="../backend/logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>
<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-bg-success mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">View Menu</h5>
                    <a href="view_menu.php" class="btn btn-light">Browse Menu</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-info mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">Order History</h5>
                    <a href="order_history.php" class="btn btn-light">View Orders</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-primary mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">Logout</h5>
                    <a href="../backend/logout.php" class="btn btn-light">Logout</a>
                </div>
            </div>
        </div>
    </div>
    <h3>Recent Orders</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= $order['order_id'] ?></td>
                <td><?= $order['order_date'] ?></td>
                <td>₹<?= number_format($order['total_amount'],2) ?></td>
                <td>
                    <?php
                    $badge = 'warning';
                    if ($order['status'] === 'completed') $badge = 'success';
                    if ($order['status'] === 'cancelled') $badge = 'danger';
                    ?>
                    <span class="badge bg-<?= $badge ?>"><?= ucfirst($order['status']) ?></span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
require_once '../includes/footer.php';
?>