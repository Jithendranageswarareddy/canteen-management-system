<?php
session_start();
require_once '../includes/db_connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Accept JSON input for AJAX
$data = json_decode(file_get_contents('php://input'), true);
$order_id = intval($data['order_id'] ?? 0);
$status = $data['status'] ?? null;
if (!$order_id || !in_array($status, ['pending','completed','cancelled'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$stmt = $conn->prepare('UPDATE orders SET status = ? WHERE order_id = ?');
$stmt->bind_param('si', $status, $order_id);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Order status updated']);
} else {
    echo json_encode(['success' => false, 'message' => 'Update failed']);
}
$stmt->close();
?>