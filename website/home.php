<?xml version="1.0" encoding="UTF-8"?>
<?php

// Initialize the session
session_start();

 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>

<html class="home">
<head>
	<title>Flight Tracker</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
	<header>
		<div class "row">
			<div class="logo">
 				<img src="logo.png">
			</div>
		
			<ul class="main-nav">
				<li class="active"><a href="home.php">Home</a></li>
				<li><a href="allflights.php">Map</a></li>
				<li><a href="database.php?table_name=flight&Submit=Submit">Database</a></li>
				<li><a href="logout.php">Sign Out</a></li>
				<li><a href="reset-password.php">Reset Password</a></li>
			</ul>
		</div>
	</header>

	<div class="tracker-box">
		<div class="ftrack-headline">
			<h1>Flight Tracker
				&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
				&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
				&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<span>✈</span></h1>
		</div>

		<div class="flight-tracker-form">
			<form id="ft-form" action="database.php">
				<div class = "airline-form">
					<input name="airline" id="flight-tracker-form-airline" type="text" placeholder="Airline Code" class="form-control">
				</div>

				<div class="flight-number-form">
					<input name="flight_num"id="flight-tracker-form-number" type="text" placeholder="Flight Number" class="form-control">
				</div>
            <input type="submit" value="Track Flight" name="Submit">
			</form>
		</div>

	</div>

</body>
</html>


