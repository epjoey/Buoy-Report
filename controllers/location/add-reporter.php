<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$user = UserService::getUser();
if (!$user->isLoggedIn) {
	exit();
}
$locationId = $_REQUEST['locationId'];


if ($_REQUEST['bookmark']) {
	ReporterService::reporterAddLocation($user->id, $locationId);

} elseif ($_REQUEST['unbookmark']) {
	Persistence::removeLocationFromUser($locationId, $user->id);
}

header('Location:'.Path::toLocation($locationId));
?>