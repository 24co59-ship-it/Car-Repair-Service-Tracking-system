<?php
require_once __DIR__ . '/auth.php';
$pageTitle = $pageTitle ?? 'GearHaus';
$user = current_user();
$role = current_role();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= e($pageTitle) ?> | GearHaus</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body>
<header class="navbar">
    <a href="<?= base_url('index.php') ?>" class="brand">
        <span class="brand-icon"><i class="fa-solid fa-wrench"></i></span>
        <span>
            <strong>GearHaus</strong>
            <small>Service Tracking</small>
        </span>
    </a>

    <nav>
        <?php if (!$user): ?>
            <a href="<?= base_url('login.php') ?>">Login</a>
            <a href="<?= base_url('register.php') ?>" class="btn-small">Get Started</a>
        <?php else: ?>
            <?php if ($role === 'customer'): ?>
                <a href="<?= base_url('customer/dashboard.php') ?>">Dashboard</a>
            <?php elseif ($role === 'mechanic'): ?>
                <a href="<?= base_url('mechanic/dashboard.php') ?>">Workbench</a>
            <?php elseif ($role === 'admin'): ?>
                <a href="<?= base_url('admin/dashboard.php') ?>">Admin Panel</a>
            <?php endif; ?>
            <span class="nav-user"><?= e($user['name']) ?> · <?= e($role) ?></span>
            <a href="<?= base_url('logout.php') ?>" class="btn-small ghost">Logout</a>
        <?php endif; ?>
    </nav>
</header>

<main>
<?php if ($msg = flash('success')): ?>
    <div class="alert success"><?= e($msg) ?></div>
<?php endif; ?>
<?php if ($msg = flash('error')): ?>
    <div class="alert error"><?= e($msg) ?></div>
<?php endif; ?>
