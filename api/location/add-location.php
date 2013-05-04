<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$user = UserService::getUser();
if (!$user->isLoggedIn) {
	exit();
}
if (empty($_POST['locationname'])) {
	$error = 1;
	header('Location:'.Path::toSubmitLocation($error));
	exit();
}

if (Persistence::dbContainsLocation($_POST['locationname'])) {
	$error = 2;
	header('Location:'.Path::toSubmitLocation($error));
	exit();		
}

//success
if (!empty($_POST['timezone'])) {
	$timezone = $_POST['timezone'];
} else {
	$timezone = 'UTC';
}

$newLocationId = Persistence::insertLocation($_POST['locationname'], $timezone, $user->id);
header('Location:'.Path::toLocation($newLocationId));
?>