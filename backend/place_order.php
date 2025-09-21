<?php
// Place order backend
session_start();
require_once '../includes/db_connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$cart = $data['cart'] ?? [];
if (empty($cart)) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty.']);
    exit;
}

$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Insert order
$stmt = $conn->prepare('INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, "pending")');
$stmt->bind_param('id', $_SESSION['user_id'], $total);
if ($stmt->execute()) {
    $order_id = $stmt->insert_id;
    $stmt->close();
    // Insert order items
    $success = true;
    foreach ($cart as $item) {
        $stmt_item = $conn->prepare('INSERT INTO order_items (order_id, item_id, quantity) VALUES (?, ?, ?)');
        $stmt_item->bind_param('iii', $order_id, $item['itemId'], $item['quantity']);
        if (!$stmt_item->execute()) {
            $success = false;
        }
        $stmt_item->close();
    }
    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Order placed!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Order items failed.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Order failed.']);
}
?>