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
	default:
		$typeAttribute = "";
}

/* Initialize current data for when the user leaves a review */
// check to see if $_SESSION exists (if the user is logged in).
$curUser = ""; 
if(isset($_SESSION['username'])) {
	$curUser = $_SESSION['username'];
	unset($_SESSION['username']);
}
$curDate = date("m/d/Y");
$curRating = 0;
$curType = $typeAttribute;
$curComment = "";

?>

<html>
<body>

<div class="review-form">
	<form>
		Title: <input type = "text" name = "title"><br>
		Review: <input type = "text" name = "review">
	</form>
</div>

</body>
</html>
