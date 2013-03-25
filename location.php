<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

if (!isset($_GET['location']) || !$_GET['location']) {
	header('Location:'.Path::toLocations());
	exit();
}

$detailpage = new LocationDetailPage();
$detailpage->loadData();

if (isset($_REQUEST['submit'])) {
	$detailpage->afterSubmit();
}

$detailpage->renderPage();
?>