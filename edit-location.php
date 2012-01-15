<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/utility/Paths.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/model/User.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/pages/EditLocationPage.php';



$user = new User;
if (!$user->userIsLoggedIn()) {
	header('Location:'.Paths::toLogin());
	exit();
}

if (!isset($_GET['location']) || !$_GET['location']) {
	header('Location:'.Paths::to404());
	exit();
}

$editlocpage = new EditLocationPage();
if (isset($_POST['submit'])) {
	$editlocpage->loadData();
	$editlocpage->afterSubmit();
	exit();
}
$editlocpage->loadData();
$editlocpage->renderPage();


?>