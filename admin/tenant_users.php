<?php
// path: /trubetech/crm/admin/tenant_users.php
// Manage users for a tenant
// REV: 20251123.1

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/before.inc';

if (!is_system_admin()) die("Access denied.");

$account_id = intval($_GET['id'] ?? 0);
if ($account_id < 1) die("Invalid tenant.");

$stmt = $con->prepare("
    SELECT user_id, email, role, active, is_system_admin
    FROM ttcrm_users
    WHERE account_id = ?
    ORDER BY email
");
$stmt->bind_param("i", $account_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>Users for Tenant #<?php echo $account_id; ?></h2>

<p><a href="/admin/tenant_user_edit.php?account_id=<?php echo $account_id; ?>">Add User</a></p>

<table border="1" cellpadding="6">
<tr>
    <th>Email</th>
    <th>Role</th>
    <th>Status</th>
    <th>Actions</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?php echo h($row['email']); ?></td>
    <td><?php echo h($row['role']); ?></td>
    <td><?php echo $row['active'] ? "Active" : "Inactive"; ?></td>
    <td>
        <a href="/admin/tenant_user_edit.php?id=<?php echo $row['user_id']; ?>">Edit</a> |
        <a href="/admin/tenant_user_reset_password.php?id=<?php echo $row['user_id']; ?>">Reset Password</a>
    </td>
</tr>
<?php endwhile; ?>

</table>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/after.inc'; ?>
