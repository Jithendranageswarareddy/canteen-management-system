<?php
// Login page for students and admins
session_start();
require_once '../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | College Canteen</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="mb-4 text-center">Login</h2>
            <form id="loginForm" method="post" class="card p-4 shadow">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required minlength="6">
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            <div id="loginMsg" class="mt-3"></div>
            <div class="text-center mt-2">
                <a href="register.php">Don't have an account? Register</a>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// AJAX login
const form = document.getElementById('loginForm');
form.addEventListener('submit', function(e) {
    e.preventDefault();
    const data = new FormData(form);
    fetch('../backend/login.php', {
        method: 'POST',
        body: data
    })
    .then(res => res.json())
    .then(resp => {
        if (resp.success) {
            // Redirect based on role
            if (resp.role === 'admin') {
                window.location.href = 'admin_dashboard.php';
            } else {
                window.location.href = 'student_dashboard.php';
            }
        } else {
            document.getElementById('loginMsg').innerHTML = '<div class="alert alert-danger">' + (resp.errors ? resp.errors.join('<br>') : 'Login failed.') + '</div>';
        }
    })
    .catch(() => {
        document.getElementById('loginMsg').innerHTML = '<div class="alert alert-danger">Login failed.</div>';
    });
});
</script>
</body>
</html>
<?php
require_once '../includes/footer.php';
?>