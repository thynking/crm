<?php
// path: /trubetech/crm/includes/global.inc
// Shared functions used across the CRM
// REV: 20251121.1

// ------------------------------------------------------------
// Safe redirect
// ------------------------------------------------------------
function go($url) {
    header("Location: $url");
    exit;
}

// ------------------------------------------------------------
// Escape output to prevent XSS
// ------------------------------------------------------------
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// ------------------------------------------------------------
// Check user login (used in before.inc)
// ------------------------------------------------------------
function require_login() {
    if (!isset($_SESSION['user_id'])) {
        go('/admin/login.php');
    }
}

// ------------------------------------------------------------
// Load logged-in staff user
// ------------------------------------------------------------
function current_user($con) {
    if (!isset($_SESSION['user_id'])) return null;

    $uid = $_SESSION['user_id'];
    $stmt = $con->prepare("SELECT user_id, account_id, email, role, is_system_admin FROM ttcrm_users WHERE user_id = ? LIMIT 1");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    return $user;
}

// ------------------------------------------------------------
// Check if current user is system admin
// ------------------------------------------------------------

function is_logged_in() {
    return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
}


function is_system_admin() {
    return (isset($_SESSION['is_system_admin']) && $_SESSION['is_system_admin'] == 1);
}

// ------------------------------------------------------------
// Simple date formatting helper
// ------------------------------------------------------------
function dt($datetime) {
    if (!$datetime) return '';
    return date("Y-m-d H:i", strtotime($datetime));
}

// ------------------------------------------------------------
// Simple random password generator
// ------------------------------------------------------------
function generate_password($length = 16) {
    return bin2hex(random_bytes($length / 2));
}
?>
