<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$user = UserService::getUser();

$reporterId = $_POST['reporterid'];
$reporter = ReporterService::getReporter($reporterId);

//a user can only edit him/herself
if ($user->id !== $reporterId || !$reporter) {
	throw new InvalidSubmissionException();
}


$properties = array();


if (!empty($_POST['new-email']) && $_POST['new-email'] != $reporter->email) {
	if (filter_var($_POST['new-email'], FILTER_VALIDATE_EMAIL) != TRUE ) {
		StatusMessageService::setStatusMsgForAction('You must enter a valid email address', 'edit-account');
		header('Location:'.Path::toProfile($reporter->id));
		exit();
	}

	$properties['email'] = $_POST['new-email'];
}

if (!empty($_POST['new-name']) && $reporter->name != $_POST['new-name']) {
	if (Persistence::databaseContainsName($_POST['new-name'])) {
		StatusMessageService::setStatusMsgForAction('That username is already taken', 'edit-account');
		header('Location:'.Path::toProfile($reporter->id));
		exit();	
	}			
	$properties['name'] = $_POST['new-name'];	
} 

if (isset($_POST['report-status']) && $reporter->public != $_POST['report-status']) {
	if ($_POST['report-status'] == '0') {
		Persistence::makeAllUserReportsPrivate($reporter->id);
	} else if ($_POST['report-status'] == '1') {
		Persistence::makeAllUserReportsPublic($reporter->id);
	}

	$properties['public'] = $_POST['report-status'];				
} 	

if (!empty($_POST['new-password'])) {
	if (strlen($_POST['new-password']) < 5) {
		StatusMessageService::setStatusMsgForAction('Password must be more than 5 characters', 'edit-account');
		header('Location:'.Path::toProfile($reporter->id));
		exit();       
	}

	$properties['password'] = $_POST['new-password'];
}	

if (!$properties) {
	StatusMessageService::setStatusMsgForAction('No changes specified', 'edit-account');
	header('Location:'.Path::toProfile($reporter->id));
	exit();	
}

try {
	ReporterService::updateReporter($reporter, $properties);
} catch (InvalidSubmissionException $e) {
	StatusMessageService::setStatusMsgForAction($e->getMessage(), 'edit-account');	
	header('Location:'.Path::toProfile($reporter->id));
	exit();		
}

StatusMessageService::setStatusMsgForAction('Your changes have been made', 'edit-account');
header('Location:'.Path::toProfile($reporter->id));
exit();		
