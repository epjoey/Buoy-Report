<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/User.php';
		
/* --------------- HANDLE SIGNUP FORM SUBMISSION --------------- */

//first check for bot
if (isset($_POST['bot-check']) && $_POST['bot-check'] != '') {
	$error = 6;
	header('Location:'.Path::toRegister($error));
	exit();	
}

if (
	!isset($_POST['reg-name']) 
	|| $_POST['reg-name'] == '' 
	//|| !isset($_POST['reg-email']) 
	//|| $_POST['reg-email'] == ''
	|| !isset($_POST['reg-password']) 
	|| $_POST['reg-password'] == '') 
{
	$error = 1;
	header('Location:'.Path::toRegister($error));
	exit();	
}
/*
if (filter_var($_POST['reg-email'], FILTER_VALIDATE_EMAIL) != TRUE) {
	$error = 2;
	header('Location:'.Path::toRegister($error));
	exit();	
}
*/
if (strlen($_POST['reg-password']) < 5) {
	$error = 5;
	header('Location:'.Path::toRegister($error));
	exit();	
}
/*	
if (Persistence::databaseContainsEmail($_POST['reg-email'])) {
	$error = 3;
	header('Location:'.Path::toRegister($error));
	exit();	
}
*/
if (Persistence::databaseContainsName($_POST['reg-name'])) {
	$error = 4;
	header('Location:'.Path::toRegister($error));
	exit();	
}

			
$reporterId = Persistence::insertUser(
	$_POST['reg-name'], 
	//$_POST['reg-email'], 
	md5($_POST['reg-password'] . 'reportdb'), 
	$_POST['report-status']
);

User::logInUser($reporterId, NULL, $newCookie = TRUE, $fromRegistration = TRUE);
header('Location:'.Path::toUserHome());
exit();



?>