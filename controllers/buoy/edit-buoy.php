<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$id = $_POST['buoyid'];
$name = $_POST['buoyname'];

BuoyService::editBuoy($id, $name);
header("Location:".Path::toBuoys());
?>