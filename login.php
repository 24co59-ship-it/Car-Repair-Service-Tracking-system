<?php
require_once __DIR__ . "/includes/auth.php";

$selectedRole = $_GET['role'] ?? 'customer';
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'customer';

    $result = login_user($email, $password, $role);

    if ($result['ok']) {
        header("Location: " . base_url($role . "/dashboard.php"));
        exit;
    }

    $error = $result['error'];
    $selectedRole = $role;
}

$pageTitle = "Login";
require_once __DIR__ . "/includes/header.php";
?>

<div class="form-wrap">
    <div class="card">
        <div class="label">Sign in</div>
        <h1>Welcome back to <span class="accent">GearHaus</span>.</h1>
        <p class="muted">Pick your role to access the correct workspace.</p>

        <?php if ($error): ?>
            <div class="alert error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="role-tabs">
                <?php foreach (['customer' => 'Customer', 'mechanic' => 'Mechanic', 'admin' => 'Admin'] as $value => $label): ?>
                    <input type="radio" name="role" id="role-<?= $value ?>" value="<?= $value ?>" <?= $selectedRole === $value ? 'checked' : '' ?>>
                    <label for="role-<?= $value ?>"><?= $label ?></label>
                <?php endforeach; ?>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="you@example.com">
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Password">
            </div>

            <button class="btn-primary" style="width:100%">Sign in</button>

            <p class="muted" style="text-align:center">
                New customer? <a class="accent" href="<?= base_url('register.php') ?>">Create an account</a>
            </p>

            <p class="muted">
                Demo Admin: admin@carrepair.com / Admin@123<br>
                Demo Mechanic: mechanic@carrepair.com / Mech@123
            </p>
        </form>
    </div>
</div>

<?php require_once __DIR__ . "/includes/footer.php"; ?>
