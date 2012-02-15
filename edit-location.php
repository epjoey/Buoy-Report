<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/User.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/EditLocationPage.php';



if (!isset($_GET['location']) || !$_GET['location']) {
	header('Location:'.Path::to404());
	exit();
}

$user = new User;

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