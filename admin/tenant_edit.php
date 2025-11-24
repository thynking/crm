<?php
// path: /trubetech/crm/admin/tenant_edit.php
// System Admin: Tenant create/edit form
// REV: 20251123.1

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/before.inc';

if (!is_system_admin()) {
    die("Access denied.");
}

$account_id = intval($_GET['id'] ?? 0);
$editing = $account_id > 0;

$account_name = "";
$admin_email = "";
$active = 1;

if ($editing) {
    $stmt = $con->prepare("
        SELECT account_name, admin_email, active
        FROM ttcrm_accounts
        WHERE account_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("i", $account_id);
    $stmt->execute();
    $stmt->bind_result($account_name, $admin_email, $active);
    $stmt->fetch();
    $stmt->close();
}
?>

<h2><?php echo $editing ? "Edit Tenant" : "Create Tenant"; ?></h2>

<form method="post" action="/admin/tenant_save.php">

<input type="hidden" name="account_id" value="<?php echo $account_id; ?>">

<label>Tenant Name</label><br>
<input type="text" name="account_name" value="<?php echo h($account_name); ?>" required><br><br>

<label>Admin Email</label><br>
<input type="email" name="admin_email" value="<?php echo h($admin_email); ?>" required><br><br>

<label>Status</label><br>
<select name="active">
    <option value="1" <?php if ($active == 1) echo "selected"; ?>>Active</option>
    <option value="0" <?php if ($active == 0) echo "selected"; ?>>Inactive</option>
</select><br><br>

<input type="submit" value="Save Tenant">

</form>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/after.inc'; ?>
