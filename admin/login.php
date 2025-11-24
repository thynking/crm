<?php
// path: /trubetech/crm/admin/login.php
// Login form
// REV: 20251123.5
// $2y$12$lyvW8m6YcUoO.iS04g1jFeVsXyQrLC6zeKe3iMRRf2YPLh79l0xLO
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/before.inc';
?>

<h2>Login</h2>

<form method="post" action="/admin/do_login.php">
    <label>Email</label>
    <input type="email" name="email" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <input type="submit" value="Login">
</form>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/after.inc'; ?>
