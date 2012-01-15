<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/utility/Paths.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/model/User.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/pages/RegisterPage.php';

$user = new User;

if ($user->userIsLoggedIn()) {
	header('Location:'.Paths::toUserHome());
	exit();
}

if (isset($_POST['submit']) && $_POST['submit'] == 'register') {
	$user->handleRegFormSubmission();
}

$form = new RegisterPage;
$form->loadData();
$form->renderPage();

?>