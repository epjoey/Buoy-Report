<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';
		
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
	|| !isset($_POST['reg-email']) 
	|| $_POST['reg-email'] == ''
	|| !isset($_POST['reg-password']) 
	|| $_POST['reg-password'] == '') 
{
	$error = 1;
	header('Location:'.Path::toRegister($error));
	exit();	
}

if (filter_var($_POST['reg-email'], FILTER_VALIDATE_EMAIL) != TRUE) {
	$error = 2;
	header('Location:'.Path::toRegister($error));
	exit();	
}

if (strlen($_POST['reg-password']) < 5) {
	$error = 5;
	header('Location:'.Path::toRegister($error));
	exit();	
}

if (Persistence::databaseContainsEmail($_POST['reg-email'])) {
	$error = 3;
	header('Location:'.Path::toRegister($error));
	exit();	
}

if (Persistence::databaseContainsName($_POST['reg-name'])) {
	$error = 4;
	header('Location:'.Path::toRegister($error));
	exit();	
}

			
$reporter = ReporterService::createReporter(
	$_POST['reg-name'], 
	$_POST['reg-email'], 
	$_POST['reg-password'], 
	array('reportPublicly' => $_POST['report-status'])
);

UserService::logInUser($reporter->name, $_POST['reg-password']);
header('Location:'.Path::toReports($reporter->id));
exit();



?>