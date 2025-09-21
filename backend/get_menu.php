<?php
// Fetch menu items for AJAX (only available)
require_once '../includes/db_connect.php';
header('Content-Type: application/json');

$result = $conn->query('SELECT * FROM menu WHERE availability = "yes"');
$menu = [];
while ($row = $result->fetch_assoc()) {
    $menu[] = $row;
}
echo json_encode($menu);
?>
