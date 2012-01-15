<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/utility/Paths.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/model/User.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/pages/LocationDetailPage.php';

if (!isset($_GET['location']) || !$_GET['location']) {
	header('Location:'.Paths::toLocations());
	exit();
}

$detailpage = new LocationDetailPage();
$detailpage->loadData();

if (isset($_REQUEST['submit'])) {
	$detailpage->afterSubmit();
}

$detailpage->renderPage();
?>