<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$user = UserService::getUser();

if ($user->isLoggedIn) {
	header('Location:'.Path::toUserHome());
	exit();
}

$error = null;
if (isset($_GET['error']) && $_GET['error']) {
	switch($_GET['error']) {
		case 1: $error = 'Please fill in both fields'; break;
		case 2: $error = 'The specified username or password was incorrect.';
	}
}		
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