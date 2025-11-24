<?php
// path: /trubetech/crm/admin/task_photo_upload.php
// Task photo upload
// REV: 20251122.1

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/before.inc';
if (!is_logged_in()) die('Access denied.');

$task_id = intval($_POST['task_id'] ?? 0);
$account_id = $_SESSION['active_account_id'];

if ($task_id < 1) die('Invalid task.');

$upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/tasks/';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

if (!empty($_FILES['photo']['name'])) {
    $filename = time() . '_' . basename($_FILES['photo']['name']);
    $target = $upload_dir . $filename;

    move_uploaded_file($_FILES['photo']['tmp_name'], $target);

    $stmt = $con->prepare("INSERT INTO ttcrm_task_photos (task_id, file_path) VALUES (?, ?)");
    $stmt->bind_param("is", $task_id, $filename);
    $stmt->execute();
    $stmt->close();
}

header("Location: /admin/task_view.php?id=" . $task_id);
exit;
