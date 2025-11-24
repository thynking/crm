<?php
// path: /trubetech/crm/admin/dashboard.php
// Admin dashboard with login identity display
// REV: 20251123.7

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/before.inc';

if (!is_logged_in()) die("Access denied.");

// Pull user info
$user_email  = $_SESSION['user_email']      ?? '';
$user_role   = $_SESSION['role']            ?? '';
$is_sys      = $_SESSION['is_system_admin'] ?? 0;
$acct_id     = $_SESSION['active_account_id'] ?? 0;

// Get tenant name
$tenant_name = '';
$stmt = $con->prepare("SELECT account_name FROM ttcrm_accounts WHERE account_id = ? LIMIT 1");
$stmt->bind_param("i", $acct_id);
$stmt->execute();
$stmt->bind_result($tenant_name);
$stmt->fetch();
$stmt->close();
?>

<h2>Admin Dashboard</h2>
<p>Account ID: <?php echo h($acct_id); ?></p>

<p>You are logged in as: <strong><?php echo h($user_email); ?></strong></p>

<p>Role: <strong><?php echo h($user_role); ?><?php echo $is_sys ? ' (System Admin)' : ''; ?></strong></p>

<p>Tenant: <strong><?php echo h($tenant_name ?: 'Unknown'); ?></strong></p>

<p><a href="/admin/logout.php">Logout</a></p>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/after.inc';
?>
