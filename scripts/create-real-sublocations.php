<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$sql = "SELECT a.*, b.locationid
		FROM sublocation a
		INNER JOIN locationsublocation b ON a.sl_id = b.sublocationid";
$result = Persistence::run($sql);
while ($row = mysqli_fetch_object($result)) {
	$sublocation = new Sublocation($row);
	$parentlocation = LocationService::getLocation($sublocation->locationid);
	$sql2 = "INSERT INTO location SET locname = '$sublocation->sl_name',
		timezone = '$parentlocation->timezone',
		creator='$parentlocation->creator'";
	Persistence::run($sql2);

	$sql3 = "SELECT id FROM location WHERE locname = '$sublocation->sl_name'";
	$result3 = Persistence::run($sql3);
	$location = new Location(mysqli_fetch_object($result3));
	$locationid = intval($location->id);
	$sql4 = "UPDATE report SET
		locationid = '$locationid'
		WHERE sublocationid = '$sublocation->sl_id'";
	Persistence::run($sql4);

	$sql5 = "UPDATE locationsublocation SET
		sublocationid = '$locationid'
		WHERE sublocationid = '$sublocation->sl_id'";
	Persistence::run($sql5);
}

