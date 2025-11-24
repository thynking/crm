<?php
// path: /trubetech/crm/admin/tenants.php
// System Admin: Tenant list
// REV: 20251123.1

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/before.inc';

if (!is_system_admin()) {
    die("Access denied.");
}

$stmt = $con->prepare("
    SELECT account_id, account_name, admin_email, active
    FROM ttcrm_accounts
    ORDER BY account_name ASC
");
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>Tenant Accounts</h2>

<p><a href="/admin/tenant_edit.php">Create New Tenant</a></p>

<table border="1" cellpadding="6" cellspacing="0">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Admin Email</th>
    <th>Status</th>
    <th>Actions</th>
	<th>Reset</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?php echo h($row['account_id']); ?></td>
    <td><?php echo h($row['account_name']); ?></td>
    <td><?php echo h($row['admin_email']); ?></td>
    <td><?php echo $row['active'] ? 'Active' : 'Inactive'; ?></td>
    <td>
        <a href="/admin/tenant_edit.php?id=<?php echo $row['account_id']; ?>">Edit</a> |
        <a href="/admin/tenant_domains.php?id=<?php echo $row['account_id']; ?>">Domains</a>
    </td>
	<td><a href="/admin/reset_password.php?id=<?php echo $row['account_id']; ?>">Reset Pass</a></td>
	
</tr>
<?php endwhile; ?>
</table>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/after.inc';
?>
