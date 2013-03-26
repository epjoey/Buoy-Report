<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';


if (!isset($_GET['location']) || !$_GET['location']) {
	header("HTTP/1.0 404 Not Found");
	include_once $_SERVER['DOCUMENT_ROOT'] . Path::to404();
	exit();
}

$user = UserService::getUser();

if (!$user->isLoggedIn) {
	header('Location:'.Path::toLogin(null, Path::toEditLocation($_GET['location'])));
	exit();
}

$editlocpage = new EditLocationPage();
if (isset($_REQUEST['submit'])) {
	$editlocpage->loadData();
	$editlocpage->afterSubmit();
	exit();
}
$editlocpage->loadData();
$editlocpage->renderPage();


?>