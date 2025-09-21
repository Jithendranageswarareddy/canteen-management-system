<?php
// Registration page for students and admins
require_once '../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | College Canteen</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="mb-4 text-center">User Registration</h2>
            <form id="regForm" method="post" class="card p-4 shadow">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required minlength="6">
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="student" selected>Student</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success w-100">Register</button>
            </form>
            <div id="regMsg" class="mt-3"></div>
            <div class="text-center mt-2">
                <a href="login.php">Already have an account? Login</a>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// AJAX registration
const regForm = document.getElementById('regForm');
regForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const data = new FormData(regForm);
    fetch('../backend/register.php', {
        method: 'POST',
        body: data
    })
    .then(res => res.json())
    .then(resp => {
        if (resp.success) {
            document.getElementById('regMsg').innerHTML = '<div class="alert alert-success">' + resp.message + '</div>';
            regForm.reset();
        } else {
            document.getElementById('regMsg').innerHTML = '<div class="alert alert-danger">' + (resp.errors ? resp.errors.join('<br>') : 'Registration failed.') + '</div>';
        }
    })
    .catch(() => {
        document.getElementById('regMsg').innerHTML = '<div class="alert alert-danger">Registration failed.</div>';
    });
});
</script>
</body>
</html>
<?php
require_once '../includes/footer.php';
?>