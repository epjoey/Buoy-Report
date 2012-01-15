<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Paths.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/User.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/LocationDetailPage.php';

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