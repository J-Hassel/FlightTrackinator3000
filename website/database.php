<?xml version="1.0" encoding="UTF-8"?>
<?php

// Initialize the session
session_start();

 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Include config file
require_once "config.php";
?>


<html>
<head>
    <meta charset="UTF-8">
    <meta name="google" content="notranslate">
    <title>Database</title>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
   
   <style>
      table#t01 {
        padding: 10px;
      }
      table#t01 tr:nth-child(even) {
          background-color: #fff;
          border: 1px solid black;
      }
      table#t01 tr:nth-child(odd) {
         background-color: #eee;
      }
      table#t01 th {
          padding: 10px;
          background-color: #337ab7;
          color: white;
      }

      input {
          width: 100%;
          padding: 10px;
          margin: 0px;
      }
      #searchsubmit
      {    
        display: none;
      }
   </style>
</head>
<body>
  <?php include_once("header.php"); ?>

<center>
<form  action = "<?php $_PHP_SELF ?>">

   

  <select name="table_name" class="db-selector" onchange="this.form.submit()">
    <option value="flight" selected hidden>Select Database</option>
    <option value="flight">Flights</option>
    <option value="airline">Airlines</option>
    <option value="airport">Airports</option>
    <option value="airplane">Airplanes</option>
  </select>

<!--   <input name = "Submit" type = "submit" id = "Submit" value="GO!" class="db-button">
 -->
</form>
</center>
<?php
function mysqli_field_name($result, $field_offset)
{
    $properties = mysqli_fetch_field_direct($result, $field_offset);
    return is_object($properties) ? $properties->name : null;
}

function build_table($result, $link, $table_name)
{
   if ($result->num_rows > 0) 
   {
      switch($table_name)
      {
        case 'airline':
          $heading = "Airlines Database";
          $temp = "Airlines";
          break;

        case 'airplane':
          $heading = "Airplanes Database";
          $temp = "Airplanes";
          break;

        case 'flight':
          $heading = "Flights Database";
          $temp = "Flights";
          break;

        case 'airport':
          $heading = "Airports Database";
          $temp  = "Airports";
          break;


      }
      echo "<center><h1 style='padding: 20px;'>" . $heading . "</h1>";
      echo "Showing $result->num_rows $temp";

      $page = "#";
      $var = "";
      $headVal = "";
      if( $table_name == "flight"){
          $page = "trackflight.php";
          $var = "aircraft_id";
          $headVal = "aircraft_id";
      }else if( $table_name == "airport"){
          $page = "airport.php";
          $var = "iataCode";
          $headVal = "iataCode";
      }
      $count = mysqli_field_count($link);
      $header = "<table id='t01'><tr>";
      $line = "<tr><form autocomplete='off'><input type='hidden' name = 'table_name' value = '$table_name'>";
      for($x = 0; $x < $count; $x++){
         $field_name = mysqli_field_name($result, $x);
         $header = $header . "<th>$field_name</th>";
         if($x > 0){
            $line = $line . "</td>";
         }
         if(isset($_GET[$field_name])){
            $line = $line . "<td><input name='$field_name' type='text' id='$field_name' value='$_GET[$field_name]'>";
         }else{
            $line = $line . "<td><input name='$field_name' type='text' id='$field_name'>";
         }
      }
      $line = $line . "<input name = 'Submit' type = 'submit' id = 'searchsubmit' value = 'Submit'></form></tr>";
      $header = $header . "</tr>";
      echo $header;
      echo utf8_encode($line);
       // output data of each row
       while($row = $result->fetch_assoc()) {
          $line = "<tr>";
         for($x = 0; $x < $count; $x++)
         {
            if(mysqli_field_name($result, $x) == $headVal)
            {
               $aircraftID = $row[mysqli_field_name($result, $x)];
               $line = $line . "<td><a href='$page?$var=$aircraftID'>" . $row[mysqli_field_name($result, $x)] . "</a></td>";
            }
            else if($table_name == "flight" and (mysqli_field_name($result, $x) == "source" OR mysqli_field_name($result, $x) == "destination"))
            {
               $iataCode = $row[mysqli_field_name($result, $x)];
               $line = $line . "<td><a href='airport.php?iataCode=$iataCode'>" . $row[mysqli_field_name($result, $x)] . "</a></td>";
            }
            else if($table_name =="flight" and mysqli_field_name($result, $x) == "airline")
            {
               $airline = $row[mysqli_field_name($result, $x)];
               $line = $line . "<td><a href='airline.php?iataCode=$airline'>" . $row[mysqli_field_name($result, $x)] . "</a></td>";
            }
            else if($table_name =="flight" and mysqli_field_name($result, $x) == "aircraft_icao")
            {
              $airplane = $row[mysqli_field_name($result, $x)];
              $line = $line . "<td><a href='airplane.php?icaoCode=$airplane'>" . $row[mysqli_field_name($result, $x)] . "</a></td>";
            }
            else if($table_name =="airline" and mysqli_field_name($result, $x) == "iataCode")
            {
              $airline = $row[mysqli_field_name($result, $x)];
              $line = $line . "<td><a href='airline.php?iataCode=$airline'>" . $row[mysqli_field_name($result, $x)] . "</a></td>";
            }
            else if($table_name =="airplane" and mysqli_field_name($result, $x) == "icaoCode")
            {
              $airplane = $row[mysqli_field_name($result, $x)];
              $line = $line . "<td><a href='airplane.php?icaoCode=$airplane'>" . $row[mysqli_field_name($result, $x)] . "</a></td>";
            }
            else
            {
              $line = $line . "<td>" . $row[mysqli_field_name($result, $x)] . "</td>";
            }
         }
         echo utf8_encode($line)."</tr>";
       }
       echo "</table></center>";
   } else {
       echo "0 results";
   }
}

// Check connection
if ($link->connect_error) {
    die("Connection failed: " . $link->connect_error);
} 
if(isset($_GET['table_name'])) {
   $table_name = $_GET['table_name'];

   /* Attempted start of range query... this is out of place */
   // if(isset($_GET["altitude"])) {
   //    $val = $_GET["altitude"];
   //    $minVal = $_GET["altitude"] - 500;
   //    $maxVal = $_GET["altitude"] + 500;
   //    $sql = $sql . " WHERE $val BETWEEN '$minVal' AND '$maxVal'";
   // }

   if(count($_GET) > 2){
      $sql = "SELECT * FROM $table_name";
      $count = 0;
      foreach ($_GET as $key => $value) {
         if($key != "table_name" AND $key != "Submit"){
            if($count == 0){
               $sql = $sql . " WHERE $key LIKE '%$_GET[$key]%'";
            }else{
               $sql = $sql . " AND $key LIKE '%$_GET[$key]%'";
            }
            $count++;
         }
      }
   }else{
      $sql = "SELECT * FROM $table_name";
   }
   $result = $link->query($sql);
   build_table($result, $link, $table_name);
   $link->close();
}
   ?>
</body>
</html>
