<!-- Camper Registration // Henry Quinn -->

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
	header("Location: heatmap.php?loc=$loc");
}

}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Registration</title>
 
	<!-- CSS - Bootstrap & Custom -->
	<link href="http://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.1.1/css/bootstrap.css" rel="stylesheet" media="screen">
	<link href="css/style.css" rel="stylesheet" type="text/css">

	<!-- JS - Bootstrap jQuery, Bootstrap JS, & Google Maps Geometry Library -->
	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
	<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.1.1/js/bootstrap.min.js"></script>
</head>
	
<body>
<div class="container">
	<div class="row">
		<div class="col-md-3"></div>
		<div id="outer" class="col-md-6">
			<div id="nudge" class="col-md-1"></div>
			<div id="camper_info" class="col-md-10">
				<h2 align="center">Tell Us About Your Camper</h2>
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
          		</div>
			<div id="nudge" class="col-md-1"></div>
      		</div>
		<div class="col-md-3"></div>
  	</div>
</div>
</body>
</html>
