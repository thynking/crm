<?php
// path: /trubetech/crm/admin/task_edit.php
// Tenant: Add/Edit task (matches actual ttcrm_tasks schema)
// REV: 20251123.12

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/before.inc';

if (!is_logged_in()) die('Access denied.');

$session_account_id = intval($_SESSION['active_account_id']);
$task_id = intval($_GET['id'] ?? 0);
$editing = $task_id > 0;

// --------------------------------------------
// Load companies belonging to this tenant
// --------------------------------------------
$stmt = $con->prepare("
    SELECT company_id, company_name
    FROM ttcrm_companies
    WHERE account_id = ?
    ORDER BY company_name
");
$stmt->bind_param("i", $session_account_id);
$stmt->execute();
$companies = $stmt->get_result();
$stmt->close();

// --------------------------------------------
// Defaults for NEW TASK
// --------------------------------------------
$company_id = 0;
$customer_id = 0;
$task_name = '';
$title = '';
$description = '';
$estimated_cost = '0.00';
$bill_amount = '0.00';
$is_completed = 0;
$completed_at = '';
$status = 0; // 0 = pending

// --------------------------------------------
// Load existing task if editing
// --------------------------------------------
if ($editing) {

    $stmt = $con->prepare("
        SELECT 
            t.company_id,
            t.customer_id,
            t.task_name,
            t.title,
            t.description,
            t.estimated_cost,
            t.bill_amount,
            t.is_completed,
            t.completed_at,
            t.status
        FROM ttcrm_tasks t
        JOIN ttcrm_companies c ON c.company_id = t.company_id
        WHERE t.task_id = ? AND c.account_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("ii", $task_id, $session_account_id);
    $stmt->execute();
    $stmt->bind_result(
        $company_id,
        $customer_id,
        $task_name,
        $title,
        $description,
        $estimated_cost,
        $bill_amount,
        $is_completed,
        $completed_at,
        $status
    );

    if (!$stmt->fetch()) {
        die("Task not found or access denied.");
    }
    $stmt->close();
}

// --------------------------------------------
// Load customers for this tenant
// --------------------------------------------
$stmt = $con->prepare("
    SELECT cu.customer_id, cu.first_name, cu.last_name
    FROM ttcrm_customers cu
    JOIN ttcrm_companies c ON c.company_id = cu.company_id
    WHERE c.account_id = ?
    ORDER BY cu.first_name, cu.last_name
");
$stmt->bind_param("i", $session_account_id);
$stmt->execute();
$customers = $stmt->get_result();
$stmt->close();
?>

<h2><?php echo $editing ? 'Edit Task' : 'Create Task'; ?></h2>

<form method="post" action="/admin/task_save.php">
<input type="hidden" name="task_id" value="<?php echo $task_id; ?>">

<label>Company</label><br>
<select name="company_id" required>
    <option value="">-- Select Company --</option>
    <?php while ($co = $companies->fetch_assoc()): ?>
        <option value="<?php echo $co['company_id']; ?>"
            <?php echo $co['company_id'] == $company_id ? 'selected' : ''; ?>>
            <?php echo h($co['company_name']); ?>
        </option>
    <?php endwhile; ?>
</select>
<br><br>

<label>Customer</label><br>
<select name="customer_id">
    <option value="0">-- None --</option>
    <?php while ($cu = $customers->fetch_assoc()): ?>
        <option value="<?php echo $cu['customer_id']; ?>"
            <?php echo $cu['customer_id'] == $customer_id ? 'selected' : ''; ?>>
            <?php echo h($cu['first_name'].' '.$cu['last_name']); ?>
        </option>
    <?php endwhile; ?>
</select>
<br><br>

<label>Task Name</label><br>
<input type="text" name="task_name" value="<?php echo h($task_name); ?>" required>
<br><br>

<label>Title</label><br>
<input type="text" name="title" value="<?php echo h($title); ?>" required>
<br><br>

<label>Description</label><br>
<textarea name="description" rows="5"><?php echo h($description); ?></textarea>
<br><br>

<label>Estimated Cost</label><br>
<input type="text" name="estimated_cost" value="<?php echo h($estimated_cost); ?>">
<br><br>

<label>Bill Amount</label><br>
<input type="text" name="bill_amount" value="<?php echo h($bill_amount); ?>">
<br><br>

<label>Status</label><br>
<select name="status">
    <option value="0" <?php if($status==0) echo 'selected'; ?>>Pending</option>
    <option value="1" <?php if($status==1) echo 'selected'; ?>>In Progress</option>
    <option value="2" <?php if($status==2) echo 'selected'; ?>>Completed</option>
</select>
<br><br>

<label>Completed?</label><br>
<select name="is_completed">
    <option value="0" <?php if(!$is_completed) echo 'selected'; ?>>No</option>
    <option value="1" <?php if($is_completed) echo 'selected'; ?>>Yes</option>
</select>
<br><br>

<label>Completed At</label><br>
<input type="datetime-local" 
       name="completed_at"
       value="<?php echo $completed_at ? date('Y-m-d\TH:i', strtotime($completed_at)) : ''; ?>">
<br><br>

<input type="submit" value="Save Task">
</form>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/after.inc'; ?>
