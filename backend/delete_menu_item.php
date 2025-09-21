<?php
// Delete menu item backend (admin)
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';
if (!is_admin()) {
    echo json_encode(['success'=>false,'message'=>'Unauthorized']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['item_id'] ?? 0);
    // Get image filename to delete
    $stmt = $conn->prepare('SELECT image FROM menu WHERE item_id=?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($image);
    $stmt->fetch();
    $stmt->close();
    // Delete DB record
    $stmt = $conn->prepare('DELETE FROM menu WHERE item_id=?');
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        if ($image && file_exists("../assets/uploads/$image")) {
            unlink("../assets/uploads/$image");
        }
        echo json_encode(['success'=>true,'message'=>'Menu item deleted!']);
    } else {
        echo json_encode(['success'=>false,'message'=>'Delete failed.']);
    }
    $stmt->close();
    exit;
}
?>