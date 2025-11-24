<?php
// path: /trubetech/crm/admin/customer_save.php
// Tenant: Save customer (NEW SCHEMA MATCHED)
// REV: 20251123.6

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/before.inc';

if (!is_logged_in()) die('Access denied.');

$session_account_id = intval($_SESSION['active_account_id']);

$customer_id      = intval($_POST['customer_id'] ?? 0);
$company_id       = intval($_POST['company_id'] ?? 0);

$first_name       = trim($_POST['first_name'] ?? '');
$last_name        = trim($_POST['last_name'] ?? '');
$email            = trim($_POST['email'] ?? '');
$phone            = trim($_POST['phone'] ?? '');
$billing_address  = trim($_POST['billing_address'] ?? '');
$active           = intval($_POST['active'] ?? 1);

// Basic validation
if (!$company_id) die("Invalid company.");
if (!$first_name) die("First name required.");
if (!$last_name) die("Last name required.");

// ------------------------------------------------------------
// TENANT SECURITY CHECK: Ensure the company belongs to this tenant
// ------------------------------------------------------------
$stmt = $con->prepare("
    SELECT company_id 
    FROM ttcrm_companies 
    WHERE company_id = ? AND account_id = ?
    LIMIT 1
");
$stmt->bind_param("ii", $company_id, $session_account_id);
$stmt->execute();
if (!$stmt->fetch()) {
    die("Access denied: company does not belong to your tenant.");
}
$stmt->close();

// ------------------------------------------------------------
// UPDATE EXISTING CUSTOMER
// ------------------------------------------------------------
if ($customer_id > 0) {

    $stmt = $con->prepare("
        UPDATE ttcrm_customers
        SET company_id = ?, first_name = ?, last_name = ?, email = ?,
            phone = ?, billing_address = ?, active = ?
        WHERE customer_id = ?
        LIMIT 1
    ");

    $stmt->bind_param(
        "isssssis",
        $company_id,
        $first_name,
        $last_name,
        $email,
        $phone,
        $billing_address,
        $active,
        $customer_id
    );

    $stmt->execute();
    $stmt->close();
}

// ------------------------------------------------------------
// INSERT NEW CUSTOMER
// ------------------------------------------------------------
else {

    $stmt = $con->prepare("
        INSERT INTO ttcrm_customers
            (company_id, first_name, last_name, email, phone, billing_address, active)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "isssssi",
        $company_id,
        $first_name,
        $last_name,
        $email,
        $phone,
        $billing_address,
        $active
    );

    $stmt->execute();
    $stmt->close();
}

// ------------------------------------------------------------

header("Location: /admin/customers.php");
exit;
