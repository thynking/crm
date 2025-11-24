<?php
// path: /trubetech/crm/admin/customers.php
// Tenant: List customers
// REV: 20251123.2

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/before.inc';

if (!is_logged_in()) die('Access denied.');

$account_id = intval($_SESSION['active_account_id']);

// Fetch customers for this tenant (via companies)
$stmt = $con->prepare("
    SELECT 
        c.customer_id,
        c.company_id,
        c.first_name,
        c.last_name,
        c.email,
        c.phone,
        c.active
    FROM ttcrm_customers c
    JOIN ttcrm_companies cmp ON cmp.company_id = c.company_id
    WHERE cmp.account_id = ?
    ORDER BY c.last_name, c.first_name
");
$stmt->bind_param("i", $account_id);
$stmt->execute();
$result = $stmt->get_result();

// Load companies for mapping
$stmt2 = $con->prepare("
    SELECT company_id, company_name 
    FROM ttcrm_companies 
    WHERE account_id = ?
    ORDER BY company_name
");
$stmt2->bind_param("i", $account_id);
$stmt2->execute();
$companies_res = $stmt2->get_result();

$companies = [];
while ($row = $companies_res->fetch_assoc()) {
    $companies[$row['company_id']] = $row['company_name'];
}
?>

<h2>Customers</h2>

<p><a href="/admin/customer_edit.php">Add New Customer</a></p>

<table border="1" cellpadding="6" cellspacing="0">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Company</th>
    <th>Email</th>
    <th>Phone</th>
    <th>Active</th>
    <th>Action</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?php echo h($row['customer_id']); ?></td>
    <td><?php echo h($row['first_name'] . ' ' . $row['last_name']); ?></td>
    <td><?php echo h($companies[$row['company_id']] ?? 'Unknown'); ?></td>
    <td><?php echo h($row['email']); ?></td>
    <td><?php echo h($row['phone']); ?></td>
    <td><?php echo $row['active'] ? 'Yes' : 'No'; ?></td>
    <td>
        <a href="/admin/customer_view.php?id=<?php echo $row['customer_id']; ?>">View</a> |
        <a href="/admin/customer_edit.php?id=<?php echo $row['customer_id']; ?>">Edit</a>
    </td>
</tr>
<?php endwhile; ?>

</table>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/after.inc'; ?>
