<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Paths.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/User.php';

$user = new User;
$user->logOutUser();

header('Location:'.Paths::toIntro());
exit();

?>