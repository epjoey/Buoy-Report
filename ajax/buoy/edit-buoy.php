<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/buoy/service/BuoyService.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';

$id = $_POST['buoyid'];
$name = $_POST['buoyname'];

BuoyService::editBuoy($id, $name);
header("Location:".Path::toBuoys());
?>