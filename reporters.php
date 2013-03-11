<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user/model/User.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/ReporterPage.php';

$page = new ReporterPage();
$page->loadData();
$page->renderPage();
?>