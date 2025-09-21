<?php
// Admin order management page with AJAX status update
session_start();
require_once '../includes/header.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
require_once '../includes/db_connect.php';
// Fetch orders
$orders = [];
$res = $conn->query('SELECT o.*, u.name as student FROM orders o JOIN users u ON o.user_id = u.user_id ORDER BY o.order_date DESC');
while ($row = $res->fetch_assoc()) {
    $orders[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management | Canteen Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="admin_dashboard.php">Canteen Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_menu.php">Menu Management</a></li>
        <li class="nav-item"><a class="nav-link active" href="admin_orders.php">Order Management</a></li>
        <li class="nav-item"><a class="nav-link" href="../backend/logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>
<div class="container mt-5">
    <h3>All Orders</h3>
    <!-- Order status filter for admin -->
    <div class="row mb-3">
        <div class="col-md-4">
            <select id="orderStatusFilter" class="form-select">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>
    <table class="table table-bordered" id="orders-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Student</th>
                <th>Total</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr class="order-row" data-status="<?= strtolower($order['status']) ?>">
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
                <td>
                    <?php if ($order['status'] === 'pending'): ?>
                        <button class="btn btn-success btn-sm update-status-btn" data-id="<?= $order['order_id'] ?>" data-status="completed">Mark Completed</button>
                        <button class="btn btn-danger btn-sm update-status-btn" data-id="<?= $order['order_id'] ?>" data-status="cancelled">Cancel</button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php
        /*
            body: JSON.stringify({order_id: orderId, status})
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
        session_start();
        require_once '../includes/header.php';
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: login.php');
            exit;
        }
        require_once '../includes/db_connect.php';
        // Fetch orders
        $orders = [];
        $res = $conn->query('SELECT o.*, u.name as student FROM orders o JOIN users u ON o.user_id = u.user_id ORDER BY o.order_date DESC');
        while ($row = $res->fetch_assoc()) {
            $orders[] = $row;
        }
        ?>
                showToast('Order updated!', 'success');
                setTimeout(() => location.reload(), 1200);
            } else {
                showToast('Update failed: ' + (data.message || 'Unknown error'), 'danger');
            }
        })
        .catch(err => handleAjaxError(err));
    }
});

// Order status filter logic
document.getElementById('orderStatusFilter').addEventListener('change', function(e) {
    const val = e.target.value;
    document.querySelectorAll('.order-row').forEach(function(row) {
        if (val === '' || row.getAttribute('data-status') === val) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>
</body>
</html>
<?php
require_once '../includes/footer.php';
?>