<?php
// path: /trubetech/crm/admin/companies.php
// Company list (tenant admin OR system admin)
// REV: 20251123.3

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/before.inc';

if (!is_logged_in()) die("Not logged in.");

$account_id = intval($_SESSION['active_account_id']);
$is_sys = is_system_admin();

// Sorting logic
$allowed_sort = ['company_id','company_name','tenant','active'];
$sort = $_GET['sort'] ?? 'company_name';
if (!in_array($sort, $allowed_sort)) $sort = 'company_name';

$sort_sql = $sort;

// Special case for tenant name sort
if ($sort === 'tenant') $sort_sql = "a.account_name";

if ($is_sys) {
    // System admin sees ALL tenants
    $sql = "
        SELECT 
            c.company_id,
            c.company_name,
            c.active,
            a.account_name AS tenant_name,
            c.account_id
        FROM ttcrm_companies c
        JOIN ttcrm_accounts a ON a.account_id = c.account_id
        ORDER BY $sort_sql ASC
    ";

    $stmt = $con->prepare($sql);

} else {
    // Tenant admin sees only *their* companies
    $sql = "
        SELECT 
            c.company_id,
            c.company_name,
            c.active,
            a.account_name AS tenant_name,
            c.account_id
        FROM ttcrm_companies c
        JOIN ttcrm_accounts a ON a.account_id = c.account_id
        WHERE c.account_id = ?
        ORDER BY $sort_sql ASC
    ";

    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $account_id);
}

$stmt->execute();
$result = $stmt->get_result();

?>

<h2>Companies</h2>

<p><a href="/admin/company_edit.php">Create New Company</a></p>

<table border="1" cellpadding="6" cellspacing="0">
<tr>
    <th><a href="?sort=company_id">ID</a></th>
    <th><a href="?sort=company_name">Company Name</a></th>
    <?php if ($is_sys): ?>
        <th><a href="?sort=tenant">Tenant</a></th>
    <?php endif; ?>
    <th><a href="?sort=active">Status</a></th>
    <th>Actions</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?php echo h($row['company_id']); ?></td>
    <td><?php echo h($row['company_name']); ?></td>

    <?php if ($is_sys): ?>
        <td><?php echo h($row['tenant_name']); ?></td>
    <?php endif; ?>

    <td><?php echo $row['active'] ? 'Active' : 'Inactive'; ?></td>

    <td>
        <a href="/admin/company_edit.php?id=<?php echo $row['company_id']; ?>">Edit</a> |
        <a href="/admin/company_logo_upload.php?id=<?php echo $row['company_id']; ?>">Logo</a>
    </td>
</tr>
<?php endwhile; ?>

</table>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/after.inc'; ?>
