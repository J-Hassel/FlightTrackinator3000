<?php
   // Include config file
   require_once "config.php";
   ini_set('max_execution_time', 300);
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_URL, 'http://aviation-edge.com/v2/public/flights?key=92f3ea-e1bdf9');
   $result = curl_exec($ch);
   curl_close($ch);

   $json = json_decode($result);
   $link->query("DELETE FROM flight");
   $link->query("ALTER TABLE flight ADD PRIMARY KEY(airline, flight_num)");
   foreach($json as $obj){
      if($obj->status == "en-route"){
         $aircraft_id = $obj->aircraft->regNumber;
         $altitude = round(intval($obj->geography->altitude) * 3.28084, 1);
         $latitude = $obj->geography->latitude;
         $longitude = $obj->geography->longitude;
         $direction = $obj->geography->direction;
         $speed = round(intval($obj->speed->horizontal) * 0.621371, 1);
         $airline = $obj->airline->iataCode;
         $flight_num = $obj->flight->number;
         $aircraft_icao = $obj->aircraft->icaoCode;
         $source = $obj->departure->iataCode;
         $destination = $obj->arrival->iataCode;

         if($airline == "" or $aircraft_icao == "" or $source == "" or $destination == "" or $altitude > 45000 or $altitude < -200 or $speed > 700 or $speed < 0)
         {
            continue;
         }
         if(preg_match("/[a-z]/i", $flight_num)){
             continue;
         }
         $sql = "INSERT INTO flight (aircraft_id, altitude, latitude, longitude, direction, speed, airline, flight_num, aircraft_icao, source, destination) VALUES ('$aircraft_id', '$altitude', '$latitude', '$longitude', '$direction', '$speed', '$airline', '$flight_num', '$aircraft_icao', '$source', '$destination')";
         $link->query($sql);
      }
      
   }
   $link->close();
?>
