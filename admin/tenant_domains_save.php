<?php
// path: /trubetech/crm/admin/tenant_domains_save.php
// Saves tenant domains
// REV: 20251123.1

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/before.inc';

if (!is_system_admin()) {
    die("Access denied.");
}

$account_id = intval($_POST['account_id'] ?? 0);

$domain_id     = $_POST['domain_id'] ?? [];
$domain_name   = $_POST['domain_name'] ?? [];
$domain_active = $_POST['domain_active'] ?? [];
$domain_delete = $_POST['domain_delete'] ?? [];

for ($i = 0; $i < count($domain_id); $i++) {

    $id = intval($domain_id[$i]);
    $name = trim($domain_name[$i]);
    $active = intval($domain_active[$i]);
    $delete = in_array($id, $domain_delete);

    if ($delete) {
        $stmt = $con->prepare("DELETE FROM ttcrm_domains WHERE domain_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        continue;
    }

    if ($name) {
        $stmt = $con->prepare("
            UPDATE ttcrm_domains
            SET domain_name = ?, active = ?
            WHERE domain_id = ?
        ");
        $stmt->bind_param("sii", $name, $active, $id);
        $stmt->execute();
        $stmt->close();
    }
}

// New domain
$new_domain = trim($_POST['new_domain'] ?? '');
$new_active = intval($_POST['new_domain_active'] ?? 1);

if ($new_domain) {
    $stmt = $con->prepare("
        INSERT INTO ttcrm_domains (account_id, domain_name, active)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("isi", $account_id, $new_domain, $new_active);
    $stmt->execute();
    $stmt->close();
}

header("Location: /admin/tenant_domains.php?id=$account_id");
exit;
