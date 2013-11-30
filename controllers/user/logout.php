<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

UserService::logOutUser();

header('Location:'.Path::toIntro());
exit();

?>