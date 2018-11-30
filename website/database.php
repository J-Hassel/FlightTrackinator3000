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
    <title>Database</title>
    <link rel="stylesheet" type="text/css" href="style.css">
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
   
   <style>
      table#t01 tr:nth-child(even) {
          background-color: #eee;
      }
      table#t01 tr:nth-child(odd) {
         background-color: #fff;
      }
      table#t01 th {
          background-color: black;
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
   <table width = "400" border = "0" cellspacing = "1" 
      cellpadding = "2">
   
      <tr>
      <select name="table_name">
       <option value="airline">Airlines</option>
       <option value="airplane">Airplanes</option>
       <option value="flight">Flights</option>
       <option value="airport">Airports</option>
      </select>
      </tr>
   
      <tr>
         <td>
            <input name = "Submit" type = "submit" id = "Submit" value="View Table">
         </td>
      </tr>
   
   </table>
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
          break;

        case 'airplane':
          $heading = "Airplanes Database";
          break;

        case 'flight':
          $heading = "Flights Database";
          break;

        case 'airport':
          $heading = "Airports Database";
          break;


      }
      echo "<center><h1 style=\"padding: 20px;\">" . $heading . "</h1>";
      $page = "#";
      $var = "";
      $headVal = "";
      if( $table_name == "flight"){
          $page = "singleflight.php";
          $var = "aircraft_id";
          $headVal = "aircraft_id";
      }else if( $table_name == "airport"){
          $page = "airport.php";
          $var = "iataCode";
          $headVal = "iataCode";
      }
      $count = mysqli_field_count($link);
      $header = "<table id='t01'><tr>";
      $line = "<tr><form><input type=\"hidden\" name = \"table_name\" value = \"$table_name\">";
      for($x = 0; $x < $count; $x++){
         $field_name = mysqli_field_name($result, $x);
         $header = $header . "<th>$field_name</th>";
         if($x > 0){
            $line = $line . "</td>";
         }
         if(isset($_GET[$field_name])){
            $line = $line . "<td><input name=\"$field_name\" type=\"text\" id=\"$field_name\" value='$_GET[$field_name]'>";
         }else{
            $line = $line . "<td><input name=\"$field_name\" type=\"text\" id=\"$field_name\">";
         }
      }
      $line = $line . "<input name = \"Submit\" type = \"submit\" id = \"searchsubmit\" value = \"Submit\"></form></tr>";
      $header = $header . "</tr>";
      echo $header;
      echo $line;
       // output data of each row
       while($row = $result->fetch_assoc()) {
          $line = "<tr>";
         for($x = 0; $x < $count; $x++){
            if(mysqli_field_name($result, $x) == $headVal){
               $aircraftID = $row[mysqli_field_name($result, $x)];
               $line = $line . "<td><a href=\"$page?$var=$aircraftID\">" . $row[mysqli_field_name($result, $x)] . "</a></td>";
            }else if($table_name == "flight" and (mysqli_field_name($result, $x) == "source" OR mysqli_field_name($result, $x) == "destination")){
               $iataCode = $row[mysqli_field_name($result, $x)];
               $line = $line . "<td><a href=\"airport.php?iataCode=$iataCode\">" . $row[mysqli_field_name($result, $x)] . "</a></td>";
            }else{
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
