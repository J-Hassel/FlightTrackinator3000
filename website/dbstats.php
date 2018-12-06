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


function printStats($table) {
    global $conn;
    
    switch($table) {
        case "airline":
            $sql = $conn->query("SELECT COUNT(DISTINCT airline.iataCode), COUNT(DISTINCT country) FROM airline,flight WHERE airline.iataCode = flight.airline");
            $row = $sql->fetch_row();
            echo "There are currently <b>" . $row[0] . "</b> airlines in operation";
            echo " from <b>" . $row[1] . "</b> different countries.<br>";

            $sql = $conn->query(
                "SELECT name, iataCode, COUNT(*) AS count
                FROM airline INNER JOIN flight
                ON airline.iataCode = flight.airline
                GROUP BY airline
                ORDER BY COUNT(*) DESC LIMIT 1");
            $row = $sql->fetch_assoc();
            echo "The busiest airline is <b>" . $row['name'] . " (" . $row['iataCode'] . ")</b> with <b>" . $row['count'] . "</b> flights currently en-route." ;
            break;
        case "airport":
            $sql = $conn->query("SELECT COUNT(DISTINCT airport.iataCode) FROM airport, flight WHERE airport.iataCode = flight.source");
            $row = $sql->fetch_row();
            echo "There are currently <b>" . $row[0] . "</b> airports in operation.<br>";


			$sql = $conn->query(
                "SELECT name, iataCode, city, country, destination, COUNT(*) AS count
                FROM airport INNER JOIN flight
                ON airport.iataCode = flight.source
                GROUP BY source
                ORDER BY COUNT(*) DESC LIMIT 1"
            );
            $row = $sql->fetch_assoc();
            echo "The current busiest source is the <b>" . $row["name"] . " (" . $row['iataCode'] . ")</b>, located in <b>" . $row['city'] . ", " . $row['country'] . "</b><br>"; 
            echo "There are currently <b>" . $row['count'] . "</b> flights in the air who started their journey at <b>" . $row['iataCode'] . "</b>.<br>";
            

            $sql = $conn->query(
                "SELECT name, iataCode, city, country, destination, COUNT(*) AS count
                FROM airport INNER JOIN flight
                ON airport.iataCode = flight.destination
                GROUP BY destination
                ORDER BY COUNT(*) DESC LIMIT 1"
            );
            $row = $sql->fetch_assoc();
            echo "The current busiest destination is the <b>" . $row["name"] . " (" . $row['iataCode'] . ")</b>, located in <b>" . $row['city'] . ", " . $row['country'] . "</b><br>"; 
            echo "There are currently <b>" . $row['count'] . "</b> flights in the air who will end their journey at <b>" . $row['iataCode'] . "</b>.";
            break;
        case "flight":
            $sql = $conn->query("SELECT COUNT(aircraft_id) FROM flight");
            $row = $sql->fetch_row();
            echo "There are currently <b>" . $row[0] . "</b> flights en-route(in the air or on the runway) right now.<br>";

            $sql = $conn->query("SELECT AVG(speed) FROM flight");
            $row = $sql->fetch_row();
            echo "The average speed of all the flights is <b>" . round(floatval($row[0]), 1) . " mph</b>.<br>";

            $sql = $conn->query("SELECT MIN(speed) FROM flight WHERE speed > 0");
            $row = $sql->fetch_row();
            echo "The slowest plane is currently crawling across the globe at a whopping <b>"
                . $row[0] . " mph</b>(on the runway), ";
            $sql = $conn->query("SELECT MAX(speed) FROM flight");
            $row = $sql->fetch_row();
            echo "while the fastest plane has reached <b>" . round(floatval($row[0]), 1) . " mph</b>.<br>";

            $sql = $conn->query("SELECT AVG(altitude) FROM flight");
            $row = $sql->fetch_row();
            echo "The average altitude is: <b>" . round(floatval($row[0]), 1) . " ft</b>.<br>";

            $sql = $conn->query("SELECT MIN(altitude) FROM flight");
            $row = $sql->fetch_row();
            echo "The lowest plane right now is at an altitude of <b>" . $row[0] . " ft</b>(on the runway), ";
            $sql = $conn->query("SELECT MAX(altitude) FROM flight WHERE altitude");
            $row = $sql->fetch_row();
            echo "and the highest altitude reached is <b>" . $row[0] . " ft</b>.<br>";
            echo "<b>Note:</b> True altitude is used (elevation above the average sea level).";
            break;
        case "user":
            $sql = $conn->query("SELECT COUNT(username) FROM user");
            $row = $sql->fetch_row();
            echo "There are currently <b>" . $row[0] . "</b> users and so far, ";
            $sql = $conn->query("SELECT COUNT(username) FROM review");
            $row = $sql->fetch_row();
            echo "and a total of <b>" . $row[0] . "</b> reviews have been made.<br>";

            $sql = $conn->query(
                "SELECT user.username
                FROM review INNER JOIN user
                ON review.username = user.username
                GROUP BY username
                ORDER BY COUNT(*) DESC LIMIT 1"
            );
            $row = $sql->fetch_row();
            echo "The current most active user is <b>" . $row[0] . "</b>, ";
            $sql = $conn->query("SELECT COUNT(username) from review WHERE username = '$row[0]'");
            $row = $sql->fetch_row();
            echo "with a grand total of <b>" . $row[0] . "</b> reviews!";
            break;
    }

}

?>

<html class = "db-stats">
    <head>
        <?php include_once("header.php"); ?>
        <style>
            .db-stats
            {
                background-image: url(bg-home.jpg);
                -webkit-background-size: cover;
                -moz-background-size: cover;
                -o-background-size: cover;
                background-size: cover;
            }

            .stats-box
            {
                box-sizing: border-box;
                font-family: 'Roboto', sans-serif;
                font-size: 14pt;
                border: 1px solid rgba(0,0,0,.5);
                width: 1310px;
                height: 620px;
                background-color: rgba(255,255,255,.8);
                margin: 0 auto;
                margin-top: 80px;
                padding: 50px;
                border-radius: 15px;
            }
            
        </style>
    </head>
    <body>
            <div class="stats-box">
                <h2>Airlines:</h2>
                    <?php printStats("airline"); ?>
                    <br><br>
                <h2>Airports:</h2>
                <?php printStats("airport"); ?>
                    <br><br>
                <h2>Flights:</h2>
                    <?php printStats("flight"); ?>
                    <br><br>
                <h2>Users:</h2>
                    <?php printStats("user"); ?>
            </div>
    </body>
</html>