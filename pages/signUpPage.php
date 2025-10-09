<?php
require_once "../classes/forms.php";
$pageTitle = "Sign Up - Create Your Account";
include '../template/layout.php'; 
?>

<?php
$form = new Forms();
echo $form->signUp(); 
?>

<?php include '../template/footer.php'; ?>