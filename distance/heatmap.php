<?php
// Declare Variables With Default Values
$addressErr = "";
$address = "298 E Haddam Moodus Road Moodus CT";

if(isset($_GET['loc'])){
        $address = $_GET['loc'];
}

// Block - Set Up Variables For Page Load
if ($_SERVER["REQUEST_METHOD"] == "POST") {
   if (empty($_POST["address"])) {
     $addressErr = "Address Is Required";
   } else {
     $address = test_input($_POST["address"]);
   }
}

function test_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}
// End Block

$address_calc = str_replace(" ","+",$address);

#echo $address_calc;

function curl_request($sURL,$sQueryString=null)
{
$cURL=curl_init();
curl_setopt($cURL,CURLOPT_URL,$sURL.'?'.$sQueryString);
curl_setopt($cURL,CURLOPT_RETURNTRANSFER, TRUE);
$cResponse=trim(curl_exec($cURL));
curl_close($cURL);
return $cResponse;
}

$sResponse=curl_request('http://maps.googleapis.com/maps/api/distancematrix/json',
'origins='.$address_calc.'&destinations=298+N+Moodus+Rd+Moodus+CT&mode=driving&units=imperial&sensor=false');
$oJSON=json_decode($sResponse);
if ($oJSON->status=='OK')
$fDistanceInMiles=(float)preg_replace('/[^\d\.]/','',$oJSON->rows[0]->elements[0]->distance->text);
else
$fDistanceInMiles=0;

// Code Block For Finding Distance As The Crow Flies
$url = "http://maps.google.com/maps/api/geocode/json?address=$address_calc&sensor=false";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
$response = curl_exec($ch);
curl_close($ch);
$response_a = json_decode($response);


$lat = $response_a->results[0]->geometry->location->lat;
$long = $response_a->results[0]->geometry->location->lng;

$DistanceInMeters = ($fDistanceInMiles * 1609);
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
	// Create LatLng Objects - Camp & Input
	var camp_location = new google.maps.LatLng(41.5346081, -72.4580859);
	var your_location = new google.maps.LatLng(<?php echo $lat?>, <?php echo $long?>);

	// Find Distance - Meters
	var distance = (google.maps.geometry.spherical.computeDistanceBetween(camp_location, your_location));

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
                                <h2>How Far Are You From Camp?</h2>
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                                        <div class="form=group">
                                                <label for="address">Address</label>
                                                <input type="text" class="form-control" name="address" id="address" placeholder="<?php echo $address;?>">
                                                <span class="error"><?php echo $addressErr;?></span>
                                        </div>
                                        <br />
                                        <button type="submit" class="btn btn-default">Submit</button>
                                </form>
				<br>
				<h4>Coordinates Of Your Address:</h4>
				<p>Lat: <?php echo $lat;?></p>
				<p>Lng: <?php echo $long;?></p><br/>
				<h4>Driving Distance</h4>
				<p>Miles: <?php echo $fDistanceInMiles;?></p>
				<p>Meters: <?php echo $DistanceInMeters;?></p><br/>
				<a class="btn btn-default" href="registration.php">Add A Camper</a>
			</div>
                <div id="map-container" class="col-md-8"></div>
        </div><!-- /map-outer -->
</div> <!-- /row -->
</div><!-- /container -->

</body>
</html>
