<?php
require_once "../autoload.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputCode = $_POST['code'] ?? '';
    if ($inputCode === $_SESSION['verification_code']) {
        echo "Verification successful. Account activated.";
    } else {
        echo "Invalid verification code.";
    }
}
?>
<form method="POST">
    <label>Enter Verification Code:</label>
    <input type="text" name="code" required>
    <button type="submit">Verify</button>
</form>
