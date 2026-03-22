<?php
session_start();

/* Unset all session variables */
$_SESSION = [];

/* Destroy session */
session_destroy();

/* Prevent caching (so back button won’t reopen dashboard) */
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

/* Redirect to login page */
header("Location: teacher_login.php");
exit();
?>