<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../includes/functions.php";
require_login("customer");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = create_request(
        $_SESSION['user_id'],
        trim($_POST['make'] ?? ''),
        trim($_POST['model'] ?? ''),
        trim($_POST['year'] ?? ''),
        trim($_POST['plate'] ?? ''),
        trim($_POST['issue'] ?? '')
    );

    if ($result['ok']) {
        flash('success', 'Service request submitted successfully.');
    } else {
        flash('error', $result['error']);
    }
}

header("Location: " . base_url("customer/dashboard.php"));
exit;
?>
