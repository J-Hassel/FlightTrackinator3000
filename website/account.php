<?php
// Initialize the session
session_start();
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Initialize DB connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "flighttrackinator3000";
$conn = mysqli_connect($servername, $username, $password, $dbname) or die("Connection failed: " . mysqli_connect_error());
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

// Include config file
require_once "config.php";


function printAccountReview($username) {
    global $conn;
 
    $sql = "SELECT *
       FROM	review
       WHERE	username = '$username'";
    $result = $conn->query($sql);
 
    if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<br>&nbsp&nbsp&nbsp&nbsp&nbsp" . $row["refID"]; // POSSIBLE TODO: make this a link???
            // echo "<div style='background-color: #eee; margin-top: 10px;' id='review". $row['hashID'] ."'>";
			echo "<br>&nbsp&nbsp&nbsp&nbsp&nbspRating: " . $row["rating"] . "/5";
			echo "&nbsp&nbsp&nbsp&nbsp&nbsp" . explode('-', $row["time"])[1] . "/" . explode(' ', explode('-', $row["time"])[2])[0] . "/" . explode('-', $row["time"])[0] . "<br>";
			echo "&nbsp&nbsp&nbsp&nbsp&nbspComment: " . $row["comment"] . "<br><br>";
            // echo "</div>";
       }
    } else {
       echo "No Reviews";
    }
 }

?>

<html>
    <head>
        <?php include_once("header.php"); ?>
        <style>
            * {
                box-sizing: border-box;
            }
            .column1 {
                float: left;
                width: 40%;
                padding: 10px;
                background-color: #BED2D9;
            }

            .column2 {
                float: left;
                width: 60%;
                padding: 10px;
                background-color: #98B5BE;
            }

            .row {
                content: "";
                display: table;
                clear: both;
                height: 100%;
            }
        </style>
    </head>
    <body>
        <div class="row">
            <div class="column1">
                <center><h1>Control Panel</h1></center>
                <hr>
                This is some text
                <br>
                maybe
                <br>
                we could
                <br>
                include
                <br>
                a delete account link?
            </div>
            <div class="column2">
                <center><h1><?php echo "Reviews by " . $_SESSION['username'];?></h1></center>
                <hr>
                <h4><?php printAccountReview($_SESSION['username']);?></h4>
            </div>
        </div>
    </body>
</html>