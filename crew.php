<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

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