<?xml version="1.0" encoding="UTF-8"?>
<?php

// Initialize the session
session_start();

// Include config file
require_once "config.php";

$username = $_SESSION['username'];
echo $username;

$sql = "DELETE FROM user WHERE username = '$username'";
$link->query($sql);

$sql = "DELETE FROM review WHERE username = '$username'";
$link->query($sql);

// Unset all of the session variables
$_SESSION = array();
 
// Destroy the session.
session_destroy();

header("location: login.php");
exit;
?>