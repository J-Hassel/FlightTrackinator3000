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


function updateReview($hashID, $rating, $comment) {
	global $conn;

	$date = date('Y-m-d H:i:s');

	/* Attempt to update review */
	$sql = "UPDATE review SET rating = '$rating', comment = '$comment' WHERE review.hashID = '$hashID'";

	/* Create pop-up notifying the user of result */
	if ($conn->query($sql) === FALSE) {
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

<h2 style="margin-left: 20px; margin-top: 20px;">User Reviews</h2>
<br>
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
			echo "<br>&nbsp&nbsp&nbsp&nbsp&nbsp" . $row["username"];

      $updateDiv = "";
			/* User commands if they have previously left a review */
			if($row["username"] == $_SESSION['username']) {
				$review_exists = true;


				/* Update Review Option */
				echo 	"<font size ="."2".">
						<a style='color: white; border-radius: 3px; background-color: #337ab7; opacity: .8; margin: 5px; cursor: pointer;' onclick='openUpdate(\"". $row['hashID'] ."\"); change(". $row["rating"] .", \"". $row['hashID'] . "rating\");'>&nbsp update &nbsp</a>
						</font>";
            $hashID = $row['hashID'];
            $updateDiv = '<div class="form-popup" id="'.$row['hashID'].'">
                     <form class="form-container">
                           <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
                           <div class="rating" id="'. $hashID .'rating">
                               <span onclick="change(5, \''. $hashID .'rating\');"><input type="radio" name="rating" value="5"><label for="str5" class="fa fa-star"></label></span>
                               <span onclick="change(4, \''. $hashID .'rating\');"><input type="radio" name="rating" r4" value="4"><label for="str4" class="fa fa-star"></label></span>
                               <span onclick="change(3, \''. $hashID .'rating\');"><input type="radio" name="rating" value="3"><label for="str3" class="fa fa-star"></label></span>
                               <span onclick="change(2, \''. $hashID .'rating\');"><input type="radio" name="rating" value="2"><label for="str2" class="fa fa-star"></label></span>
                               <span onclick="change(1, \''. $hashID .'rating\');"><input type="radio" name="rating" value="1"><label for="str1" class="fa fa-star"></label></span>
                           </div>
                        <label for="comment"><b>Rating</b></label>
                        <textarea id="comment" rows="4" cols="50" name="comment" form="'.$row['hashID'].'">'. $row["comment"] .'</textarea>
                        <button type="button" onclick="updateReview(\''. $hashID .'\')" class="btn">Submit Review</button>
                        <button type="button" class="btn cancel" onclick="openUpdate(\''.$row['hashID'].'\')">Cancel</button>
                     </form>
                  </div>';

				/* Delete Review Option */
				echo 	"<font size ="."2".">
						<a style=' text-decoration : none; color: white; border-radius: 3px; background-color: #337ab7; opacity: .8; margin: 5px; cursor: pointer;' href='#' onclick='deleteReview(\"". $row['hashID'] ."\");'>&nbsp delete &nbsp"."</a>
						</font>";
			}
			echo "<br>";
   
         echo $updateDiv;
         echo "<div class='review' id='review". $row['hashID'] ."'>";
			echo "<br>Rating: " . $row["rating"] . "/5";
			echo "&nbsp&nbsp&nbsp&nbsp&nbsp" . explode('-', $row["time"])[1] . "/" . explode(' ', explode('-', $row["time"])[2])[0] . "/" . explode('-', $row["time"])[0] . "<br>";
			echo "Comment: " . $row["comment"] . "<br><br>";
         echo "</div>";
		}
	}else{
      echo "<br>&nbsp&nbsp&nbsp&nbsp&nbspNo Reviews";
   }
}
if(isset($_POST['deleteReview'])) {
	deleteReview($_POST['hashID']);
}

if(isset($_POST['updateReview'])){
   $hashID = $_POST['hashID'];
   $rating = $_POST['rating'];
   $comment = $_POST['comment'];
   updateReview($hashID, $rating, $comment);
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
  font-family: "Roboto", sans-serif;
  margin-left: 20px;
  background-color: #337ab7;
  border-radius: 5px;
  color: white;
  padding: 16px 20px;
  border: none;
  cursor: pointer;
  opacity: 0.8;
  width: 280px;
}

/* The popup form - hidden by default */
.form-popup {
  max-width: 500px;
  margin-left: 20px;
  display: none;
  border: 3px solid #f1f1f1;
  z-index: 9;
}

/* Add styles to the form container */
.form-container {
  padding: 10px;
  background-color: white;
}

