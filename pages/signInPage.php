<?php
require_once "../classes/forms.php";
$pageTitle = "Sign In - Your Account";
include '../template/layout.php'; 
?>

<?php
$form = new Forms();
echo $form->signIn(); 
?>

<?php include '../template/footer.php'; ?>