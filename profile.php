<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user/model/User.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/ProfilePage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/EditProfilePage.php';


if (!isset($_GET['reporter'])) {
	header('Location:'.Path::to404());
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