/* Full-width input fields */
.form-container input[type=text], .form-container input[type=password] {
  width: 300px;
  padding: 15px;
  margin: 5px 0 22px 0;
  border: none;
  background: #f1f1f1;
}

textarea
{
  width: 100%;
  padding: 15px;
  margin: 5px 0 22px 0;
  border: none;
  border-radius: 5px;
  background: #f1f1f1;
  font-family: "Roboto", sans-serif;
  resize: none;
}

/* When the inputs get focus, do something */
.form-container input[type=text]:focus, .form-container input[type=password]:focus {
  background-color: #ddd;
  outline: none;
}

/* Set a style for the submit/login button */
.form-container .btn {
  font-family: "Roboto", sans-serif;
  background-color: #4CAF50;
  color: white;
  padding: 16px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  width: 100%;
  margin-bottom: 10px;
  opacity: 0.8;
}

/* Add a red background color to the cancel button */
.form-container .cancel {
  background-color: red;
  border-radius: 5px;
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
    color: #FFF;
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
.review {
   background-color: #eee;
   word-wrap: break-word;
   margin-top: 10px;
   margin-left: 20px;
   margin-right: 20px;
   padding: 10px;
}
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

<button class="open-button" onclick="openForm('myForm')">Leave a Review</button>

<div class="form-popup" id="myForm">
  <form class="form-container">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
      <div class="rating" id='create'>
    <span onclick="change(5, 'create');"><input type="radio" name="rating" id="5" value="5"><label for="str5" class="fa fa-star"></label></span>
    <span onclick="change(4, 'create');"><input type="radio" name="rating" id="4" value="4"><label for="str4" class="fa fa-star"></label></span>
    <span onclick="change(3, 'create');"><input type="radio" name="rating" id="3" value="3"><label for="str3" class="fa fa-star"></label></span>
    <span onclick="change(2, 'create');"><input type="radio" name="rating" id="2" value="2"><label for="str2" class="fa fa-star"></label></span>
    <span onclick="change(1, 'create');"><input type="radio" name="rating" id="1" value="1"><label for="str1" class="fa fa-star"></label></span>
      </div>

    <label for="comment"><b>Rating</b></label>
    <textarea id="comment" rows="4" cols="50" placeholder="Enter Comment" name="comment" form="myForm"></textarea>

    <button type="button" onclick="createReview()" class="btn">Submit Review</button>
    <button type="button" class="btn cancel" onclick="closeForm('myForm')">Close</button>
  </form>
</div>

<script>

var userRating = {};
function change(rating, element){
 var div = document.getElementById(element);
 userRating[element] = rating;
 var spans = div.getElementsByTagName('span');
 for(var i = 0; i < spans.length; i++){
    var span = spans[i];
    var radio = span.getElementsByTagName('input')[0];
    if(radio.getAttribute('value') <= rating){
       span.classList.add('checked');
    }else{
       span.classList.remove('checked');
    }
 }
}

function openForm(id) {
   if(document.getElementById(id).style.display == "none"){
    document.getElementById(id).style.display = "block";
   }else{
      document.getElementById(id).style.display = "none"
   }
}

function openUpdate(id) {
   if(document.getElementById(id).style.display == "none"){
      document.getElementById("review" + id).style.display = "none";
      document.getElementById(id).style.display = "block";
   }else{
      document.getElementById(id).style.display = "none"
      document.getElementById("review" + id).style.display = "block";
   }
}

function closeForm(id) {
    document.getElementById(id).style.display = "none";
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
   }else if($page == 'airline.php'){
      ?>"
      type = 'airline';
      id = "<?php echo $iataCode ?>";
      "<?php
   }else if($page == 'airplane.php'){
      ?>"
      type = 'airplane';
      id = "<?php echo $icaoCode ?>";
      "<?php
   }
   ?>"
   var rating = userRating['create'];
   var comment = document.getElementById("comment").value;
   comment = comment.replace(/'/g, "''");
   var dataString = "createReview=1&rating=" + rating + "&comment=" + comment + "&id=" + id + "&type=" + type;
    $.ajax({
      url:"review.php",
      type: "POST",
      data: dataString,
      success: function() {
         window.location.reload();
      }
   });
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

function updateReview(hashID){
      var div = document.getElementById(hashID);
      var rating = userRating[hashID + "rating"];
      var comment = div.getElementsByTagName("textarea")[0].value;
      comment = comment.replace(/'/g, "''");
      var dataString = "updateReview=1&rating=" + rating + "&comment=" + comment + "&hashID=" + hashID;
      $.ajax({
      url:"review.php",
      type: "POST",
      data: dataString,
      success: function(html) {
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