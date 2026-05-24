<?php
$pageTitle = "Home";
require_once __DIR__ . "/includes/header.php";
?>

<section class="hero">
    <div class="container">
        <div class="grid grid-2">
            <div>
                <div class="label">Live service tracking</div>
                <h1>Your garage,<br><span class="accent">tuned to perfection.</span></h1>
                <p class="lead">
                    GearHaus is a complete car repair service tracking platform.
                    Customers submit requests, administrators assign mechanics,
                    and mechanics log every task — all in one place.
                </p>
                <div class="actions">
                    <a class="btn-primary" href="<?= base_url('register.php') ?>">Book a repair <i class="fa-solid fa-arrow-right"></i></a>
                    <a class="btn-ghost" href="<?= base_url('login.php') ?>">Sign in</a>
                </div>
            </div>

            <div class="card">
                <div class="label">By the numbers</div>
                <div class="grid grid-3">
                    <div class="stat"><strong>24h</strong><span class="muted">Assign time</span></div>
                    <div class="stat"><strong>100%</strong><span class="muted">Visibility</span></div>
                    <div class="stat"><strong>3</strong><span class="muted">Role panels</span></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container">
    <h2>Three doors. One garage.</h2>
    <div class="grid grid-3">
        <a href="<?= base_url('login.php?role=customer') ?>" class="card">
            <h3><i class="fa-solid fa-user accent"></i> Customer</h3>
            <p class="muted">Book your repair. Track every wrench turn in real time.</p>
        </a>

        <a href="<?= base_url('login.php?role=mechanic') ?>" class="card">
            <h3><i class="fa-solid fa-gear accent"></i> Mechanic</h3>
            <p class="muted">Receive assignments. Add tasks. Update the job.</p>
        </a>

        <a href="<?= base_url('login.php?role=admin') ?>" class="card">
            <h3><i class="fa-solid fa-shield-halved accent"></i> Administrator</h3>
            <p class="muted">Assign mechanics. Approve task lists. Oversee the garage.</p>
        </a>
    </div>
</section>

<section class="container">
    <h2>How a job moves through GearHaus</h2>
    <div class="grid grid-4">
        <div class="card"><h3>01</h3><p>Customer submits vehicle and issue.</p></div>
        <div class="card"><h3>02</h3><p>Admin assigns a mechanic.</p></div>
        <div class="card"><h3>03</h3><p>Mechanic logs task checklist.</p></div>
        <div class="card"><h3>04</h3><p>Customer tracks status and tasks.</p></div>
    </div>
</section>

<?php require_once __DIR__ . "/includes/footer.php"; ?>
