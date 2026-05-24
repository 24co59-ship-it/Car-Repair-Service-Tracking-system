<?php
require_once __DIR__ . '/config.php';

function current_user() {
    return $_SESSION['user'] ?? null;
}

function current_role() {
    return $_SESSION['role'] ?? null;
}

function require_login($role = null) {
    if (!isset($_SESSION['user'])) {
        header("Location: " . base_url("login.php"));
        exit;
    }

    if ($role !== null && $_SESSION['role'] !== $role) {
        header("Location: " . base_url("login.php?error=unauthorized"));
        exit;
    }
}

function flash($key, $message = null) {
    if ($message === null) {
        $value = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $value;
    }

    $_SESSION['flash'][$key] = $message;
}

function register_customer($name, $email, $phone, $password) {
    global $mysqli;

    $stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {
        return ["ok" => false, "error" => "Email already exists"];
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);
    $role = "customer";

    $stmt = $mysqli->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $phone, $hash, $role);

    if (!$stmt->execute()) {
        return ["ok" => false, "error" => $stmt->error];
    }

    return ["ok" => true];
}

function login_user($email, $password, $role) {
    global $mysqli;

    $stmt = $mysqli->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
    $stmt->bind_param("ss", $email, $role);
    $stmt->execute();

    $user = $stmt->get_result()->fetch_assoc();

    if (!$user || !password_verify($password, $user['password'])) {
        return ["ok" => false, "error" => "Invalid email, password, or role"];
    }

    unset($user['password']);

    $_SESSION['user'] = $user;
    $_SESSION['role'] = $user['role'];
    $_SESSION['user_id'] = $user['id'];

    return ["ok" => true, "role" => $user['role']];
}
?>
