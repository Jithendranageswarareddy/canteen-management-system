<?php
// Update menu item backend (admin)
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';
if (!is_admin()) {
    echo json_encode(['success'=>false,'message'=>'Unauthorized']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['item_id'] ?? 0);
    $name = sanitize($_POST['item_name'] ?? '');
    $desc = sanitize($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $category = sanitize($_POST['category'] ?? '');
    $avail = ($_POST['availability'] ?? 'yes') === 'no' ? 'no' : 'yes';
    $image = $_POST['current_image'] ?? '';
    // Handle new image upload (validate file type/size)
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $img_name = preg_replace('/[^A-Za-z0-9_.-]/', '', basename($_FILES['image']['name']));
        $target = '../assets/uploads/' . $img_name;
        $allowed = ['image/jpeg','image/png','image/gif'];
        if (in_array($_FILES['image']['type'], $allowed) && $_FILES['image']['size'] <= 2*1024*1024) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $image = $img_name;
            }
        }
    }
    $stmt = $conn->prepare('UPDATE menu SET item_name=?, description=?, price=?, category=?, availability=?, image=? WHERE item_id=?');
    $stmt->bind_param('ssdssss', $name, $desc, $price, $category, $avail, $image, $id);
    if ($stmt->execute()) {
        echo json_encode(['success'=>true,'message'=>'Menu item updated!']);
    } else {
        echo json_encode(['success'=>false,'message'=>'Update failed.']);
    }
    $stmt->close();
    exit;
}
?>