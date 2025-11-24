<?php
// path: /trubetech/crm/admin/company_edit.php
// Create/edit a company (tenant admin OR system admin)
// FIXED: proper tenant logic + safe binding
// REV: 20251123.5

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/before.inc';

if (!is_logged_in()) die("Not logged in.");

$is_sys = is_system_admin();
$session_account_id = intval($_SESSION['active_account_id']);
$company_id = intval($_GET['id'] ?? 0);

$company_name = "";
$notes = "";
$active = 1;
$company_account_id = $session_account_id; // Default tenant

// ----- LOAD EXISTING COMPANY -----
if ($company_id > 0) {

    if ($is_sys) {
        // Sysadmin loads ANY company
        $stmt = $con->prepare("
            SELECT company_name, active, notes, account_id
            FROM ttcrm_companies
            WHERE company_id = ?
            LIMIT 1
        ");
        $stmt->bind_param("i", $company_id);

    } else {
        // Tenant loads ONLY their companies
        $stmt = $con->prepare("
            SELECT company_name, active, notes, account_id
            FROM ttcrm_companies
            WHERE company_id = ? AND account_id = ?
            LIMIT 1
        ");
        $stmt->bind_param("ii", $company_id, $session_account_id);
    }

    $stmt->execute();
    $stmt->bind_result($company_name, $active, $notes, $company_account_id);
    $found = $stmt->fetch();
    $stmt->close();

    if (!$found) {
        die("Company not found or you do not have permission to edit it.");
    }
}
?>

<h2><?php echo $company_id ? "Edit Company" : "Create Company"; ?></h2>

<form method="post" action="/admin/company_save.php">

<input type="hidden" name="company_id" value="<?php echo $company_id; ?>">

<?php if ($is_sys): ?>

    <label>Tenant (Account)</label><br>
    <select name="account_id" required>
        <?php
        $stmtA = $con->prepare("SELECT account_id, account_name FROM ttcrm_accounts ORDER BY account_name");
        $stmtA->execute();
        $resA = $stmtA->get_result();

        while ($t = $resA->fetch_assoc()):
        ?>
            <option value="<?php echo $t['account_id']; ?>"
                <?php if ($t['account_id'] == $company_account_id) echo "selected"; ?>>
                <?php echo h($t['account_name']); ?>
            </option>
        <?php endwhile; ?>
    </select>
    <br><br>

<?php else: ?>

    <input type="hidden" name="account_id" value="<?php echo $session_account_id; ?>">

<?php endif; ?>

<label>Company Name</label><br>
<input type="text" name="company_name" value="<?php echo h($company_name); ?>" required><br><br>

<label>Status</label><br>
<select name="active">
    <option value="1" <?php if ($active) echo "selected"; ?>>Active</option>
    <option value="0" <?php if (!$active) echo "selected"; ?>>Inactive</option>
</select>
<br><br>

<label>Notes</label><br>
<textarea name="notes" rows="4" cols="60"><?php echo h($notes); ?></textarea>
<br><br>

<input type="submit" value="Save Company">

</form>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/after.inc'; ?>
