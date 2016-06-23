<?php

// Instantiate variable and error message values
$Name = $Address = $City = $State = $Zip = "";

$NameErr = $AddressErr = $CityErr = $StateErr = $ZipErr = "";

$NameCheck = $AddressCheck = $CityCheck = $StateCheck = $ZipCheck = "";

$RegExCount = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

// Input sanitization
function test_input($input) {
        $input = trim($input);
        $input = stripslashes($input);
        $input = htmlspecialchars($input);
        return $input;
}

  $NameCheck = test_input($_POST["name"]);
  if (!preg_match("/^[a-zA-Z ]{1,255}$/",$NameCheck)) {
          $NameErr = "Only letters and white space allowed.";
  } else {
          $Name = $NameCheck;
          $RegExCount++;
  }

  $AddressCheck = test_input($_POST["address"]);
  echo "Address: ".$AddressCheck;
  if (!preg_match("/^[a-zA-Z0-9 \-\.\,]{1,255}$/",$AddressCheck)) {
          $AddressErr = "Only letters, numbers, white space and the following punctuation allowed: . , -";
  } else {
          $Address = $AddressCheck;
          $RegExCount++;
  }

  $CityCheck = test_input($_POST["city"]);
  if (!preg_match("/^[a-zA-Z ]{1,255}$/",$CityCheck)) {
        $CityErr = "Only letters and white space allowed.";
  } else {
          $City = $CityCheck;
          $RegExCount++;
  }

  $StateCheck = test_input($_POST["state"]);
  if (!preg_match("/^[a-zA-Z]{2}$/",$StateCheck)) {
        $StateErr = "Only two-character state codes allowed.";
  } else {
          $State = $StateCheck;
          $RegExCount++;
  }

  $ZipCheck = test_input($_POST["zip"]);
  if (!preg_match("/^\d{5}(-\d{4})?$/",$ZipCheck)) {
        $ZipErr = "Up to ten characters are allowed (numbers and dashes). Ex: 06510 or 06510-2004";
  } else {
          $Zip = $ZipCheck;
          $RegExCount++;
  }

if($RegExCount == 5){
        require 'include/more_info.php';
        require 'config/pdo_insert.php';
}
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
	<title>Camp Address Hotspots</title>

        <!-- CSS - Bootstrap & Custom -->
        <link href="http://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.1.1/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="css/style.css" rel="stylesheet" type="text/css">

        <!-- JS - Bootstrap jQuery, Bootstrap JS, & Google Maps Geometry Library -->
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
        <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.1.1/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false&libraries=geometry"></script>
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA1TqhYcVTRtZ8paLBmDUBJu9lSXx7iNoI&libraries=visualization"></script>

	<script type="text/javascript">
	// Array Of Locations - Camp
	var campmap = {
        	camp: {
                        center: {lat: 41.5346081, lng: -72.4580859},
                },
        };

	var heatpoints = [];

	//<![CDATA[

        function load() {
                // Create the map.
                var map = new google.maps.Map(document.getElementById("map-container"), {
                        zoom: 8,
                        center: {lat: 41.5346081, lng: -72.4580859},
                        mapTypeId: 'roadmap',
			mapTypeControl: false,
			streetViewControl: false,
			styles: [
					{
						featureType: "road.local",
						stylers: [
							{ visibility: "off" }
						]
					},
			],
                });
		
		var heatpoints = [];

		downloadUrl("generate_xml.php", function(data) {
                        var xml = data.responseXML;
                        var markers = xml.documentElement.getElementsByTagName("marker");
                        for (var i = 0; i < markers.length; i++) {
                                lat = parseFloat(markers[i].getAttribute("lat"));
                                lng = parseFloat(markers[i].getAttribute("lng"));

                                heatpoints.push(new google.maps.LatLng(lat, lng));
                        }
			return heatpoints;
                });


		var heatmap = new google.maps.visualization.HeatmapLayer({
                        data: heatpoints,
                        map: map
                });		

		/*// Block To Create Circle
                for (var camp in campmap) {
                        var campCircle = new google.maps.Circle({
                                strokeColor: '#219534',
                                strokeOpacity: 0.8,
                                strokeWeight: 2,
                                fillOpacity: 0.1,
                                map: map,
                                center: campmap[camp].center,
                                radius: distance,
                        });
                };

                // Set Up Markers - Camp & Input
                var var_marker = new google.maps.Marker({
                        position: camp_location,
                        map: map,
                        title:"Middlesex County Camp"});
                        // Use the MCC logo to mark camp on the map

                var var_marker_2 = new google.maps.Marker({
			position: your_location,
                        map: map,
                        title: "Your Address"});
                        // Maybe custom markers for houses

                // Set Markers
                var_marker.setMap(map);
                var_marker_2.setMap(map);

                // Zoom Map To Fit Circle
                map.fitBounds(campCircle.getBounds());

		distance_in_miles = (distance / 1609.344);*/
        }

	function getPoints() {
		downloadUrl("generate_xml.php", function(data) {
                        var xml = data.responseXML;
                        var markers = xml.documentElement.getElementsByTagName("marker");
                        for (var i = 0; i < markers.length; i++) {
                        	lat = parseFloat(markers[i].getAttribute("lat"));
				lng = parseFloat(markers[i].getAttribute("lng"));

				heatpoints.push(new google.maps.LatLng(lat, lng));
			}
                });
		return heatpoints;
	}

        function downloadUrl(url, callback) {
                var request = window.ActiveXObject ?
                        new ActiveXObject('Microsoft.XMLHTTP') :
                        new XMLHttpRequest;

                request.onreadystatechange = function() {
                        if (request.readyState == 4) {
                                request.onreadystatechange = doNothing;
                                callback(request, request.status);
                        }
                };

                request.open('GET', url, true);
                request.send(null);
        }

        function doNothing() {}

	//]]>
	</script>
