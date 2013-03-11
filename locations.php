<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user/model/User.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/LocationPage.php';

$page = new LocationPage();
$page->loadData();
$page->renderPage();
?>