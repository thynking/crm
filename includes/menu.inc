<?php
// path: /trubetech/crm/includes/menu.inc
// Admin navigation menu (SAFELY written)
// REV: 20251123.3
?>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php if (is_logged_in() && is_system_admin()): ?>
<nav style="padding:10px; background:#ddd; margin-bottom:20px;">

    <a href="/admin/dashboard.php">Dashboard</a> |

    <a href="/admin/tenants.php">Tenants</a> |

    <a href="/admin/companies.php">Companies</a> |

    <a href="/admin/customers.php">Customers</a> |
    <a href="/admin/tasks.php">Tasks</a> |

    <a href="/admin/change_password.php">Change Password</a> |
    <a href="/admin/logout.php">Logout</a>

</nav>

<?php elseif (is_logged_in()): ?>
<nav style="padding:10px; background:#ddd; margin-bottom:20px;">

    <a href="/admin/dashboard.php">Dashboard</a> |
    <a href="/admin/companies.php">Companies</a> |
    <a href="/admin/customers.php">Customers</a> |
    <a href="/admin/tasks.php">Tasks</a> |
    <a href="/invoices/">Invoices</a> |
    <a href="/admin/change_password.php">Change Password</a> |
    <a href="/admin/logout.php">Logout</a>

</nav>
<?php endif; ?>
