<?php
// path: /trubetech/crm/admin/customer_edit.php
// Tenant: Add/Edit customer (NEW SCHEMA)
// REV: 20251123.5


require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/before.inc';

if (!is_logged_in()) die('Access denied.');

$is_sys = is_system_admin();
$session_account_id = intval($_SESSION['active_account_id']);

$customer_id = intval($_GET['id'] ?? 0);
$editing = $customer_id > 0;

// Load companies for this tenant
$stmt = $con->prepare("
    SELECT company_id, company_name 
    FROM ttcrm_companies 
    WHERE account_id = ?
    ORDER BY company_name
");
$stmt->bind_param("i", $session_account_id);
$stmt->execute();
$companies = $stmt->get_result();

// Defaults
$company_id = 0;
$first_name = '';
$last_name  = '';
$email      = '';
$phone      = '';
$billing_address = '';
$active = 1;

// Load customer if editing
if ($editing) {

    $stmt = $con->prepare("
        SELECT c.company_id, c.first_name, c.last_name, c.email, 
               c.phone, c.billing_address, c.active
        FROM ttcrm_customers c
        JOIN ttcrm_companies cmp ON cmp.company_id = c.company_id
        WHERE c.customer_id = ? AND cmp.account_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("ii", $customer_id, $session_account_id);
    $stmt->execute();
    $stmt->bind_result(
        $company_id,
        $first_name,
        $last_name,
        $email,
        $phone,
        $billing_address,
        $active
    );

    if (!$stmt->fetch()) {
        die("Customer not found or access denied.");
    }

    $stmt->close();
}
?>

<h2><?php echo $editing ? 'Edit Customer' : 'Add Customer'; ?></h2>

<form method="post" action="/admin/customer_save.php">
<input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">

<label>Company</label><br>
<select name="company_id" required>
    <option value="">-- Select Company --</option>
    <?php while ($c = $companies->fetch_assoc()): ?>
        <option value="<?php echo $c['company_id']; ?>"
            <?php echo ($c['company_id'] == $company_id ? 'selected' : ''); ?>>
            <?php echo h($c['company_name']); ?>
        </option>
    <?php endwhile; ?>
</select>
<br><br>

<label>First Name</label><br>
<input type="text" name="first_name" value="<?php echo h($first_name); ?>" required>
<br><br>

<label>Last Name</label><br>
<input type="text" name="last_name" value="<?php echo h($last_name); ?>" required>
<br><br>

<label>Email</label><br>
<input type="email" name="email" value="<?php echo h($email); ?>">
<br><br>

<label>Phone</label><br>
<input type="text" name="phone" value="<?php echo h($phone); ?>">
<br><br>

<label>Billing Address</label><br>
<textarea name="billing_address" rows="4" cols="60"><?php echo h($billing_address); ?></textarea>
<br><br>

<label>Active</label><br>
<select name="active">
    <option value="1" <?php echo $active ? 'selected' : ''; ?>>Yes</option>
    <option value="0" <?php echo !$active ? 'selected' : ''; ?>>No</option>
</select>
<br><br>

<input type="submit" value="Save">
</form>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/after.inc'; ?>
