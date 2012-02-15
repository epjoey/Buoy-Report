<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/User.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/ReportFormPage.php';


$user = new User;

if (!isset($_GET['location'])) {
	header('Location:'.Path::toLocations($reporter = null, $toPost = TRUE));
	exit();	
}

if (!$user->isLoggedIn) {
	header('Location:'.Path::toLogin(null, Path::toPostReport($_GET['location'])));
	exit();
}

$reportform = new ReportFormPage;


	
$reportform->loadData();
$reportform->renderPage();
?>