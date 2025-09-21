<?php
// Admin menu management page
session_start();
require_once '../includes/header.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
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
    <title>Menu Management | Canteen Admin</title>
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
        <li class="nav-item"><a class="nav-link active" href="admin_menu.php">Menu Management</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_orders.php">Order Management</a></li>
        <li class="nav-item"><a class="nav-link" href="../backend/logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Menu Items</h3>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addItemModal">Add Item</button>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Price</th>
                <th>Category</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($menu as $item): ?>
            <tr>
                <td><?= $item['item_id'] ?></td>
                <td>
                  <?php
                  // Determine image source
                  $img = $item['image'] && file_exists("../assets/uploads/{$item['image']}") ? "../assets/uploads/{$item['image']}" : "../assets/img/placeholder_food.png";
                  ?>
                  <img src="<?= $img ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="height:50px;width:50px;object-fit:cover;border-radius:8px;">
                </td>
                <td><?= htmlspecialchars($item['item_name']) ?></td>
                <td>₹<?= number_format($item['price'],2) ?></td>
                <td><?= htmlspecialchars($item['category']) ?></td>
                <td>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editItemModal<?= $item['item_id'] ?>">Edit</button>
                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteItemModal<?= $item['item_id'] ?>">Delete</button>
                </td>
            </tr>
            <!-- Edit Modal -->
            <div class="modal fade" id="editItemModal<?= $item['item_id'] ?>" tabindex="-1" aria-labelledby="editItemLabel<?= $item['item_id'] ?>" aria-hidden="true">
              <div class="modal-dialog">
                <form method="post" action="../backend/update_menu_item.php" enctype="multipart/form-data">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="editItemLabel<?= $item['item_id'] ?>">Edit Item</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
                      <input type="hidden" name="current_image" value="<?= $item['image'] ?>">
                      <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="item_name" class="form-control" value="<?= htmlspecialchars($item['item_name']) ?>" required>
                      </div>
                      <div class="mb-3">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="2" required><?= htmlspecialchars($item['description']) ?></textarea>
                      </div>
                      <div class="mb-3">
                        <label>Price</label>
                        <input type="number" name="price" class="form-control" value="<?= $item['price'] ?>" step="0.01" required>
                      </div>
                      <div class="mb-3">
                        <label>Category</label>
                        <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($item['category']) ?>" required>
                      </div>
                      <div class="mb-3">
                        <label>Availability</label>
                        <select name="availability" class="form-select">
                          <option value="yes" <?= $item['availability'] === 'yes' ? 'selected' : '' ?>>Yes</option>
                          <option value="no" <?= $item['availability'] === 'no' ? 'selected' : '' ?>>No</option>
                        </select>
                      </div>
                      <div class="mb-3">
                        <label>Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small>Current: <?= htmlspecialchars($item['image']) ?></small>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
            <!-- Delete Modal -->
            <div class="modal fade" id="deleteItemModal<?= $item['item_id'] ?>" tabindex="-1" aria-labelledby="deleteItemLabel<?= $item['item_id'] ?>" aria-hidden="true">
              <div class="modal-dialog">
                <form method="post" action="../backend/delete_menu_item.php">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="deleteItemLabel<?= $item['item_id'] ?>">Delete Item</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
                      <p>Are you sure you want to delete <strong><?= htmlspecialchars($item['name']) ?></strong>?</p>
                    </div>
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
            <?php endforeach; ?>
        <?php
        /*
          <div class="mb-3">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control" rows="2" required></textarea>
          </div>
          <div class="mb-3">
        session_start();
        require_once '../includes/header.php';
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
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
            <label for="price">Price</label>
            <input type="number" name="price" id="price" class="form-control" step="0.01" required>
          </div>
          <div class="mb-3">
            <label for="category">Category</label>
            <input type="text" name="category" id="category" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="availability">Availability</label>
            <select name="availability" id="availability" class="form-select">
              <option value="yes" selected>Yes</option>
              <option value="no">No</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="image">Image</label>
            <input type="file" name="image" id="image" class="form-control" accept="image/*">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Add Item</button>
        </div>
      </div>
    </form>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
require_once '../includes/footer.php';
?>