<?php
// path: /trubetech/crm/admin/logout.php
// REV: 20251123.3

session_start();

$_SESSION = [];
session_unset();
session_destroy();

setcookie(session_name(), '', time() - 3600, '/');

header("Location: /admin/login.php");
exit;
