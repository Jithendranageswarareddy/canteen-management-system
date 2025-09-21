<?php
// User/Admin login backend
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $errors = [];
    if (!$email) $errors[] = 'Email required.';
    if (!$password) $errors[] = 'Password required.';
    if (empty($errors)) {
        $stmt = $conn->prepare('SELECT user_id, password, role FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($user_id, $hashed, $role);
            $stmt->fetch();
            if (password_verify($password, $hashed)) {
                $_SESSION['user_id'] = $user_id;
                $_SESSION['role'] = $role;
                echo json_encode(['success'=>true, 'role'=>$role]);
                exit;
            } else {
                $errors[] = 'Invalid password.';
            }
        } else {
            $errors[] = 'No user found.';
        }
        $stmt->close();
    }
    if (!empty($errors)) {
        echo json_encode(['success'=>false,'errors'=>$errors]);
        exit;
    }
}
?>