<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/User.php';

/* --------------- HANDLE LOGIN FORM SUBMISSION --------------- */

if (!isset($_POST['login-email']) 
	|| $_POST['login-email'] == '' 
	|| !isset($_POST['login-password']) 
	|| $_POST['login-password'] == '') 
{
	$error = 1;
} 
else {
	$reporterId = Persistence::returnReporterId($_POST['login-email'], md5($_POST['login-password'] . 'reportdb'));
	if (!isset($reporterId)) {
		$error = 2;	
	}
}


if (isset($error)) {
	header('Location:'.Path::toLogin($error));
	exit();	
} 
else {	
	$user = new User;	
	$user->logInUser($reporterId, NULL, $newCookie = TRUE);
		
	if (isset($_POST['login-rel']) && $_POST['login-rel'] != '') {
		header('Location:'.$_POST['login-rel']);
	} 
	else {
		header('Location:'.Path::toUserHome());	
	}
}		
?>