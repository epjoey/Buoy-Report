<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Paths.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/User.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/EditLocationPage.php';



if (!isset($_GET['location']) || !$_GET['location']) {
	header('Location:'.Paths::to404());
	exit();
}

$user = new User;

if (!$user->isLoggedIn) {
	header('Location:'.Paths::toLogin(null, Paths::toEditLocation($_GET['location'])));
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