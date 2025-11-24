<?php
// path: /trubetech/crm/admin/task_save.php
// Tenant: Save task
// REV: 20251122.1

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/before.inc';
if (!is_logged_in()) die('Access denied.');

$account_id = $_SESSION['active_account_id'];

$task_id = intval($_POST['task_id'] ?? 0);
$company_id = intval($_POST['company_id'] ?? 0);
$customer_id = intval($_POST['customer_id'] ?? 0);

$title = trim($_POST['task_title'] ?? '');
$description = trim($_POST['task_description'] ?? '');
$cost = trim($_POST['cost_amount'] ?? 0);
$bill = trim($_POST['bill_amount'] ?? 0);
$due_date = trim($_POST['due_date'] ?? null);
$status = trim($_POST['status'] ?? 'open');

if ($task_id > 0) {
    $stmt = $con->prepare("
        UPDATE ttcrm_tasks
        SET company_id=?, customer_id=?, task_title=?, task_description=?,
            cost_amount=?, bill_amount=?, due_date=?, status=?
        WHERE task_id=? AND account_id=?
        LIMIT 1
    ");
    $stmt->bind_param("iissddssii", 
        $company_id, $customer_id, $title, $description,
        $cost, $bill, $due_date, $status,
        $task_id, $account_id
    );
} else {
    $stmt = $con->prepare("
        INSERT INTO ttcrm_tasks
        (account_id, company_id, customer_id, task_title, task_description,
         cost_amount, bill_amount, due_date, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iiissddss",
        $account_id, $company_id, $customer_id, $title, $description,
        $cost, $bill, $due_date, $status
    );
}

$stmt->execute();
$stmt->close();

header("Location: /admin/tasks.php");
exit;
