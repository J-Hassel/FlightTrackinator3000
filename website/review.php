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


function updateReview($type, $id, $username, $url) {
	global $conn;

	$date = date('Y-m-d H:i:s');

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

function createReview($conn, $type, $id, $username, $rating, $comment){
   $date = date('Y-m-d H:i:s');
   $unique = uniqid();
   echo $unique;
   $sql = "INSERT INTO review(hashID, refID, type, username, rating, time, comment) VALUES ('$unique', '$id', '$type', '$username', '$rating', '$date', '$comment')";
   $result = $conn->query($sql);
   return $result;
}


function deleteReview($hashID) {
	global $conn;

	/* Attempt to delete from table */
	$sql = "DELETE FROM	review WHERE hashID = '$hashID'";

	/* Create pop-up notifying the user of result */
	if ($conn->query($sql) === FALSE) {
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
						<a href='#' onclick='deleteReview(\"". $row['hashID'] ."\");'>(delete)"."</a>
						</font>";
			}
			echo "<br>";

			echo "Score: " . $row["rating"] . "<br>";
			echo $row["time"] . "<br>";
			echo $row["comment"] . "<br><br>";
		}
	}else{
      echo "No Reviews";
   }
}
if(isset($_POST['deleteReview'])) {
	deleteReview($_POST['hashID']);
}

if(isset($_POST['createReview'])){
   session_start();
   // Check if the user is logged in, if not then redirect him to login page
   if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
       header("location: login.php");
       exit;
   }
   $id = $_POST['id'];
   $type = $_POST['type'];
   $rating = $_POST['rating'];
   $comment = $_POST['comment'];
   $username = $_SESSION['username'];
   if(createReview($conn, $type, $id, $username, $rating, $comment) == true){
      echo "SUBMIT";
   }else{
      echo "OH NO";
   }
}else{
?>

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

textarea{
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

@.rating {
    float:left;
    width:300px;
}
.rating span { float:right; position:relative; }
.rating span input {
    position:absolute;
    top:0px;
    left:0px;
    opacity:0;
}
.rating span label {
    display:inline-block;
    width:30px;
    height:30px;
    text-align:center;
    color:#FFF;
    background:#ccc;
    font-size:30px;
    margin-right:2px;
    line-height:30px;
    border-radius:50%;
    -webkit-border-radius:50%;
}
.rating span:hover ~ span label,
.rating span:hover label,
.rating span.checked label,
.rating span.checked ~ span label {
    background:#F90;
    color:#FFF;
}
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

<button class="open-button" onclick="openForm()">Create Review</button>

<div class="form-popup" id="myForm">
  <form class="form-container">

    <label for="title"><b>Rating</b></label>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
      <div class="rating">
    <span><input type="radio" name="rating" id="str5" value="5"><label for="str5" class="fa fa-star"></label></span>
    <span><input type="radio" name="rating" id="str4" value="4"><label for="str4" class="fa fa-star"></label></span>
    <span><input type="radio" name="rating" id="str3" value="3"><label for="str3" class="fa fa-star"></label></span>
    <span><input type="radio" name="rating" id="str2" value="2"><label for="str2" class="fa fa-star"></label></span>
    <span><input type="radio" name="rating" id="str1" value="1"><label for="str1" class="fa fa-star"></label></span>
      </div>

    <label for="comment"><b>Comment</b></label>
    <textarea id="comment" rows="4" cols="50" placeholder="Enter Comment" name="comment" form="myForm"></textarea>

    <button type="button" onclick="createReview()" class="btn">Submit Review</button>
    <button type="button" class="btn cancel" onclick="closeForm()">Close</button>
  </form>
</div>

<script>
var userRating;

$(document).ready(function(){
    // Check Radio-box
    $(".rating input:radio").attr("checked", false);

    $('.rating input').click(function () {
        $(".rating span").removeClass('checked');
        $(this).parent().addClass('checked');
    });

    $('input:radio').change(
      function(){
        userRating = this.value;
    }); 
});
function openForm() {
   if(document.getElementById("myForm").style.display == "none"){
    document.getElementById("myForm").style.display = "block";
   }else{
      document.getElementById("myForm").style.display = "none"
   }
}

function closeForm() {
    document.getElementById("myForm").style.display = "none";
}

function createReview() {
   var type, id;
   "<?php 
   $page = basename($_SERVER['PHP_SELF']);
   if($page == 'airport.php'){ 
   ?>"
      type = 'airport';
      id = "<?php echo $iataCode ?>";
   "<?php
   }
   ?>"
   var rating = userRating;
   var comment = document.getElementById("comment").value;
   var dataString = "createReview=1&rating=" + rating + "&comment=" + comment + "&id=" + id + "&type=" + type;
    $.ajax({
      url:"review.php",
      type: "POST",
      data: dataString,
      success: function() {
         window.location.reload();
      }
   });
   closeForm();
}

function deleteReview(hashID){
   var dataString = "deleteReview=1&hashID=" + hashID;
   $.ajax({
      url:"review.php",
      type: "POST",
      data: dataString,
      success: function() {
         window.location.reload();
      }
   });
}
</script>
<br>

<?php
}
?>

</body>
</html>