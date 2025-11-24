<?php
// path: /trubetech/crm/admin/file_view.php
// RAW binary output — MUST NOT LOAD before.inc
// REV: 20251123.4

// Load DB connection ONLY
require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/secure/conn.php';

// ABSOLUTELY NO OUTPUT BEFORE HEADERS
ini_set('output_buffering', 'off');
ini_set('zlib.output_compression', 0);
while (ob_get_level()) { ob_end_clean(); }

$file_id = intval($_GET['id'] ?? 0);
if ($file_id < 1) die("Invalid file.");

$stmt = $con->prepare("
    SELECT mime_type, file_data
    FROM ttcrm_files
    WHERE file_id = ?
    LIMIT 1
");
$stmt->bind_param("i", $file_id);
$stmt->execute();

$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if (!$row) die("File not found.");

header("Content-Type: " . $row['mime_type']);
header("Content-Length: " . strlen($row['file_data']));
header("Cache-Control: private, max-age=0, no-cache");

// Final binary output
echo $row['file_data'];
exit;
?>
