<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$page = new ReporterPage();
$page->loadData();
$page->renderPage();
?>