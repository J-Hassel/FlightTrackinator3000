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
      <div class="page-header">
        <p>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>.</p>
            <p>
        <a href="reset-password.php" class="btn btn-warning">Reset Your Password</a>
        <a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a>
    </p>
    </div>

<?php
//header("Refresh:30");
header("Content-Type: text/html;charset=UTF-8");
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "flighttrackinator3000";

$srcLat = "";
$srcLng = "";
$dstLat = "";
$dstLng= "";

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

$iataCode = $_GET['iataCode'];
echo "<center><h1> Airport </h1>";
?>
<html>
  <head>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: right; }
    </style>
    <style>
      /* Set the size of the div element that contains the map */
      #map {
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
          background-color: black;
          color: white;
          width: 0.5%;
      }
    </style>
  </head>
  <body>
    <!--The div element for the map -->
    <div id="map"></div>
    <h3> <?php echo $iataCode?> </h3>
<?php    
$sql = "SELECT * FROM airport WHERE iataCode = '$iataCode'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  $count = mysqli_field_count($conn);
  $header = "<table id='t01'><tr>";
  for($x = 0; $x < $count; $x++){
     $header = $header . "<th>" . mysqli_field_name($result, $x) . "</th>";
  }
  $header = $header . "</tr>";
  echo $header;
   // output data of each row
   while($row = $result->fetch_assoc()) {
      $line = "<tr>";
      for($x = 0; $x < $count; $x++){
        $line = $line . "<td>" . $row[mysqli_field_name($result, $x)] . "</td>";
      }
      
      $latitude = $row['latitude'];
      $longitude = $row['longitude'];
      echo utf8_encode($line)."</tr>";
   }
   echo "</table>";
   echo "<h1> Flights From $iataCode</h1>";
   $from = $conn->query("SELECT * FROM flight WHERE source = '$iataCode'");
   $locs = array();
   if ($from->num_rows > 0) {
          $count = mysqli_field_count($conn);
          $header = "<table id='t01'><tr>";
          for($x = 0; $x < $count; $x++){
             $header = $header . "<th>" . mysqli_field_name($from, $x) . "</th>";
          }
          $header = $header . "</tr>";
          echo $header;
           // output data of each row
           while($row = $from->fetch_assoc()) {
              $line = "<tr>";
              for($x = 0; $x < $count; $x++){
                if(mysqli_field_name($from, $x) == "destination"){
                    $variable = $row[mysqli_field_name($from, $x)];
                    $line = $line . "<td><a href='airport.php?iataCode=$variable'>" . $variable . "</a></td>";
                }else if(mysqli_field_name($from, $x) == "aircraft_id"){
                    $variable = $row[mysqli_field_name($from, $x)];
                    $line = $line . "<td><a href='map.php?aircraft_id=$variable'>" . $variable . "</a></td>";
                }else{
                    $line = $line . "<td>" . $row[mysqli_field_name($from, $x)] . "</td>";
                }
              }
              array_push($locs, array($row["latitude"],$row["longitude"], $row['aircraft_id'], $row['direction'], $header . $line . "</tr></table>"));
              echo utf8_encode($line)."</tr>";
        }
        echo "</table>";
   }else{
       echo "No flights have currently departed from this airport";
   }
   echo "<h1> Flights To $iataCode</h1>";
   $to = $conn->query("SELECT * FROM flight WHERE destination = '$iataCode'");
   if ($to->num_rows > 0) {
          $count = mysqli_field_count($conn);
          $header = "<table id='t01'><tr>";
          for($x = 0; $x < $count; $x++){
             $header = $header . "<th>" . mysqli_field_name($to, $x) . "</th>";
          }
          $header = $header . "</tr>";
          echo $header;
           // output data of each row
           while($row = $to->fetch_assoc()) {
              $line = "<tr>";
              for($x = 0; $x < $count; $x++){
                if(mysqli_field_name($to, $x) == "source"){
                    $variable = $row[mysqli_field_name($to, $x)];
                    $line = $line . "<td><a href='airport.php?iataCode=$variable'>" . $variable . "</a></td>";
                }else if(mysqli_field_name($to, $x) == "aircraft_id"){
                    $variable = $row[mysqli_field_name($to, $x)];
                    $line = $line . "<td><a href='map.php?aircraft_id=$variable'>" . $variable . "</a></td>";
                }else{
                    $line = $line . "<td>" . $row[mysqli_field_name($to, $x)] . "</td>";
                }
              }
            array_push($locs, array($row["latitude"],$row["longitude"], $row['aircraft_id'], $row['direction'], $header . $line . "</tr></table>"));
              echo utf8_encode($line)."</tr>";
        }
        echo "</table>";
   }else{
       echo "No flights are currently flying to this airport";
   }
   echo "</center>";

