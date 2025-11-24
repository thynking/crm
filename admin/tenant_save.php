<?php
// path: /trubetech/crm/admin/tenant_save.php
// Saves tenant records with duplicate email handling
// REV: 20251124.1

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/before.inc';

if (!is_system_admin()) {
    die("Access denied.");
}

$account_id   = intval($_POST['account_id'] ?? 0);
$account_name = trim($_POST['account_name'] ?? '');
$admin_email  = trim($_POST['admin_email'] ?? '');
$active       = intval($_POST['active'] ?? 1);

if (!$account_name || !$admin_email) {
    die("Missing required fields.");
}

try {

    if ($account_id > 0) {

        $stmt = $con->prepare("
            UPDATE ttcrm_accounts
            SET account_name = ?, admin_email = ?, active = ?
            WHERE account_id = ?
            LIMIT 1
        ");
        $stmt->bind_param("ssii", $account_name, $admin_email, $active, $account_id);
        $stmt->execute();
        $stmt->close();

    } else {

        $stmt = $con->prepare("
            INSERT INTO ttcrm_accounts (account_name, admin_email, active)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("ssi", $account_name, $admin_email, $active);
        $stmt->execute();
        $stmt->close();
    }

} catch (mysqli_sql_exception $e) {

    // Duplicate key error
    if ($e->getCode() == 1062) {

        // Soft error — don’t kill the whole page
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/before.inc';

        echo "<h2>Error: Duplicate Email</h2>";
        echo "<p>The email <strong>" . h($admin_email) . "</strong> is already used by another tenant.</p>";
        echo "<p>Please choose a different admin email.</p>";
        echo "<p><a href=\"/admin/tenant_edit.php?id=$account_id\">Return to Tenant Editor</a></p>";

        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/after.inc';
        exit;
    }

    // Unknown SQL error – rethrow
    throw $e;
}

// All good
header("Location: /admin/tenants.php");
exit;
