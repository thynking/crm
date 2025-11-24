<?php
// path: /trubetech/crm/admin/tenant_users_add.php
// System Admin: Create/Edit tenant user
// REV: 20251121.1

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/before.inc';

if (!is_system_admin()) die('Access denied.');

$account_id = intval($_GET['id'] ?? 0);
$user_id = intval($_GET['user'] ?? 0);

if ($account_id < 1) die('Invalid tenant.');

$editing = $user_id > 0;

// load tenant name
$stmt = $con->prepare("SELECT account_name FROM ttcrm_accounts WHERE account_id = ? LIMIT 1");
$stmt->bind_param("i", $account_id);
$stmt->execute();
$stmt->bind_result($account_name);
$stmt->fetch();
$stmt->close();

if ($editing) {
    $stmt = $con->prepare("
        SELECT email, role, active, is_system_admin
        FROM ttcrm_users
        WHERE user_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($email, $role, $active, $is_system_admin);
    $stmt->fetch();
    $stmt->close();
} else {
    $email = '';
    $role = 'admin';
    $active = 1;
    $is_system_admin = 0;
}
?>

<h2><?php echo $editing ? 'Edit User' : 'Add User'; ?> for <?php echo h($account_name); ?></h2>

<form method="post" action="/admin/tenant_users_save.php">
    <input type="hidden" name="account_id" value="<?php echo $account_id; ?>">
    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

    <label>Email</label>
    <input type="email" name="email" value="<?php echo h($email); ?>" required>

    <label>Role</label>
    <select name="role">
        <option value="admin" <?php echo $role == 'admin' ? 'selected' : ''; ?>>Admin</option>
        <option value="staff" <?php echo $role == 'staff' ? 'selected' : ''; ?>>Staff</option>
    </select>

    <label>Active</label>
    <select name="active">
        <option value="1" <?php echo $active ? 'selected' : ''; ?>>Yes</option>
        <option value="0" <?php echo !$active ? 'selected' : ''; ?>>No</option>
    </select>

    <label>System Admin?</label>
    <select name="is_system_admin">
        <option value="0" <?php echo !$is_system_admin ? 'selected' : ''; ?>>No</option>
        <option value="1" <?php echo $is_system_admin ? 'selected' : ''; ?>>Yes</option>
    </select>

    <label>Password (leave blank to keep current)</label>
    <input type="password" name="password">

    <input type="submit" value="Save User">
</form>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/after.inc'; ?>
