<?php
// path: /trubetech/crm/admin/task_view.php
// Tenant: View task detail
// REV: 20251122.1

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/before.inc';
if (!is_logged_in()) die('Access denied.');

$account_id = $_SESSION['active_account_id'];
$task_id = intval($_GET['id'] ?? 0);

$stmt = $con->prepare("
    SELECT t.task_title, t.task_description, t.status, t.cost_amount, 
           t.bill_amount, t.due_date,
           c.customer_name, co.company_name
    FROM ttcrm_tasks t
    LEFT JOIN ttcrm_customers c ON c.customer_id = t.customer_id
    LEFT JOIN ttcrm_companies co ON co.company_id = t.company_id
    WHERE t.task_id=? AND t.account_id=?
    LIMIT 1
");
$stmt->bind_param("ii", $task_id, $account_id);
$stmt->execute();
$stmt->bind_result($title, $description, $status,
                   $cost, $bill, $due, $customer_name, $company_name);
$stmt->fetch();
$stmt->close();

// photos
$p = $con->prepare("SELECT photo_id, file_path FROM ttcrm_task_photos WHERE task_id=?");
$p->bind_param("i", $task_id);
$p->execute();
$photos = $p->get_result();
?>

<h2>Task: <?php echo h($title); ?></h2>

<p>Company: <?php echo h($company_name); ?></p>
<p>Customer: <?php echo h($customer_name); ?></p>

<p>Description:<br><?php echo nl2br(h($description)); ?></p>

<p>Cost: <?php echo h($cost); ?><br>
Bill Amount: <?php echo h($bill); ?></p>

<p>Due: <?php echo h($due); ?></p>

<p>Status: <?php echo h($status); ?></p>

<h3>Photos</h3>

<form method="post" enctype="multipart/form-data" action="/admin/task_photo_upload.php">
    <input type="hidden" name="task_id" value="<?php echo $task_id; ?>">
    <input type="file" name="photo">
    <input type="submit" value="Upload">
</form>

<?php while ($ph = $photos->fetch_assoc()): ?>
    <div>
        <img src="/uploads/tasks/<?php echo $ph['file_path']; ?>" width="200"><br>
        <a href="/admin/task_photo_delete.php?id=<?php echo $ph['photo_id']; ?>&task=<?php echo $task_id; ?>">Delete</a>
    </div>
<?php endwhile; ?>

<p><a href="/admin/task_edit.php?id=<?php echo $task_id; ?>">Edit Task</a></p>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/after.inc'; ?>
