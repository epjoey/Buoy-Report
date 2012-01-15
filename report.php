<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/utility/Paths.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/model/User.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/pages/ReportFormPage.php';


$user = new User;
if (!$user->userIsLoggedIn()) {
	header('Location:'.Paths::toLogin());
	exit();
}

$reportform = new ReportFormPage;

if (!isset($_GET['location'])) {
	header('Location:'.Paths::toLocations($reporter = null, $toPost = TRUE));
	exit();	
}
	
$reportform->loadData();
$reportform->renderPage();
?>