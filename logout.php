<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user/model/User.php';

$user = new User;
$user->logOutUser();

header('Location:'.Path::toIntro());
exit();

?>