<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

/* --------------- HANDLE LOGIN FORM SUBMISSION --------------- */

if (
	//!isset($_POST['login-email']) 
	//|| $_POST['login-email'] == '' 
	!isset($_POST['login-username']) 
	|| $_POST['login-username'] == '' 
	|| !isset($_POST['login-password']) 
	|| $_POST['login-password'] == '') 
{
	$error = 1;
	header('Location:'.Path::toLogin($error));
	exit();		
} 


$userId = Persistence::returnUserId($_POST['login-username'], $_POST['login-password']);

if (!isset($userId)) {
	$error = 2;	
	header('Location:'.Path::toLogin($error));
	exit();		
}

User::logInUser($userId, NULL);
	
if (isset($_POST['login-rel']) && $_POST['login-rel']) {
	header('Location:'.$_POST['login-rel']);
	exit();	
} 

header('Location:'.Path::toUserHome());	

		
?>