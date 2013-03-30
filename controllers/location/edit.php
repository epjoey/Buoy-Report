<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';
$location = LocationService::getLocation($_POST['locationId']);
$user = UserService::getUser();

//empty or same name
if (!$_POST['locname']) {
	$error = 1;
	header('Location:'.Path::toEditLocation($location->id, $error));
	exit();				
}

//empty or same name
if (!$_POST['timezone']) {
	$error = 2;
	header('Location:'.Path::toEditLocation($location->id, $error));
	exit();				
}			

//duplicate name
if ($_POST['locname'] != $location->locname && Persistence::dbContainsLocation($_POST['locname'])) {
	$error = 3;
	header('Location:'.Path::toEditLocation($location->id, $error));
	exit();		
}


//first handle deleting an image, then handle uploading a new one
if (isset($_POST['delete-image']) && $_POST['delete-image'] == 'true') {
	$location->coverImagePath = '';
}

if (isset($_FILES['upload']['tmp_name']) && $_FILES['upload']['tmp_name'] !='') {
	$location->coverImagePath = handleFileUpload($_FILES['upload'], $user->id);

/* in case they used picup, its a remote url */	
} else if (isset($_POST['remoteImageURL']) && $_POST['remoteImageURL'] !='') {
	$location->coverImagePath = rawurldecode($_POST['remoteImageURL']);
}

$location->locname = $_POST['locname'];
$location->timezone = $_POST['timezone'];

LocationService::updateLocation($location);
header('Location:'.Path::toEditLocation($location->id));
exit();

?>