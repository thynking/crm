<?php
// path: /trubetech/crm/admin/task_photo_delete.php
// Delete task photo
// REV: 20251122.1

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/before.inc';
if (!is_logged_in()) die('Access denied.');

$photo_id = intval($_GET['id'] ?? 0);
$task_id  = intval($_GET['task'] ?? 0);

if ($photo_id < 1) die('Bad photo.');

$stmt = $con->prepare("SELECT file_path FROM ttcrm_task_photos WHERE photo_id=?");
$stmt->bind_param("i", $photo_id);
$stmt->execute();
$stmt->bind_result($file_path);
$stmt->fetch();
$stmt->close();

if ($file_path) {
    $full = $_SERVER['DOCUMENT_ROOT'] . '/uploads/tasks/' . $file_path;
    if (file_exists($full)) unlink($full);
}

$stmt = $con->prepare("DELETE FROM ttcrm_task_photos WHERE photo_id=? LIMIT 1");
$stmt->bind_param("i", $photo_id);
$stmt->execute();
$stmt->close();

header("Location: /admin/task_view.php?id=" . $task_id);
exit;
