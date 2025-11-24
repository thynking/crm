<?php
echo password_hash("", PASSWORD_DEFAULT).'<br>';
?>

<?php
echo "Session save path: " . ini_get('session.save_path');
?>
