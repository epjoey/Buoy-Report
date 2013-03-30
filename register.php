<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$user = UserService::getUser();

if ($user->isLoggedIn) {
	header('Location:'.Path::toUserHome());
	exit();
}

$e = null;
if (isset($_GET['error']) && $_GET['error']) {
	switch($_GET['error']) {
		case 1: $e = 'Please fill in all fields'; break;
		case 2: $e = 'You must enter a valid email address'; break;
		case 3: $e = 'An account with that email already exists.'; break;
		case 4: $e = 'An account with that username already exists.'; break;
		case 5: $e = 'Password must contain at least 5 characters.'; break;
		case 6: $e = 'Your not human!';
	}
}	


$page = new RegisterPage();
$page->renderPage(array(
	'pageTitle' => 'Sign Up',
	'user' => $user,
	'registerError' => $e
));

?>