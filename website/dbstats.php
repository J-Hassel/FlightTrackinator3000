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
            $sql = $conn->query("SELECT COUNT(DISTINCT name) FROM airline");
            $row = $sql->fetch_row();
            echo "There are currently <b>" . $row[0] . "</b> airlines in operation";
            $sql = $conn->query("SELECT COUNT(DISTINCT country) FROM airline");
            $row = $sql->fetch_row();
            echo " from <b>" . $row[0] . "</b> different countries.";
            break;
        case "airport":
            $sql = $conn->query(
                "SELECT name, iataCode, city, country, destination
                FROM airport INNER JOIN flight
                ON airport.iataCode = flight.destination
                GROUP BY destination
                ORDER BY COUNT(*) DESC LIMIT 1"
            );
            $row = $sql->fetch_assoc();
            echo "The current most popular destination is the <b>" . $row["name"] . "</b><br>";
            echo "Which is located in: <b>" . $row["city"] . "</b>, ";
            echo "<b>" . $row["country"] . "</b>.";
            break;
        case "flight":
            $sql = $conn->query("SELECT COUNT(DISTINCT aircraft_id) FROM flight");
            $row = $sql->fetch_row();
            echo "There are currently <b>" . $row[0] . "</b> flights en-route right now.<br>";

            $sql = $conn->query("SELECT AVG(speed) FROM flight");
            $row = $sql->fetch_row();
            echo "The average speed of all the flights is <b>" . $row[0] . "mph</b>.<br>";

            $sql = $conn->query("SELECT MIN(speed) FROM flight");
            $row = $sql->fetch_row();
            echo "The slowest plane is currently crawling across the globe at a whopping <b>"
                . $row[0] . "mph</b>, ";
            $sql = $conn->query("SELECT MAX(speed) FROM flight");
            $row = $sql->fetch_row();
            echo "while the fastest plane has reached <b>" . $row[0] . "mph</b>.<br>";

            $sql = $conn->query("SELECT AVG(altitude) FROM flight");
            $row = $sql->fetch_row();
            echo "The average altitude is: <b>" . $row[0] . "ft</b>.<br>";

            $sql = $conn->query("SELECT MIN(altitude) FROM flight");
            $row = $sql->fetch_row();
            echo "The lowest plane right now is at an altitude of <b>" . $row[0] . "ft</b>. ";
            $sql = $conn->query("SELECT MAX(altitude) FROM flight");
            $row = $sql->fetch_row();
            echo "and the highest altitude reached is <b>" . $row[0] . "ft</b>.<br>";
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

<html>
    <head>
        <?php include_once("header.php"); ?>
        <style>
            * {
                box-sizing: border-box;
            }
            .the-meat {
                float: left;
                font-family: 'Verdana', sans-serif;
                font-size: 14pt;
                background-color: #B7D8DE;
                padding: 20px;
                width: 70%;
                height: 100%;
            }
            .the-greens {
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
            <div class="the-greens"></div>
            <div class="the-meat">
                <center><h1>Database Statistics</h1></center><br><hr><br>
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
            <div class="the-greens"></div>
        </div>
    </body>
</html>