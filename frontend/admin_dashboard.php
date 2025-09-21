<?php
// Admin dashboard page
session_start();
require_once '../includes/header.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
require_once '../includes/db_connect.php';

// Summary stats (secure: use prepared statements)
$total_orders = $pending_orders = $completed_orders = $total_revenue = 0;
$stmt = $conn->prepare('SELECT COUNT(*) as total, SUM(total_amount) as revenue FROM orders');
$stmt->execute();
$stmt->bind_result($total_orders, $total_revenue);
$stmt->fetch();
<?php
/*
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" href="admin_dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_menu.php">Menu Management</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_orders.php">Order Management</a></li>
session_start();
require_once '../includes/header.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
require_once '../includes/db_connect.php';

// Summary stats (secure: use prepared statements)
$total_orders = $pending_orders = $completed_orders = $total_revenue = 0;
$stmt = $conn->prepare('SELECT COUNT(*) as total, SUM(total_amount) as revenue FROM orders');
$stmt->execute();
$stmt->bind_result($total_orders, $total_revenue);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) as pending FROM orders WHERE status='pending'");
$stmt->execute();
$stmt->bind_result($pending_orders);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) as completed FROM orders WHERE status='completed'");
$stmt->execute();
$stmt->bind_result($completed_orders);
$stmt->fetch();
$stmt->close();

// Recent orders (secure: use prepared statement)
$orders = [];
$stmt = $conn->prepare('SELECT o.order_id, o.order_date, o.total_amount, o.status, u.name as student FROM orders o JOIN users u ON o.user_id = u.user_id ORDER BY o.order_date DESC LIMIT 10');
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}
$stmt->close();
?>
        <li class="nav-item"><a class="nav-link" href="../backend/logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>
<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Orders</h5>
                    <p class="card-text fs-3"><?= $total_orders ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">Pending Orders</h5>
                    <p class="card-text fs-3"><?= $pending_orders ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Completed Orders</h5>
                    <p class="card-text fs-3"><?= $completed_orders ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Revenue</h5>
                    <p class="card-text fs-3">₹<?= number_format($total_revenue,2) ?></p>
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
                <th>Student</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= $order['order_id'] ?></td>
                <td><?= $order['order_date'] ?></td>
                <td><?= htmlspecialchars($order['student']) ?></td>
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