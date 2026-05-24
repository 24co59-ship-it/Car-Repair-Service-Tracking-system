<?php
require_once __DIR__ . '/config.php';

function status_label($status) {
    return ucwords(str_replace('_', ' ', $status));
}

function status_badge($status) {
    $map = [
        "pending" => "badge-pending",
        "assigned" => "badge-assigned",
        "in_progress" => "badge-progress",
        "completed" => "badge-completed"
    ];

    $class = $map[$status] ?? "badge-pending";
    return "<span class='status-badge {$class}'>" . e(status_label($status)) . "</span>";
}

function create_request($customer_id, $make, $model, $year, $plate, $issue) {
    global $mysqli;

    $stmt = $mysqli->prepare(
        "INSERT INTO service_requests 
        (customer_id, vehicle_make, vehicle_model, vehicle_year, license_plate, issue_description)
        VALUES (?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param("isssss", $customer_id, $make, $model, $year, $plate, $issue);

    if (!$stmt->execute()) {
        return ["ok" => false, "error" => $stmt->error];
    }

    return ["ok" => true];
}

function get_customer_requests($customer_id) {
    global $mysqli;

    $stmt = $mysqli->prepare(
        "SELECT sr.*, m.name AS mechanic_name
         FROM service_requests sr
         LEFT JOIN users m ON m.id = sr.mechanic_id
         WHERE sr.customer_id = ?
         ORDER BY sr.id DESC"
    );

    $stmt->bind_param("i", $customer_id);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function get_mechanic_requests($mechanic_id) {
    global $mysqli;

    $stmt = $mysqli->prepare(
        "SELECT sr.*, c.name AS customer_name, c.email AS customer_email, c.phone AS customer_phone
         FROM service_requests sr
         JOIN users c ON c.id = sr.customer_id
         WHERE sr.mechanic_id = ?
         ORDER BY sr.id DESC"
    );

    $stmt->bind_param("i", $mechanic_id);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function get_all_requests() {
    global $mysqli;

    $res = $mysqli->query(
        "SELECT sr.*, c.name AS customer_name, c.email AS customer_email, c.phone AS customer_phone,
                m.name AS mechanic_name
         FROM service_requests sr
         JOIN users c ON c.id = sr.customer_id
         LEFT JOIN users m ON m.id = sr.mechanic_id
         ORDER BY sr.id DESC"
    );

    return $res->fetch_all(MYSQLI_ASSOC);
}

function get_request($id) {
    global $mysqli;

    $stmt = $mysqli->prepare(
        "SELECT sr.*, c.name AS customer_name, c.email AS customer_email, c.phone AS customer_phone,
                m.name AS mechanic_name, m.phone AS mechanic_phone, m.specialty AS mechanic_specialty
         FROM service_requests sr
         JOIN users c ON c.id = sr.customer_id
         LEFT JOIN users m ON m.id = sr.mechanic_id
         WHERE sr.id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc();
}

function get_tasks($request_id) {
    global $mysqli;

    $stmt = $mysqli->prepare("SELECT * FROM repair_tasks WHERE request_id = ? ORDER BY id ASC");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function add_task($request_id, $description, $cost) {
    global $mysqli;

    $stmt = $mysqli->prepare("INSERT INTO repair_tasks (request_id, description, estimated_cost) VALUES (?, ?, ?)");
    $stmt->bind_param("isd", $request_id, $description, $cost);

    return $stmt->execute();
}

function delete_task($task_id) {
    global $mysqli;

    $stmt = $mysqli->prepare("DELETE FROM repair_tasks WHERE id = ?");
    $stmt->bind_param("i", $task_id);

    return $stmt->execute();
}

function assign_mechanic($request_id, $mechanic_id) {
    global $mysqli;

    $status = "assigned";

    $stmt = $mysqli->prepare("UPDATE service_requests SET mechanic_id = ?, status = ? WHERE id = ?");
    $stmt->bind_param("isi", $mechanic_id, $status, $request_id);

    return $stmt->execute();
}

function update_status($request_id, $status) {
    global $mysqli;

    $stmt = $mysqli->prepare("UPDATE service_requests SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $request_id);

    return $stmt->execute();
}

function forward_tasks($request_id) {
    global $mysqli;

    $stmt = $mysqli->prepare("UPDATE service_requests SET tasks_forwarded = 1 WHERE id = ?");
    $stmt->bind_param("i", $request_id);

    return $stmt->execute();
}

function delete_request($id) {
    global $mysqli;

    $stmt = $mysqli->prepare("DELETE FROM service_requests WHERE id = ?");
    $stmt->bind_param("i", $id);

    return $stmt->execute();
}

function get_users_by_role($role) {
    global $mysqli;

    $stmt = $mysqli->prepare("SELECT * FROM users WHERE role = ? ORDER BY id DESC");
    $stmt->bind_param("s", $role);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function add_mechanic($name, $email, $phone, $password, $specialty) {
    global $mysqli;

    $hash = password_hash($password, PASSWORD_BCRYPT);
    $role = "mechanic";

    $stmt = $mysqli->prepare(
        "INSERT INTO users (name, email, phone, password, role, specialty) VALUES (?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param("ssssss", $name, $email, $phone, $hash, $role, $specialty);

    return $stmt->execute();
}

function delete_user($id) {
    global $mysqli;

    $stmt = $mysqli->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);

    return $stmt->execute();
}

function admin_stats() {
    global $mysqli;

    return [
        "customers" => $mysqli->query("SELECT COUNT(*) c FROM users WHERE role='customer'")->fetch_assoc()['c'],
        "mechanics" => $mysqli->query("SELECT COUNT(*) c FROM users WHERE role='mechanic'")->fetch_assoc()['c'],
        "total" => $mysqli->query("SELECT COUNT(*) c FROM service_requests")->fetch_assoc()['c'],
        "pending" => $mysqli->query("SELECT COUNT(*) c FROM service_requests WHERE status='pending'")->fetch_assoc()['c'],
        "assigned" => $mysqli->query("SELECT COUNT(*) c FROM service_requests WHERE status='assigned'")->fetch_assoc()['c'],
        "progress" => $mysqli->query("SELECT COUNT(*) c FROM service_requests WHERE status='in_progress'")->fetch_assoc()['c'],
        "completed" => $mysqli->query("SELECT COUNT(*) c FROM service_requests WHERE status='completed'")->fetch_assoc()['c']
    ];
}
?>
