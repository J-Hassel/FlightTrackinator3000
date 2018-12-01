<?php

$host="localhost";
$username="root";
$password="";
$databasename="flighttrackinator3000";

$conn = new mysqli($host, $username, $password, $databasename);

$searchTerm = $_GET['term'];

$select =$conn->query("SELECT * FROM airline WHERE name LIKE '%".$searchTerm."%'");
while ($row=$select->fetch_assoc()) 
{
 $data[] = $row['name'] . ' - ' . $row['iataCode'];
}
//return json data
echo json_encode($data);
?>