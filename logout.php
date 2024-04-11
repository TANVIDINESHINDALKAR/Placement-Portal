<?php
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the login page or wherever you prefer
header("Location: index.php");
exit;
?>