</head>
	
<body onload="load()">

<div class="container">
        <div class="row">
                <div id="outer" class="col-md-12">
                        <div id="address" class="col-md-4">
				<h2>Tell Us About Your Camper</h2>
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                                        <div class="form=group">
                                                <label for="name">Name</label>
                                                <input type="text" class="form-control" name="name" id="name" maxlength="255" value="<?php echo htmlspecialchars($NameCheck);?>" required><span class="error"><?php echo $NameErr;?></span>
                                        </div>
                                        <div class="form=group">
                                                <label for="address">Address</label>
                                                <input type="text" class="form-control" name="address" id="address" maxlength="255" value="<?php echo htmlspecialchars($AddressCheck);?>" required><span class="error"><?php echo $AddressErr;?></span>
                                        </div>
                                        <div class="form=group">
                                                <label for="city">city</label>
                                                <input type="text" class="form-control" name="city" id="city" maxlength="255" value="<?php echo htmlspecialchars($CityCheck);?>" required><span class="error"><?php echo $CityErr;?></span>
                                        </div>
                                        <div class="form=group">
                                                <label for="state">State</label>
                                                <input type="text" class="form-control" name="state" id="state" maxlength="2" value="<?php echo htmlspecialchars($StateCheck);?>" required><span class="error"><?php echo $StateErr;?></span>
                                        </div>
                                        <div class="form=group">
                                                <label for="zip">Zip</label>
                                                <input type="text" class="form-control" name="zip" id="zip" maxlength="5" value="<?php echo htmlspecialchars($ZipCheck);?>" required><span class="error"><?php echo $ZipErr;?></span>
                                        </div>
                                        <br />
                                        <button type="submit" class="btn btn-default">Submit</button>
                                </form>
                                <br/>
				<a class="btn btn-default" href="campermap.php">Marker Map</a><a class="btn btn-default" href="../distance/index.php">Distance Map</a>
			</div>
                <div id="map-container" class="col-md-8"></div>
        </div><!-- /map-outer -->
</div> <!-- /row -->
</div><!-- /container -->

</body>
</html>
