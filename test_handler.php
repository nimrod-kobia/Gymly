<?php
// Simulate a logged-in session for testing
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_email'] = 'test@test.com';

// Simulate GET parameters
$_GET['muscle_group'] = 'back';
$_GET['limit'] = 50;

// Now include the handler
require_once 'handlers/searchExercises.php';
