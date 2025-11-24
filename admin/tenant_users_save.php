<?php
// path: /trubetech/crm/admin/tenant_users_save.php
// System Admin: Save tenant user
// REV: 20251121.1

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/before.inc';

if (!is_system_admin()) die('Access denied.');

$account_id = intval($_POST['account_id'] ?? 0);
$user_id = intval($_POST['user_id'] ?? 0);

$email = trim($_POST['email'] ?? '');
$role = trim($_POST['role'] ?? 'staff');
$active = intval($_POST['active'] ?? 1);
$is_system_admin = intval($_POST['is_system_admin'] ?? 0);
$password = trim($_POST['password'] ?? '');

if ($account_id < 1) die('Invalid tenant.');

if ($user_id > 0) {
    // update
    if ($password !== '') {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $con->prepare("
            UPDATE ttcrm_users
            SET email=?, role=?, active=?, is_system_admin=?, password_hash=?
            WHERE user_id=? LIMIT 1
        ");
        $stmt->bind_param("ssissi", $email, $role, $active, $is_system_admin, $hash, $user_id);
    } else {
        $stmt = $con->prepare("
            UPDATE ttcrm_users
            SET email=?, role=?, active=?, is_system_admin=?
            WHERE user_id=? LIMIT 1
        ");
        $stmt->bind_param("ssiii", $email, $role, $active, $is_system_admin, $user_id);
    }
} else {
    // create
    if ($password === '') $password = generate_password(12);
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $con->prepare("
        INSERT INTO ttcrm_users (account_id, email, password_hash, role, active, is_system_admin)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("isssii", $account_id, $email, $hash, $role, $active, $is_system_admin);
}

$stmt->execute();
$stmt->close();

header("Location: /admin/tenant_users.php?id=" . $account_id);
exit;
