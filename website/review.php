<?php
// include_once("config.php");
session_start();
/* Database connection start */
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "flighttrackinator3000";
$conn = mysqli_connect($servername, $username, $password, $dbname) or die("Connection failed: " . mysqli_connect_error());
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

/* Determine the type and ID */
$typeAttribute;
$idAttribute;
switch(basename($_SERVER['PHP_SELF'])) {
	case "my_reviews.php":
		$typeAttribute = "self";
		break;
	case "map.php":
		$typeAttribute = "aircraft_id";
		break;
	case "airport.php":
		$typeAttribute = "iataCode";
		break;
}

/* Initialize current data for when the user leaves a review */
$curUser = $_SESSION["username"];
$curDate = date("m/d/Y");
$curRating = 0;
$curType = $typeAttribute;
$curComment = "";

?>