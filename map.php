<?xml version="1.0" encoding="UTF-8"?>
<?php
header("Content-Type: text/html;charset=UTF-8");
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "flighttrackinator3000";

// Establish
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

function mysqli_field_name($result, $field_offset)
{
    $properties = mysqli_fetch_field_direct($result, $field_offset);
    return is_object($properties) ? $properties->name : null;
}

$aircraft_id = $_GET['aircraft_id'];
$sql = "SELECT * FROM flight WHERE aircraft_id = '$aircraft_id'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  echo "<center><h1> flight </h1>";
  $count = mysqli_field_count($conn);
  $header = "<table id='t01'><tr>";
  for($x = 0; $x < $count; $x++){
     $header = $header . "<th>" . mysqli_field_name($result, $x) . "</th>";
  }
  $header . "</tr>";
  echo $header;
   // output data of each row
   while($row = $result->fetch_assoc()) {
      $line = "<tr>";
      for($x = 0; $x < $count; $x++){
        $line = $line . "<td>" . $row[mysqli_field_name($result, $x)] . "</td>";
      }
      echo utf8_encode($line)."</tr>";
      $latitude = $row['latitude'];
      $longitude = $row['longitude'];
   }
   echo "</table></center>";
   
} else {
   echo "0 results";
}

$conn->close();
?>
<html>
  <head>
    <style>
      /* Set the size of the div element that contains the map */
      #map {
        height: 400px;  /* The height is 400 pixels */
        width: 100%;  /* The width is the width of the web page */
       }
    </style>
  </head>
  <body>
    <h3> <?php echo $aircraft_id?> </h3>
    <!--The div element for the map -->
    <div id="map"></div>
    <script>
// Initialize and add the map
function initMap() {
  var latitude = "<?php echo $latitude ?>";
  var longitude = "<?php echo $latitude ?>";
  // The location of flightLocation
  var flightLocation = {lat: parseFloat(latitude), lng: parseFloat(longitude)};
  // The map, centered at flightLocation
  var map = new google.maps.Map(
      document.getElementById('map'), {zoom: 4, center: flightLocation});
  // The marker, positioned at flightLocation
  var marker = new google.maps.Marker({position: flightLocation, map: map});
}
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD-dMxwYbOXRN84TnGsGhVS7xdPDbzMS54&callback=initMap">
    </script>
  </body>
</html>