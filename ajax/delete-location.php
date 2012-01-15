<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Paths.php';

Persistence::deleteLocation($_REQUEST['id']);
header('Location:'.Paths::toUserHome());

?>