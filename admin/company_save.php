<?php
// path: /trubetech/crm/admin/company_save.php
// Saves a company record (tenant admin OR system admin)
// REV: 20251123.6

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/before.inc';

if (!is_logged_in()) die("Not logged in.");

$is_sys = is_system_admin();
$session_account_id = intval($_SESSION['active_account_id']);

$company_id   = intval($_POST['company_id'] ?? 0);
$company_name = trim($_POST['company_name'] ?? '');
$active       = intval($_POST['active'] ?? 1);
$notes        = trim($_POST['notes'] ?? '');

if (!$company_name) die("Missing company name.");

// Determine the tenant assignment
if ($is_sys) {
    // Sysadmin can set tenant explicitly
    $account_id = intval($_POST['account_id']);
} else {
    // Tenant user always uses THEIR tenant
    $account_id = $session_account_id;
}

// ----------------------
// CREATE NEW COMPANY
// ----------------------
if ($company_id === 0) {

    $stmt = $con->prepare("
        INSERT INTO ttcrm_companies (account_id, company_name, active, notes)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("isis", $account_id, $company_name, $active, $notes);
    $stmt->execute();
    $stmt->close();

    header("Location: /admin/companies.php");
    exit;
}


// ----------------------
// UPDATE EXISTING COMPANY
// ----------------------
if ($is_sys) {

    // Sysadmin may move companies across tenants
    $stmt = $con->prepare("
        UPDATE ttcrm_companies
        SET account_id = ?, company_name = ?, active = ?, notes = ?
        WHERE company_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("isisi", $account_id, $company_name, $active, $notes, $company_id);

} else {

    // Tenant user cannot change tenant
    $stmt = $con->prepare("
        UPDATE ttcrm_companies
        SET company_name = ?, active = ?, notes = ?
        WHERE company_id = ? AND account_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("sisi", $company_name, $active, $notes, $company_id, $session_account_id);
}

$stmt->execute();
$stmt->close();

header("Location: /admin/companies.php");
exit;
