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
$current_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

/* Fixes the url after a redirect */
if(isset($_GET['deleteReview'])) {
	$the_OG_url_hell_yea = strtok($current_url, '&');
	header("LOCATION: $the_OG_url_hell_yea");
}	// this took me almost an hour to figure out...


function updateReview($type, $id, $username, $url) {
	global $conn;

	$date = date("d/m/Y");

	/* Attempt to update review */
	$sql = "UPDATE
			SET		rating = '', time = '$date', comment = ''
			WHERE	type = '$type'
			AND		refID = '$id'
			AND		username = '$username'";

	/* Create pop-up notifying the user of result */
	if ($conn->query($sql) === TRUE) {
		echo 	"<script type='text/javascript'>
				alert('Review successfully updated!');
				window.location.href = '$url';" 	// redirects user to original page
				. "</script>";
	} else {
		echo 	"<script type='text/javascript'>
				alert('Error updating review!');
				</script>";
	}
}


function deleteReview($type, $id, $username, $url) {
	global $conn;

	/* Attempt to delete from table */
	$sql = "DELETE
			FROM	review
			WHERE	type = '$type'
			AND		refID = '$id'
			AND		username = '$username'";

	/* Create pop-up notifying the user of result */
	if ($conn->query($sql) === TRUE) {
		echo 	"<script type='text/javascript'>
				alert('Review successfully deleted!');
				window.location.href = '$url';" 	// redirects user to original page
				. "</script>";
	} else {
		echo 	"<script type='text/javascript'>
				alert('Error deleting review!');
				</script>";
	}
}

?>


<html>
<body>

<h2>User Reviews</h2>
<?php

function printReview($type, $id) {
	/* Escape if incorrect attribute found */
	if(!isset($type) || !isset($id)) {
		echo "Error displaying review.";
		return;
	}
	
	global $conn;
	global $current_url;

	$sql = "SELECT *
			FROM	review
			WHERE	type = '$type'
			AND		refID = '$id'";
	$result = $conn->query($sql);

	if($result->num_rows > 0) {
		$review_exists = false;
		while($row = $result->fetch_assoc()) {
			echo $row["username"];

			/* User commands if they have previously left a review */
			if($row["username"] == $_SESSION['username']) {
				$review_exists = true;

				/* Update Review Option */
				echo 	"<font size ="."2".">
						<a href=".$current_url."&updateReview".">(update)"."</a>
						</font>";
				if(isset($_GET['updateReview'])) {
					updateReview($type, $id, $_SESSION['username'], $current_url);
				}

				/* Delete Review Option */
				echo 	"<font size ="."2".">
						<a href=".$current_url."&deleteReview".">(delete)"."</a>
						</font>";
				if(isset($_GET['deleteReview'])) {
					deleteReview($type, $id, $_SESSION['username'], $current_url);
				}
			}
			echo "<br>";

			echo "Score: " . $row["rating"] . "<br>";
			echo $row["time"] . "<br>";
			echo $row["comment"] . "<br><br>";
		}

		if(!$review_exists) {
			echo "Create review?";
		}
	}

}
?>

</body>
</html>
