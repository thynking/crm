<?php
// path: /trubetech/crm/admin/tenant_view.php
// Tenant overview (system admin)
// REV: 20251123.1

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/before.inc';

if (!is_system_admin()) die("Access denied.");

$account_id = intval($_GET['id'] ?? 0);
if ($account_id < 1) die("Invalid tenant.");

$stmt = $con->prepare("
    SELECT account_name, admin_email, active
    FROM ttcrm_accounts
    WHERE account_id = ?
");
$stmt->bind_param("i", $account_id);
$stmt->execute();
$stmt->bind_result($name, $email, $active);
$stmt->fetch();
$stmt->close();
?>

<h2>Tenant: <?php echo h($name); ?></h2>

<p>
<b>Admin Email:</b> <?php echo h($email); ?><br>
<b>Status:</b> <?php echo $active ? "Active" : "Inactive"; ?>
</p>

<ul>
    <li><a href="/admin/tenant_edit.php?id=<?php echo $account_id; ?>">Edit Tenant Settings</a></li>
    <li><a href="/admin/tenant_users.php?id=<?php echo $account_id; ?>">Manage Users</a></li>
    <li><a href="/admin/tenant_domains.php?id=<?php echo $account_id; ?>">Manage Domains</a></li>
    <li><a href="/admin/tenant_companies.php?id=<?php echo $account_id; ?>">Manage Companies</a></li>
</ul>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/after.inc'; ?>
