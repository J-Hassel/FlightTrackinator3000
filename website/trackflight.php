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
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  
  <style>
    /* Set the size of the div element that contains the map */
    #map {
      margin: auto;
      height: 600px;  /* The height is 400 pixels */
      width: 100%;  /* The width is the width of the web page */
     }
     table#t01 tr:nth-child(even) {
        background-color: #eee;
    }
    table#t01 tr:nth-child(odd) {
       background-color: #fff;
    }
    table#t01 th {
        padding: 10px;
        background-color: #337ab7;
        color: white;
        width: 0.5%;
    }
  </style>
</head>
<body>
    <?php include_once("header.php"); ?>


<?php
//$output = shell_exec("python flight_parser.py");
//header("Refresh:30");
header("Content-Type: text/html; charset=utf-8");

$srcLat = "";
$srcLng = "";
$dstLat = "";
$dstLng= "";

if ($link->connect_error) {
  die("Connection failed: " . $link->connect_error);
}

function mysqli_field_name($result, $field_offset)
{
    $properties = mysqli_fetch_field_direct($result, $field_offset);
    return is_object($properties) ? $properties->name : null;
}

$aircraft_id = $_GET['aircraft_id'];

$flightRow = $link->query("SELECT name FROM flight, airline WHERE aircraft_id = '$aircraft_id' AND airline = iataCode")->fetch_assoc();
$airline = $flightRow['name'];

$flightRow = $link->query("SELECT flight_num, city, country FROM flight, airport WHERE aircraft_id = '$aircraft_id' AND source = iataCode")->fetch_assoc();
$flight_num = $flightRow['flight_num'];
$srcCity = $flightRow['city'];
$srcCountry = $flightRow['country'];


$flightRow = $link->query("SELECT city, country FROM flight, airport WHERE aircraft_id = '$aircraft_id' AND destination = iataCode")->fetch_assoc();
$dstCity = $flightRow['city'];
$dstCountry = $flightRow['country'];

$sql = "SELECT * FROM flight WHERE aircraft_id = '$aircraft_id'";
$result = $link->query($sql);
if ($result->num_rows > 0) {
  echo "<center><h1 style=\"padding: 20px;\">" . utf8_encode($airline) . " - Flight " .  utf8_encode($flight_num) . "  &nbsp :: &nbsp " . utf8_encode($srcCity) . ", " . utf8_encode($srcCountry) . " &nbsp ✈ &nbsp " . utf8_encode($dstCity) . ", " . utf8_encode($dstCountry) . "</h1>";
  $count = mysqli_field_count($link);
  $header = "<table id='t01'><tr>";
  for($x = 0; $x < $count; $x++){
     $header = $header . "<th>" . mysqli_field_name($result, $x) . "</th>";
  }
  $header = $header . "</tr>";
  echo $header;
   // output data of each row
   while($row = $result->fetch_assoc()) {
      $line = "<tr>";
      for($x = 0; $x < $count; $x++)
      {
        if(mysqli_field_name($result, $x) == "destination" or mysqli_field_name($result, $x) == "source")
        {
            $variable = $row[mysqli_field_name($result, $x)];
            $line = $line . "<td><a href='airport.php?iataCode=$variable'>" . $variable . "</a></td>";
        }
        else if(mysqli_field_name($result, $x) == "aircraft_id")
        {
            $variable = $row[mysqli_field_name($result, $x)];
            $line = $line . "<td><a href='trackflight.php?aircraft_id=$variable'>" . $variable . "</a></td>";
        }
        else if(mysqli_field_name($result, $x) == "airline")
        {
            $variable = $row[mysqli_field_name($result, $x)];
            $line = $line . "<td><a href='airline.php?iataCode=$variable'>" . $variable . "</a></td>";
        }
        else if(mysqli_field_name($result, $x) == "aircraft_icao")
        {
            $variable = $row[mysqli_field_name($result, $x)];
            $line = $line . "<td><a href='airplane.php?icaoCode=$variable'>" . $variable . "</a></td>";
        }
        else
        {
            $line = $line . "<td>" . $row[mysqli_field_name($result, $x)] . "</td>";
        }
      }
      echo utf8_encode($line)."</tr>";
      $latitude = $row['latitude'];
      $longitude = $row['longitude'];
      $direction = $row['direction'];
	  $source = $row['source'];
	  $destination = $row['destination'];
	  
	  $srcResult = $link->query("SELECT * FROM airport WHERE iataCode = '$source'");
	  if($srcResult->num_rows > 0) {
            while($srcRow = $srcResult->fetch_assoc()) {
                $srcLat = $srcRow['latitude'];
                $srcLng = $srcRow['longitude'];
			}
	  }
      
      $dstResult = $link->query("SELECT * FROM airport WHERE iataCode = '$destination'");
	  if($dstResult->num_rows > 0) {
            while($dstRow = $dstResult->fetch_assoc()) {
                $dstLat = $dstRow['latitude'];
                $dstLng = $dstRow['longitude'];
			}
	  }

   }
   echo "</center>";
   

   
} else {
   echo "0 results";
}


$link->close();
?>



    <!--The div element for the map -->
    <div id="map"></div>
    <script>
    function initMap() {
      var latitude = "<?php echo $latitude ?>";
      var longitude = "<?php echo $longitude ?>";
      // The location of flightLocation
      var flightLocation = {lat: parseFloat(latitude), lng: parseFloat(longitude)};
      // The map, centered at flightLocation
      var map = new google.maps.Map(
          document.getElementById('map'), {zoom: 4, center: flightLocation}
      );
      
      var planeSymbol = {
        path: 'M362.985,430.724l-10.248,51.234l62.332,57.969l-3.293,26.145 l-71.345-23.599l-2.001,13.069l-2.057-13.529l-71.278,22.928l-5.762-23.984l64.097-59.271l-8.913-51.359l0.858-114.43 l-21.945-11.338l-189.358,88.76l-1.18-32.262l213.344-180.08l0.875-107.436l7.973-32.005l7.642-12.054l7.377-3.958l9.238,3.65 l6.367,14.925l7.369,30.363v106.375l211.592,182.082l-1.496,32.247l-188.479-90.61l-21.616,10.087l-0.094,115.684',
        scale: 0.05,
        strokeOpacity: 1,
        color: 'black',
        strokeWeight: 1,
        anchor: new google.maps.Point(400, 400)	
      };
      var marker, i;
      for(i = 0; i < locations.length; i++){
          if(i > 0){
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(locations[i][0],locations[i][1]),
                title: names[i],
                map: map
            });
          }else{
              planeSymbol.rotation = parseInt("<?php echo $direction?>");
              marker = new google.maps.Marker({
                position: new google.maps.LatLng(locations[i][0],locations[i][1]),
                title: names[i],
                icon: planeSymbol,
                map: map
            });
          }
      }
    };
    var names = [
        "<?php echo $aircraft_id?>",
        "source",
        "destination"
    ];
    var locations = [
        [ parseFloat("<?php echo $latitude ?>"), parseFloat("<?php echo $longitude ?>")],
        [ parseFloat("<?php echo $srcLat ?>"), parseFloat("<?php echo $srcLng ?>")],
        [ parseFloat("<?php echo $dstLat ?>"), parseFloat("<?php echo $dstLng ?>")]
    ];
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD-dMxwYbOXRN84TnGsGhVS7xdPDbzMS54&callback=initMap">
    </script>
  </body>
</html>