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

<html class="home">
<head>
  <meta charset="UTF-8">
	<title>Flight Tracker</title>
   <script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
   <script type="text/javascript" src="http://code.jquery.com/ui/1.10.1/jquery-ui.min.js"></script>    
   <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/themes/base/minified/jquery-ui.min.css" type="text/css" /> 
   <script type="text/javascript">
      $(function() 
      {
       $( "#airline" ).autocomplete({
        source: 'searchAirline.php',
        select: function (event, ui) {
           event.preventDefault();
           var data = ui.item;
          var arr = data.value.split(' - ');
          $("#airline").val(arr[1])  
         }
       });
      });
      
      $(function() 
      {
       $( "#flight_num" ).autocomplete({
        source: function(request, response) {
          $.getJSON("searchFN.php", { airline: $('#airline').val(), term: request.term }, 
                    response);
        }
       });
      });
   </script>
</head>

<body>
	<?php include_once("header.php"); ?>

	<div class="tracker-box">
		<div class="ftrack-headline">
			<h1>Track a Flight
				&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<span>✈</span></h1>
		</div>

		<div class="flight-tracker-form">
			<form id="ft-form" action="database.php">
            <input name="table_name" type="hidden" value="flight">
				<div class = "airline-form">
					<input name="airline" id="airline" type="text" placeholder="Airline Code" class="form-control">
				</div>

				<div class="flight-number-form">
					<input name="flight_num"id="flight_num" type="text" placeholder="Flight Number" class="form-control">
				</div>
            <input type="submit" value="Track Flight" name="Submit" class="ft-button">
			</form>
		</div>

	</div>

</body>
</html>


