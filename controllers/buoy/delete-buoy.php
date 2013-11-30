<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$id = $_POST['buoyid'];
$delete = $_POST['delete'];

if ($delete) {
	BuoyService::deleteBuoy($id);
}
header("Location:".Path::toBuoys());
?>