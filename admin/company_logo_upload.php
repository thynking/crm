<?php
// path: /trubetech/crm/admin/company_logo_upload.php
// Upload and preview company logo
// REV: 20251123.2

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/before.inc';

if (!is_logged_in()) die("Not logged in.");

$account_id  = intval($_SESSION['active_account_id']);
$company_id  = intval($_GET['id'] ?? 0);

if ($company_id < 1) die("Invalid company.");

// Fetch latest logo (if exists)
$current_logo_id = null;

$stmt = $con->prepare("
    SELECT f.file_id
    FROM ttcrm_company_files cf
    JOIN ttcrm_files f ON f.file_id = cf.file_id
    WHERE cf.company_id = ?
    ORDER BY cf.id DESC
    LIMIT 1
");
$stmt->bind_param("i", $company_id);
$stmt->execute();
$stmt->bind_result($current_logo_id);
$stmt->fetch();
$stmt->close();

// Handle upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_FILES['logo']) || $_FILES['logo']['error'] != 0) {
        die("File upload error.");
    }

    $file_tmp  = $_FILES['logo']['tmp_name'];
    $file_name = basename($_FILES['logo']['name']);
    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    $allowed = ['jpg','jpeg','png','gif'];
    if (!in_array($ext, $allowed)) {
        die("Invalid file type.");
    }

    $data = file_get_contents($file_tmp);
    $mime = $_FILES['logo']['type'];

    $stmt = $con->prepare("
        INSERT INTO ttcrm_files (account_id, file_name, mime_type, file_data)
        VALUES (?, ?, ?, ?)
    ");
    $null = null;
    $stmt->bind_param("issb", $account_id, $file_name, $mime, $null);
    $stmt->send_long_data(3, $data);
    $stmt->execute();
    $file_id = $stmt->insert_id;
    $stmt->close();

    $stmt2 = $con->prepare("
        INSERT INTO ttcrm_company_files (company_id, file_id)
        VALUES (?, ?)
    ");
    $stmt2->bind_param("ii", $company_id, $file_id);
    $stmt2->execute();
    $stmt2->close();

    header("Location: /admin/company_logo_upload.php?id=" . $company_id);
    exit;
}
?>

<h2>Upload Company Logo</h2>

<?php if ($current_logo_id): ?>
<div style="margin-bottom:20px;">
    <p>Current Logo:</p>
    <img src="/admin/file_view.php?id=<?php echo $current_logo_id; ?>" style="width: 300px; max-width:100%; height:auto; border:1px solid #ccc; padding:5px;">
</div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <input type="file" name="logo" required>
    <br><br>
    <input type="submit" value="Upload">
</form>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/after.inc'; ?>
