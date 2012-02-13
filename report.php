<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Paths.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/User.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/ReportFormPage.php';


$user = new User;

if (!isset($_GET['location'])) {
	header('Location:'.Paths::toLocations($reporter = null, $toPost = TRUE));
	exit();	
}

if (!$user->userIsLoggedIn()) {
	header('Location:'.Paths::toLogin(null, Paths::toPostReport($_GET['location'])));
	exit();
}

$reportform = new ReportFormPage;


	
$reportform->loadData();
$reportform->renderPage();
?>