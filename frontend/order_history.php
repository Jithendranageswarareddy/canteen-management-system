<?php
// Student order history page with AJAX cancel
session_start();
require_once '../includes/header.php';
// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once '../includes/db_connect.php';

// Fetch orders for logged-in student
$orders = [];
$stmt = $conn->prepare('SELECT order_id, order_date, total_amount, status FROM orders WHERE user_id = ? ORDER BY order_date DESC');
$stmt->bind_param('i', $_SESSION['user_id']);
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
    <title>Order History | College Canteen</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="student_dashboard.php">Canteen</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="student_dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="view_menu.php">View Menu</a></li>
        <li class="nav-item"><a class="nav-link active" href="order_history.php">Order History</a></li>
        <li class="nav-item"><a class="nav-link" href="../backend/logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>
<div class="container mt-5">
    <h3>Order History</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Total</th>
                <th>Status</th>
                <th>Action</th>
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
                    // Color-code status badges
                    $badge = 'warning';
                    if ($order['status'] === 'completed') $badge = 'success';
                    if ($order['status'] === 'cancelled') $badge = 'danger';
                    ?>
                    <span class="badge bg-<?= $badge ?>"><?= ucfirst($order['status']) ?></span>
                </td>
                <td>
                    <?php if ($order['status'] === 'pending'): ?>
                        <button class="btn btn-danger btn-sm cancel-order-btn" data-id="<?= $order['order_id'] ?>">Cancel</button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/script.js"></script>
<script>
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('cancel-order-btn')) {
        if (!confirm('Are you sure you want to cancel this order?')) return;
        const orderId = e.target.dataset.id;
        fetch('../backend/cancel_order.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({order_id: orderId})
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast('Order cancelled!', 'success');
                setTimeout(() => location.reload(), 1200);
            } else {
                showToast('Cancel failed: ' + (data.message || 'Unknown error'), 'danger');
            }
        })
        .catch(err => handleAjaxError(err));
    }
});
</script>
</body>
</html>
<?php
require_once '../includes/footer.php';
?>