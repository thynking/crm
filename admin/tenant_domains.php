<?php
// path: /trubetech/crm/admin/tenant_domains.php
// Manage domains for a tenant
// REV: 20251123.1

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/before.inc';

if (!is_system_admin()) {
    die("Access denied.");
}

$account_id = intval($_GET['id'] ?? 0);
if ($account_id < 1) die("Missing tenant ID.");

$stmt = $con->prepare("
    SELECT account_name
    FROM ttcrm_accounts
    WHERE account_id = ?
    LIMIT 1
");
$stmt->bind_param("i", $account_id);
$stmt->execute();
$stmt->bind_result($account_name);
$stmt->fetch();
$stmt->close();

$domains = [];
$stmt = $con->prepare("
    SELECT domain_id, domain_name, active
    FROM ttcrm_domains
    WHERE account_id = ?
    ORDER BY domain_name
");
$stmt->bind_param("i", $account_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $domains[] = $row;
}
$stmt->close();
?>

<h2>Domains for Tenant: <?php echo h($account_name); ?></h2>

<form method="post" action="/admin/tenant_domains_save.php">

<input type="hidden" name="account_id" value="<?php echo $account_id; ?>">

<table border="1" cellpadding="6" cellspacing="0">
<tr>
    <th>Domain</th>
    <th>Status</th>
    <th>Delete</th>
</tr>

<?php foreach ($domains as $d): ?>
<tr>
    <td><input type="text" name="domain_name[]" value="<?php echo h($d['domain_name']); ?>"></td>
    <td>
        <select name="domain_active[]">
            <option value="1" <?php if ($d['active']) echo "selected"; ?>>Active</option>
            <option value="0" <?php if (!$d['active']) echo "selected"; ?>>Inactive</option>
        </select>
    </td>
    <td>
        <input type="checkbox" name="domain_delete[]" value="<?php echo $d['domain_id']; ?>">
    </td>
    <input type="hidden" name="domain_id[]" value="<?php echo $d['domain_id']; ?>">
</tr>
<?php endforeach; ?>

<tr>
    <td><input type="text" name="new_domain" placeholder="Add new domain"></td>
    <td>
        <select name="new_domain_active">
            <option value="1">Active</option>
            <option value="0">Inactive</option>
        </select>
    </td>
    <td></td>
</tr>

</table>

<br>
<input type="submit" value="Save Domains">

</form>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/after.inc'; ?>
