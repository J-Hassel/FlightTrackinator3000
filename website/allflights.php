<?xml version="1.0" encoding="utf-8"?>
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
    <title>Map</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
   <body>
   <?php include_once("header.php"); ?>
   <?php
      //header("Refresh:30");
      // header("Content-Type: text/html;charset=UTF-8");

      if ($link->connect_error) {
        die("Connection failed: " . $link->connect_error);
      }

      function mysqli_field_name($result, $field_offset)
      {
          $properties = mysqli_fetch_field_direct($result, $field_offset);
          return is_object($properties) ? $properties->name : null;
      }

      $sql = "SELECT * FROM flight";
      $result = $link->query($sql);
      ?>
         
</style>
   <style>
      /* Set the size of the div element that contains the map */
      #map {
        height: 90%;  /* The height is 90% of the web page */
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
   <!--The div element for the map -->
   <div id="map" >
   </div>
   <?php
      $locs=array();
     if ($result->num_rows > 0) {
       $count = mysqli_field_count($link);
       $header = "<table id='t01'><tr>";
       for($x = 0; $x < $count; $x++){
          $header = $header . "<th>" . mysqli_field_name($result, $x) . "</th>";
       }
       $header = $header . "</tr>";
       //echo $header;
        // output data of each row
        while($row = $result->fetch_assoc()) {
           $line = "<tr>";
           for($x = 0; $x < $count; $x++){
             if(mysqli_field_name($result, $x) == "destination"){
                 $variable = $row[mysqli_field_name($result, $x)];
                 $line = $line . "<td><a href='airport.php?iataCode=$variable'>" . $variable . "</a></td>";
             }else if(mysqli_field_name($result, $x) == "aircraft_id"){
                 $variable = $row[mysqli_field_name($result, $x)];
                 $line = $line . "<td><a href='map.php?aircraft_id=$variable'>" . $variable . "</a></td>";
             }else{
                 $line = $line . "<td>" . $row[mysqli_field_name($result, $x)] . "</td>";
             }
           }
        array_push($locs, array($row["latitude"],$row["longitude"], $row['aircraft_id'], $row['direction'], $header . $line . "</tr></table>"));
        //echo utf8_encode($line)."</tr>";
         }
      } else {
         echo "0 results";
      }
   $link->close();
   ?>
   <script>
// Initialize and add the map
function initMap() {
      var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 3,
          center: {lat: -28.024, lng: 140.887}
        });
      var planeSymbol = {
        path: 'M362.985,430.724l-10.248,51.234l62.332,57.969l-3.293,26.145 l-71.345-23.599l-2.001,13.069l-2.057-13.529l-71.278,22.928l-5.762-23.984l64.097-59.271l-8.913-51.359l0.858-114.43 l-21.945-11.338l-189.358,88.76l-1.18-32.262l213.344-180.08l0.875-107.436l7.973-32.005l7.642-12.054l7.377-3.958l9.238,3.65 l6.367,14.925l7.369,30.363v106.375l211.592,182.082l-1.496,32.247l-188.479-90.61l-21.616,10.087l-0.094,115.684',
        scale: 0.05,
        strokeOpacity: 1,
        color: 'black',
        strokeWeight: 1,
        anchor: new google.maps.Point(400, 400)	
      };
      
      var infoWindow = new google.maps.InfoWindow(), marker, i;

      var markers = locations.map(function(location, i) {
          planeSymbol.rotation = parseInt(headings[i]);
          var marker = new google.maps.Marker({
            position: new google.maps.LatLng(location[0],location[1]),
            title: names[i],
            icon: planeSymbol
          });
          
          google.maps.event.addListener(marker, 'click', (function(marker, i) {
            return function() {
                infoWindow.setContent(infoWindowContent[i]);
                infoWindow.open(map, marker);
            }
         })(marker, i));
          return marker;
      });

      var markerCluster = new MarkerClusterer(map, markers,
        {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
       
    };

    var names = [
        
    ]
    
    var headings = [
        
    ]

    var locations = [
        
    ]
    var infoWindowContent = [

    ]
    
    <?php foreach($locs as $key => $val){ ?>
        locations.push([parseFloat('<?php echo $val[0]; ?>'),parseFloat('<?php echo $val[1]; ?>')]);
        names.push('<?php echo $val[2]; ?>');
        headings.push('<?php echo $val[3]; ?>');
        infoWindowContent.push("<?php echo $val[4]; ?>");
    <?php } ?>
   </script> 
   <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js">
   </script>
   
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD-dMxwYbOXRN84TnGsGhVS7xdPDbzMS54&callback=initMap">
    </script>
</body>
</html>
