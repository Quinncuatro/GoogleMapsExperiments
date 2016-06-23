<!DOCTYPE html>
<html>
<head>
	<title>Camp Address Hotspots</title>
 
	<!-- CSS - Bootstrap & Custom -->
	<link href="http://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.1.1/css/bootstrap.css" rel="stylesheet" media="screen">
	<link href="css/style.css" rel="stylesheet" type="text/css">

	<!-- JS - Bootstrap jQuery, Bootstrap JS, & Google Maps Geometry Library -->
	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
	<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.1.1/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false&libraries=geometry"></script>
</head>
	
<body>

<?php
// Declare Variables With Default Values
$addressErr = "";
$address = "298 E Haddam Moodus Road Moodus CT";

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
?>

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
<?php
echo "<br>";

$address = str_replace(" ","+",$address);

#echo $address;

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
    'origins='.$address.'&destinations=298+N+Moodus+Rd+Moodus+CT&mode=driving&units=imperial&sensor=false');
$oJSON=json_decode($sResponse);
if ($oJSON->status=='OK')
        $fDistanceInMiles=(float)preg_replace('/[^\d\.]/','',$oJSON->rows[0]->elements[0]->distance->text);
else
        $fDistanceInMiles=0;

// Code Block For Finding Distance As The Crow Flies
$url = "http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
$response = curl_exec($ch);
curl_close($ch);
$response_a = json_decode($response);
echo "Coordinates Of Your Address:";
echo "<br />";
echo "Lat: ".$lat = $response_a->results[0]->geometry->location->lat;
echo "<br />";
echo "Lng: ".$long = $response_a->results[0]->geometry->location->lng;
echo "<br><br>";
// End Of Code Block

$DistanceInMeters = ($fDistanceInMiles * 1609);

echo 'Driving Distance:';
echo '<br>';
echo 'Miles: '.$fDistanceInMiles;
echo '<br>';
echo 'Meters: :'.$DistanceInMeters;
?>
				
          		</div>
        		<div id="map-container" class="col-md-8"></div>
      		</div><!-- /map-outer -->
  	</div> <!-- /row -->
</div><!-- /container --> 

	<script type="text/javascript" src="include/Map.js"></script>
    <script>	
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
	
	function init_map() {
		// Create the map.
		var map = new google.maps.Map(document.getElementById('map-container'), {
			zoom: 8,
			center: {lat: 41.5346081, lng: -72.4580859},
			mapTypeId: google.maps.MapTypeId.ROADMAP,
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

		// Block To Create Circle	
		for (var camp in campmap) {
			var campCircle = new google.maps.Circle({
				strokeColor: '#219534',
				strokeOpacity: 0.8,
				strokeWeight: 2,
				fillColor: '#219534',
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
	};
      
	// Load Map On Page Load
	google.maps.event.addDomListener(window, 'load', init_map);
    </script>

</body>
</html>
