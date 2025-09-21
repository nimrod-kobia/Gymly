<?php
require_once "../autoload.php";
$form = new Forms();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Gymly</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
    <!-- Optionally include Bootstrap if you’re using it -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php echo $form->signIn(); ?>
</body>
</html>
