<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Paths.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/User.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/ProfilePage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/EditProfilePage.php';


if (!isset($_GET['reporter'])) {
	header('Location:'.Paths::to404());
	exit();	
}

$profile = new ProfilePage;
$profile->loadData();


if ($profile->userIsLoggedIn && $_GET['reporter'] == $profile->userId) {
	$editprofile = new EditProfilePage;
	$editprofile->loadData();

	if (isset($_POST['submit'])) {
		$editprofile->afterSubmit();
		exit();
	}	

	$editprofile->renderPage();
	exit();
}

if (isset($_POST['submit'])) {
	$profile->afterSubmit();
	exit(); 		
}

$profile->renderPage(); 
?>