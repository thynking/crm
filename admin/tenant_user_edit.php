<?php
// path: /trubetech/crm/admin/tenant_user_edit.php
// Add/edit a user in a tenant
// REV: 20251123.1

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/before.inc';

if (!is_system_admin()) die("Access denied.");

$user_id = intval($_GET['id'] ?? 0);
$account_id = intval($_GET['account_id'] ?? 0);

$editing = $user_id > 0;
$email = "";
$role = "staff";
$active = 1;

if ($editing) {
    $stmt = $con->prepare("
        SELECT account_id, email, role, active
        FROM ttcrm_users
        WHERE user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($account_id, $email, $role, $active);
    $stmt->fetch();
    $stmt->close();
}
?>

<h2><?php echo $editing ? "Edit User" : "Add User"; ?></h2>

<form method="post" action="/admin/tenant_user_save.php">

<input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
<input type="hidden" name="account_id" value="<?php echo $account_id; ?>">

<label>Email</label><br>
<input type="email" name="email" value="<?php echo h($email); ?>" required><br><br>

<label>Role</label><br>
<select name="role">
    <option value="staff" <?php if ($role=='staff') echo "selected"; ?>>Staff</option>
    <option value="tenant_admin" <?php if ($role=='tenant_admin') echo "selected"; ?>>Tenant Admin</option>
</select><br><br>

<label>Status</label><br>
<select name="active">
    <option value="1" <?php if ($active==1) echo "selected"; ?>>Active</option>
    <option value="0" <?php if ($active==0) echo "selected"; ?>>Inactive</option>
</select><br><br>

<input type="submit" value="Save User">

</form>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/after.inc'; ?>
