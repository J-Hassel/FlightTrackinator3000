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

function printReview($username) {
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
            .controls {
                text-align: center;
                font-family: 'Verdana', sans-serif;
                font-size: 14pt;
                float: left;
                width: 70%;
                background-color: #B7D8DE;
                height: 100%;
            }
            .reviews {
                text-align: left;
                float: inherit;
                padding: 20px;
                background-color: #B7D8DE;
                height: 100%;
            }
            .edge {
                float: left;
                background-color: #83B0B9;
                width: 15%;
                height: 100%;
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
            <div class="edge"></div>
            <div class="controls">
                <br>
                <h1>Control Panel</h1>
                <br><hr><br><br>
                <a href='reset-password.php'>Reset Password</a>
                <br><br><br>
                <a href="logout.php">Sign Out</a>
                <br><br><br>
                Delete Account
                <br><br><br><hr>
                <div class="reviews">
                    <center><h1><?php echo "Reviews by " . $_SESSION['username'];?></h1></center>
                    <br><hr>
                    <?php printReview($_SESSION['username']);?>
                </div>
            </div>
            <div class="edge"></div>
        </div>
    </body>
</html>
