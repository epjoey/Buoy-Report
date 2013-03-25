<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$user = new User;
$user->logOutUser();

header('Location:'.Path::toIntro());
exit();

?>