<?php
// Add menu item backend (admin)
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';
if (!is_admin()) {
    echo json_encode(['success'=>false,'message'=>'Unauthorized']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['item_name'] ?? '');
    $desc = sanitize($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $category = sanitize($_POST['category'] ?? '');
    $avail = ($_POST['availability'] ?? 'yes') === 'no' ? 'no' : 'yes';
    $errors = [];
    if (!$name) $errors[] = 'Name required.';
    if (!$desc) $errors[] = 'Description required.';
    if ($price <= 0) $errors[] = 'Price must be positive.';
    if (!$category) $errors[] = 'Category required.';
    // Handle image upload (validate file type/size)
    $image = '';
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
    if (empty($errors)) {
        $stmt = $conn->prepare('INSERT INTO menu (item_name, description, price, category, availability, image) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssdsss', $name, $desc, $price, $category, $avail, $image);
        if ($stmt->execute()) {
            echo json_encode(['success'=>true,'message'=>'Menu item added!']);
        } else {
            echo json_encode(['success'=>false,'message'=>'Add failed.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success'=>false,'errors'=>$errors]);
    }
    exit;
}
?>