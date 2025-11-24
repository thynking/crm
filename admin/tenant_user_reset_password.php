<?php
// path: /trubetech/crm/admin/tenant_user_reset_password.php
// Admin resets user password
// REV: 20251123.1

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/before.inc';

if (!is_system_admin()) die("Access denied.");

$user_id = intval($_GET['id'] ?? 0);
if ($user_id < 1) die("Invalid user.");

function make_temp_pass($len=12) {
    return bin2hex(random_bytes($len/2));
}

$temp_pass = make_temp_pass();
$temp_hash = password_hash($temp_pass, PASSWORD_BCRYPT);

$stmt = $con->prepare("
    UPDATE ttcrm_users
    SET password_hash = ?
    WHERE user_id = ?
");
$stmt->bind_param("si", $temp_hash, $user_id);
$stmt->execute();
$stmt->close();
?>

<h2>Password Reset</h2>
<p>User ID <?php echo $user_id; ?> temporary password:</p>

<div style="font-size:20px; color:red; margin:10px 0;">
    <?php echo $temp_pass; ?>
</div>

<p>Ask the user to log in and immediately change their password.</p>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/after.inc'; ?>
