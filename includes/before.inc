<?php
// path: /trubetech/crm/includes/before.inc
// Session bootstrap with custom session directory
// REV: 20251123.10
/*
$logfile = $_SERVER['DOCUMENT_ROOT'] . '/debug_session.txt';
function sdbg($msg) {
    global $logfile;
    file_put_contents($logfile, date('Y-m-d H:i:s')." ".$msg."\n", FILE_APPEND);
}
*/


// DO NOT LOAD before.inc
require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/secure/conn.php';
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/global.inc';



// Fix cookie domain
$domain = $_SERVER['HTTP_HOST'];
if (substr($domain, 0, 4) === 'www.') {
    $domain = substr($domain, 4);
}

// Create and use safe session storage directory
$custom_session_path = $_SERVER['DOCUMENT_ROOT'] . '/../sessions';
ini_set('session.save_path', $custom_session_path);

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => $domain,
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}




// 5. Normalize path for StartLogic
$current_path = strtolower($_SERVER['PHP_SELF']);

// 6. Safe login and logout page checks
$is_login_page =
    strpos($current_path, 'admin/login.php') !== false ||
    strpos($current_path, 'admin/do_login.php') !== false;

$is_logout_page =
    strpos($current_path, 'admin/logout.php') !== false;

// 7. Determine tenant domain
$requested_host = strtolower($_SERVER['HTTP_HOST']);

$tenant_account_id = null;
$stmt = $con->prepare("
    SELECT account_id
    FROM ttcrm_domains
    WHERE domain_name = ? AND active = 1
    LIMIT 1
");
$stmt->bind_param("s", $requested_host);
$stmt->execute();
$stmt->bind_result($tenant_account_id);
$stmt->fetch();
$stmt->close();

// 8. System admin override
$is_system_admin = $_SESSION['is_system_admin'] ?? 0;

// 9. Unknown tenant redirect (except login pages)
if (!$tenant_account_id && !$is_system_admin && !$is_login_page) {
    die("Unknown or inactive CRM domain.");
}

// 10. Active account resolution
if ($is_system_admin && isset($_SESSION['impersonate_account_id'])) {
    $active_account_id = $_SESSION['impersonate_account_id'];
} else {
    $active_account_id = $tenant_account_id;
}

$_SESSION['active_account_id'] = $active_account_id;

// 11. Load user session vars
$user_id = $_SESSION['user_id'] ?? null;

// 12. Protect authenticated pages
if (!$user_id && !$is_login_page && !$is_logout_page) {
    header("Location: /admin/login.php");
    exit;
}

// 13. Cross-tenant protection
if ($user_id && !$is_system_admin) {
    if (($_SESSION['account_id'] ?? 0) != $active_account_id) {
        die("Access denied: account mismatch.");
    }
}

// 14. Load branding
$branding = [
    'primary_color' => '',
    'secondary_color' => '',
    'button_color' => '',
    'css_overrides' => '',
    'logo_file_id' => null
];

$stmt = $con->prepare("
    SELECT primary_color, secondary_color, button_color, css_overrides, logo_file_id
    FROM ttcrm_account_branding
    WHERE account_id = ?
    LIMIT 1
");
$stmt->bind_param("i", $active_account_id);
$stmt->execute();
$stmt->bind_result(
    $branding['primary_color'],
    $branding['secondary_color'],
    $branding['button_color'],
    $branding['css_overrides'],
    $branding['logo_file_id']
);
$stmt->fetch();
$stmt->close();

$active_style = $_SESSION['active_style'] ?? 'style01.css';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>CRM Admin - TrubeTech</title>
<link rel="stylesheet" type="text/css" href="/styles/<?php echo h($active_style); ?>">
<?php if (!empty($branding['css_overrides'])) echo "<style>".$branding['css_overrides']."</style>"; ?>
</head>
<body>
<main>
<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/menu.inc'; ?>
