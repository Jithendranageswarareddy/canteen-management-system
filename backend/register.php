<?php
// User registration backend (student/admin)
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $role = ($_POST['role'] === 'admin') ? 'admin' : 'student';
    $errors = [];
    if (!$name) $errors[] = 'Name required.';
    if (!$email) $errors[] = 'Valid email required.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 chars.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';
    if (empty($errors)) {
        $stmt = $conn->prepare('SELECT user_id FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = 'Email already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('ssss', $name, $email, $hash, $role);
            if ($stmt->execute()) {
                echo json_encode(['success'=>true,'message'=>'Registration successful!']);
                exit;
            } else {
                $errors[] = 'Registration failed.';
            }
        }
        $stmt->close();
    }
    if (!empty($errors)) {
        echo json_encode(['success'=>false,'errors'=>$errors]);
        exit;
    }
}
?>