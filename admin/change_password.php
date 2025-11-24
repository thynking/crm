<?php
// path: /trubetech/crm/admin/change_password.php
// User changes their own password
// REV: 20251123.1

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/before.inc';

if (!is_logged_in()) die("Not logged in.");

$user_id = intval($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $old = $_POST['old_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $new2 = $_POST['new_password2'] ?? '';

    if ($new !== $new2) {
        die("New passwords do not match.");
    }

    // Fetch current hash
    $stmt = $con->prepare("SELECT password_hash FROM ttcrm_users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($db_hash);
    $stmt->fetch();
    $stmt->close();

    if (!password_verify($old, $db_hash)) {
        die("Old password is incorrect.");
    }

    $new_hash = password_hash($new, PASSWORD_BCRYPT);

    $stmt2 = $con->prepare("UPDATE ttcrm_users SET password_hash=? WHERE user_id=?");
    $stmt2->bind_param("si", $new_hash, $user_id);
    $stmt2->execute();
    $stmt2->close();

    echo "Password changed successfully.";
    require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/after.inc';
    exit;
}
?>

<h2>Change Password</h2>
<form method="post">
    <label>Old Password</label><br>
    <input type="password" name="old_password" required><br><br>

    <label>New Password</label><br>
    <input type="password" name="new_password" required><br><br>

    <label>Confirm New Password</label><br>
    <input type="password" name="new_password2" required><br><br>

    <input type="submit" value="Change Password">
</form>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/after.inc'; ?>
