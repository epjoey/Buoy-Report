<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

if (!$_REQUEST['delete-location']) {
	exit;
}
Persistence::deleteLocation($_REQUEST['locationId']);
header('Location:'.Path::toLocations());

?>