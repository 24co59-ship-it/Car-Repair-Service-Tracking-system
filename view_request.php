<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../includes/functions.php";
require_login("customer");

$id = (int)($_GET['id'] ?? 0);
$req = get_request($id);

if (!$req || $req['customer_id'] != $_SESSION['user_id']) {
    flash('error', 'Request not found.');
    header("Location: " . base_url("customer/dashboard.php"));
    exit;
}

$tasks = get_tasks($id);
$stages = ['pending', 'assigned', 'in_progress', 'completed'];
$current = array_search($req['status'], $stages, true);

$pageTitle = "Track Request";
require_once __DIR__ . "/../includes/header.php";
?>

<div class="container">
    <a class="muted" href="<?= base_url('customer/dashboard.php') ?>">← Back</a>
    <h1>Repair #<?= $req['id'] ?> · <span class="accent"><?= e($req['license_plate']) ?></span></h1>
    <?= status_badge($req['status']) ?>

    <div class="timeline">
        <?php foreach ($stages as $i => $s): ?>
            <div class="<?= $i <= $current ? 'done' : '' ?>">
                <span></span>
                <small><?= e(status_label($s)) ?></small>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="grid grid-2">
        <div class="card">
            <h2>Vehicle & Issue</h2>
            <p><strong><?= e($req['vehicle_year'] . ' ' . $req['vehicle_make'] . ' ' . $req['vehicle_model']) ?></strong></p>
            <p class="muted"><?= e($req['license_plate']) ?></p>
            <p><?= nl2br(e($req['issue_description'])) ?></p>
        </div>

        <div class="card">
            <h2>Assigned Mechanic</h2>
            <?php if ($req['mechanic_name']): ?>
                <p><strong><?= e($req['mechanic_name']) ?></strong></p>
                <p class="muted"><?= e($req['mechanic_specialty']) ?> · <?= e($req['mechanic_phone']) ?></p>
            <?php else: ?>
                <p class="muted">Awaiting administrator assignment.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <h2>Tasks from mechanic</h2>
        <?php if (!$req['tasks_forwarded']): ?>
            <p class="muted">Tasks have not been forwarded by admin yet.</p>
        <?php elseif (empty($tasks)): ?>
            <p class="muted">No tasks added yet.</p>
        <?php else: ?>
            <table class="table">
                <tr><th>Task</th><th>Estimated Cost</th></tr>
                <?php foreach ($tasks as $t): ?>
                    <tr>
                        <td><?= e($t['description']) ?></td>
                        <td>₹<?= number_format((float)$t['estimated_cost'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
