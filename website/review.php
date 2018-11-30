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

$review_exists;

function printReview($type, $id) {
	/* Escape if incorrect attribute found */
	if(!isset($type) || !isset($id)) {
		echo "Error displaying review.";
		return;
	}
	
	global $conn;
	global $current_url;
	global $review_exists;

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
	}

if(!$review_exists) {
	echo "create review";
}

}
?>

<!-- https://www.w3schools.com/howto/howto_js_popup_form.asp -->
</body>
</html>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body {font-family: Arial, Helvetica, sans-serif;}
* {box-sizing: border-box;}

/* Button used to open the contact form - fixed at the bottom of the page */
.open-button {
  background-color: #555;
  color: white;
  padding: 16px 20px;
  border: none;
  cursor: pointer;
  opacity: 0.8;
  bottom: 23px;
  right: 28px;
  width: 280px;
}

/* The popup form - hidden by default */
.form-popup {
  display: none;
  bottom: 0;
  right: 15px;
  border: 3px solid #f1f1f1;
  z-index: 9;
}

/* Add styles to the form container */
.form-container {
  max-width: 300px;
  padding: 10px;
  background-color: white;
}

/* Full-width input fields */
.form-container input[type=text], .form-container input[type=password] {
  width: 100%;
  padding: 15px;
  margin: 5px 0 22px 0;
  border: none;
  background: #f1f1f1;
}

/* When the inputs get focus, do something */
.form-container input[type=text]:focus, .form-container input[type=password]:focus {
  background-color: #ddd;
  outline: none;
}

/* Set a style for the submit/login button */
.form-container .btn {
  background-color: #4CAF50;
  color: white;
  padding: 16px 20px;
  border: none;
  cursor: pointer;
  width: 100%;
  margin-bottom:10px;
  opacity: 0.8;
}

/* Add a red background color to the cancel button */
.form-container .cancel {
  background-color: red;
}

/* Add some hover effects to buttons */
.form-container .btn:hover, .open-button:hover {
  opacity: 1;
}
</style>
</head>
<body>

<button class="open-button" onclick="openForm()">Open Form</button>

<div class="form-popup" id="myForm">
  <form action="/action_page.php" class="form-container">
    <h1>Login</h1>

    <label for="email"><b>Email</b></label>
    <input type="text" placeholder="Enter Email" name="email" required>

    <label for="psw"><b>Password</b></label>
    <input type="password" placeholder="Enter Password" name="psw" required>

    <button type="submit" class="btn">Login</button>
    <button type="button" class="btn cancel" onclick="closeForm()">Close</button>
  </form>
</div>

<script>
function openForm() {
    document.getElementById("myForm").style.display = "block";
}

function closeForm() {
    document.getElementById("myForm").style.display = "none";
}
</script>
<br>

</body>
</html>