;
} else {
   echo "0 results";
}

$conn->close();
?>
    <script>
    function initMap() {
      var latitude = "<?php echo $latitude ?>";
      var longitude = "<?php echo $longitude ?>";
      // The location of flightLocation
      var flightLocation = {lat: parseFloat(latitude), lng: parseFloat(longitude)};
      // The map, centered at flightLocation
      var map = new google.maps.Map(
          document.getElementById('map'), {zoom: 4, center: flightLocation});
      // The marker, positioned at flightLocation
      
      var planeSymbol = {
        path: 'M362.985,430.724l-10.248,51.234l62.332,57.969l-3.293,26.145 l-71.345-23.599l-2.001,13.069l-2.057-13.529l-71.278,22.928l-5.762-23.984l64.097-59.271l-8.913-51.359l0.858-114.43 l-21.945-11.338l-189.358,88.76l-1.18-32.262l213.344-180.08l0.875-107.436l7.973-32.005l7.642-12.054l7.377-3.958l9.238,3.65 l6.367,14.925l7.369,30.363v106.375l211.592,182.082l-1.496,32.247l-188.479-90.61l-21.616,10.087l-0.094,115.684',
        scale: 0.05,
        strokeOpacity: 1,
        color: 'black',
        strokeWeight: 1,
        anchor: new google.maps.Point(400, 400)	
      };
      var infoWindow = new google.maps.InfoWindow(), marker, i;
      for(i = 0; i < locations.length; i++){
          if(i == 0){
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(locations[i][0],locations[i][1]),
                title: names[i],
                map: map
            });
          }else{
              planeSymbol.rotation = parseInt(headings[i]);
              marker = new google.maps.Marker({
                position: new google.maps.LatLng(locations[i][0],locations[i][1]),
                title: names[i],
                icon: planeSymbol,
                map: map
            });
          }
          
        google.maps.event.addListener(marker, 'click', (function(marker, i) {
            return function() {
                infoWindow.setContent(infoWindowContent[i]);
                infoWindow.open(map, marker);
            }
        })(marker, i));
      }
    };
    
    var names = [
        "<?php echo $iataCode?>"
    ]
    
    var headings = [
        ""
    ]
    var temp = [
        parseFloat("<?php echo $latitude; ?>"),
        parseFloat("<?php echo $longitude; ?>")
    ];
    var locations = [
        temp
    ];
    var infoWindowContent = [
        "<?php echo $iataCode; ?>"
    ];
    
    <?php foreach($locs as $key => $val){ ?>
        locations.push([parseFloat('<?php echo $val[0]; ?>'),parseFloat('<?php echo $val[1]; ?>')]);
        names.push('<?php echo $val[2]; ?>');
        headings.push('<?php echo $val[3]; ?>');
        infoWindowContent.push("<?php echo $val[4]; ?>");
    <?php } ?>
        

    </script>

    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD-dMxwYbOXRN84TnGsGhVS7xdPDbzMS54&callback=initMap">
    </script>
  </body>
</html>
<?php 
//system("START /B python flight_parser.py > temp.txt");
?>