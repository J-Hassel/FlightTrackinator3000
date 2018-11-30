<?php
// include_once("config.php");

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
require_once "config.php";


/* Initialize current data for when the user leaves a review */
$revType;
$revID;
$revUser = $_SESSION['username'];
$revDate = date("m/d/Y");
$revRating = 0;
$revComment = "";


function printReview($type, $id) {
	if(!isset($type) || !isset($id)) {
		echo "Error displaying review.";
		return;
	}
	
	global $conn;

	$sql = "SELECT *
			FROM	review
			WHERE	type = '$type'
			AND		refID = '$id'";
	$result = $conn->query($sql);

	if($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			echo $row["username"] . "<br>";
			echo "Score: " . $row["rating"] . "<br>";
			echo $row["time"] . "<br>";
			echo $row["comment"] . "<br><br>";
		}
	}
	

	// echo $revUser;
	// echo $revDate;
	// echo $revRating;
	// echo $revComment;
}

function printUserReview() { };


?>
