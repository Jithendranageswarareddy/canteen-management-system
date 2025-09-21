<?php
// Shared PHP functions for canteen system
function sanitize($input) {
    return htmlspecialchars(trim($input));
}
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}
function is_logged_in() {
    return isset($_SESSION['user_id']);
}
?>
