<?php
require_once "../autoload.php";

// Only allow access if user is in signup or verification process
if (!SessionManager::isInSignupProcess() && !SessionManager::isInVerificationProcess()) {
    header("Location: ../pages/signUpPage.php");
    exit();
}

$pageTitle = "Verify Your Account";
include '../template/layout.php';

$form = new Forms();
echo $form->verifyForm();
?>

<?php include '../template/footer.php'; ?>