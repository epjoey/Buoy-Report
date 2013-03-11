<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user/model/User.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/CrewPage.php';

if (!isset($_GET['id']) || !$_GET['id']) {
	header('Location:'.Path::toSubmitCrew()); //make this go to list of crews
	exit();
}

$crewPage = new CrewPage();
$crewPage->loadData();

if (isset($_REQUEST['submit'])) {
	$crewPage->afterSubmit();
}
$crewPage->renderPage();
?>