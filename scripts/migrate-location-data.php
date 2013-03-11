<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/location/persistence/LocationPersistence.php';
$result = Persistence::run("SELECT * FROM location");
while ($row = mysqli_fetch_object($result)) {	
	$locations[] = new Location($row);
}
foreach($locations as $location) {
	$ts = $location->tidestation;
	print $ts . '<br>';
	$lid = $location->id;
	print $lid . '<br>';
	$sql = "INSERT INTO tidestation_location ($ts, $lid)";
	print $sql . '<br>';
	try { 
		$result = Persistence::run("INSERT INTO tidestation_location (`tidestationid`, `locationid`) VALUES ($ts, $lid)");
	} catch (Exception $e) {
		print $e->getMessage();
	}
}
?>