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
	StatusMessageService::setStatusMsgForAction('Please fill in both fields', 'login');	
	header('Location:'.Path::toLogin());
	exit();		
} 


try {
	UserService::logInUser($_POST['login-username'], $_POST['login-password']);

} catch (InvalidSubmissionException $e) {
	StatusMessageService::setStatusMsgForAction($e->getMessage(), 'login');	
	header('Location:'.Path::toLogin());
	exit();		
}
	
if (isset($_POST['login-rel']) && $_POST['login-rel']) {
	header('Location:'.$_POST['login-rel']);
	exit();	
} 

header('Location:'.Path::toUserHome());	

		
?>