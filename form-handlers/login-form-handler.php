<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/User.php';

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


$userId = Persistence::returnUserId($_POST['login-username'], md5($_POST['login-password'] . 'reportdb'));

if (!isset($userId)) {
	$error = 2;	
	header('Location:'.Path::toLogin($error));
	exit();		
}

$user = new User;	
$user->logInUser($userId, NULL, $newCookie = TRUE);
	
if (isset($_POST['login-rel']) && $_POST['login-rel']) {
	header('Location:'.$_POST['login-rel']);
	exit();	
} 

header('Location:'.Path::toUserHome());	

		
?>