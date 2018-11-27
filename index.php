<?xml version="1.0" encoding="UTF-8"?>
<?php
header("Content-Type: text/html;charset=UTF-8");
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "flighttrackinator3000";
?>
<head>
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
   </style>
</head>
<body>
<form method = "post" action = "<?php $_PHP_SELF ?>">
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
            <input name = "Submit" type = "submit" id = "Submit" 
               value = "Submit">
         </td>
      </tr>
   
   </table>
</form>
<?php
function mysqli_field_name($result, $field_offset)
{
    $properties = mysqli_fetch_field_direct($result, $field_offset);
    return is_object($properties) ? $properties->name : null;
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
if(isset($_POST['Submit'])) {
   $table_name = $_POST['table_name'];
   $sql = "SELECT * FROM $table_name";
   $result = $conn->query($sql);
   if ($result->num_rows > 0) {
      echo "<center><h1>" . $table_name . "</h1>";
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
       }
       echo "</table></center>";
   } else {
       echo "0 results";
   }
   $conn->close();
}
   ?>
</body>

