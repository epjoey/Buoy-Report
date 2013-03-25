<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

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