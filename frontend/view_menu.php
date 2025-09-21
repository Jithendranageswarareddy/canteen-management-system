<?php
// Student menu page with AJAX cart
session_start();
require_once '../includes/header.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit;
}
require_once '../includes/db_connect.php';
// Fetch menu items
$menu = [];
$res = $conn->query('SELECT * FROM menu ORDER BY item_id DESC');
while ($row = $res->fetch_assoc()) {
    $menu[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Menu | College Canteen</title>
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
        <li class="nav-item"><a class="nav-link active" href="view_menu.php">View Menu</a></li>
        <li class="nav-item"><a class="nav-link" href="order_history.php">Order History</a></li>
        <li class="nav-item"><a class="nav-link" href="../backend/logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>
<div class="container mt-5">
    <h3>Menu</h3>
    <!-- Search/filter bar for menu items -->
    <div class="row mb-3">
        <div class="col-md-6">
            <input type="text" id="menuSearch" class="form-control" placeholder="Search menu by name or category...">
        </div>
    </div>
    <div class="row" id="menu-list">
        <?php foreach ($menu as $item): ?>
        <div class="col-md-4 mb-4 menu-item" data-name="<?= strtolower($item['item_name']) ?>" data-category="<?= strtolower($item['category']) ?>">
            <div class="card h-100">
                <?php
                // Determine image source
                $img = $item['image'] && file_exists("../assets/uploads/{$item['image']}") ? "../assets/uploads/{$item['image']}" : "../assets/img/placeholder_food.png";
                ?>
                <img src="<?= $img ?>" class="card-img-top" alt="<?= htmlspecialchars($item['item_name']) ?>" style="height:180px;object-fit:cover;">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($item['item_name']) ?></h5>
                    <p class="card-text">Category: <?= htmlspecialchars($item['category'] ?? '') ?></p>
                    <p class="card-text">Price: ₹<?= number_format($item['price'],2) ?></p>
                    <button class="btn btn-success add-to-cart" data-id="<?= $item['item_id'] ?>" data-name="<?= htmlspecialchars($item['item_name']) ?>" data-price="<?= $item['price'] ?>">Add to Cart</button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <hr>
    <h4>Your Cart</h4>
    <div id="cart-container">
        <table class="table table-bordered" id="cart-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Total</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>
                <!-- Cart items will be injected by JS -->
            <?php
            /*
        <div class="modal-body">
          <p>Are you sure you want to place this order?</p>
          <div id="order-summary"></div>
        </div>
        <div class="modal-footer">
            session_start();
            require_once '../includes/header.php';
            if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
                header('Location: login.php');
                exit;
            }
            require_once '../includes/db_connect.php';
            // Fetch menu items
            $menu = [];
            $res = $conn->query('SELECT * FROM menu ORDER BY item_id DESC');
            while ($row = $res->fetch_assoc()) {
                $menu[] = $row;
            }
            ?>
          <button type="submit" class="btn btn-success">Confirm</button>
        </div>
      </div>
    </form>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/script.js"></script>
<script>
// Simple cart logic
let cart = {};
// Update cart table in UI
function updateCartTable() {
    const tbody = document.querySelector('#cart-table tbody');
    tbody.innerHTML = '';
    let total = 0;
    Object.values(cart).forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `<td>${item.name}</td><td>₹${item.price.toFixed(2)}</td><td><input type='number' min='1' value='${item.qty}' class='form-control qty-input' data-id='${item.id}' style='width:70px'></td><td>₹${(item.price * item.qty).toFixed(2)}</td><td><button class='btn btn-danger btn-sm remove-item' data-id='${item.id}'>Remove</button></td>`;
        tbody.appendChild(row);
        total += item.price * item.qty;
    });
    document.getElementById('place-order-btn').disabled = Object.keys(cart).length === 0;
}

// Menu search/filter logic
document.getElementById('menuSearch').addEventListener('input', function(e) {
    const val = e.target.value.trim().toLowerCase();
    document.querySelectorAll('.menu-item').forEach(function(item) {
        const name = item.getAttribute('data-name');
        const category = item.getAttribute('data-category');
        if (name.includes(val) || category.includes(val) || val === '') {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
});
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('add-to-cart')) {
        const id = e.target.dataset.id;
        if (!cart[id]) {
            cart[id] = {
                id,
                name: e.target.dataset.name,
                price: parseFloat(e.target.dataset.price),
                qty: 1
            };
        } else {
            cart[id].qty++;
        }
        updateCartTable();
    }
    if (e.target.classList.contains('remove-item')) {
        delete cart[e.target.dataset.id];
        updateCartTable();
    }
});
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('qty-input')) {
        const id = e.target.dataset.id;
        let val = parseInt(e.target.value);
        if (val < 1) val = 1;
        cart[id].qty = val;
        updateCartTable();
    }
});
document.getElementById('place-order-btn').addEventListener('click', function() {
    // Show order summary in modal
    let summary = '<ul>';
    let total = 0;
    Object.values(cart).forEach(item => {
        summary += `<li>${item.name} x ${item.qty} = ₹${(item.price * item.qty).toFixed(2)}</li>`;
        total += item.price * item.qty;
    });
    summary += `</ul><strong>Total: ₹${total.toFixed(2)}</strong>`;
    document.getElementById('order-summary').innerHTML = summary;
});
document.getElementById('order-form').addEventListener('submit', function(e) {
    e.preventDefault();
    // Prepare cart data for backend
    const cartData = Object.values(cart).map(item => ({
        itemId: parseInt(item.id),
        quantity: item.qty,
        price: item.price
    }));
    fetch('../backend/place_order.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({cart: cartData})
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            cart = {};
            updateCartTable();
            alert('Order placed successfully!');
            location.reload();
        } else {
            alert('Order failed: ' + (data.message || 'Unknown error'));
        }
    });
    document.getElementById('placeOrderModal').querySelector('.btn-close').click();
});
updateCartTable();
</script>
</body>
</html>
<?php
require_once '../includes/footer.php';
?>