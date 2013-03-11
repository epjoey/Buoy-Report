<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/location/persistence/LocationPersistence.php';
$result = Persistence::run("SELECT * FROM location");
while ($row = mysqli_fetch_object($result)) {	
	$locations[] = new Location($row);
}
foreach($locations as $location) {
	$b1 = $location->buoy1;
	$b2 = $location->buoy2;
	$b3 = $location->buoy3;
	$lid = $location->id;
	print $lid . '<br>';
	if ($location->buoy1) {
		Persistence::run("INSERT INTO buoy_location (`buoyid`, `locationid`) VALUES ($location->buoy1, $lid)");
	}
	if ($location->buoy2) {
		Persistence::run("INSERT INTO buoy_location (`buoyid`, `locationid`) VALUES ($location->buoy2, $lid)");	
	}
	if ($location->buoy3) {
		Persistence::run("INSERT INTO buoy_location (`buoyid`, `locationid`) VALUES ($location->buoy3, $lid)");	
	}
}
?>