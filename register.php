<?php
require_once __DIR__ . "/includes/auth.php";

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || $email === '' || $phone === '' || strlen($password) < 6) {
        $error = "All fields are required and password must be 6+ characters.";
    } else {
        $result = register_customer($name, $email, $phone, $password);

        if ($result['ok']) {
            login_user($email, $password, 'customer');
            header("Location: " . base_url("customer/dashboard.php"));
            exit;
        }

        $error = $result['error'];
    }
}

$pageTitle = "Register";
require_once __DIR__ . "/includes/header.php";
?>

<div class="form-wrap">
    <div class="card">
        <div class="label">Customer registration</div>
        <h1>Join <span class="accent">GearHaus</span>.</h1>
        <p class="muted">Create your customer account to submit repair requests.</p>

        <?php if ($error): ?>
            <div class="alert error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label>Full name</label>
                <input name="name" required placeholder="Alex Carter">
            </div>

            <div class="row">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required placeholder="you@example.com">
                </div>

                <div class="form-group">
                    <label>Phone</label>
                    <input name="phone" required placeholder="9876543210">
                </div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required minlength="6">
            </div>

            <button class="btn-primary" style="width:100%">Create customer account</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . "/includes/footer.php"; ?>
