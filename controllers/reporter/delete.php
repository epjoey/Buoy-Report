<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$user = UserService::getUser();
$reporterId = $_POST['reporterid'];

//a user can only delete him/herself
if ($user->id !== $reporterId) {
  exit;
}

$reporter = ReporterService::getReporter($reporterId);

if (!$reporter || $_POST['submit'] != 'delete-reporter') {
  StatusMessageService::setStatusMsgForAction('An error occured', 'edit-account');
  header('Location:'.Path::toProfile($reporterId));
  exit(); 
}

ReporterService::deleteReporter($reporter);

//if user deleted him/herself, log them out
if ($user->id == $reporterId) {
  header('Location:'.Path::toLogout());
}