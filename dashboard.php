<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../includes/functions.php";
require_login("mechanic");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_task'])) {
        add_task((int)$_POST['request_id'], trim($_POST['description']), (float)$_POST['cost']);
        flash('success', 'Task added.');
    }

    if (isset($_POST['delete_task'])) {
        delete_task((int)$_POST['task_id']);
        flash('success', 'Task removed.');
    }

    if (isset($_POST['status'])) {
        update_status((int)$_POST['request_id'], $_POST['status']);
        flash('success', 'Status updated.');
    }

    header("Location: " . base_url("mechanic/dashboard.php"));
    exit;
}

$jobs = get_mechanic_requests($_SESSION['user_id']);
$pageTitle = "Mechanic Dashboard";
require_once __DIR__ . "/../includes/header.php";
?>

<div class="container">
    <div class="label">Mechanic</div>
    <h1>Workbench, <span class="accent"><?= e(explode(' ', $_SESSION['user']['name'])[0]) ?></span>.</h1>
    <p class="muted">Open jobs, log tasks, push updates.</p>

    <?php if (empty($jobs)): ?>
        <div class="card">No jobs assigned yet.</div>
    <?php endif; ?>

    <?php foreach ($jobs as $job): ?>
        <?php $tasks = get_tasks($job['id']); ?>
        <div class="card" style="margin-bottom:24px">
            <h2><?= e($job['vehicle_year'] . ' ' . $job['vehicle_make'] . ' ' . $job['vehicle_model']) ?></h2>
            <p class="muted"><?= e($job['license_plate']) ?> · Customer: <?= e($job['customer_name']) ?> · <?= e($job['customer_phone']) ?></p>
            <?= status_badge($job['status']) ?>
            <p><?= e($job['issue_description']) ?></p>

            <h3>Task list</h3>
            <?php if (empty($tasks)): ?>
                <p class="muted">No tasks yet.</p>
            <?php else: ?>
                <table class="table">
                    <tr><th>Task</th><th>Cost</th><th></th></tr>
                    <?php foreach ($tasks as $t): ?>
                        <tr>
                            <td><?= e($t['description']) ?></td>
                            <td>₹<?= number_format((float)$t['estimated_cost'], 2) ?></td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="task_id" value="<?= $t['id'] ?>">
                                    <button name="delete_task" class="btn-ghost" data-confirm="Remove this task?">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>

            <form method="post" class="row">
                <input type="hidden" name="request_id" value="<?= $job['id'] ?>">
                <div class="form-group"><label>Task description</label><input name="description" required></div>
                <div class="form-group"><label>Estimated cost</label><input type="number" step="0.01" name="cost" value="0"></div>
                <button name="add_task" class="btn-primary">Add task</button>
            </form>

            <form method="post" class="actions" style="margin-top:14px">
                <input type="hidden" name="request_id" value="<?= $job['id'] ?>">
                <button name="status" value="in_progress" class="btn-ghost">Mark in progress</button>
                <button name="status" value="completed" class="btn-primary">Mark completed</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
