<?php
// path: /trubetech/crm/admin/tasks.php
// Tenant: Task list matched to existing ttcrm_tasks schema
// REV: 20251123.10

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/before.inc';

if (!is_logged_in()) die("Access denied.");

$session_account_id = intval($_SESSION['active_account_id']);

// Pull all tasks for this tenant
$stmt = $con->prepare("
    SELECT 
        t.task_id,
        t.task_name,
        t.title,
        t.description,
        t.estimated_cost,   /* internal cost */
        t.bill_amount,      /* billing */
        t.is_completed,
        t.completed_at,
        t.created_at,
        t.status,
        c.company_name,
        cu.first_name,
        cu.last_name
    FROM ttcrm_tasks t
    JOIN ttcrm_companies c ON c.company_id = t.company_id
    LEFT JOIN ttcrm_customers cu ON cu.customer_id = t.customer_id
    WHERE c.account_id = ?
    ORDER BY t.created_at DESC
");
$stmt->bind_param("i", $session_account_id);
$stmt->execute();
$tasks = $stmt->get_result();
$stmt->close();
?>

<h2>Tasks</h2>

<p><a href="/admin/task_edit.php">Add New Task</a></p>

<table border="1" cellpadding="6" cellspacing="0" width="100%">
<tr>
    <th>ID</th>
    <th>Task Name</th>
    <th>Company</th>
    <th>Customer</th>
    <th>Status</th>
    <th>Bill Amount</th>
    <th>Estimated Cost</th>
    <th>Created</th>
    <th>Completed</th>
    <th>Actions</th>
</tr>

<?php while ($t = $tasks->fetch_assoc()): ?>
<tr>
    <td><?php echo h($t['task_id']); ?></td>

    <td>
        <?php echo h($t['task_name']); ?>
        <?php if ($t['title']) echo "<br><small>".h($t['title'])."</small>"; ?>
    </td>

    <td><?php echo h($t['company_name']); ?></td>

    <td>
        <?php 
            if ($t['first_name']) {
                echo h($t['first_name'].' '.$t['last_name']);
            } else {
                echo "<i>None</i>";
            }
        ?>
    </td>

    <td>
        <?php 
            echo $t['is_completed'] ? "<span style='color:green;'>Completed</span>" : "Open";
        ?>
    </td>

    <td>$<?php echo number_format($t['bill_amount'], 2); ?></td>

    <td>$<?php echo number_format($t['estimated_cost'], 2); ?></td>

    <td><?php echo h($t['created_at']); ?></td>

    <td>
        <?php echo $t['is_completed'] ? h($t['completed_at']) : "<i>—</i>"; ?>
    </td>

    <td>
        <a href="/admin/task_view.php?id=<?php echo $t['task_id']; ?>">View</a> |
        <a href="/admin/task_edit.php?id=<?php echo $t['task_id']; ?>">Edit</a>
    </td>
</tr>
<?php endwhile; ?>

</table>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/after.inc'; ?>
