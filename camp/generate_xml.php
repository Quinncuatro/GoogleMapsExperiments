<?php

require("config/auth.php");

function parseToXML($htmlStr)
{
$xmlStr=str_replace('<','&lt;',$htmlStr);
$xmlStr=str_replace('>','&gt;',$xmlStr);
$xmlStr=str_replace('"','&quot;',$xmlStr);
$xmlStr=str_replace("'",'&#39;',$xmlStr);
$xmlStr=str_replace("&",'&amp;',$xmlStr);
return $xmlStr;
}

// Opens a connection to a MySQL server
$connection=mysql_connect ('localhost', $username, $password);
if (!$connection) {
  die('Not connected : ' . mysql_error());
}

// Set the active MySQL database
$db_selected = mysql_select_db($database, $connection);
if (!$db_selected) {
  die ('Can\'t use db : ' . mysql_error());
}

// Select all the rows in the markers table
$query = "SELECT cm_name,cm_address,cm_city,cm_state,cm_distance,cm_lat,cm_lng FROM campers";
$result = mysql_query($query);
if (!$result) {
  die('Invalid query: ' . mysql_error());
}

header("Content-type: text/xml");

// Start XML file, echo parent node
echo '<markers>';

// Iterate through the rows, printing XML nodes for each
while ($row = @mysql_fetch_assoc($result)){
  // ADD TO XML DOCUMENT NODE
  echo '<marker ';
  echo 'name="' . parseToXML($row['cm_name']) . '" ';
  echo 'address="' . parseToXML($row['cm_address']) . '" ';
  echo 'city="' . parseToXML($row['cm_city']) . '" ';
  echo 'state="' . parseToXML($row['cm_state']) . '" ';
  echo 'distance="' . parseToXML($row['cm_distance']) . '" ';
  echo 'lat="' . $row['cm_lat'] . '" ';
  echo 'lng="' . $row['cm_lng'] . '" ';
  echo '/>';
}

// End XML file
echo '</markers>';

?>

