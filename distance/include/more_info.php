<?php

// Make address info ready for geocoding
$geo_address = str_replace(" ","+",$Address);
$geo_city = str_replace(" ","+",$City);
$geo_state = str_replace(" ","+",$State);

// Geocode - Tranlate address to lat/lng
$geocode=file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$geo_address.'+'.$geo_city.'+'.$geo_state.'&sensor=false');
$output = json_decode($geocode);

if($output->status == 'OK'){
$lat = $output->results[0]->geometry->location->lat;
$lng = $output->results[0]->geometry->location->lng;
$loc = $output->results[0]->formatted_address;
}

// Calculate distance - As the crow flies
function distance($yourlat, $yourlng) {
	$camplat = 41.488927;
	$camplng = -72.459270;
	$theta = $yourlng - $camplng;
	$dist = sin(deg2rad($yourlat)) * sin(deg2rad($camplat)) + cos(deg2rad($yourlat)) * cos(deg2rad($camplat)) * cos(deg2rad($theta));
	$dist = acos($dist);
	$dist = rad2deg($dist);
	$miles = $dist * 60 * 1.1515;
	return $miles;
}

$distance = distance($lat, $lng);

?>
