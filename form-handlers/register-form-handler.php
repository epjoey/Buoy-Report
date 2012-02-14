<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Paths.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/User.php';
		
/* --------------- HANDLE SIGNUP FORM SUBMISSION --------------- */

//first check for bot
if (isset($_POST['bot-check']) && $_POST['bot-check'] != '') {
	$error = 6;
}

else if (
	!isset($_POST['reg-name']) 
	|| $_POST['reg-name'] == '' 
	|| !isset($_POST['reg-email']) 
	|| $_POST['reg-email'] == ''
	|| !isset($_POST['reg-password']) 
	|| $_POST['reg-password'] == '') 
{
	$error = 1;
}

else if (filter_var($_POST['reg-email'], FILTER_VALIDATE_EMAIL) != TRUE) {
	$error = 2;
}

else if (strlen($_POST['reg-password']) < 5) {
	$error = 5;
}
 		
else if (Persistence::databaseContainsEmail($_POST['reg-email'])) {
	$error = 3;
}

else if (Persistence::databaseContainsName($_POST['reg-name'])) {
	$error = 4;
}

if (isset($error)) {
	header('Location:'.Paths::toRegister($error));
	exit();
	
} else {		
	$reporterId = Persistence::insertReporter(
		$_POST['reg-name'], 
		$_POST['reg-email'], 
		md5($_POST['reg-password'] . 'reportdb'), 
		$_POST['report-status']
	);
	$user = new User;
	$user->logInUser($reporterId, NULL, $newCookie = TRUE, $fromRegistration = TRUE);
	header('Location:'.Paths::toUserHome());
	exit();
}


?>