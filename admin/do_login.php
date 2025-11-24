<?php
// path: /trubetech/crm/admin/do_login.php
// Login processor
// REV: 20251123.2

$logfile = $_SERVER['DOCUMENT_ROOT'] . '/login_log.txt';
file_put_contents(
$logfile,
    "DO_LOGIN HIT AT ".date('Y-m-d H:i:s')."\n",
    FILE_APPEND
);


// Set same session directory as before.inc
$custom_session_path = $_SERVER['DOCUMENT_ROOT'] . '/../sessions';
ini_set('session.save_path', $custom_session_path);

// Start session BEFORE loading anything else
session_start();

require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/secure/conn.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/global.inc';


$email = strtolower(trim($_POST['email'] ?? ''));
$password = trim($_POST['password'] ?? '');

if (!$email || !$password) {
    die("Invalid login.");
}

$stmt = $con->prepare("
    SELECT user_id, account_id, email, password_hash, role, is_system_admin
    FROM ttcrm_users
    WHERE LOWER(email) = LOWER(?)
    LIMIT 1
");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($user_id, $account_id, $db_email, $db_hash, $role, $is_system_admin);
$found = $stmt->fetch();
$stmt->close();

if (!$found || !password_verify($password, $db_hash)) {
    die("Invalid login.");
}

// Set session values
$_SESSION['user_id'] = $user_id;
$_SESSION['account_id'] = $account_id;
$_SESSION['role'] = $role;
$_SESSION['is_system_admin'] = $is_system_admin;
$_SESSION['active_account_id'] = $account_id;

header("Location: /admin/dashboard.php");
exit;
