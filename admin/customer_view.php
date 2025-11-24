<?php
// path: /trubetech/crm/admin/customer_view.php
// Tenant: Customer detail view
// REV: 20251122.1

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/before.inc';

if (!is_logged_in()) die('Access denied.');

$account_id = $_SESSION['active_account_id'];
$customer_id = intval($_GET['id'] ?? 0);

$stmt = $con->prepare("
    SELECT c.customer_name, c.email, c.phone, 
           c.address1, c.address2, c.city, c.state, c.zip,
           comp.company_name
    FROM ttcrm_customers c
    LEFT JOIN ttcrm_companies comp ON comp.company_id = c.company_id
    WHERE c.customer_id=? AND c.account_id=?
    LIMIT 1
");
$stmt->bind_param("ii", $customer_id, $account_id);
$stmt->execute();
$stmt->bind_result($name, $email, $phone, $address1, $address2, $city, $state, $zip, $company_name);
$stmt->fetch();
$stmt->close();
?>

<h2>Customer: <?php echo h($name); ?></h2>

<p>Company: <?php echo h($company_name); ?></p>
<p>Email: <?php echo h($email); ?></p>
<p>Phone: <?php echo h($phone); ?></p>

<p>Address:<br>
<?php echo h($address1); ?><br>
<?php echo h($address2); ?><br>
<?php echo h($city . ', ' . $state . ' ' . $zip); ?></p>

<p><a href="/admin/customer_edit.php?id=<?php echo $customer_id; ?>">Edit</a></p>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/after.inc'; ?>
