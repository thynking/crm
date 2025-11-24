<?php
// path: /trubetech/crm/admin/tenant_edit_save.php
// System Admin: Save Tenant
// REV: 20251121.1

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/before.inc';

if (!is_system_admin()) {
    die('Access denied.');
}

$account_id = intval($_POST['account_id'] ?? 0);
$account_name = trim($_POST['account_name'] ?? '');
$admin_email = trim($_POST['admin_email'] ?? '');
$active = intval($_POST['active'] ?? 1);

if ($account_id > 0) {
    $stmt = $con->prepare("
        UPDATE ttcrm_accounts
        SET account_name = ?, admin_email = ?, active = ?
        WHERE account_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("ssii", $account_name, $admin_email, $active, $account_id);
} else {
    $stmt = $con->prepare("
        INSERT INTO ttcrm_accounts (account_name, admin_email, active)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("ssi", $account_name, $admin_email, $active);
}

$stmt->execute();
$stmt->close();

header("Location: /admin/tenants.php");
exit;
