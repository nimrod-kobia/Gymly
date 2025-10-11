<?php
require_once "../autoload.php";

// Destroy the session completely
SessionManager::destroySession();

// Redirect to home page with success message
header("Location: ../pages/home.php?success=Logged+out+successfully");
exit();
?>