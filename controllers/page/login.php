<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$user = UserService::getUser();

if ($user->isLoggedIn) {
	header('Location:'.Path::toReports($user->id));
	exit();
}

$error = StatusMessageService::getStatusMsgForAction('login');
StatusMessageService::clearStatusForAction('login');

	
$loginRel = null;
if (isset($_GET['rel']) && $_GET['rel'] != '') {
	$loginRel = $_GET['rel'];
}	

$page = new LoginPage();
$page->renderPage(array(
	'pageTitle' => 'Log In',
	'user' => $user,
	'loginError' => $error,
	'loginRel' => $loginRel
));

?>