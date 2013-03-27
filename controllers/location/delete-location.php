<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

Persistence::deleteLocation($_REQUEST['id']);
header('Location:'.Path::toUserHome());

?>