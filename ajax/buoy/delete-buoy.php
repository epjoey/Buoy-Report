<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/buoy/service/BuoyService.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';

$id = $_POST['buoyid'];
$delete = $_POST['delete'];

if ($delete) {
	BuoyService::deleteBuoy($id);
}
header("Location:".Path::toBuoys());
?>