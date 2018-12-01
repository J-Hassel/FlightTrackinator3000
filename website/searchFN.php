<?php

$host="localhost";
$username="root";
$password="";
$databasename="flighttrackinator3000";

$conn = new mysqli($host, $username, $password, $databasename);

$searchTerm = $_GET['term'];
$airline = $_GET['airline'];

$select =$conn->query("SELECT * FROM flight WHERE airline LIKE '%$airline%' AND flight_num LIKE '$searchTerm%'");
while ($row=$select->fetch_assoc()) 
{
 $data[] = $row['flight_num'];
}
//return json data
echo json_encode($data);
?>