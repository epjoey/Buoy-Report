<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

if (!$_GET['reporter']) {
	header("HTTP/1.0 404 Not Found");
	include_once $_SERVER['DOCUMENT_ROOT'] . Path::to404();
	exit();	
}

$user = new User;

if ($user->isLoggedIn && $_GET['reporter'] == $user->id) {
	
	$editprofile = new EditProfilePage;
	$editprofile->loadData();
	if (isset($_POST['submit'])) {
		$editprofile->afterSubmit();
		exit();
	}	
	$editprofile->renderPage();

} else {
	
	$profile = new ProfilePage;
	$profile->loadData();
	$profile->renderPage(); 
}



?>